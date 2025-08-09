import { Transition } from '@headlessui/react';
import { Head, router, useForm } from '@inertiajs/react';
import { Eye, ImageIcon, Plus, Trash2, Upload, Youtube } from 'lucide-react';
import { FormEventHandler, useEffect, useState } from 'react';

import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import CompanyLayout from '@/layouts/company-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile / Career page',
        href: '/company/career-page',
    },
];

interface CareerPageVideo {
    id?: string;
    url: string;
    title?: string;
}

interface CompanyData {
    career_page_enabled: boolean;
    career_page_slug: string | null;
    career_page_image: string | null;
    career_page_videos: CareerPageVideo[];
    career_page_domain: string | null;
    spontaneous_application_enabled: boolean;
    career_page_visibility: boolean;
}

interface Props {
    company: CompanyData;
    status?: string;
}

type CareerPageForm = {
    career_page_enabled: boolean;
    career_page_slug: string;
    career_page_image: File | null;
    career_page_domain: string;
    spontaneous_application_enabled: boolean;
    career_page_visibility: boolean;
};

export default function CareerPageEdit({ company, status }: Props) {
    const [dragActive, setDragActive] = useState(false);
    const [newVideoUrl, setNewVideoUrl] = useState('');
    const [showVideoInput, setShowVideoInput] = useState(false);
    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const { data, setData, post, processing, recentlySuccessful } = useForm<CareerPageForm>({
        career_page_enabled: company.career_page_enabled,
        career_page_slug: company.career_page_slug || '',
        career_page_image: null,
        career_page_domain: company.career_page_domain || '',
        spontaneous_application_enabled: company.spontaneous_application_enabled,
        career_page_visibility: company.career_page_visibility,
    });

    const [showSuccessMessage, setShowSuccessMessage] = useState(false);

    // Cleanup preview URL on unmount
    useEffect(() => {
        return () => {
            if (imagePreview) {
                URL.revokeObjectURL(imagePreview);
            }
        };
    }, [imagePreview]);

    // Show success message when form is successful
    useEffect(() => {
        if (recentlySuccessful || status === 'career-page-updated' || status === 'video-added' || status === 'video-removed') {
            setShowSuccessMessage(true);
        }
    }, [recentlySuccessful, status]);

    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setDragActive(true);
        } else if (e.type === 'dragleave') {
            setDragActive(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    };

    const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;
        if (files && files.length > 0) {
            handleFile(files[0]);
        }
    };

    const handleFile = (file: File) => {
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not a valid image file`);
            return;
        }

        // Check file size (3.5MB)
        if (file.size > 3.5 * 1024 * 1024) {
            alert(`${file.name} is too large. Maximum size is 3.5MB`);
            return;
        }

        // Validate dimensions
        const img = new Image();
        img.onload = () => {
            if (img.width < 752 || img.height < 480) {
                alert(`${file.name} dimensions are too small. Minimum size is 752x480 pixels`);
                return;
            }

            // Clean up existing preview URL if it exists
            if (imagePreview) {
                URL.revokeObjectURL(imagePreview);
            }

            // If validation passes, set the file and create preview
            const previewUrl = URL.createObjectURL(file);
            setData('career_page_image', file);
            setImagePreview(previewUrl);
        };
        img.src = URL.createObjectURL(file);
    };

    const removeImage = () => {
        if (imagePreview) {
            URL.revokeObjectURL(imagePreview);
        }

        setData('career_page_image', null);
        setImagePreview(null);

        if (company.career_page_image) {
            router.delete(route('company.career-page.image.destroy'), {
                preserveScroll: true,
            });
        }
    };

    const addVideo = () => {
        if (!newVideoUrl.trim()) return;

        // Basic YouTube URL validation
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+/;
        if (!youtubeRegex.test(newVideoUrl)) {
            alert('Please enter a valid YouTube URL');
            return;
        }

        // Make direct Inertia call to backend
        router.post(
            route('company.career-page.videos.store'),
            {
                url: newVideoUrl,
                title: 'YouTube Video',
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setNewVideoUrl('');
                    setShowVideoInput(false);
                },
                onError: (errors) => {
                    if (errors.url) {
                        alert(errors.url);
                    } else {
                        alert('Failed to add video. Please try again.');
                    }
                },
            },
        );
    };

    const removeVideo = (videoId: string) => {
        // Make direct Inertia call to backend
        router.delete(route('company.career-page.videos.destroy', { videoId }), {
            preserveScroll: true,
            onError: () => {
                alert('Failed to remove video. Please try again.');
            },
        });
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('company.career-page.update'));
    };

    const previewPage = () => {
        window.open(route('company.career-page.preview'), '_blank');
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Career page" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        {/* Main Content */}
                        <div className="space-y-8 lg:col-span-2">
                            {/* Header */}
                            <div className="flex items-center justify-between">
                                <div>
                                    <Heading title="Career Page Settings" description="Customize your company's career page to attract top talent." />
                                </div>
                                <div className="flex gap-3">
                                    <Button variant="outline" onClick={previewPage}>
                                        <Eye className="h-4 w-4" />
                                        Preview
                                    </Button>
                                    <Button onClick={submit} disabled={processing}>
                                        Save Changes
                                    </Button>
                                </div>
                            </div>

                            <form onSubmit={submit} className="space-y-8">
                                {/* Success Message */}
                                <Transition
                                    show={showSuccessMessage}
                                    appear
                                    className="transition duration-300 ease-in-out data-closed:opacity-0"
                                    afterEnter={() => setTimeout(() => setShowSuccessMessage(false), 2000)}
                                >
                                    <div className="rounded-md bg-green-50 p-4">
                                        <p className="text-sm text-green-700">
                                            {status === 'video-added' && 'Video added successfully!'}
                                            {status === 'video-removed' && 'Video removed successfully!'}
                                            {(status === 'career-page-updated' || recentlySuccessful) && 'Career page updated successfully!'}
                                        </p>
                                    </div>
                                </Transition>

                                {/* Career Page Toggle */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Career Page</CardTitle>
                                        <CardDescription>Enable or disable your company's career page</CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="flex items-center justify-between">
                                            <div className="flex-1">
                                                <Label htmlFor="career-page-enabled">Enable Career Page</Label>
                                                <p className="mt-1 text-sm text-gray-500">
                                                    When enabled, your company will have a dedicated career page
                                                </p>
                                            </div>
                                            <Switch
                                                id="career-page-enabled"
                                                checked={data.career_page_enabled}
                                                onCheckedChange={(checked) => setData('career_page_enabled', checked)}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>

                                {data.career_page_enabled && (
                                    <>
                                        {/* Career Page Image */}
                                        <Card>
                                            <CardHeader>
                                                <CardTitle>Career Page Image</CardTitle>
                                                <CardDescription>Add an image to showcase your company culture and workspace</CardDescription>
                                            </CardHeader>
                                            <CardContent className="space-y-6">
                                                {/* Upload Area */}
                                                <div
                                                    className={`rounded-lg border-2 border-dashed p-8 text-center transition-colors ${
                                                        dragActive ? 'border-primary bg-primary/5' : 'border-gray-300 hover:border-gray-400'
                                                    }`}
                                                    onDragEnter={handleDrag}
                                                    onDragLeave={handleDrag}
                                                    onDragOver={handleDrag}
                                                    onDrop={handleDrop}
                                                >
                                                    <ImageIcon className="mx-auto mb-4 h-12 w-12 text-gray-400" />
                                                    <h3 className="mb-2 text-lg font-medium text-gray-900">
                                                        {data.career_page_image || company.career_page_image
                                                            ? 'Replace image'
                                                            : 'Drag & drop image upload'}
                                                    </h3>
                                                    <p className="mb-4 text-gray-500">
                                                        Upload an image smaller than 3.5MB and at least 752px by 480px.
                                                    </p>
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        onClick={() => document.getElementById('file-upload')?.click()}
                                                    >
                                                        <Upload className="mr-2 h-4 w-4" />
                                                        {data.career_page_image || company.career_page_image ? 'Replace Image' : 'Choose Image'}
                                                    </Button>
                                                    <input
                                                        id="file-upload"
                                                        type="file"
                                                        accept="image/*"
                                                        className="hidden"
                                                        onChange={handleFileInput}
                                                    />
                                                </div>

                                                {/* Image Preview */}
                                                {(data.career_page_image || imagePreview || company.career_page_image) && (
                                                    <div className="group relative inline-block">
                                                        <img
                                                            src={imagePreview || company.career_page_image || ''}
                                                            alt="Career page image"
                                                            className="h-48 w-full max-w-md rounded-lg object-cover"
                                                        />
                                                        <button
                                                            type="button"
                                                            onClick={removeImage}
                                                            className="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </button>
                                                    </div>
                                                )}
                                            </CardContent>
                                        </Card>

                                        {/* Videos Section */}
                                        <Card>
                                            <CardHeader>
                                                <CardTitle>Videos</CardTitle>
                                                <CardDescription>
                                                    Add YouTube videos to give candidates a better sense of your company
                                                </CardDescription>
                                            </CardHeader>
                                            <CardContent className="space-y-6">
                                                {company.career_page_videos.length === 0 && !showVideoInput ? (
                                                    <div className="py-12 text-center">
                                                        <Youtube className="mx-auto mb-4 h-12 w-12 text-gray-400" />
                                                        <h3 className="mb-2 text-lg font-medium text-gray-900">No videos yet</h3>
                                                        <p className="mb-6 text-gray-500">
                                                            You haven't added any videos yet. You can add videos from YouTube.
                                                        </p>
                                                        <Button type="button" onClick={() => setShowVideoInput(true)}>
                                                            <Plus className="mr-2 h-4 w-4" />
                                                            Add video
                                                        </Button>
                                                    </div>
                                                ) : (
                                                    <div className="space-y-4">
                                                        {/* Existing videos */}
                                                        {company.career_page_videos.map((video) => (
                                                            <div key={video.id} className="flex items-center justify-between rounded-lg border p-4">
                                                                <div className="flex items-center gap-3">
                                                                    <Youtube className="h-5 w-5 text-red-500" />
                                                                    <div>
                                                                        <p className="font-medium">{video.title || 'YouTube Video'}</p>
                                                                        <p className="text-sm text-gray-500">{video.url}</p>
                                                                    </div>
                                                                </div>
                                                                <Button
                                                                    type="button"
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    onClick={() => removeVideo(video.id!)}
                                                                >
                                                                    <Trash2 className="h-4 w-4" />
                                                                </Button>
                                                            </div>
                                                        ))}

                                                        {/* Add video input */}
                                                        {showVideoInput && (
                                                            <div className="flex gap-2">
                                                                <Input
                                                                    type="url"
                                                                    placeholder="Enter YouTube URL"
                                                                    value={newVideoUrl}
                                                                    onChange={(e) => setNewVideoUrl(e.target.value)}
                                                                />
                                                                <Button type="button" onClick={addVideo}>
                                                                    Add
                                                                </Button>
                                                                <Button
                                                                    type="button"
                                                                    variant="outline"
                                                                    onClick={() => {
                                                                        setShowVideoInput(false);
                                                                        setNewVideoUrl('');
                                                                    }}
                                                                >
                                                                    Cancel
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {!showVideoInput && (
                                                            <Button type="button" variant="outline" onClick={() => setShowVideoInput(true)}>
                                                                <Plus className="mr-2 h-4 w-4" />
                                                                Add video
                                                            </Button>
                                                        )}
                                                    </div>
                                                )}
                                            </CardContent>
                                        </Card>

                                        {/* Domain Section */}
                                        <Card>
                                            <CardHeader>
                                                <CardTitle>Domain</CardTitle>
                                                <CardDescription>Customize your company's career page URL</CardDescription>
                                            </CardHeader>
                                            <CardContent>
                                                <div className="grid gap-2">
                                                    <div className="flex">
                                                        <span className="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                                                            https://
                                                        </span>
                                                        <Input
                                                            id="domain"
                                                            type="text"
                                                            value={data.career_page_domain}
                                                            onChange={(e) => setData('career_page_domain', e.target.value)}
                                                            className="rounded-l-none"
                                                        />
                                                    </div>
                                                </div>
                                            </CardContent>
                                        </Card>

                                        {/* Visibility Options */}
                                        <Card>
                                            <CardHeader>
                                                <CardTitle>Visibility Options</CardTitle>
                                            </CardHeader>
                                            <CardContent className="space-y-6">
                                                <div className="flex items-start justify-between">
                                                    <div className="flex-1">
                                                        <Label htmlFor="spontaneous">Spontaneous Applications</Label>
                                                        <p className="mt-1 text-sm text-gray-500">
                                                            Allow candidates to apply to your company even if they haven't found a suitable job
                                                            posting.
                                                        </p>
                                                    </div>
                                                    <Switch
                                                        id="spontaneous"
                                                        checked={data.spontaneous_application_enabled}
                                                        onCheckedChange={(checked) => setData('spontaneous_application_enabled', checked)}
                                                    />
                                                </div>
                                                <div className="flex items-start justify-between">
                                                    <div className="flex-1">
                                                        <Label htmlFor="visibility">Career Page Visibility</Label>
                                                        <p className="mt-1 text-sm text-gray-500">
                                                            Control whether your career page is publicly visible.
                                                        </p>
                                                    </div>
                                                    <Switch
                                                        id="visibility"
                                                        checked={data.career_page_visibility}
                                                        onCheckedChange={(checked) => setData('career_page_visibility', checked)}
                                                    />
                                                </div>
                                            </CardContent>
                                        </Card>
                                    </>
                                )}
                            </form>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Tips Card */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Tips for Success</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm text-gray-600">
                                        <li>• Use a high-quality image that showcases your company culture</li>
                                        <li>• Keep your career page URL short and memorable</li>
                                        <li>• Enable spontaneous applications to catch passive candidates</li>
                                        <li>• Add videos to give candidates a better sense of your workplace</li>
                                        <li>• Keep your career page content up-to-date</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            {/* Why Add Image Card */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Why add an image?</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-gray-600">
                                        Including a photo on your company profile helps potential candidates get a glimpse of your workplace and
                                        company culture.
                                    </p>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
