/**
 * Example usage of CompanyImageUploader in a job listing form
 * This shows how to integrate with Inertia.js forms
 */

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useForm } from '@inertiajs/react';
import React from 'react';
import { CompanyImageUploader } from './CompanyImageUploader';

interface JobListingFormData {
    title: string;
    description: string;
    // ... other job listing fields
    banner_image?: File;
    logo_image?: File;
}

export function JobListingFormWithImages() {
    const { data, setData, post, processing, errors } = useForm<JobListingFormData>({
        title: '',
        description: '',
        // ... other initial values
    });

    const handleBannerChange = (file: File | null) => {
        setData('banner_image', file || undefined);
    };

    const handleLogoChange = (file: File | null) => {
        setData('logo_image', file || undefined);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Inertia.js will handle file uploads automatically when using FormData
        post(route('company.job-listings.store'), {
            forceFormData: true, // Ensures files are handled properly
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-8">
            {/* Company Branding Section - Place above job title */}
            <Card>
                <CardHeader>
                    <CardTitle>Company Branding</CardTitle>
                    <CardDescription>Add your company banner and logo to make your job listing stand out</CardDescription>
                </CardHeader>
                <CardContent>
                    <CompanyImageUploader
                        currentBannerUrl={undefined} // Could be existing banner URL from server
                        currentLogoUrl={undefined} // Could be existing logo URL from server
                        onBannerChange={handleBannerChange}
                        onLogoChange={handleLogoChange}
                        disabled={processing}
                        errors={{
                            banner: errors.banner_image,
                            logo: errors.logo_image,
                        }}
                    />
                </CardContent>
            </Card>

            {/* Rest of your job listing form... */}
            {/* Job title, description, etc. */}
        </form>
    );
}

/**
 * Alternative usage with company settings/profile page
 */
export function CompanyProfileWithImages() {
    const { data, setData, patch, processing, errors } = useForm({
        name: '',
        // ... other company fields
        banner_image: undefined as File | undefined,
        logo_image: undefined as File | undefined,
    });

    return (
        <div className="space-y-8">
            {/* Company Images Section */}
            <Card>
                <CardHeader>
                    <CardTitle>Company Images</CardTitle>
                    <CardDescription>Upload your company banner and logo</CardDescription>
                </CardHeader>
                <CardContent>
                    <CompanyImageUploader
                        currentBannerUrl="/storage/company-banners/current-banner.jpg"
                        currentLogoUrl="/storage/company-logos/current-logo.jpg"
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

            {/* Other company profile fields... */}
        </div>
    );
}
