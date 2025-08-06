import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { PencilIcon } from 'lucide-react';

interface BannerPreviewCardProps {
    /**
     * URL of the banner image to display
     */
    imageUrl?: string;
    /**
     * Callback when edit button is clicked
     */
    onEditClick: () => void;
    /**
     * Whether the component is disabled
     */
    disabled?: boolean;
    /**
     * Error message to display
     */
    error?: string;
    /**
     * Whether to show company label (for fallback mode)
     */
    showCompanyLabel?: boolean;
}

export function BannerPreviewCard({ imageUrl, onEditClick, disabled = false, error, showCompanyLabel = false }: BannerPreviewCardProps) {
    return (
        <div className="space-y-2">
            <Card className="group relative overflow-hidden rounded-lg">
                {/* Banner Image Container with 3:1 aspect ratio */}
                <div className="relative aspect-[3/1] w-full">
                    {imageUrl ? (
                        <img
                            src={imageUrl}
                            alt="Company banner preview"
                            className="h-full w-full object-cover transition-opacity group-hover:opacity-90"
                        />
                    ) : (
                        <div className="bg-muted text-muted-foreground flex h-full w-full items-center justify-center">Banner Preview</div>
                    )}

                    {/* Semi-transparent overlay on hover */}
                    <div className="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20" />

                    {/* Company Label */}
                    {showCompanyLabel && imageUrl && (
                        <div className="absolute top-3 left-3 rounded bg-blue-600/90 px-2 py-1 text-xs font-medium text-white">Company Banner</div>
                    )}

                    {/* Edit Button */}
                    {!disabled && (
                        <Button
                            size="sm"
                            variant="secondary"
                            className={cn(
                                'absolute top-3 right-3 shadow-md transition-all',
                                'opacity-0 group-hover:opacity-100',
                                disabled && 'cursor-not-allowed opacity-50',
                            )}
                            onClick={onEditClick}
                            disabled={disabled}
                        >
                            <PencilIcon className="h-3 w-3" />
                            <span className="sr-only">Edit banner</span>
                            Edit
                        </Button>
                    )}
                </div>
            </Card>

            {/* Error Message */}
            {error && <p className="text-destructive text-sm">{error}</p>}

            {/* Helper Text */}
            <p className="text-muted-foreground text-xs">
                {showCompanyLabel ? "Using your company's banner image" : 'Click "Edit" to upload a custom banner image'}
            </p>
        </div>
    );
}
