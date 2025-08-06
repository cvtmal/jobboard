# Company Image Upload Components

This directory contains React components for handling company banner and logo uploads in the Laravel + Inertia.js job board application.

## Components Overview

### 1. CompanyImageUploader

The main component that coordinates banner and logo uploads.

### 2. BannerPreviewCard

Displays banner preview with edit functionality in a 3:1 aspect ratio card.

### 3. LogoUploadTile

Square tile for logo upload/preview with upload icon when empty.

### 4. ImageCropModal

Modal for cropping images with react-easy-crop integration.

### 5. CompanyImageManager

High-level wrapper that provides consistent styling and supports both upload modes.

## Upload Modes

### Form Mode (Default)

Files are passed to parent component for form submission. Ideal for job listing creation where images are submitted along with other form data.

```tsx
import { CompanyImageUploader } from '@/components/company';
import { useForm } from '@inertiajs/react';

function JobListingForm() {
    const { data, setData, post } = useForm({
        title: '',
        banner_image: undefined as File | undefined,
        logo_image: undefined as File | undefined,
    });

    return (
        <form
            onSubmit={(e) => {
                e.preventDefault();
                post(route('company.job-listings.store'), { forceFormData: true });
            }}
        >
            <CompanyImageUploader
                mode="form"
                onBannerChange={(file) => setData('banner_image', file || undefined)}
                onLogoChange={(file) => setData('logo_image', file || undefined)}
                disabled={processing}
                errors={{
                    banner: errors.banner_image,
                    logo: errors.logo_image,
                }}
            />
            {/* Other form fields... */}
        </form>
    );
}
```

### Direct Mode

Images are uploaded immediately to dedicated backend endpoints. Perfect for company settings pages.

```tsx
import { CompanyImageUploader } from '@/components/company';

function CompanySettings({ currentBannerUrl, currentLogoUrl }) {
    return (
        <CompanyImageUploader
            mode="direct"
            currentBannerUrl={currentBannerUrl}
            currentLogoUrl={currentLogoUrl}
            // Images upload automatically when cropped
            // No need for onChange callbacks
        />
    );
}
```

## Backend Integration

The components integrate with these Laravel routes:

- `GET /company/images` - Get current images
- `POST /company/images/logo` - Upload logo (expects `logo` file field)
- `POST /company/images/banner` - Upload banner (expects `banner` file field)
- `DELETE /company/images/logo` - Delete logo
- `DELETE /company/images/banner` - Delete banner

## Image Requirements

### Banner Images

- **Aspect Ratio**: 3:1
- **Minimum Size**: 1200 × 400 pixels
- **Maximum File Size**: 16MB
- **Formats**: PNG, JPG

### Logo Images

- **Aspect Ratio**: 1:1 (square)
- **Minimum Size**: 320 × 320 pixels
- **Maximum File Size**: 8MB
- **Formats**: PNG, JPG

## Features

- ✅ Client-side validation (file size, type, dimensions)
- ✅ Image cropping with zoom controls
- ✅ Responsive design (mobile-first)
- ✅ Accessibility support (ARIA labels, keyboard navigation)
- ✅ Loading states and error handling
- ✅ Two upload modes (form-based and direct)
- ✅ TypeScript support throughout
- ✅ Integration with existing UI components

## File Structure

```
components/company/
├── CompanyImageUploader.tsx     # Main coordinator component
├── CompanyImageManager.tsx      # High-level wrapper with styling
├── BannerPreviewCard.tsx        # Banner preview component
├── LogoUploadTile.tsx          # Logo upload tile component
├── ImageCropModal.tsx          # Cropping modal component
├── CompanyImageUploaderDemo.tsx # Demo/testing component
├── usage-example.tsx           # Usage examples
├── index.ts                    # Component exports
└── README.md                   # This documentation

hooks/
└── use-image-upload.tsx        # Custom hook for upload logic

utils/
└── cropImage.ts               # Image cropping utilities
```

## Dependencies

- `react-easy-crop` - Image cropping functionality
- `@radix-ui/react-*` - UI primitives (already installed)
- `lucide-react` - Icons
- `@inertiajs/react` - Laravel integration

## Usage Examples

### Job Listing Creation (Current Implementation)

```tsx
// Already integrated in resources/js/pages/company/job-listings/create.tsx
<Card>
    <CardHeader>
        <CardTitle>Company Branding</CardTitle>
        <CardDescription>Add your company banner and logo to make your job listing stand out</CardDescription>
    </CardHeader>
    <CardContent>
        <CompanyImageUploader
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
```

### Company Settings Page

```tsx
<CompanyImageManager mode="direct" currentBannerUrl={company.banner_url} currentLogoUrl={company.logo_url} />
```

## Error Handling

The components handle various error scenarios:

- File size too large
- Invalid file format
- Insufficient image dimensions
- Network upload failures
- Server validation errors

## Accessibility

- Proper ARIA labels for screen readers
- Keyboard navigation support
- Focus management in modals
- Semantic HTML structure
- Alternative text for images

## Browser Support

Compatible with all modern browsers that support:

- ES6+ JavaScript
- CSS Grid and Flexbox
- File API
- Canvas API (for image cropping)

## Testing

The components can be tested using the demo component:

```tsx
import { CompanyImageUploaderDemo } from '@/components/company';
```
