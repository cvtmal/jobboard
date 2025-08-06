import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import { Slider } from '@/components/ui/slider';
import { TrashIcon, UploadIcon } from 'lucide-react';
import React, { useCallback, useRef, useState } from 'react';
import Cropper from 'react-easy-crop';

// Types from react-easy-crop
interface Area {
    x: number;
    y: number;
    width: number;
    height: number;
}

interface Point {
    x: number;
    y: number;
}

export interface ImageCropModalProps {
    /**
     * Whether the modal is open
     */
    isOpen: boolean;
    /**
     * Callback when modal is closed
     */
    onClose: () => void;
    /**
     * Image to crop (blob URL or data URL)
     */
    image?: string;
    /**
     * Callback when cropping is complete
     */
    onCrop: (croppedFile: File) => void;
    /**
     * Callback when delete is clicked
     */
    onDelete: () => void;
    /**
     * Callback when choose new image is clicked
     */
    onChooseNew: () => void;
    /**
     * Type of image being cropped
     */
    type: 'banner' | 'logo';
    /**
     * Modal title
     */
    title: string;
    /**
     * Helper description text
     */
    description: string;
    /**
     * Aspect ratio for cropping
     */
    aspectRatio: number;
    /**
     * Minimum dimensions required
     */
    minDimensions: { width: number; height: number };
    /**
     * Maximum file size allowed
     */
    maxFileSize: number;
}

export function ImageCropModal({
    isOpen,
    onClose,
    image,
    onCrop,
    onDelete,
    onChooseNew,
    type,
    title,
    description,
    aspectRatio,
    minDimensions: _minDimensions,
    maxFileSize,
}: ImageCropModalProps) {
    const [crop, setCrop] = useState<Point>({ x: 0, y: 0 });
    const [zoom, setZoom] = useState(1);
    const [croppedAreaPixels, setCroppedAreaPixels] = useState<Area | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const onCropComplete = useCallback((croppedArea: Area, croppedAreaPixels: Area) => {
        setCroppedAreaPixels(croppedAreaPixels);
    }, []);

    const handleUpload = useCallback(async () => {
        if (!image || !croppedAreaPixels) return;

        try {
            const { createCroppedImage } = await import('@/utils/cropImage');
            const croppedFile = await createCroppedImage(image, croppedAreaPixels, `${type}-image.jpg`);

            onCrop(croppedFile);
            onClose();
        } catch (error) {
            console.error('Error cropping image:', error);
            alert('Failed to crop image. Please try again.');
        }
    }, [image, croppedAreaPixels, onCrop, onClose, type]);

    const handleChooseNew = () => {
        fileInputRef.current?.click();
    };

    const handleFileSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            // Validate file
            if (file.size > maxFileSize) {
                alert(`File size must be less than ${Math.round(maxFileSize / (1024 * 1024))}MB`);
                return;
            }

            if (!file.type.match(/^image\/(png|jpe?g)$/)) {
                alert('Only PNG and JPG files are supported');
                return;
            }

            // Create object URL and trigger choose new callback
            URL.createObjectURL(file);
            // In real implementation, this would update the parent component's state
            onChooseNew();
        }
    };

    return (
        <>
            <Dialog open={isOpen} onOpenChange={onClose}>
                <DialogContent className="max-w-4xl">
                    <DialogHeader>
                        <DialogTitle>{title}</DialogTitle>
                        <DialogDescription className="text-sm">{description}</DialogDescription>
                    </DialogHeader>

                    {image ? (
                        <div className="space-y-6">
                            {/* Crop Area */}
                            <div className="relative h-[500px] overflow-hidden rounded-lg bg-black">
                                <Cropper
                                    image={image}
                                    crop={crop}
                                    zoom={zoom}
                                    aspect={aspectRatio}
                                    onCropChange={setCrop}
                                    onZoomChange={setZoom}
                                    onCropComplete={onCropComplete}
                                    minZoom={1}
                                    maxZoom={3}
                                    cropShape="rect"
                                    showGrid
                                    restrictPosition={false}
                                    zoomWithScroll={false}
                                />
                            </div>

                            {/* Zoom Control */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Zoom</label>
                                <Slider
                                    value={[zoom]}
                                    onValueChange={(values) => setZoom(values[0])}
                                    min={0.5}
                                    max={3}
                                    step={0.1}
                                    className="w-full"
                                />
                            </div>
                        </div>
                    ) : (
                        /* No Image Selected State */
                        <div className="border-muted-foreground/25 bg-muted/50 flex h-[500px] flex-col items-center justify-center rounded-lg border-2 border-dashed">
                            <div className="text-center">
                                <UploadIcon className="text-muted-foreground mx-auto h-12 w-12" />
                                <p className="text-muted-foreground mt-2 text-sm">Choose an image to get started</p>
                            </div>
                        </div>
                    )}

                    <Separator />

                    <DialogFooter className="flex-col-reverse gap-2 sm:flex-row sm:justify-between">
                        <div className="flex gap-2">
                            <Button variant="ghost" onClick={handleChooseNew} className="flex-1 sm:flex-none">
                                Choose new Image
                            </Button>
                            <Button variant="destructive" onClick={onDelete} className="flex-1 sm:flex-none">
                                <TrashIcon className="h-4 w-4" />
                                Delete
                            </Button>
                        </div>

                        <div className="flex gap-2">
                            <Button variant="outline" onClick={onClose}>
                                Cancel
                            </Button>
                            <Button onClick={handleUpload} disabled={!image || !croppedAreaPixels}>
                                Upload
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Hidden file input */}
            <input ref={fileInputRef} type="file" accept="image/png,image/jpeg,image/jpg" className="hidden" onChange={handleFileSelect} />
        </>
    );
}
