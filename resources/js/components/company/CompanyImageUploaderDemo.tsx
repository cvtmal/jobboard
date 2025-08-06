/**
 * Demo component showing how to integrate CompanyImageUploader
 * into the existing job listing creation form
 */

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { CompanyImageUploader } from './CompanyImageUploader';

interface CreateJobListingWithImagesProps {
    auth: { company?: { id: string; city?: string } };
    errors: Record<string, string>;
    categoryOptions: Record<string, string>;
}

export default function CreateJobListingWithImages({ auth, errors, categoryOptions }: CreateJobListingWithImagesProps) {
    const { data, setData, post, processing } = useForm({
        // Existing fields
        title: '',
        company_description: '',
        description: '',
        requirements: '',

        // New image fields
        banner_image: undefined as File | undefined,
        logo_image: undefined as File | undefined,

        // Other required fields
        company_id: auth.company?.id || '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!auth.company) {
            window.location.href = route('company.login');
            return;
        }

        // Important: Use forceFormData for file uploads
        post(route('company.job-listings.store'), {
            forceFormData: true,
        });
    };

    return (
        <div className="py-8">
            <div className="mx-auto max-w-5xl">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold tracking-tight">Create New Job Listing</h1>
                    <p className="text-muted-foreground mt-2">
                        Fill in the details below to create your job listing and start attracting candidates.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-8">
                    {/* NEW: Company Branding Section - Placed at the top */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Company Branding</CardTitle>
                            <CardDescription>
                                Add your company banner and logo to make your job listing stand out from the competition
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <CompanyImageUploader
                                onBannerChange={(file) => setData('banner_image', file || undefined)}
                                onLogoChange={(file) => setData('logo_image', file || undefined)}
                                disabled={processing}
                                errors={{
                                    banner: errors.banner_image,
                                    logo: errors.logo_image,
                                }}
                            />
                        </CardContent>
                    </Card>

                    {/* Existing Job Information Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Job Information</CardTitle>
                            <CardDescription>Basic information about the job position</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div>
                                <Label htmlFor="title" className="text-base">
                                    Job Title <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="title"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className="mt-1.5"
                                    required
                                    placeholder="e.g., Senior React Developer"
                                />
                                {errors.title && <p className="mt-1 text-sm text-red-500">{errors.title}</p>}
                            </div>

                            <div>
                                <Label htmlFor="company_description" className="text-base">
                                    Company Description
                                </Label>
                                <Textarea
                                    id="company_description"
                                    value={data.company_description}
                                    onChange={(e) => setData('company_description', e.target.value)}
                                    className="mt-1.5 min-h-[100px]"
                                    placeholder="Tell candidates about your company, culture, and mission..."
                                />
                                {errors.company_description && <p className="mt-1 text-sm text-red-500">{errors.company_description}</p>}
                            </div>

                            <div>
                                <Label htmlFor="description" className="text-base">
                                    Job Description <span className="text-red-500">*</span>
                                </Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="mt-1.5 min-h-[150px]"
                                    placeholder="Describe the role, responsibilities, and what makes this position exciting..."
                                    required
                                />
                                {errors.description && <p className="mt-1 text-sm text-red-500">{errors.description}</p>}
                            </div>

                            <div>
                                <Label htmlFor="requirements" className="text-base">
                                    Requirements <span className="text-red-500">*</span>
                                </Label>
                                <Textarea
                                    id="requirements"
                                    value={data.requirements}
                                    onChange={(e) => setData('requirements', e.target.value)}
                                    className="mt-1.5 min-h-[150px]"
                                    placeholder="List the qualifications, skills, and experience required for this role..."
                                    required
                                />
                                {errors.requirements && <p className="mt-1 text-sm text-red-500">{errors.requirements}</p>}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Submit Section */}
                    <div className="bg-card flex items-center justify-between rounded-lg border p-6">
                        <div>
                            <h3 className="font-semibold">Ready to publish?</h3>
                            <p className="text-muted-foreground text-sm">
                                Your job listing will be visible to candidates immediately after creation.
                            </p>
                        </div>
                        <div className="flex items-center gap-4">
                            <Button type="button" variant="outline" onClick={() => window.history.back()} disabled={processing}>
                                Cancel
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Creating...' : 'Create Job Listing'}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

/**
 * Preview of how the banner and logo would appear in the job listing
 */
export function JobListingPreview({
    bannerUrl,
    logoUrl,
    companyName,
    jobTitle,
}: {
    bannerUrl?: string;
    logoUrl?: string;
    companyName: string;
    jobTitle: string;
}) {
    return (
        <Card className="overflow-hidden">
            {/* Banner */}
            {bannerUrl && (
                <div className="relative aspect-[3/1] w-full">
                    <img src={bannerUrl} alt={`${companyName} banner`} className="h-full w-full object-cover" />
                </div>
            )}

            {/* Content */}
            <CardContent className="p-6">
                <div className="flex items-start gap-4">
                    {/* Logo */}
                    {logoUrl && (
                        <div className="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border">
                            <img src={logoUrl} alt={`${companyName} logo`} className="h-full w-full object-contain" />
                        </div>
                    )}

                    {/* Job Info */}
                    <div className="min-w-0 flex-1">
                        <h2 className="text-xl font-semibold">{jobTitle}</h2>
                        <p className="text-muted-foreground">{companyName}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
