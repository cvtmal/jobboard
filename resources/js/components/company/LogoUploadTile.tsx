import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { UploadIcon } from 'lucide-react';

interface LogoUploadTileProps {
    /**
     * URL of the logo image to display
     */
    imageUrl?: string;
    /**
     * Callback when the tile is clicked
     */
    onClick: () => void;
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

export function LogoUploadTile({ imageUrl, onClick, disabled = false, error, showCompanyLabel = false }: LogoUploadTileProps) {
    return (
        <div className="space-y-2">
            <div className="flex items-start gap-4">
                {/* Square Logo Tile */}
                <Card
                    className={cn(
                        'group relative flex-shrink-0 overflow-hidden transition-all hover:shadow-lg',
                        !disabled && 'hover:ring-primary/20 cursor-pointer hover:ring-2',
                        disabled && 'cursor-not-allowed opacity-50',
                    )}
                >
                    <Button variant="ghost" className="h-32 w-32 p-0 hover:bg-transparent" onClick={onClick} disabled={disabled}>
                        {imageUrl ? (
                            // Display uploaded logo
                            <div className="relative h-full w-full">
                                <img
                                    src={imageUrl}
                                    alt="Company logo"
                                    className="h-full w-full object-contain transition-opacity group-hover:opacity-90"
                                />

                                {/* Company Label */}
                                {showCompanyLabel && (
                                    <div className="absolute top-2 left-2 rounded bg-blue-600/90 px-1.5 py-0.5 text-xs font-medium text-white">
                                        Company
                                    </div>
                                )}

                                {/* Overlay with edit hint */}
                                {!disabled && (
                                    <div className="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/20">
                                        <div className="rounded-md bg-white/90 px-2 py-1 text-xs font-medium opacity-0 transition-opacity group-hover:opacity-100">
                                            Edit
                                        </div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            // Upload placeholder
                            <div className="text-muted-foreground group-hover:text-foreground flex h-full w-full flex-col items-center justify-center gap-2 transition-colors">
                                <div className="bg-muted group-hover:bg-muted-foreground/10 rounded-full p-3 transition-colors">
                                    <UploadIcon className="h-6 w-6" />
                                </div>
                                <span className="text-xs font-medium">Upload Logo</span>
                            </div>
                        )}
                    </Button>
                </Card>

                {/* Upload Instructions */}
                <div className="flex-1 space-y-1">
                    <h3 className="font-medium">{showCompanyLabel ? 'Company Logo' : 'Upload new Logo'}</h3>
                    <p className="text-muted-foreground text-sm">
                        {showCompanyLabel
                            ? "Using your company's logo image"
                            : 'Add your company logo to make your job listing stand out. Square format works best (1:1 ratio).'}
                    </p>
                    {!showCompanyLabel && <p className="text-muted-foreground text-xs">Minimum 320×320px, max 8MB (PNG, JPG)</p>}
                </div>
            </div>

            {/* Error Message */}
            {error && <p className="text-destructive text-sm">{error}</p>}
        </div>
    );
}
