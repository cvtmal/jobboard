import { Card } from '@/components/ui/card';
import { useImageUpload } from '@/hooks/use-image-upload';
import { BannerPreviewCard } from './BannerPreviewCard';
import { ImageCropModal } from './ImageCropModal';
import { LogoUploadTile } from './LogoUploadTile';

interface CompanyImageUploaderProps {
    /**
     * Current banner image URL (if exists)
     */
    currentBannerUrl?: string;
    /**
     * Current logo image URL (if exists)
     */
    currentLogoUrl?: string;
    /**
     * Callback when banner image changes
     */
    onBannerChange?: (file: File | null) => void;
    /**
     * Callback when logo image changes
     */
    onLogoChange?: (file: File | null) => void;
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

export function CompanyImageUploader({
    currentBannerUrl,
    currentLogoUrl,
    onBannerChange,
    onLogoChange,
    disabled = false,
    errors = {},
    mode = 'form',
}: CompanyImageUploaderProps) {
    const imageUpload = useImageUpload({
        onBannerUpload: onBannerChange,
        onLogoUpload: onLogoChange,
        mode,
    });

    return (
        <Card className="overflow-hidden">
            <div className="space-y-6 p-6">
                {/* Banner Upload Section */}
                <BannerPreviewCard
                    imageUrl={currentBannerUrl || imageUpload.banner.previewUrl}
                    onEditClick={() => imageUpload.openModal('banner')}
                    disabled={disabled}
                    error={errors.banner}
                />

                {/* Logo Upload Section */}
                <LogoUploadTile
                    imageUrl={currentLogoUrl || imageUpload.logo.previewUrl}
                    onClick={() => imageUpload.openModal('logo')}
                    disabled={disabled}
                    error={errors.logo}
                />
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
                title="Upload new Image"
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
                title="Upload new Logo"
                description="Die Mindestgrösse des Logos beträgt: 320 x 320 Pixel, max 8MB Formate: PNG, JPG."
                aspectRatio={1} // 1:1 ratio for logo
                minDimensions={{ width: 320, height: 320 }}
                maxFileSize={8 * 1024 * 1024} // 8MB
            />
        </Card>
    );
}
