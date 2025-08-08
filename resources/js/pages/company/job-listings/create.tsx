import { CompanyImageUploader } from '@/components/company';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Slider } from '@/components/ui/slider';
import { Textarea } from '@/components/ui/textarea';
import { FormSection } from '@/components/ui/form-section';
import { FormProgress } from '@/components/ui/form-progress';
import { Stepper } from '@/components/ui/stepper';
import { AutoSaveStatus } from '@/components/ui/auto-save-status';
import { FieldHelper, CharacterCount } from '@/components/ui/field-helper';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { ApplicationLanguage } from '@/types/enums/ApplicationLanguage';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { CustomEmploymentType } from '@/types/enums/CustomEmploymentType';
import { JobStatus } from '@/types/enums/JobStatus';
import { SalaryPeriod } from '@/types/enums/SalaryPeriod';
import { SeniorityLevel } from '@/types/enums/SeniorityLevel';
import { Workplace } from '@/types/enums/Workplace';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent, useEffect, useState, useCallback } from 'react';
// @ts-ignore
import { debounce } from 'lodash';
import { Building2, MapPin, Briefcase, DollarSign, Award, Image, Loader2 } from 'lucide-react';
import { useFormValidation } from '@/hooks/use-form-validation';
import Heading from '@/components/heading';

interface Props {
    auth: Auth;
    errors: Record<string, string>;
    categoryOptions: Record<string, string>;
    companyLogo?: string | null;
    companyBanner?: string | null;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings / Create New Job',
        href: '/company/job-listings/create',
    },
];

