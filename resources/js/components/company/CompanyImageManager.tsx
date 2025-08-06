/**
 * Comprehensive Company Image Manager
 * This component demonstrates both 'form' and 'direct' modes for image uploads
 */

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CompanyImageUploader } from './CompanyImageUploader';

interface CompanyImageManagerProps {
    /**
     * Current banner image URL from server
     */
    currentBannerUrl?: string;
    /**
     * Current logo image URL from server
     */
    currentLogoUrl?: string;
    /**
     * Upload mode:
     * - 'form': Images are handled via form submission (default, for job listings)
     * - 'direct': Images are uploaded directly to backend endpoints (for company settings)
     */
    mode?: 'form' | 'direct';
    /**
     * Callbacks for form mode
     */
    onBannerChange?: (file: File | null) => void;
    onLogoChange?: (file: File | null) => void;
    /**
     * Whether the component is disabled
     */
    disabled?: boolean;
    /**
     * Form validation errors
     */
    errors?: {
        banner?: string;
        logo?: string;
    };
}

/**
 * Company Image Manager - supports both form and direct upload modes
 */
export function CompanyImageManager({
    currentBannerUrl,
    currentLogoUrl,
    mode = 'form',
    onBannerChange,
    onLogoChange,
    disabled = false,
    errors = {},
}: CompanyImageManagerProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Company Branding</CardTitle>
                <CardDescription>
                    {mode === 'direct'
                        ? 'Upload and manage your company banner and logo. Changes are saved immediately.'
                        : 'Add your company banner and logo to make your job listing stand out from the competition.'}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <CompanyImageUploader
                    currentBannerUrl={currentBannerUrl}
                    currentLogoUrl={currentLogoUrl}
                    onBannerChange={onBannerChange}
                    onLogoChange={onLogoChange}
                    disabled={disabled}
                    errors={errors}
                    mode={mode}
                />
            </CardContent>
        </Card>
    );
}

/**
 * Example: Usage in Job Listing Form (Form Mode)
 */
export function JobListingFormExample() {
    // This would typically come from useForm
    const handleBannerChange = (file: File | null) => {
        console.log('Banner changed:', file?.name);
        // setData('banner_image', file || undefined);
    };

    const handleLogoChange = (file: File | null) => {
        console.log('Logo changed:', file?.name);
        // setData('logo_image', file || undefined);
    };

    return (
        <CompanyImageManager
            mode="form"
            onBannerChange={handleBannerChange}
            onLogoChange={handleLogoChange}
            errors={{
                banner: undefined, // errors.banner_image
                logo: undefined, // errors.logo_image
            }}
        />
    );
}

/**
 * Example: Usage in Company Settings (Direct Mode)
 */
export function CompanySettingsExample() {
    return (
        <CompanyImageManager
            mode="direct"
            currentBannerUrl="/storage/company-images/banners/current-banner.jpg"
            currentLogoUrl="/storage/company-images/logos/current-logo.jpg"
            // No need for onChange callbacks in direct mode
            // Images are uploaded immediately when cropped
        />
    );
}
