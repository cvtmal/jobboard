import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { useImageUpload } from '@/hooks/use-image-upload';
import { useState } from 'react';
import { BannerPreviewCard } from './BannerPreviewCard';
import { ImageCropModal } from './ImageCropModal';
import { LogoUploadTile } from './LogoUploadTile';

interface JobListingImageUploaderProps {
    /**
     * Current job listing banner URL (if exists)
     */
    currentBannerUrl?: string;
    /**
     * Current job listing logo URL (if exists)
     */
    currentLogoUrl?: string;
    /**
     * Company fallback banner URL
     */
    companyBannerUrl?: string;
    /**
     * Company fallback logo URL
     */
    companyLogoUrl?: string;
    /**
     * Whether to use company logo (vs custom)
     */
    useCompanyLogo?: boolean;
    /**
     * Whether to use company banner (vs custom)
     */
    useCompanyBanner?: boolean;
    /**
     * Callback when banner image changes
     */
    onBannerChange?: (file: File | null) => void;
    /**
     * Callback when logo image changes
     */
    onLogoChange?: (file: File | null) => void;
    /**
     * Callback when company image toggle changes
     */
    onToggleChange?: (useCompanyLogo: boolean, useCompanyBanner: boolean) => void;
    /**
     * Whether the component is in a disabled state
     */
    disabled?: boolean;
    /**
     * Any validation errors from the server
     */
    errors?: {
        banner?: string;
        logo?: string;
    };
    /**
     * Upload mode:
     * - 'form': Files are passed to parent component for form submission (default)
     * - 'direct': Files are uploaded directly to backend endpoints
     */
    mode?: 'form' | 'direct';
}

export function JobListingImageUploader({
    currentBannerUrl,
    currentLogoUrl,
    companyBannerUrl,
    companyLogoUrl,
    useCompanyLogo = true,
    useCompanyBanner = true,
    onBannerChange,
    onLogoChange,
    onToggleChange,
    disabled = false,
    errors = {},
    mode = 'form',
}: JobListingImageUploaderProps) {
    const [localUseCompanyLogo, setLocalUseCompanyLogo] = useState(useCompanyLogo);
    const [localUseCompanyBanner, setLocalUseCompanyBanner] = useState(useCompanyBanner);

    const imageUpload = useImageUpload({
        onBannerUpload: onBannerChange,
        onLogoUpload: onLogoChange,
        mode,
    });

    const handleLogoToggle = (useCompany: boolean) => {
        setLocalUseCompanyLogo(useCompany);
        onToggleChange?.(useCompany, localUseCompanyBanner);
    };

    const handleBannerToggle = (useCompany: boolean) => {
        setLocalUseCompanyBanner(useCompany);
        onToggleChange?.(localUseCompanyLogo, useCompany);
    };

    // Determine effective image URLs based on toggle state
    const effectiveLogoUrl = localUseCompanyLogo ? companyLogoUrl : currentLogoUrl || imageUpload.logo.previewUrl;

    const effectiveBannerUrl = localUseCompanyBanner ? companyBannerUrl : currentBannerUrl || imageUpload.banner.previewUrl;

    return (
        <Card className="overflow-hidden">
            <div className="space-y-6 p-6">
                <div className="space-y-4">
                    <h3 className="text-lg font-medium">Job Listing Images</h3>
                    <p className="text-muted-foreground text-sm">
                        Choose to use your company's default images or upload custom images for this job listing.
                    </p>
                </div>

                {/* Banner Section */}
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <Label className="text-base font-medium">Banner Image</Label>
                        <div className="flex items-center space-x-2">
                            <Label htmlFor="use-company-banner" className="text-sm">
                                Use company banner
                            </Label>
                            <Switch
                                id="use-company-banner"
                                checked={localUseCompanyBanner}
                                onCheckedChange={handleBannerToggle}
                                disabled={disabled}
                            />
                        </div>
                    </div>

                    <BannerPreviewCard
                        imageUrl={effectiveBannerUrl}
                        onEditClick={() => !localUseCompanyBanner && imageUpload.openModal('banner')}
                        disabled={disabled || localUseCompanyBanner}
                        error={errors.banner}
                        showCompanyLabel={localUseCompanyBanner}
                    />

                    {localUseCompanyBanner && !companyBannerUrl && (
                        <p className="text-sm text-amber-600">
                            No company banner available. Upload a company banner in your profile settings or use a custom banner for this job.
                        </p>
                    )}
                </div>

                {/* Logo Section */}
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <Label className="text-base font-medium">Logo</Label>
                        <div className="flex items-center space-x-2">
                            <Label htmlFor="use-company-logo" className="text-sm">
                                Use company logo
                            </Label>
                            <Switch id="use-company-logo" checked={localUseCompanyLogo} onCheckedChange={handleLogoToggle} disabled={disabled} />
                        </div>
                    </div>

                    <LogoUploadTile
                        imageUrl={effectiveLogoUrl}
                        onClick={() => !localUseCompanyLogo && imageUpload.openModal('logo')}
                        disabled={disabled || localUseCompanyLogo}
                        error={errors.logo}
                        showCompanyLabel={localUseCompanyLogo}
                    />

                    {localUseCompanyLogo && !companyLogoUrl && (
                        <p className="text-sm text-amber-600">
                            No company logo available. Upload a company logo in your profile settings or use a custom logo for this job.
                        </p>
                    )}
                </div>
            </div>

            {/* Crop Modals */}
            <ImageCropModal
                isOpen={imageUpload.modal.isOpen && imageUpload.modal.type === 'banner'}
                onClose={imageUpload.closeModal}
                image={imageUpload.banner.selectedImage}
                onCrop={imageUpload.handleBannerCrop}
                onDelete={() => imageUpload.handleDelete('banner')}
                onChooseNew={() => imageUpload.selectImage('banner')}
                type="banner"
                title="Upload Custom Banner"
                description="Die Mindestgrösse des Banners beträgt 1200 x 400 Pixel, max 16 MB Formate: PNG, JPG."
                aspectRatio={3} // 3:1 ratio for banner
                minDimensions={{ width: 1200, height: 400 }}
                maxFileSize={16 * 1024 * 1024} // 16MB
            />

            <ImageCropModal
                isOpen={imageUpload.modal.isOpen && imageUpload.modal.type === 'logo'}
                onClose={imageUpload.closeModal}
                image={imageUpload.logo.selectedImage}
                onCrop={imageUpload.handleLogoCrop}
                onDelete={() => imageUpload.handleDelete('logo')}
                onChooseNew={() => imageUpload.selectImage('logo')}
                type="logo"
                title="Upload Custom Logo"
                description="Die Mindestgrösse des Logos beträgt: 320 x 320 Pixel, max 8MB Formate: PNG, JPG."
                aspectRatio={1} // 1:1 ratio for logo
                minDimensions={{ width: 320, height: 320 }}
                maxFileSize={8 * 1024 * 1024} // 8MB
            />
        </Card>
    );
}