export default function CreateJobListing({ auth, errors, categoryOptions, companyLogo, companyBanner }: Props) {
    const { data, setData, post, processing } = useForm({
        title: '',
        company_description: '',
        description: '',
        requirements: '',
        benefits: '',
        final_words: '',
        workload_min: 80,
        workload_max: 100,

        // Location information
        workplace: Workplace.ONSITE,
        office_location: auth.company?.city || '',

        // Application details
        application_language: ApplicationLanguage.ENGLISH,
        category: '',
        employment_type: CustomEmploymentType.PERMANENT,
        seniority_level: SeniorityLevel.MID_LEVEL,

        // Salary information
        salary_min: '',
        salary_max: '',
        salary_period: SalaryPeriod.YEARLY,

        // Additional fields
        skills: '',

        // Image upload fields
        banner_image: undefined as File | undefined,
        logo_image: undefined as File | undefined,

        // Hidden fields for form processing
        application_process: ApplicationProcess.EMAIL,
        status: JobStatus.PUBLISHED,
        company_id: auth.company?.id || '',
    });

    const [autoSaveStatus, setAutoSaveStatus] = useState<'idle' | 'saving' | 'saved' | 'error'>('idle');
    const [lastSavedAt, setLastSavedAt] = useState<Date>();

    // Form validation
    const validationRules = {
        title: { required: true, minLength: 5, maxLength: 100 },
        description: { required: true, minLength: 50, maxLength: 2000 },
        requirements: { required: true, minLength: 30, maxLength: 1500 },
        company_description: { maxLength: 500 },
        benefits: { maxLength: 800 },
        final_words: { maxLength: 400 },
        skills: { maxLength: 300 },
        workplace: { required: true },
        office_location: { required: true, minLength: 2 },
        category: { required: true },
        employment_type: { required: true },
        application_language: { required: true },
    };

    const {
        errors: validationErrors,
        touched,
        markFieldTouched,
        isFieldValid,
        isFormValid,
    } = useFormValidation(data, validationRules);

    // Calculate form progress
    const calculateProgress = useCallback(() => {
        const requiredFields = [
            data.title,
            data.description,
            data.requirements,
            data.workplace,
            data.office_location,
            data.application_language,
            data.category,
            data.employment_type,
        ];
        
        const optionalFields = [
            data.company_description,
            data.benefits,
            data.final_words,
            data.skills,
            data.salary_min,
            data.salary_max,
        ];
        
        const filledRequired = requiredFields.filter(field => field && field.toString().trim().length > 0).length;
        const filledOptional = optionalFields.filter(field => field && field.toString().trim().length > 0).length;
        
        const requiredWeight = 80;
        const optionalWeight = 20;
        
        const requiredProgress = (filledRequired / requiredFields.length) * requiredWeight;
        const optionalProgress = (filledOptional / optionalFields.length) * optionalWeight;
        
        return Math.round(requiredProgress + optionalProgress);
    }, [data]);

    const [formProgress, setFormProgress] = useState(0);

    useEffect(() => {
        setFormProgress(calculateProgress());
    }, [calculateProgress]);

    // Auto-save functionality (debounced)
    const debouncedAutoSave = useCallback(
        debounce(async (formData: any) => {
            try {
                setAutoSaveStatus('saving');
                // Store in localStorage as backup
                localStorage.setItem('job-listing-draft', JSON.stringify(formData));
                setAutoSaveStatus('saved');
                setLastSavedAt(new Date());
            } catch (error) {
                setAutoSaveStatus('error');
                console.error('Auto-save failed:', error);
            }
        }, 2000),
        []
    );

    useEffect(() => {
        if (formProgress > 5) { // Only auto-save if there's meaningful content
            debouncedAutoSave(data);
        }
    }, [data, debouncedAutoSave, formProgress]);

    // Load draft on component mount
    useEffect(() => {
        const draft = localStorage.getItem('job-listing-draft');
        if (draft) {
            try {
                const draftData = JSON.parse(draft);
                // Only load draft if form is empty
                if (!data.title && !data.description) {
                    Object.keys(draftData).forEach(key => {
                        if (draftData[key] && key !== 'banner_image' && key !== 'logo_image') {
                            setData(key as any, draftData[key]);
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to load draft:', error);
            }
        }
    }, []);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!auth.company) {
            window.location.href = route('company.login');
            return;
        }

        // Clear auto-save draft on successful submit
        localStorage.removeItem('job-listing-draft');
        
        post(route('company.job-listings.store'), {
            forceFormData: true, // Required for file uploads
        });
    };

    // Check section completion
    const isBrandingComplete = !!(data.banner_image || data.logo_image);
    const isJobInfoComplete = !!(data.title && data.description && data.requirements) && !validationErrors.title && !validationErrors.description && !validationErrors.requirements;
    const isLocationComplete = !!(data.workplace && data.office_location) && !validationErrors.workplace && !validationErrors.office_location;
    const isJobDetailsComplete = !!(data.category && data.employment_type) && !validationErrors.category && !validationErrors.employment_type;
    const isSalaryComplete = !!(data.salary_min || data.salary_max);
    const isSkillsComplete = !!data.skills;

    // Stepper configuration
    const stepperSteps = [
        {
            title: 'Job Details',
            description: 'Basic information',
            isCompleted: false,
            isCurrent: true,
        },
        {
            title: 'Screening Questions',
            description: 'Application requirements',
            isCompleted: false,
            isCurrent: false,
        },
        {
            title: 'Review & Publish',
            description: 'Final review',
            isCompleted: false,
            isCurrent: false,
        },
    ];

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Job" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Header with Progress and Stepper */}
                    <div className="mb-8 space-y-6">
                        <div className="text-center">
                            <h1 className="text-3xl font-bold tracking-tight">Create New Job Listing</h1>
                            <p className="text-muted-foreground mt-2">
                                Fill out the job details below. Your progress is automatically saved.
                            </p>
                        </div>
                        
                        {/* Multi-step indicator */}
                        <div className="bg-muted/30 rounded-lg p-6">
                            <Stepper steps={stepperSteps} className="max-w-2xl mx-auto" />
                        </div>

                        {/* Progress and Auto-save Status */}
                        <div className="flex items-center justify-between">
                            <FormProgress currentProgress={formProgress} className="flex-1 max-w-md" />
                            <AutoSaveStatus status={autoSaveStatus} lastSavedAt={lastSavedAt} />
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Company Branding Section */}
                        <FormSection
                            title="Company Branding"
                            description="Upload your company logo and banner to make your listing stand out"
                            icon={<Image className="h-5 w-5" />}
                            isCompleted={isBrandingComplete}
                            defaultOpen={!isBrandingComplete}
                        >
                            <CompanyImageUploader
                                currentBannerUrl={companyBanner || undefined}
                                currentLogoUrl={companyLogo || undefined}
                                onBannerChange={(file) => setData('banner_image', file || undefined)}
                                onLogoChange={(file) => setData('logo_image', file || undefined)}
                                disabled={processing}
                                errors={{
                                    banner: errors.banner_image,
                                    logo: errors.logo_image,
                                }}
                            />
                        </FormSection>

                        {/* Job Information Section */}
                        <FormSection
                            title="Job Information"
                            description="Provide detailed information about the position"
                            icon={<Briefcase className="h-5 w-5" />}
                            isCompleted={isJobInfoComplete}
                            isRequired
                            defaultOpen={!isJobInfoComplete}
                        >
                            <div>
                                <Label htmlFor="title" className="text-base">
                                    Job Title <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="title"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    onBlur={() => markFieldTouched('title')}
                                    className={`mt-1.5 ${
                                        touched.title 
                                            ? validationErrors.title
                                                ? 'border-red-500 focus-visible:border-red-500'
                                                : isFieldValid('title')
                                                ? 'border-green-500'
                                                : ''
                                            : ''
                                    }`}
                                    placeholder="e.g., Senior Frontend Developer"
                                    required
                                />
                                <FieldHelper>Use a clear, specific title that candidates will search for</FieldHelper>
                                {(validationErrors.title && touched.title) && <p className="mt-1 text-sm text-red-500">{validationErrors.title}</p>}
                                {errors.title && <p className="mt-1 text-sm text-red-500">{errors.title}</p>}
                            </div>

                            <div>
                                <Label htmlFor="company_description" className="text-base">
                                    Company Description
                                </Label>
                                <Textarea
                                    id="company_description"
                                    value={data.company_description}
                                    onChange={(e) => setData('company_description', e.target.value)}
                                    className="mt-1.5 min-h-[80px] md:min-h-[100px]"
                                    placeholder="Tell candidates about your company..."
                                />
                                <CharacterCount current={data.company_description.length} max={500} />
                                <FieldHelper>Brief overview of your company, mission, and culture (optional but recommended)</FieldHelper>
                                {errors.company_description && <p className="mt-1 text-sm text-red-500">{errors.company_description}</p>}
                            </div>

                            <div>
                                <Label htmlFor="description" className="text-base">
                                    Job Description <span className="text-red-500">*</span>
                                </Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    onBlur={() => markFieldTouched('description')}
                                    className={`mt-1.5 min-h-[120px] md:min-h-[150px] ${
                                        touched.description 
                                            ? validationErrors.description
                                                ? 'border-red-500 focus-visible:border-red-500'
                                                : isFieldValid('description')
                                                ? 'border-green-500'
                                                : ''
                                            : ''
                                    }`}
                                    placeholder="Describe the role, responsibilities, and expectations..."
                                    required
                                />
                                <CharacterCount current={data.description.length} max={2000} />
                                <FieldHelper>Clearly outline the role, key responsibilities, and day-to-day expectations</FieldHelper>
                                {(validationErrors.description && touched.description) && <p className="mt-1 text-sm text-red-500">{validationErrors.description}</p>}
                                {errors.description && <p className="mt-1 text-sm text-red-500">{errors.description}</p>}
                            </div>

                            <div>
                                <Label htmlFor="requirements" className="text-base">
                                    Requirements <span className="text-red-500">*</span>
                                </Label>
                                <Textarea
                                    id="requirements"
                                    value={data.requirements}
                                    onChange={(e) => setData('requirements', e.target.value)}
                                    onBlur={() => markFieldTouched('requirements')}
                                    className={`mt-1.5 min-h-[120px] md:min-h-[150px] ${
                                        touched.requirements 
                                            ? validationErrors.requirements
                                                ? 'border-red-500 focus-visible:border-red-500'
                                                : isFieldValid('requirements')
                                                ? 'border-green-500'
                                                : ''
                                            : ''
                                    }`}
                                    placeholder="List the qualifications, skills, and experience required..."
                                    required
                                />
                                <CharacterCount current={data.requirements.length} max={1500} />
                                <FieldHelper>List must-have qualifications, skills, and experience. Be specific but not overly restrictive</FieldHelper>
                                {(validationErrors.requirements && touched.requirements) && <p className="mt-1 text-sm text-red-500">{validationErrors.requirements}</p>}
                                {errors.requirements && <p className="mt-1 text-sm text-red-500">{errors.requirements}</p>}
                            </div>

                            <div>
                                <Label htmlFor="benefits" className="text-base">
                                    Benefits & Perks
                                </Label>
                                <Textarea
                                    id="benefits"
                                    value={data.benefits}
                                    onChange={(e) => setData('benefits', e.target.value)}
                                    className="mt-1.5 min-h-[80px] md:min-h-[100px]"
                                    placeholder="Describe what you offer (e.g., flexible hours, remote work, health insurance)..."
                                />
                                <CharacterCount current={data.benefits.length} max={800} />
                                <FieldHelper>Highlight competitive benefits, perks, and what makes your company attractive</FieldHelper>
                                {errors.benefits && <p className="mt-1 text-sm text-red-500">{errors.benefits}</p>}
                            </div>

                            <div>
                                <Label htmlFor="final_words" className="text-base">
                                    Closing Message
                                </Label>
                                <Textarea
                                    id="final_words"
                                    value={data.final_words}
                                    onChange={(e) => setData('final_words', e.target.value)}
                                    className="mt-1.5 min-h-[80px] md:min-h-[100px]"
                                    placeholder="Add any closing remarks or application instructions..."
                                />
                                <CharacterCount current={data.final_words.length} max={400} />
                                <FieldHelper>Optional closing message, application tips, or next steps information</FieldHelper>
                                {errors.final_words && <p className="mt-1 text-sm text-red-500">{errors.final_words}</p>}
                            </div>
                        </FormSection>


                        {/* Job Location Section */}
                        <FormSection
                            title="Location & Work Type"
                            description="Specify where and how the work will be performed"
                            icon={<MapPin className="h-5 w-5" />}
                            isCompleted={isLocationComplete}
                            isRequired
                            defaultOpen={!isLocationComplete}
                        >
                            <div>
                                <Label htmlFor="workplace" className="text-base">
                                    Work Arrangement <span className="text-red-500">*</span>
                                </Label>
                                <Select value={data.workplace} onValueChange={(value) => setData('workplace', value as Workplace)}>
                                    <SelectTrigger id="workplace" className="mt-1.5">
                                        <SelectValue placeholder="Select work arrangement" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value={Workplace.ONSITE}>On-site</SelectItem>
                                        <SelectItem value={Workplace.HYBRID}>Hybrid</SelectItem>
                                        <SelectItem value={Workplace.REMOTE}>Remote</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FieldHelper>Choose the primary work arrangement for this position</FieldHelper>
                                {errors.workplace && <p className="mt-1 text-sm text-red-500">{errors.workplace}</p>}
                            </div>

                            <div>
                                <Label htmlFor="office_location" className="text-base">
                                    {data.workplace === Workplace.REMOTE ? 'Company Location' : 'Office Location'} <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="office_location"
                                    value={data.office_location}
                                    onChange={(e) => setData('office_location', e.target.value)}
                                    className="mt-1.5"
                                    placeholder={data.workplace === Workplace.REMOTE ? "Company headquarters location" : "Office city or location"}
                                    required
                                />
                                <FieldHelper>
                                    {data.workplace === Workplace.REMOTE 
                                        ? "Your company's main location (for legal/tax purposes)"
                                        : "Specific city, district, or area where the office is located"
                                    }
                                </FieldHelper>
                                {errors.office_location && <p className="mt-1 text-sm text-red-500">{errors.office_location}</p>}
                            </div>
                        </FormSection>


                        {/* Job Details Section */}
                        <FormSection
                            title="Job Details"
                            description="Specify the type of position and requirements"
                            icon={<Briefcase className="h-5 w-5" />}
                            isCompleted={isJobDetailsComplete}
                            isRequired
                            defaultOpen={!isJobDetailsComplete}
                        >
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <Label htmlFor="category" className="text-base">
                                        Job Category <span className="text-red-500">*</span>
                                    </Label>
                                    <Select value={data.category} onValueChange={(value) => setData('category', value)}>
                                        <SelectTrigger id="category" className="mt-1.5">
                                            <SelectValue placeholder="Select job category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(categoryOptions).map(([value, label]) => (
                                                <SelectItem key={value} value={value}>
                                                    {label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <FieldHelper>Choose the category that best fits this role</FieldHelper>
                                    {errors.category && <p className="mt-1 text-sm text-red-500">{errors.category}</p>}
                                </div>

                                <div>
                                    <Label htmlFor="application_language" className="text-base">
                                        Application Language <span className="text-red-500">*</span>
                                    </Label>
                                    <Select
                                        value={data.application_language}
                                        onValueChange={(value) => setData('application_language', value as ApplicationLanguage)}
                                    >
                                        <SelectTrigger id="application_language" className="mt-1.5">
                                            <SelectValue placeholder="Select language" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value={ApplicationLanguage.ENGLISH}>English</SelectItem>
                                            <SelectItem value={ApplicationLanguage.GERMAN}>German</SelectItem>
                                            <SelectItem value={ApplicationLanguage.FRENCH}>French</SelectItem>
                                            <SelectItem value={ApplicationLanguage.ITALIAN}>Italian</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldHelper>Language for the application form</FieldHelper>
                                    {errors.application_language && <p className="mt-1 text-sm text-red-500">{errors.application_language}</p>}
                                </div>
                            </div>

                            <div>
                                <Label htmlFor="employment_type" className="text-base">
                                    Employment Type <span className="text-red-500">*</span>
                                </Label>
                                <Select
                                    value={data.employment_type}
                                    onValueChange={(value) => setData('employment_type', value as CustomEmploymentType)}
                                >
                                    <SelectTrigger id="employment_type" className="mt-1.5">
                                        <SelectValue placeholder="Select employment type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value={CustomEmploymentType.PERMANENT}>Permanent position</SelectItem>
                                        <SelectItem value={CustomEmploymentType.TEMPORARY}>Temporary employment</SelectItem>
                                        <SelectItem value={CustomEmploymentType.FREELANCE}>Freelance</SelectItem>
                                        <SelectItem value={CustomEmploymentType.INTERNSHIP}>Internship</SelectItem>
                                        <SelectItem value={CustomEmploymentType.SIDE_JOB}>Side job</SelectItem>
                                        <SelectItem value={CustomEmploymentType.APPRENTICESHIP}>Apprenticeship</SelectItem>
                                        <SelectItem value={CustomEmploymentType.WORKING_STUDENT}>Working student</SelectItem>
                                        <SelectItem value={CustomEmploymentType.INTERIM}>Interim</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FieldHelper>Type of employment contract being offered</FieldHelper>
                                {errors.employment_type && <p className="mt-1 text-sm text-red-500">{errors.employment_type}</p>}
                            </div>

                            <div className="space-y-3">
                                <div>
                                    <Label htmlFor="workload" className="text-base">
                                        Workload Range <span className="text-red-500">*</span>
                                    </Label>
                                    <div className="mt-6 px-2">
                                        <Slider
                                            id="workload"
                                            min={10}
                                            max={100}
                                            step={10}
                                            value={[data.workload_min, data.workload_max]}
                                            onValueChange={(values) => {
                                                setData('workload_min', values[0]);
                                                setData('workload_max', values[1]);
                                            }}
                                        />
                                    </div>
                                    <div className="text-muted-foreground mt-2 flex justify-between text-sm">
                                        <span>
                                            Current range: {data.workload_min}% - {data.workload_max}%
                                        </span>
                                    </div>
                                    <FieldHelper>Specify the percentage of full-time work expected (e.g., 80-100% for full-time)</FieldHelper>
                                    {(errors.workload_min || errors.workload_max) && (
                                        <p className="mt-1 text-sm text-red-500">{errors.workload_min || errors.workload_max}</p>
                                    )}
                                </div>
                            </div>

                            <div>
                                <Label htmlFor="seniority_level" className="text-base">
                                    Experience Level
                                </Label>
                                <Select
                                    value={data.seniority_level}
                                    onValueChange={(value) => setData('seniority_level', value as SeniorityLevel)}
                                >
                                    <SelectTrigger id="seniority_level" className="mt-1.5">
                                        <SelectValue placeholder="Select experience level" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value={SeniorityLevel.NO_EXPERIENCE}>No Experience/Student</SelectItem>
                                        <SelectItem value={SeniorityLevel.JUNIOR}>Junior (1-2 years)</SelectItem>
                                        <SelectItem value={SeniorityLevel.MID_LEVEL}>Mid-Level (3-5 years)</SelectItem>
                                        <SelectItem value={SeniorityLevel.PROFESSIONAL}>Professional (5+ years)</SelectItem>
                                        <SelectItem value={SeniorityLevel.SENIOR}>Senior (7+ years)</SelectItem>
                                        <SelectItem value={SeniorityLevel.LEAD}>Lead/Management</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FieldHelper>Target experience level for this position (helps filter candidates)</FieldHelper>
                                {errors.seniority_level && <p className="mt-1 text-sm text-red-500">{errors.seniority_level}</p>}
                            </div>
                        </FormSection>


                        {/* Compensation Section */}
                        <FormSection
                            title="Compensation"
                            description="Specify salary range and benefits (optional but recommended)"
                            icon={<DollarSign className="h-5 w-5" />}
                            isCompleted={isSalaryComplete}
                            defaultOpen={!isSalaryComplete}
                        >
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <Label htmlFor="salary_min" className="text-base">
                                        Minimum Salary
                                    </Label>
                                    <Input
                                        id="salary_min"
                                        type="number"
                                        value={data.salary_min}
                                        onChange={(e) => setData('salary_min', e.target.value)}
                                        className="mt-1.5"
                                        placeholder="e.g. 80000"
                                    />
                                    {errors.salary_min && <p className="mt-1 text-sm text-red-500">{errors.salary_min}</p>}
                                </div>

                                <div>
                                    <Label htmlFor="salary_max" className="text-base">
                                        Maximum Salary
                                    </Label>
                                    <Input
                                        id="salary_max"
                                        type="number"
                                        value={data.salary_max}
                                        onChange={(e) => setData('salary_max', e.target.value)}
                                        className="mt-1.5"
                                        placeholder="e.g. 120000"
                                    />
                                    {errors.salary_max && <p className="mt-1 text-sm text-red-500">{errors.salary_max}</p>}
                                </div>
                            </div>

                            <div>
                                <Label htmlFor="salary_period" className="text-base">
                                    Salary Period
                                </Label>
                                <Select value={data.salary_period} onValueChange={(value) => setData('salary_period', value as SalaryPeriod)}>
                                    <SelectTrigger id="salary_period" className="mt-1.5">
                                        <SelectValue placeholder="Select salary period" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value={SalaryPeriod.HOURLY}>Per Hour</SelectItem>
                                        <SelectItem value={SalaryPeriod.DAILY}>Per Day</SelectItem>
                                        <SelectItem value={SalaryPeriod.WEEKLY}>Per Week</SelectItem>
                                        <SelectItem value={SalaryPeriod.MONTHLY}>Per Month</SelectItem>
                                        <SelectItem value={SalaryPeriod.YEARLY}>Per Year</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.salary_period && <p className="mt-1 text-sm text-red-500">{errors.salary_period}</p>}
                            </div>
                            
                            <FieldHelper>
                                <strong>Pro tip:</strong> Including salary information increases application quality by 40% and attracts more relevant candidates. Consider showing ranges rather than exact figures.
                            </FieldHelper>
                        </FormSection>


                        {/* Skills Section */}
                        <FormSection
                            title="Skills & Technologies"
                            description="List the key skills and technologies for this role"
                            icon={<Award className="h-5 w-5" />}
                            isCompleted={isSkillsComplete}
                            defaultOpen={!isSkillsComplete}
                        >
                            <div>
                                <Label htmlFor="skills" className="text-base">
                                    Required Skills
                                </Label>
                                <Textarea
                                    id="skills"
                                    value={data.skills}
                                    onChange={(e) => setData('skills', e.target.value)}
                                    className="mt-1.5 min-h-[60px] md:min-h-[80px]"
                                    placeholder="e.g., JavaScript, React, Node.js, PostgreSQL, AWS"
                                />
                                <CharacterCount current={data.skills.length} max={300} />
                                <FieldHelper>
                                    List the most important skills separated by commas. Focus on the technologies, tools, and competencies that are essential for success in this role.
                                </FieldHelper>
                                {errors.skills && <p className="mt-1 text-sm text-red-500">{errors.skills}</p>}
                            </div>
                        </FormSection>

                        {/* Submit Section */}
                        <div className="bg-muted/30 rounded-lg p-6 mt-8">
                            <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div className="text-sm text-muted-foreground">
                                    <div className="font-medium">Next Step:</div>
                                    <div>Set up application requirements and screening questions</div>
                                </div>
                                <div className="flex items-center gap-4">
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        onClick={() => window.history.back()} 
                                        disabled={processing}
                                        className="min-w-[100px]"
                                    >
                                        Cancel
                                    </Button>
                                    <Button 
                                        type="submit" 
                                        disabled={processing || formProgress < 50 || !isFormValid()}
                                        className="min-w-[200px]"
                                    >
                                        {processing ? (
                                            <>
                                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                Creating...
                                            </>
                                        ) : (
                                            'Save Job & Continue to Screening'
                                        )}
                                    </Button>
                                </div>
                            </div>
                            {formProgress < 50 && (
                                <div className="text-sm text-orange-600 mt-2 text-center sm:text-right">
                                    Complete required fields to continue ({Math.round(formProgress)}% done)
                                </div>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </CompanyLayout>
    );
}
