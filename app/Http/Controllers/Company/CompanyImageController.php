<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Actions\Company\DeleteCompanyImageAction;
use App\Actions\Company\UploadCompanyImageAction;
use App\Http\Requests\Company\CompanyBannerUploadRequest;
use App\Http\Requests\Company\CompanyLogoUploadRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Throwable;

final readonly class CompanyImageController
{
    public function __construct(
        private UploadCompanyImageAction $uploadAction,
        private DeleteCompanyImageAction $deleteAction
    ) {}

    /**
     * Upload company logo.
     */
    public function uploadLogo(CompanyLogoUploadRequest $request): RedirectResponse
    {
        try {
            /** @var Company $company */
            $company = $request->user('company');

            /** @var UploadedFile $logoFile */
            $logoFile = $request->file('logo');

            $success = $this->uploadAction->execute(
                $company,
                $logoFile,
                'logo'
            );

            if (! $success) {
                return back()->withErrors(['logo' => 'Failed to upload logo']);
            }

            return back()->with('success', __('Logo wurde erfolgreich hochgeladen.'));

        } catch (Throwable $e) {
            return back()->withErrors(['logo' => __('Fehler beim Hochladen des Logos: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Upload company banner.
     */
    public function uploadBanner(CompanyBannerUploadRequest $request): RedirectResponse
    {
        try {
            /** @var Company $company */
            $company = $request->user('company');

            /** @var UploadedFile $bannerFile */
            $bannerFile = $request->file('banner');

            $success = $this->uploadAction->execute(
                $company,
                $bannerFile,
                'banner'
            );

            if (! $success) {
                return back()->withErrors(['banner' => 'Failed to upload banner']);
            }

            return back()->with('success', __('Banner wurde erfolgreich hochgeladen.'));

        } catch (Throwable $e) {
            return back()->withErrors(['banner' => __('Fehler beim Hochladen des Banners: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Delete company logo.
     */
    public function deleteLogo(Request $request): RedirectResponse
    {
        try {
            /** @var Company $company */
            $company = $request->user('company');

            $success = $this->deleteAction->execute($company, 'logo');

            if (! $success) {
                return back()->withErrors(['logo' => 'Failed to delete logo']);
            }

            return back()->with('success', __('Logo wurde erfolgreich gelöscht.'));

        } catch (Throwable $e) {
            return back()->withErrors(['logo' => __('Fehler beim Löschen des Logos: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Delete company banner.
     */
    public function deleteBanner(Request $request): RedirectResponse
    {
        try {
            /** @var Company $company */
            $company = $request->user('company');

            $success = $this->deleteAction->execute($company, 'banner');

            if (! $success) {
                return back()->withErrors(['banner' => 'Failed to delete banner']);
            }

            return back()->with('success', __('Banner wurde erfolgreich gelöscht.'));

        } catch (Throwable $e) {
            return back()->withErrors(['banner' => __('Fehler beim Löschen des Banners: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Get current company images.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var Company $company */
        $company = $request->user('company');

        return $this->successResponse([
            'logo' => $company->hasLogo() ? [
                'url' => $company->logo_url,
                'original_name' => $company->logo_original_name,
                'file_size' => $company->logo_file_size_formatted,
                'mime_type' => $company->logo_mime_type,
                'dimensions' => $company->logo_dimensions,
                'uploaded_at' => $company->logo_uploaded_at?->format('Y-m-d H:i:s'),
            ] : null,
            'banner' => $company->hasBanner() ? [
                'url' => $company->banner_url,
                'original_name' => $company->banner_original_name,
                'file_size' => $company->banner_file_size_formatted,
                'mime_type' => $company->banner_mime_type,
                'dimensions' => $company->banner_dimensions,
                'uploaded_at' => $company->banner_uploaded_at?->format('Y-m-d H:i:s'),
            ] : null,
        ]);
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
}
