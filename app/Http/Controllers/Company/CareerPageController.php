<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Actions\Company\UploadCareerPageImageAction;
use App\Http\Requests\Company\CareerPageUpdateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class CareerPageController
{
    /**
     * Show the career page editor.
     */
    public function edit(Request $request): Response
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            abort(403, 'Invalid company account');
        }

        return Inertia::render('company/career-page/edit', [
            'company' => [
                'career_page_enabled' => $company->career_page_enabled,
                'career_page_slug' => $company->career_page_slug,
                'career_page_image' => $company->career_page_image_url,
                'career_page_videos' => $company->career_page_videos ?? [],
                'career_page_domain' => $company->career_page_domain,
                'spontaneous_application_enabled' => $company->spontaneous_application_enabled,
                'career_page_visibility' => $company->career_page_visibility,
            ],
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the career page settings.
     */
    public function update(CareerPageUpdateRequest $request, UploadCareerPageImageAction $uploadAction): RedirectResponse
    {
        $company = $request->user();
        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        $validated = $request->validated();

        // Set defaults for boolean fields if not provided
        $validated['career_page_enabled'] ??= true;
        $validated['spontaneous_application_enabled'] ??= false;
        $validated['career_page_visibility'] ??= true;

        // TODO: Implement sluggable package or custom slug generation
        if ($validated['career_page_enabled'] && empty($validated['career_page_slug'])) {
            $baseSlug = str($company->name)->slug();
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug uniqueness
            while (Company::where('career_page_slug', $slug)->where('id', '!=', $company->id)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            $validated['career_page_slug'] = $slug;
        }

        // If career page is disabled, clear the slug
        if (! $validated['career_page_enabled']) {
            $validated['career_page_slug'] = null;
        }

        // Handle image upload
        if ($request->hasFile('career_page_image')) {
            try {
                $uploadedFile = $request->file('career_page_image');

                // Ensure we have a single UploadedFile (not an array)
                if (! $uploadedFile instanceof UploadedFile) {
                    return back()
                        ->withInput()
                        ->withErrors(['career_page_image' => 'Invalid file upload.']);
                }

                $imageUrl = $uploadAction->execute($company, $uploadedFile);
                $validated['career_page_image'] = $imageUrl;
            } catch (Throwable) {
                return back()
                    ->withInput()
                    ->withErrors(['career_page_image' => 'Failed to upload image. Please try again.']);
            }
        } else {
            unset($validated['career_page_image']);
        }

        $company->fill($validated);
        $company->save();

        return back()->with('status', 'career-page-updated');
    }

    /**
     * Delete the career page image.
     */
    public function destroyImage(): RedirectResponse
    {
        $company = auth()->guard('company')->user();
        $company->deleteCareerPageImage();

        return back();
    }

    /**
     * Add a video to the career page.
     */
    public function addVideo(Request $request): RedirectResponse
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        $request->validate([
            'url' => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.*$/',
            ],
            'title' => 'nullable|string|max:255',
        ]);

        $videos = $company->career_page_videos ?? [];

        $newVideo = [
            'id' => Str::random(9),
            'url' => $request->input('url'),
            'title' => $request->input('title', 'YouTube Video'),
        ];

        $videos[] = $newVideo;

        $company->update(['career_page_videos' => $videos]);

        return back()->with('status', 'video-added');
    }

    /**
     * Remove a video from the career page.
     */
    public function removeVideo(Request $request, string $videoId): RedirectResponse
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        $videos = $company->career_page_videos ?? [];

        // Defensive check: ensure we have an array (Laravel casting can sometimes be unpredictable)
        // @phpstan-ignore-next-line function.alreadyNarrowedType
        if (! is_array($videos)) {
            $videos = [];
        }

        $videos = array_values(array_filter($videos, fn ($video): bool => $video['id'] !== $videoId));

        $company->update(['career_page_videos' => $videos]);

        return back()->with('status', 'video-removed');
    }

    /**
     * Show a preview of the career page.
     */
    public function preview(Request $request): Response
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            abort(403, 'Invalid company account');
        }

        if (! $company->career_page_enabled) {
            abort(404, 'Career page is not enabled');
        }

        $company->load(['jobs' => function ($query): void {
            $query->where('status', 'published')
                ->where(function ($q): void {
                    $q->whereNull('active_until')
                        ->orWhere('active_until', '>=', now());
                })
                ->with('categories')
                ->latest();
        }]);

        return Inertia::render('company/career-page/preview', [
            'company' => $company,
            'jobListings' => $company->jobs ?? collect([]),
        ]);
    }
}
