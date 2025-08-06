import { router } from '@inertiajs/react';
import { useCallback, useState } from 'react';

export interface ImageUploadState {
    selectedImage?: string;
    previewUrl?: string;
    file?: File;
}

export interface ImageUploadModal {
    isOpen: boolean;
    type: 'banner' | 'logo' | null;
}

export interface ImageUploadCallbacks {
    onBannerUpload?: (file: File | null) => void;
    onLogoUpload?: (file: File | null) => void;
    /**
     * Optional mode for handling uploads
     * - 'form': Files are passed to parent component for form submission (default)
     * - 'direct': Files are uploaded directly to backend endpoints
     */
    mode?: 'form' | 'direct';
}

export interface UseImageUploadReturn {
    banner: ImageUploadState;
    logo: ImageUploadState;
    modal: ImageUploadModal;
    openModal: (type: 'banner' | 'logo') => void;
    closeModal: () => void;
    selectImage: (type: 'banner' | 'logo') => void;
    handleBannerCrop: (file: File) => void;
    handleLogoCrop: (file: File) => void;
    handleDelete: (type: 'banner' | 'logo') => void;
}

/**
 * Custom hook for managing image upload state and operations
 */
export function useImageUpload(callbacks: ImageUploadCallbacks = {}): UseImageUploadReturn {
    const [banner, setBanner] = useState<ImageUploadState>({});
    const [logo, setLogo] = useState<ImageUploadState>({});
    const [modal, setModal] = useState<ImageUploadModal>({
        isOpen: false,
        type: null,
    });

    // Modal management
    const openModal = useCallback((type: 'banner' | 'logo') => {
        setModal({ isOpen: true, type });
    }, []);

    const closeModal = useCallback(() => {
        setModal({ isOpen: false, type: null });
    }, []);

    // File selection
    const selectImage = useCallback((type: 'banner' | 'logo') => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/png,image/jpeg,image/jpg';

        const maxSize = type === 'banner' ? 16 * 1024 * 1024 : 8 * 1024 * 1024; // 16MB for banner, 8MB for logo

        input.onchange = async (event) => {
            const file = (event.target as HTMLInputElement).files?.[0];
            if (!file) return;

            // Validate file using utility function
            const { validateImageFile, validateImageDimensions } = await import('@/utils/cropImage');

            const validation = validateImageFile(file, maxSize);
            if (!validation.valid) {
                alert(validation.error);
                return;
            }

            // Validate image dimensions
            const minWidth = type === 'banner' ? 1200 : 320;
            const minHeight = type === 'banner' ? 400 : 320;

            const dimensionsValid = await validateImageDimensions(file, minWidth, minHeight);
            if (!dimensionsValid) {
                alert(`Image must be at least ${minWidth}×${minHeight} pixels`);
                return;
            }

            // Create preview URL
            const imageUrl = URL.createObjectURL(file);

            // Update state with selected image
            if (type === 'banner') {
                setBanner((prev) => ({ ...prev, selectedImage: imageUrl, file }));
            } else {
                setLogo((prev) => ({ ...prev, selectedImage: imageUrl, file }));
            }
        };

        input.click();
    }, []);

    // Crop handlers
    const handleBannerCrop = useCallback(
        (file: File) => {
            const previewUrl = URL.createObjectURL(file);
            setBanner((prev) => ({ ...prev, previewUrl, file }));

            if (callbacks.mode === 'direct') {
                // Upload directly to backend
                const formData = new FormData();
                formData.append('banner', file);

                router.post(route('company.images.banner.upload'), formData, {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        // Handle success - the backend response will contain the banner URL
                        console.log('Banner uploaded successfully');
                    },
                    onError: (errors) => {
                        console.error('Banner upload failed:', errors);
                        // You might want to show an error toast here
                    },
                });
            } else {
                // Form mode - pass to parent component
                callbacks.onBannerUpload?.(file);
            }

            closeModal();
        },
        [callbacks, closeModal],
    );

    const handleLogoCrop = useCallback(
        (file: File) => {
            const previewUrl = URL.createObjectURL(file);
            setLogo((prev) => ({ ...prev, previewUrl, file }));

            if (callbacks.mode === 'direct') {
                // Upload directly to backend
                const formData = new FormData();
                formData.append('logo', file);

                router.post(route('company.images.logo.upload'), formData, {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        // Handle success - the backend response will contain the logo URL
                        console.log('Logo uploaded successfully');
                    },
                    onError: (errors) => {
                        console.error('Logo upload failed:', errors);
                        // You might want to show an error toast here
                    },
                });
            } else {
                // Form mode - pass to parent component
                callbacks.onLogoUpload?.(file);
            }

            closeModal();
        },
        [callbacks, closeModal],
    );

    // Delete handlers
    const handleDelete = useCallback(
        (type: 'banner' | 'logo') => {
            if (callbacks.mode === 'direct') {
                // Delete from backend directly
                const endpoint = type === 'banner' ? route('company.images.banner.delete') : route('company.images.logo.delete');

                router.delete(endpoint, {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        console.log(`${type} deleted successfully`);
                        // Clear local state
                        if (type === 'banner') {
                            if (banner.selectedImage) URL.revokeObjectURL(banner.selectedImage);
                            if (banner.previewUrl) URL.revokeObjectURL(banner.previewUrl);
                            setBanner({});
                        } else {
                            if (logo.selectedImage) URL.revokeObjectURL(logo.selectedImage);
                            if (logo.previewUrl) URL.revokeObjectURL(logo.previewUrl);
                            setLogo({});
                        }
                    },
                    onError: (errors) => {
                        console.error(`${type} deletion failed:`, errors);
                    },
                });
            } else {
                // Form mode - clean up and notify parent
                if (type === 'banner') {
                    // Clean up object URLs
                    if (banner.selectedImage) URL.revokeObjectURL(banner.selectedImage);
                    if (banner.previewUrl) URL.revokeObjectURL(banner.previewUrl);

                    setBanner({});
                    callbacks.onBannerUpload?.(null);
                } else {
                    // Clean up object URLs
                    if (logo.selectedImage) URL.revokeObjectURL(logo.selectedImage);
                    if (logo.previewUrl) URL.revokeObjectURL(logo.previewUrl);

                    setLogo({});
                    callbacks.onLogoUpload?.(null);
                }
            }

            closeModal();
        },
        [banner, logo, callbacks, closeModal],
    );

    return {
        banner,
        logo,
        modal,
        openModal,
        closeModal,
        selectImage,
        handleBannerCrop,
        handleLogoCrop,
        handleDelete,
    };
}
