import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { PencilIcon, UploadIcon } from 'lucide-react';
import { useImageUpload } from '@/hooks/use-image-upload';
import { ImageCropModal } from './ImageCropModal';
import HeadingSmall from '@/components/heading-small';

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

    const bannerUrl = currentBannerUrl || imageUpload.banner.previewUrl;
    const logoUrl = currentLogoUrl || imageUpload.logo.previewUrl;

    return (
        <div className="overflow-hidden">
            <div className="pb-6">
                {/* Combined Banner and Logo Section with Overlap */}
                <div className="space-y-4">
                    {/* Banner with Overlapping Logo */}
                    <div className="relative">
                        {/* Banner Image Container with 3:1 aspect ratio */}
                        <Card className="group relative overflow-hidden rounded-lg">
                            <div className="relative aspect-[3/1] w-full">
                                {bannerUrl ? (
                                    <img
                                        src={bannerUrl}
                                        alt="Company banner preview"
                                        className="h-full w-full object-cover transition-opacity group-hover:opacity-90"
                                    />
                                ) : (
                                    <div className="bg-muted text-muted-foreground flex h-full w-full items-center justify-center">
                                        Banner Preview
                                    </div>
                                )}

                                {/* Semi-transparent overlay on hover */}
                                <div className="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20" />

                                {/* Edit Banner Button */}
                                {!disabled && (
                                    <Button
                                        size="sm"
                                        variant="secondary"
                                        className="absolute top-3 right-3 shadow-md transition-all opacity-0 group-hover:opacity-100"
                                        onClick={() => imageUpload.openModal('banner')}
                                        disabled={disabled}
                                    >
                                        <PencilIcon className="h-3 w-3" />
                                        <span className="sr-only">Edit banner</span>
                                        Edit Banner
                                    </Button>
                                )}
                            </div>
                        </Card>

                        {/* Overlapping Logo positioned at bottom-left */}
                        <div className="absolute -bottom-16 left-6 z-10">
                            <Card
                                className={cn(
                                    'group relative flex-shrink-0 overflow-hidden transition-all hover:shadow-xl',
                                    'ring-4 ring-white shadow-lg',
                                    !disabled && 'hover:ring-primary/20 cursor-pointer hover:ring-4',
                                    disabled && 'cursor-not-allowed opacity-50',
                                )}
                            >
                                <Button 
                                    variant="ghost" 
                                    className="h-20 w-20 sm:h-24 sm:w-24 md:h-28 md:w-28 p-0 hover:bg-transparent rounded-lg" 
                                    onClick={() => imageUpload.openModal('logo')} 
                                    disabled={disabled}
                                >
                                    {logoUrl ? (
                                        // Display uploaded logo
                                        <div className="relative h-full w-full">
                                            <img
                                                src={logoUrl}
                                                alt="Company logo"
                                                className="h-full w-full object-contain transition-opacity group-hover:opacity-90"
                                            />

                                            {/* Overlay with edit hint */}
                                            {!disabled && (
                                                <div className="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/40">
                                                    <div className="rounded-md bg-white/95 px-2 py-1 text-xs font-medium opacity-0 transition-opacity group-hover:opacity-100 shadow-sm">
                                                        Edit Logo
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        // Upload placeholder
                                        <div className="text-muted-foreground group-hover:text-foreground flex h-full w-full flex-col items-center justify-center gap-1 transition-colors">
                                            <div className="bg-muted group-hover:bg-muted-foreground/10 rounded-full p-2 transition-colors">
                                                <UploadIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                                            </div>
                                            <span className="text-xs font-medium hidden sm:block">Logo</span>
                                        </div>
                                    )}
                                </Button>
                            </Card>
                        </div>
                    </div>

                    {/* Instructions and Error Messages */}
                    <div className="pt-8 space-y-3">
                        {/* Error Messages */}
                        {(errors.banner || errors.logo) && (
                            <div className="space-y-1">
                                {errors.banner && <p className="text-destructive text-sm">Banner: {errors.banner}</p>}
                                {errors.logo && <p className="text-destructive text-sm">Logo: {errors.logo}</p>}
                            </div>
                        )}
                    </div>
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
        </div>
    );
}
