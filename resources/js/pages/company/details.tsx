import { Transition } from '@headlessui/react';
import { Head, router, useForm } from '@inertiajs/react';
import { Building, CheckCircle, Image, MapPin, Target, Upload, Users, X } from 'lucide-react';
import { ChangeEvent, FormEventHandler, useRef, useState } from 'react';

import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import type { Auth, BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile / Company Details',
        href: '/company/details',
    },
];

type ProfileForm = {
    name: string;
    first_name: string;
    last_name: string;
    phone_number: string;
    address: string;
    postcode: string;
    city: string;
    url: string;
    size: string;
    type: string;
    industry: string;
    description_english: string;
};

export default function CompanyDetails({
    auth,
    company,
    shouldShowOnboarding,
    status,
}: {
    auth: Auth;
    company: any;
    shouldShowOnboarding: boolean;
    status?: string;
}) {
    const [uploadingLogo, setUploadingLogo] = useState(false);
    const [uploadingBanner, setUploadingBanner] = useState(false);
    const [logoPreview, setLogoPreview] = useState<string | null>(company.logo_url || null);
    const [bannerPreview, setBannerPreview] = useState<string | null>(company.banner_url || null);
    const logoInputRef = useRef<HTMLInputElement>(null);
    const bannerInputRef = useRef<HTMLInputElement>(null);

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Partial<ProfileForm>>({
        name: company.name || '',
        first_name: company.first_name || '',
        last_name: company.last_name || '',
        phone_number: company.phone_number || '',
        address: company.address || '',
        postcode: company.postcode || '',
        city: company.city || '',
        url: company.url || '',
        size: company.size || '',
        type: company.type || '',
        industry: company.industry || '',
        description_english: company.description_english || '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('company.details.update'), {
            preserveScroll: true,
        });
    };

    const handleLogoUpload = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            setLogoPreview(e.target?.result as string);
        };
        reader.readAsDataURL(file);

        // Upload using Inertia with file
        setUploadingLogo(true);

        router.post(
            route('company.images.logo.upload'),
            {
                logo: file,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    setUploadingLogo(false);
                    // Reload to get updated logo_url from server
                    router.reload({ only: ['company'] });
                },
                onError: () => {
                    setUploadingLogo(false);
                    setLogoPreview(company.logo_url || null);
                },
                onFinish: () => {
                    setUploadingLogo(false);
                },
            },
        );
    };

    const handleBannerUpload = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            setBannerPreview(e.target?.result as string);
        };
        reader.readAsDataURL(file);

        // Upload using Inertia with file
        setUploadingBanner(true);

        router.post(
            route('company.images.banner.upload'),
            {
                banner: file,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    setUploadingBanner(false);
                    // Reload to get updated banner_url from server
                    router.reload({ only: ['company'] });
                },
                onError: () => {
                    setUploadingBanner(false);
                    setBannerPreview(company.banner_url || null);
                },
                onFinish: () => {
                    setUploadingBanner(false);
                },
            },
        );
    };

    const handleLogoDelete = () => {
        if (confirm('Are you sure you want to delete the logo?')) {
            router.delete(route('company.images.logo.delete'), {
                preserveScroll: true,
                onSuccess: () => {
                    setLogoPreview(null);
                    // Reload to get updated data from server
                    router.reload({ only: ['company'] });
                },
            });
        }
    };

    const handleBannerDelete = () => {
        if (confirm('Are you sure you want to delete the banner?')) {
            router.delete(route('company.images.banner.delete'), {
                preserveScroll: true,
                onSuccess: () => {
                    setBannerPreview(null);
                    // Reload to get updated data from server
                    router.reload({ only: ['company'] });
                },
            });
        }
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Details" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <Heading title="Company Details" description="Manage your company profile and branding" />
                    <div className="grid gap-8 lg:grid-cols-3">
                        <div className="lg:col-span-2">
                            {/* Company Images - Simple Upload */}
                            <Card className="mb-8">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Upload className="h-5 w-5" />
                                        Company Branding (optional)
                                    </CardTitle>
                                    <CardDescription>
                                        Upload your company logo and banner. These will be used as defaults for your job listings.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {/* Logo Upload */}
                                    <div>
                                        <Label className="mb-2 block">Company Logo</Label>
                                        <div className="flex items-center gap-4">
                                            {logoPreview ? (
                                                <div className="relative">
                                                    <img src={logoPreview} alt="Company logo" className="h-24 w-24 rounded-lg border object-cover" />
                                                    <button
                                                        type="button"
                                                        onClick={handleLogoDelete}
                                                        className="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                                    >
                                                        <X className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            ) : (
                                                <div className="flex h-24 w-24 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50">
                                                    <Image className="h-8 w-8 text-gray-400" />
                                                </div>
                                            )}
                                            <div className="flex-1">
                                                <input
                                                    ref={logoInputRef}
                                                    type="file"
                                                    accept="image/png,image/jpeg,image/jpg"
                                                    onChange={handleLogoUpload}
                                                    className="hidden"
                                                />
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => logoInputRef.current?.click()}
                                                    disabled={uploadingLogo}
                                                >
                                                    {uploadingLogo ? 'Uploading...' : logoPreview ? 'Change Logo' : 'Upload Logo'}
                                                </Button>
                                                <p className="mt-1 text-xs text-gray-500">Min: 320x320px, Max: 8MB (PNG, JPG)</p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Banner Upload */}
                                    <div>
                                        <Label className="mb-2 block">Company Banner</Label>
                                        <div className="space-y-4">
                                            {bannerPreview ? (
                                                <div className="relative w-full" style={{ aspectRatio: '3/1' }}>
                                                    <img
                                                        src={bannerPreview}
                                                        alt="Company banner"
                                                        className="absolute inset-0 h-full w-full rounded-lg border object-cover"
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={handleBannerDelete}
                                                        className="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                                    >
                                                        <X className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            ) : (
                                                <div
                                                    className="flex w-full items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50"
                                                    style={{ aspectRatio: '3/1' }}
                                                >
                                                    <Image className="h-8 w-8 text-gray-400" />
                                                </div>
                                            )}
                                            <div>
                                                <input
                                                    ref={bannerInputRef}
                                                    type="file"
                                                    accept="image/png,image/jpeg,image/jpg"
                                                    onChange={handleBannerUpload}
                                                    className="hidden"
                                                />
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => bannerInputRef.current?.click()}
                                                    disabled={uploadingBanner}
                                                >
                                                    {uploadingBanner ? 'Uploading...' : bannerPreview ? 'Change Banner' : 'Upload Banner'}
                                                </Button>
                                                <p className="mt-1 text-xs text-gray-500">Min: 1200x400px, Max: 16MB (PNG, JPG)</p>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <form onSubmit={submit} className="space-y-8">
                                {/* Basic Information */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building className="h-5 w-5" />
                                            Basic Information
                                        </CardTitle>
                                        <CardDescription>Essential company information that identifies your business.</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="name">Company Name *</Label>
                                            <Input
                                                id="name"
                                                value={data.name}
                                                onChange={(e) => setData('name', e.target.value)}
                                                required
                                                autoComplete="organization"
                                                placeholder="Your company name"
                                            />
                                            <InputError message={errors.name} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="email">Email Address *</Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                value={company.email || ''}
                                                autoComplete="email"
                                                placeholder="company@example.com"
                                                disabled
                                                readOnly
                                            />
                                            <InputError message={errors.email} />
                                        </div>

                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div className="grid gap-2">
                                                <Label htmlFor="first_name">Contact First Name</Label>
                                                <Input
                                                    id="first_name"
                                                    value={data.first_name}
                                                    onChange={(e) => setData('first_name', e.target.value)}
                                                    autoComplete="given-name"
                                                    placeholder="John"
                                                />
                                                <InputError message={errors.first_name} />
                                            </div>

                                            <div className="grid gap-2">
                                                <Label htmlFor="last_name">Contact Last Name</Label>
                                                <Input
                                                    id="last_name"
                                                    value={data.last_name}
                                                    onChange={(e) => setData('last_name', e.target.value)}
                                                    autoComplete="family-name"
                                                    placeholder="Doe"
                                                />
                                                <InputError message={errors.last_name} />
                                            </div>
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="phone_number">Phone Number</Label>
                                            <Input
                                                id="phone_number"
                                                type="tel"
                                                value={data.phone_number}
                                                onChange={(e) => setData('phone_number', e.target.value)}
                                                autoComplete="tel"
                                                placeholder="+41 xx xxx xx xx"
                                            />
                                            <InputError message={errors.phone_number} />
                                        </div>
                                    </CardContent>
                                </Card>

                                {/* Contact Information */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <MapPin className="h-5 w-5" />
                                            Contact Information
                                        </CardTitle>
                                        <CardDescription>Location and contact details for your company.</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="address">Address</Label>
                                            <Input
                                                id="address"
                                                value={data.address}
                                                onChange={(e) => setData('address', e.target.value)}
                                                placeholder="Street address"
                                            />
                                            <InputError message={errors.address} />
                                        </div>

                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div className="grid gap-2">
                                                <Label htmlFor="postcode">Postal Code</Label>
                                                <Input
                                                    id="postcode"
                                                    value={data.postcode}
                                                    onChange={(e) => setData('postcode', e.target.value)}
                                                    placeholder="8000"
                                                />
                                                <InputError message={errors.postcode} />
                                            </div>

                                            <div className="grid gap-2">
                                                <Label htmlFor="city">City</Label>
                                                <Input
                                                    id="city"
                                                    value={data.city}
                                                    onChange={(e) => setData('city', e.target.value)}
                                                    placeholder="Zurich"
                                                />
                                                <InputError message={errors.city} />
                                            </div>
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="url">Website URL</Label>
                                            <Input
                                                id="url"
                                                value={data.url}
                                                onChange={(e) => setData('url', e.target.value)}
                                                placeholder="https://example.com"
                                            />
                                            <InputError message={errors.url} />
                                        </div>
                                    </CardContent>
                                </Card>

                                {/* Company Details */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Users className="h-5 w-5" />
                                            Company Details (optional)
                                        </CardTitle>
                                        <CardDescription>Information about your company size, industry, and type.</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div className="grid gap-2">
                                                <Label htmlFor="size">Company Size</Label>
                                                <Input
                                                    id="size"
                                                    value={data.size}
                                                    onChange={(e) => setData('size', e.target.value)}
                                                    placeholder="e.g. 10-50 employees"
                                                />
                                                <InputError message={errors.size} />
                                            </div>

                                            <div className="grid gap-2">
                                                <Label htmlFor="type">Company Type</Label>
                                                <Input
                                                    id="type"
                                                    value={data.type}
                                                    onChange={(e) => setData('type', e.target.value)}
                                                    placeholder="e.g. Private, Public, Startup"
                                                />
                                                <InputError message={errors.type} />
                                            </div>
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="industry">Industry</Label>
                                            <Input
                                                id="industry"
                                                value={data.industry}
                                                onChange={(e) => setData('industry', e.target.value)}
                                                placeholder="e.g. Technology, Finance, Healthcare"
                                            />
                                            <InputError message={errors.industry} />
                                        </div>
                                    </CardContent>
                                </Card>

                                {/* Company Description */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Target className="h-5 w-5" />
                                            Company Description (optional)
                                        </CardTitle>
                                        <CardDescription>Tell potential candidates about your company and mission.</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="description_english">Company Description (English)</Label>
                                            <Textarea
                                                id="description_english"
                                                value={data.description_english}
                                                onChange={(e) => setData('description_english', e.target.value)}
                                                rows={6}
                                                placeholder="Describe your company, what you do, and what makes you unique..."
                                            />
                                            <InputError message={errors.description_english} />
                                        </div>
                                    </CardContent>
                                </Card>

                                <div className="flex items-center gap-4">
                                    <Button disabled={processing}>{processing ? 'Saving...' : 'Save Details'}</Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-green-600">Details saved successfully!</p>
                                    </Transition>
                                </div>
                            </form>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Profile Tips</CardTitle>
                                    <CardDescription>Make your company profile stand out</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    <div className="flex items-start gap-2">
                                        <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                        <p>Add a clear, professional company description that highlights what makes you unique.</p>
                                    </div>
                                    <div className="flex items-start gap-2">
                                        <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                        <p>Upload a high-quality logo and banner to build brand recognition.</p>
                                    </div>
                                    <div className="flex items-start gap-2">
                                        <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                        <p>Include accurate company details to help candidates understand your organization.</p>
                                    </div>
                                    <div className="flex items-start gap-2">
                                        <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                        <p>Keep your contact information up-to-date for easy communication.</p>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
