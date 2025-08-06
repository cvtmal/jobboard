<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Actions\JobListing\DeleteJobListingImageAction;
use App\Actions\JobListing\UploadJobListingImageAction;
use App\Http\Requests\JobListing\JobListingBannerUploadRequest;
use App\Http\Requests\JobListing\JobListingLogoUploadRequest;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Throwable;

final readonly class JobListingImageController
{
    use AuthorizesRequests;

    public function __construct(
        private UploadJobListingImageAction $uploadAction,
        private DeleteJobListingImageAction $deleteAction
    ) {}

    /**
     * Upload job listing logo.
     */
    public function uploadLogo(JobListingLogoUploadRequest $request, JobListing $jobListing): JsonResponse
    {
        try {
            $this->authorize('update', $jobListing);

            /** @var Company $company */
            $company = $request->user('company');

            // Verify the job listing belongs to the authenticated company
            if ($jobListing->company_id !== $company->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            /** @var UploadedFile $logoFile */
            $logoFile = $request->file('logo');

            $success = $this->uploadAction->execute(
                $jobListing,
                $logoFile,
                'logo'
            );

            if (! $success) {
                return $this->errorResponse('Failed to upload logo', 500);
            }

            // Refresh the model to get updated data
            $jobListing->refresh();

            return $this->successResponse([
                'message' => __('Logo wurde erfolgreich hochgeladen.'),
                'logo_url' => $jobListing->logo_url,
                'effective_logo_url' => $jobListing->effective_logo_url,
                'logo_metadata' => [
                    'original_name' => $jobListing->logo_original_name,
                    'file_size' => $jobListing->logo_file_size_formatted,
                    'mime_type' => $jobListing->logo_mime_type,
                    'dimensions' => $jobListing->logo_dimensions,
                    'uploaded_at' => $jobListing->logo_uploaded_at?->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse(
                __('Fehler beim Hochladen des Logos: :error', ['error' => $e->getMessage()]),
                500
            );
        }
    }

    /**
     * Upload job listing banner.
     */
    public function uploadBanner(JobListingBannerUploadRequest $request, JobListing $jobListing): JsonResponse
    {
        try {
            $this->authorize('update', $jobListing);

            /** @var Company $company */
            $company = $request->user('company');

            // Verify the job listing belongs to the authenticated company
            if ($jobListing->company_id !== $company->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            /** @var UploadedFile $bannerFile */
            $bannerFile = $request->file('banner');

            $success = $this->uploadAction->execute(
                $jobListing,
                $bannerFile,
                'banner'
            );

            if (! $success) {
                return $this->errorResponse('Failed to upload banner', 500);
            }

            // Refresh the model to get updated data
            $jobListing->refresh();

            return $this->successResponse([
                'message' => __('Banner wurde erfolgreich hochgeladen.'),
                'banner_url' => $jobListing->banner_url,
                'effective_banner_url' => $jobListing->effective_banner_url,
                'banner_metadata' => [
                    'original_name' => $jobListing->banner_original_name,
                    'file_size' => $jobListing->banner_file_size_formatted,
                    'mime_type' => $jobListing->banner_mime_type,
                    'dimensions' => $jobListing->banner_dimensions,
                    'uploaded_at' => $jobListing->banner_uploaded_at?->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse(
                __('Fehler beim Hochladen des Banners: :error', ['error' => $e->getMessage()]),
                500
            );
        }
    }

    /**
     * Delete job listing logo.
     */
    public function deleteLogo(Request $request, JobListing $jobListing): JsonResponse
    {
        try {
            $this->authorize('update', $jobListing);

            /** @var Company $company */
            $company = $request->user('company');

            // Verify the job listing belongs to the authenticated company
            if ($jobListing->company_id !== $company->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $success = $this->deleteAction->execute($jobListing, 'logo');

            if (! $success) {
                return $this->errorResponse('Failed to delete logo', 500);
            }

            // Refresh to get updated data
            $jobListing->refresh();

            return $this->successResponse([
                'message' => __('Logo wurde erfolgreich gelöscht.'),
                'effective_logo_url' => $jobListing->effective_logo_url, // Will now fallback to company logo
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse(
                __('Fehler beim Löschen des Logos: :error', ['error' => $e->getMessage()]),
                500
            );
        }
    }

    /**
     * Delete job listing banner.
     */
    public function deleteBanner(Request $request, JobListing $jobListing): JsonResponse
    {
        try {
            $this->authorize('update', $jobListing);

            /** @var Company $company */
            $company = $request->user('company');

            // Verify the job listing belongs to the authenticated company
            if ($jobListing->company_id !== $company->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $success = $this->deleteAction->execute($jobListing, 'banner');

            if (! $success) {
                return $this->errorResponse('Failed to delete banner', 500);
            }

            // Refresh to get updated data
            $jobListing->refresh();

            return $this->successResponse([
                'message' => __('Banner wurde erfolgreich gelöscht.'),
                'effective_banner_url' => $jobListing->effective_banner_url, // Will now fallback to company banner
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse(
                __('Fehler beim Löschen des Banners: :error', ['error' => $e->getMessage()]),
                500
            );
        }
    }

    /**
     * Get current job listing images.
     */
    public function show(Request $request, JobListing $jobListing): JsonResponse
    {
        $this->authorize('view', $jobListing);

        /** @var Company $company */
        $company = $request->user('company');

        // Verify the job listing belongs to the authenticated company
        if ($jobListing->company_id !== $company->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $jobListing->load('company');

        return $this->successResponse([
            'use_company_logo' => $jobListing->use_company_logo,
            'use_company_banner' => $jobListing->use_company_banner,
            'logo' => $jobListing->hasCustomLogo() ? [
                'url' => $jobListing->logo_url,
                'original_name' => $jobListing->logo_original_name,
                'file_size' => $jobListing->logo_file_size_formatted,
                'mime_type' => $jobListing->logo_mime_type,
                'dimensions' => $jobListing->logo_dimensions,
                'uploaded_at' => $jobListing->logo_uploaded_at?->format('Y-m-d H:i:s'),
            ] : null,
            'banner' => $jobListing->hasCustomBanner() ? [
                'url' => $jobListing->banner_url,
                'original_name' => $jobListing->banner_original_name,
                'file_size' => $jobListing->banner_file_size_formatted,
                'mime_type' => $jobListing->banner_mime_type,
                'dimensions' => $jobListing->banner_dimensions,
                'uploaded_at' => $jobListing->banner_uploaded_at?->format('Y-m-d H:i:s'),
            ] : null,
            'company_logo' => $company->hasLogo() ? [
                'url' => $company->logo_url,
            ] : null,
            'company_banner' => $company->hasBanner() ? [
                'url' => $company->banner_url,
            ] : null,
            'effective_logo_url' => $jobListing->effective_logo_url,
            'effective_banner_url' => $jobListing->effective_banner_url,
        ]);
    }

    /**
     * Toggle using company images vs custom images.
     */
    public function toggleCompanyImages(Request $request, JobListing $jobListing): JsonResponse
    {
        try {
            $this->authorize('update', $jobListing);

            /** @var Company $company */
            $company = $request->user('company');

            // Verify the job listing belongs to the authenticated company
            if ($jobListing->company_id !== $company->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $validated = $request->validate([
                'use_company_logo' => 'required|boolean',
                'use_company_banner' => 'required|boolean',
            ]);

            $jobListing->update($validated);

            $jobListing->refresh();
            $jobListing->load('company');

            return $this->successResponse([
                'message' => __('Bildeinstellungen wurden erfolgreich aktualisiert.'),
                'use_company_logo' => $jobListing->use_company_logo,
                'use_company_banner' => $jobListing->use_company_banner,
                'effective_logo_url' => $jobListing->effective_logo_url,
                'effective_banner_url' => $jobListing->effective_banner_url,
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse(
                __('Fehler beim Aktualisieren der Bildeinstellungen: :error', ['error' => $e->getMessage()]),
                500
            );
        }
    }

    /**
     * Return a success JSON response.
     *
     * @param  array<string, mixed>  $data
     */
    private function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     */
    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
