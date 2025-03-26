<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static AdminFactory factory($count = null, $state = [])
 * @method static Builder<static>|Admin newModelQuery()
 * @method static Builder<static>|Admin newQuery()
 * @method static Builder<static>|Admin query()
 * @method static Builder<static>|Admin whereCreatedAt($value)
 * @method static Builder<static>|Admin whereEmail($value)
 * @method static Builder<static>|Admin whereEmailVerifiedAt($value)
 * @method static Builder<static>|Admin whereId($value)
 * @method static Builder<static>|Admin whereName($value)
 * @method static Builder<static>|Admin wherePassword($value)
 * @method static Builder<static>|Admin whereRememberToken($value)
 * @method static Builder<static>|Admin whereUpdatedAt($value)
 * @mixin Eloquent
 */
	final class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $mobile_phone
 * @property string|null $headline
 * @property string|null $bio
 * @property bool $work_permit
 * @property EmploymentType|null $employment_type_preference
 * @property Workplace|null $workplace_preference
 * @property CarbonImmutable|null $available_from
 * @property float|null $salary_expectation
 * @property string|null $resume_path
 * @property string|null $profile_photo_path
 * @property string|null $portfolio_url
 * @property string|null $linkedin_url
 * @property string|null $github_url
 * @property string|null $website_url
 * @property CarbonImmutable|null $date_of_birth
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobApplication> $jobApplications
 * @property-read int|null $job_applications_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static ApplicantFactory factory($count = null, $state = [])
 * @method static Builder<static>|Applicant newModelQuery()
 * @method static Builder<static>|Applicant newQuery()
 * @method static Builder<static>|Applicant query()
 * @method static Builder<static>|Applicant whereCreatedAt($value)
 * @method static Builder<static>|Applicant whereEmail($value)
 * @method static Builder<static>|Applicant whereEmailVerifiedAt($value)
 * @method static Builder<static>|Applicant whereId($value)
 * @method static Builder<static>|Applicant whereFirstName($value)
 * @method static Builder<static>|Applicant whereLastName($value)
 * @method static Builder<static>|Applicant wherePassword($value)
 * @method static Builder<static>|Applicant whereRememberToken($value)
 * @method static Builder<static>|Applicant whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read string $full_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereAvailableFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereEmploymentTypePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereGithubUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant wherePortfolioUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereResumePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereSalaryExpectation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereWebsiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereWorkPermit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Applicant whereWorkplacePreference($value)
 */
	final class Applicant extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $city
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $url
 * @property string|null $size
 * @property string|null $type
 * @property string|null $description_german
 * @property string|null $description_english
 * @property string|null $description_french
 * @property string|null $description_italian
 * @property string|null $logo
 * @property string|null $cover
 * @property string|null $video
 * @property bool|null $newsletter
 * @property string|null $internal_notes
 * @property bool $active
 * @property bool $blocked
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobListing> $jobs
 * @property-read int|null $jobs_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static CompanyFactory factory($count = null, $state = [])
 * @method static Builder<static>|Company newModelQuery()
 * @method static Builder<static>|Company newQuery()
 * @method static Builder<static>|Company query()
 * @method static Builder<static>|Company whereActive($value)
 * @method static Builder<static>|Company whereAddress($value)
 * @method static Builder<static>|Company whereBlocked($value)
 * @method static Builder<static>|Company whereCity($value)
 * @method static Builder<static>|Company whereCover($value)
 * @method static Builder<static>|Company whereCreatedAt($value)
 * @method static Builder<static>|Company whereDescriptionEnglish($value)
 * @method static Builder<static>|Company whereDescriptionFrench($value)
 * @method static Builder<static>|Company whereDescriptionGerman($value)
 * @method static Builder<static>|Company whereDescriptionItalian($value)
 * @method static Builder<static>|Company whereEmail($value)
 * @method static Builder<static>|Company whereEmailVerifiedAt($value)
 * @method static Builder<static>|Company whereId($value)
 * @method static Builder<static>|Company whereInternalNotes($value)
 * @method static Builder<static>|Company whereLatitude($value)
 * @method static Builder<static>|Company whereLogo($value)
 * @method static Builder<static>|Company whereLongitude($value)
 * @method static Builder<static>|Company whereName($value)
 * @method static Builder<static>|Company whereNewsletter($value)
 * @method static Builder<static>|Company wherePassword($value)
 * @method static Builder<static>|Company wherePostcode($value)
 * @method static Builder<static>|Company whereRememberToken($value)
 * @method static Builder<static>|Company whereSize($value)
 * @method static Builder<static>|Company whereType($value)
 * @method static Builder<static>|Company whereUpdatedAt($value)
 * @method static Builder<static>|Company whereUrl($value)
 * @method static Builder<static>|Company whereVideo($value)
 * @mixin Eloquent
 */
	final class Company extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * App\Models\JobApplication
 *
 * @property int $id
 * @property int $job_listing_id
 * @property int $applicant_id
 * @property string $cv_path
 * @property string|null $cover_letter_path
 * @property string|null $additional_documents_path
 * @property ApplicationStatus $status
 * @property Carbon $applied_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Applicant $applicant
 * @property-read JobListing $jobListing
 * @method static JobApplicationFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobApplication newModelQuery()
 * @method static Builder<static>|JobApplication newQuery()
 * @method static Builder<static>|JobApplication query()
 * @method static Builder<static>|JobApplication whereAdditionalDocumentsPath($value)
 * @method static Builder<static>|JobApplication whereApplicantId($value)
 * @method static Builder<static>|JobApplication whereAppliedAt($value)
 * @method static Builder<static>|JobApplication whereCoverLetterPath($value)
 * @method static Builder<static>|JobApplication whereCreatedAt($value)
 * @method static Builder<static>|JobApplication whereCvPath($value)
 * @method static Builder<static>|JobApplication whereId($value)
 * @method static Builder<static>|JobApplication whereJobListingId($value)
 * @method static Builder<static>|JobApplication whereStatus($value)
 * @method static Builder<static>|JobApplication whereUpdatedAt($value)
 * @mixin Eloquent
 */
	final class JobApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $reference_number
 * @property string $title
 * @property string $description
 * @property EmploymentType|null $employment_type
 * @property int|null $workload_min
 * @property int|null $workload_max
 * @property CarbonImmutable|null $active_from
 * @property CarbonImmutable|null $active_until
 * @property Workplace|null $workplace
 * @property string|null $hierarchy
 * @property ExperienceLevel|null $experience_level
 * @property int|null $experience_years_min
 * @property int|null $experience_years_max
 * @property string|null $education_level
 * @property array<array-key, mixed>|null $languages
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $city
 * @property bool $no_salary
 * @property SalaryType|null $salary_type
 * @property SalaryOption|null $salary_option
 * @property numeric|null $salary_min
 * @property numeric|null $salary_max
 * @property string|null $salary_currency
 * @property string|null $job_tier
 * @property ApplicationProcess $application_process
 * @property string|null $application_email
 * @property string|null $application_url
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $internal_notes
 * @property JobStatus $status
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property int|null $job_tier_id
 * @property-read Collection<int, JobApplication> $applications
 * @property-read int|null $applications_count
 * @property-read Company $company
 * @property-read JobTier|null $jobTier
 * @method static JobListingFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobListing newModelQuery()
 * @method static Builder<static>|JobListing newQuery()
 * @method static Builder<static>|JobListing query()
 * @method static Builder<static>|JobListing whereActiveFrom($value)
 * @method static Builder<static>|JobListing whereActiveUntil($value)
 * @method static Builder<static>|JobListing whereAddress($value)
 * @method static Builder<static>|JobListing whereApplicationEmail($value)
 * @method static Builder<static>|JobListing whereApplicationProcess($value)
 * @method static Builder<static>|JobListing whereApplicationUrl($value)
 * @method static Builder<static>|JobListing whereCity($value)
 * @method static Builder<static>|JobListing whereCompanyId($value)
 * @method static Builder<static>|JobListing whereContactEmail($value)
 * @method static Builder<static>|JobListing whereContactPerson($value)
 * @method static Builder<static>|JobListing whereCreatedAt($value)
 * @method static Builder<static>|JobListing whereDescription($value)
 * @method static Builder<static>|JobListing whereEducationLevel($value)
 * @method static Builder<static>|JobListing whereEmploymentType($value)
 * @method static Builder<static>|JobListing whereExperienceLevel($value)
 * @method static Builder<static>|JobListing whereExperienceYearsMax($value)
 * @method static Builder<static>|JobListing whereExperienceYearsMin($value)
 * @method static Builder<static>|JobListing whereHierarchy($value)
 * @method static Builder<static>|JobListing whereId($value)
 * @method static Builder<static>|JobListing whereInternalNotes($value)
 * @method static Builder<static>|JobListing whereJobTier($value)
 * @method static Builder<static>|JobListing whereJobTierId($value)
 * @method static Builder<static>|JobListing whereLanguages($value)
 * @method static Builder<static>|JobListing whereNoSalary($value)
 * @method static Builder<static>|JobListing wherePostcode($value)
 * @method static Builder<static>|JobListing whereReferenceNumber($value)
 * @method static Builder<static>|JobListing whereSalaryCurrency($value)
 * @method static Builder<static>|JobListing whereSalaryMax($value)
 * @method static Builder<static>|JobListing whereSalaryMin($value)
 * @method static Builder<static>|JobListing whereSalaryOption($value)
 * @method static Builder<static>|JobListing whereSalaryType($value)
 * @method static Builder<static>|JobListing whereStatus($value)
 * @method static Builder<static>|JobListing whereTitle($value)
 * @method static Builder<static>|JobListing whereUpdatedAt($value)
 * @method static Builder<static>|JobListing whereWorkloadMax($value)
 * @method static Builder<static>|JobListing whereWorkloadMin($value)
 * @method static Builder<static>|JobListing whereWorkplace($value)
 * @mixin Eloquent
 */
	final class JobListing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $duration_days
 * @property bool $featured
 * @property int|null $max_applications
 * @property int $max_active_jobs
 * @property bool $has_analytics
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, JobListing> $jobs
 * @property-read int|null $jobs_count
 * @method static JobTierFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobTier newModelQuery()
 * @method static Builder<static>|JobTier newQuery()
 * @method static Builder<static>|JobTier query()
 * @method static Builder<static>|JobTier whereCreatedAt($value)
 * @method static Builder<static>|JobTier whereDescription($value)
 * @method static Builder<static>|JobTier whereDurationDays($value)
 * @method static Builder<static>|JobTier whereFeatured($value)
 * @method static Builder<static>|JobTier whereHasAnalytics($value)
 * @method static Builder<static>|JobTier whereId($value)
 * @method static Builder<static>|JobTier whereMaxActiveJobs($value)
 * @method static Builder<static>|JobTier whereMaxApplications($value)
 * @method static Builder<static>|JobTier whereName($value)
 * @method static Builder<static>|JobTier wherePrice($value)
 * @method static Builder<static>|JobTier whereUpdatedAt($value)
 * @mixin Eloquent
 */
	final class JobTier extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobApplication> $jobApplications
 * @property-read int|null $job_applications_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
	final class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

