/**
 * Creates a cropped image from a source image and crop area
 */
export async function createCroppedImage(
    imageSrc: string,
    cropArea: { x: number; y: number; width: number; height: number },
    fileName: string = 'cropped-image.jpg',
): Promise<File> {
    return new Promise((resolve, reject) => {
        const image = new Image();

        image.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            if (!ctx) {
                reject(new Error('Failed to get canvas context'));
                return;
            }

            // Set canvas size to crop area size
            canvas.width = cropArea.width;
            canvas.height = cropArea.height;

            // Draw the cropped portion of the image
            ctx.drawImage(image, cropArea.x, cropArea.y, cropArea.width, cropArea.height, 0, 0, cropArea.width, cropArea.height);

            // Convert canvas to blob
            canvas.toBlob(
                (blob) => {
                    if (!blob) {
                        reject(new Error('Failed to create blob from canvas'));
                        return;
                    }

                    // Determine file extension from original filename or default to jpg
                    const extension = fileName.split('.').pop()?.toLowerCase() || 'jpg';
                    const mimeType = extension === 'png' ? 'image/png' : 'image/jpeg';

                    // Create file from blob
                    const file = new File([blob], fileName, {
                        type: mimeType,
                        lastModified: Date.now(),
                    });

                    resolve(file);
                },
                fileName.toLowerCase().endsWith('.png') ? 'image/png' : 'image/jpeg',
                0.95, // Quality for JPEG
            );
        };

        image.onerror = () => {
            reject(new Error('Failed to load image'));
        };

        image.src = imageSrc;
    });
}

/**
 * Validates image dimensions against minimum requirements
 */
export function validateImageDimensions(file: File, minWidth: number, minHeight: number): Promise<boolean> {
    return new Promise((resolve) => {
        const img = new Image();

        img.onload = () => {
            resolve(img.width >= minWidth && img.height >= minHeight);
        };

        img.onerror = () => {
            resolve(false);
        };

        img.src = URL.createObjectURL(file);
    });
}

/**
 * Validates file size and type
 */
export function validateImageFile(
    file: File,
    maxSize: number,
    allowedTypes: string[] = ['image/jpeg', 'image/png', 'image/jpg'],
): { valid: boolean; error?: string } {
    if (file.size > maxSize) {
        const maxSizeMB = Math.round(maxSize / (1024 * 1024));
        return {
            valid: false,
            error: `File size must be less than ${maxSizeMB}MB`,
        };
    }

    if (!allowedTypes.includes(file.type)) {
        return {
            valid: false,
            error: 'Only PNG and JPG files are supported',
        };
    }

    return { valid: true };
}
