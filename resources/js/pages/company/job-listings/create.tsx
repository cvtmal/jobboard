import { CompanyImageUploader } from '@/components/company';
import { AutoSaveStatus } from '@/components/ui/auto-save-status';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CharacterCount, FieldHelper } from '@/components/ui/field-helper';
import { FormProgress } from '@/components/ui/form-progress';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import { Slider } from '@/components/ui/slider';
import { Stepper } from '@/components/ui/stepper';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { CustomEmploymentType } from '@/types/enums/CustomEmploymentType';
import { JobStatus } from '@/types/enums/JobStatus';
import { SalaryPeriod } from '@/types/enums/SalaryPeriod';
import { SeniorityLevel } from '@/types/enums/SeniorityLevel';
import { Workplace } from '@/types/enums/Workplace';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent, useCallback, useEffect, useState } from 'react';
// @ts-ignore
import { useFormValidation } from '@/hooks/use-form-validation';
import debounce from 'lodash/debounce';
import { Award, Briefcase, Building2, ExternalLink, Loader2, Mail } from 'lucide-react';

// Question interface for screening questions (Step 4)
interface Question {
    id: string;
    text: string;
    requirement: 'optional' | 'required' | 'knockout';
    answerType: 'yes/no' | 'single-choice' | 'multiple-choice' | 'date' | 'number' | 'file-upload' | 'short-text';
    choices?: string[];
}

interface PredefinedQuestion {
    id: string;
    label: string;
    answerType: Question['answerType'];
    defaultText: string;
    choices?: string[];
}

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
    const [currentStep, setCurrentStep] = useState(1);
    const [completedSteps, setCompletedSteps] = useState<number[]>([]);

    // Predefined screening questions
    const predefinedQuestions: PredefinedQuestion[] = [
        { id: 'start-date', label: 'Start date', answerType: 'date', defaultText: 'When are you available to start working with us?' },
        { id: 'salary', label: 'Salary expectation', answerType: 'number', defaultText: 'What is your expected yearly compensation in CHF?' },
        { id: 'work-auth', label: 'Work authorization', answerType: 'yes/no', defaultText: 'Are you currently legally permitted to work in Switzerland?' },
        { id: 'current-city', label: 'Current city', answerType: 'short-text', defaultText: 'What city do you currently live in?' },
        { id: 'drivers-license', label: "Driver's license", answerType: 'yes/no', defaultText: "Do you have a valid driver's license?" },
        { id: 'visa', label: 'Visa status', answerType: 'yes/no', defaultText: 'Will you now or in the future require sponsorship for employment visa status?' },
        { id: 'onsite', label: 'Onsite work', answerType: 'yes/no', defaultText: 'Are you willing to work onsite?' },
        { id: 'remote', label: 'Remote work', answerType: 'yes/no', defaultText: 'Are you willing to work remotely?' },
        {
            id: 'german', label: 'German proficiency', answerType: 'single-choice', defaultText: 'What is your level of German proficiency?',
            choices: ['None', 'Elementary proficiency', 'Limited working proficiency', 'Professional working proficiency', 'Full professional working proficiency', 'Native or bilingual proficiency'],
        },
        {
            id: 'english', label: 'English proficiency', answerType: 'single-choice', defaultText: 'What is your level of English proficiency?',
            choices: ['None', 'Elementary proficiency', 'Limited working proficiency', 'Professional working proficiency', 'Full professional working proficiency', 'Native or bilingual proficiency'],
        },
        { id: 'employment', label: 'Employment type', answerType: 'single-choice', defaultText: 'What type of employment are you looking for?', choices: ['Full-time', 'Part-time', 'Both'] },
        { id: 'shift', label: 'Shift work', answerType: 'yes/no', defaultText: 'Are you willing to work in shifts?' },
    ];

    // Initial screening questions (default ones to show)
    const initialScreeningQuestions: Question[] = [
        {
            id: 'start-date',
            text: predefinedQuestions.find((q) => q.id === 'start-date')?.defaultText || '',
            requirement: 'optional',
            answerType: 'date',
        },
        {
            id: 'salary',
            text: predefinedQuestions.find((q) => q.id === 'salary')?.defaultText || '',
            requirement: 'optional',
            answerType: 'number',
        },
    ];

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
        categories: [] as string[],
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

        // Screening and application fields (Step 4)
        application_documents: {
            cv: 'required',
            cover_letter: 'optional',
        },
        screening_questions: initialScreeningQuestions,

        // Application process fields
        application_process: ApplicationProcess.EMAIL,
        application_email: '',
        application_url: '',
        
        // Hidden fields for form processing
        status: JobStatus.PUBLISHED,
        company_id: auth.company?.id || '',
    });

    const [autoSaveStatus, setAutoSaveStatus] = useState<'idle' | 'saving' | 'saved' | 'error'>('idle');
    const [lastSavedAt, setLastSavedAt] = useState<Date>();

    // Form validation rules by step
    const step1ValidationRules = {
        title: { required: true, minLength: 3, maxLength: 200 },
        categories: { required: true, minLength: 1 },
        workplace: { required: true },
        office_location: { required: true, minLength: 2 },
        employment_type: { required: true },
    };

    const step2ValidationRules = {
        description: { required: true, minLength: 10, maxLength: 2000 },
        requirements: { required: true, minLength: 10, maxLength: 2000 },
        company_description: { maxLength: 1000 },
        benefits: { maxLength: 800 },
        skills: { maxLength: 500 },
    };

    const step3ValidationRules = {
        final_words: { maxLength: 400 },
    };

    const step4ValidationRules = {
        application_process: { required: true },
        application_email: { 
            custom: (value: any) => {
                if (data.application_process === ApplicationProcess.EMAIL && !value) {
                    return 'Email is required when using email application process';
                }
                if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    return 'Please enter a valid email address';
                }
                return null;
            }
        },
        application_url: {
            custom: (value: any) => {
                if (data.application_process === ApplicationProcess.URL && !value) {
                    return 'URL is required when using external website application process';
                }
                return null;
            }
        },
    };

    // Combined validation rules for final submission
    const allValidationRules = {
        ...step1ValidationRules,
        ...step2ValidationRules,
        ...step3ValidationRules,
        ...step4ValidationRules,
    };

    // Step-specific validation
    const step1Validation = useFormValidation(data, step1ValidationRules);
    const step2Validation = useFormValidation(data, step2ValidationRules);
    const step3Validation = useFormValidation(data, step3ValidationRules);
    const step4Validation = useFormValidation(data, step4ValidationRules);
    const allFieldsValidation = useFormValidation(data, allValidationRules);

    // Get current step validation
    const getCurrentStepValidation = () => {
        switch (currentStep) {
            case 1:
                return step1Validation;
            case 2:
                return step2Validation;
            case 3:
                return step3Validation;
            case 4:
                return step4Validation;
            default:
                return step1Validation;
        }
    };

    const currentValidation = getCurrentStepValidation();

    // Check if steps are valid and complete
    const isStep1Valid = () => step1Validation.isFormValid();
    const isStep2Valid = () => step2Validation.isFormValid();
    const isStep3Valid = () => step3Validation.isFormValid(); // Always true since step 3 is optional
    const isStep4Valid = () => step4Validation.isFormValid();

    // Helper function to get detailed validation errors for debugging
    const getDetailedValidationErrors = () => {
        const allErrors = allFieldsValidation.validateAll();
        const missingFields: Record<string, { step: number; field: string; error: string }> = {};

        // Check step 1 fields
        Object.keys(step1ValidationRules).forEach((field) => {
            if (allErrors[field]) {
                missingFields[field] = {
                    step: 1,
                    field: field,
                    error: allErrors[field],
                };
            }
        });

        // Check step 2 fields
        Object.keys(step2ValidationRules).forEach((field) => {
            if (allErrors[field]) {
                missingFields[field] = {
                    step: 2,
                    field: field,
                    error: allErrors[field],
                };
            }
        });

        // Check step 3 fields
        Object.keys(step3ValidationRules).forEach((field) => {
            if (allErrors[field]) {
                missingFields[field] = {
                    step: 3,
                    field: field,
                    error: allErrors[field],
                };
            }
        });

        // Check step 4 fields
        Object.keys(step4ValidationRules).forEach((field) => {
            if (allErrors[field]) {
                missingFields[field] = {
                    step: 4,
                    field: field,
                    error: allErrors[field],
                };
            }
        });

        return missingFields;
    };

    // Calculate form progress based on completed steps
    const calculateProgress = useCallback(() => {
        let progress = 0;
        if (isStep1Valid()) progress += 30; // Step 1 is 30%
        if (isStep2Valid()) progress += 35; // Step 2 is 35%
        if (completedSteps.includes(3)) progress += 25; // Step 3 is 25%
        if (completedSteps.includes(4)) progress += 10; // Step 4 is 10%
        return Math.min(progress, 100);
    }, [isStep1Valid, isStep2Valid, completedSteps]);

    const [formProgress, setFormProgress] = useState(0);

    useEffect(() => {
        setFormProgress(calculateProgress());
    }, [calculateProgress]);

    // Save data immediately to localStorage (synchronous save)
    const saveDataToLocalStorage = useCallback(() => {
        try {
            localStorage.setItem('job-listing-draft', JSON.stringify(data));
            setAutoSaveStatus('saved');
            setLastSavedAt(new Date());
        } catch (error) {
            // Failed to save data to localStorage
        }
    }, [data]);

    // Step navigation functions
    const goToNextStep = () => {
        const current = getCurrentStepValidation();
        if (current.isFormValid()) {
            // Save data immediately before navigating
            saveDataToLocalStorage();
            
            if (!completedSteps.includes(currentStep)) {
                const newCompletedSteps = [...completedSteps, currentStep];
                setCompletedSteps(newCompletedSteps);
                localStorage.setItem('job-listing-completed-steps', JSON.stringify(newCompletedSteps));
            }
            if (currentStep < 4) {
                const nextStep = currentStep + 1;
                setCurrentStep(nextStep);
                localStorage.setItem('job-listing-current-step', nextStep.toString());
            }
        }
    };

    const goToPreviousStep = () => {
        if (currentStep > 1) {
            // Save data immediately before navigating
            saveDataToLocalStorage();
            
            const prevStep = currentStep - 1;
            setCurrentStep(prevStep);
            localStorage.setItem('job-listing-current-step', prevStep.toString());
        }
    };

    const goToStep = (step: number) => {
        // Only allow going to previous steps or if current step is valid
        if (step < currentStep || getCurrentStepValidation().isFormValid()) {
            // Save data immediately before navigating
            saveDataToLocalStorage();
            
            if (step < currentStep || !completedSteps.includes(currentStep)) {
                if (getCurrentStepValidation().isFormValid()) {
                    const newCompletedSteps = [...completedSteps, currentStep];
                    setCompletedSteps(newCompletedSteps);
                    localStorage.setItem('job-listing-completed-steps', JSON.stringify(newCompletedSteps));
                }
            }
            setCurrentStep(step);
            localStorage.setItem('job-listing-current-step', step.toString());
        }
    };

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
            }
        }, 2000),
        [],
    );

    useEffect(() => {
        if (formProgress > 5) {
            // Only auto-save if there's meaningful content
            debouncedAutoSave(data);
        }
    }, [data, debouncedAutoSave, formProgress]);

    // Load draft and step on component mount
    useEffect(() => {
        let hasDataToRestore = false;
        const updates: Partial<typeof data> = {};

        // Load draft data first
        const draft = localStorage.getItem('job-listing-draft');
        if (draft) {
            try {
                const draftData = JSON.parse(draft);
                Object.keys(draftData).forEach((key) => {
                    if (
                        draftData[key] !== undefined &&
                        draftData[key] !== null &&
                        (draftData[key] !== '' || key === 'employment_type') &&
                        key !== 'banner_image' &&
                        key !== 'logo_image'
                    ) {
                        updates[key as keyof typeof data] = draftData[key];
                        hasDataToRestore = true;
                    }
                });
            } catch (error) {
                // Failed to load draft
            }
        }

        // Load saved step
        const savedStep = localStorage.getItem('job-listing-current-step');
        if (savedStep) {
            try {
                const stepNumber = parseInt(savedStep, 10);
                if (stepNumber >= 1 && stepNumber <= 4) {
                    setCurrentStep(stepNumber);
                }
            } catch (error) {
                // Failed to load saved step
            }
        }

        // Load saved completed steps
        const savedCompletedSteps = localStorage.getItem('job-listing-completed-steps');
        if (savedCompletedSteps) {
            try {
                const steps = JSON.parse(savedCompletedSteps);
                if (Array.isArray(steps)) {
                    setCompletedSteps(steps);
                }
            } catch (error) {
                // Failed to load completed steps
            }
        }

        // Apply all data updates at once if we have data to restore
        if (hasDataToRestore) {
            setData((prevData: typeof data) => ({ ...prevData, ...updates }));
        }
    }, []); // Empty dependency array is correct - we only want this to run once on mount

    // Save data when component unmounts or user navigates away
    useEffect(() => {
        const handleBeforeUnload = (e: BeforeUnloadEvent) => {
            // Save data immediately when user is about to leave the page
            saveDataToLocalStorage();
            
            // Show confirmation dialog if there's unsaved data
            if (formProgress > 5 && !processing) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        };

        // Save data when visibility changes (e.g., switching tabs)
        const handleVisibilityChange = () => {
            if (document.hidden) {
                saveDataToLocalStorage();
            }
        };

        window.addEventListener('beforeunload', handleBeforeUnload);
        document.addEventListener('visibilitychange', handleVisibilityChange);

        // Cleanup
        return () => {
            // Save data one more time when component unmounts
            saveDataToLocalStorage();
            window.removeEventListener('beforeunload', handleBeforeUnload);
            document.removeEventListener('visibilitychange', handleVisibilityChange);
        };
    }, [formProgress, processing, saveDataToLocalStorage]); // Include dependencies for the save function

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!auth.company) {
            window.location.href = route('company.login');
            return;
        }

        if (!allFieldsValidation.isFormValid()) {
            return;
        }

        post(route('company.job-listings.store'), {
            forceFormData: true,
            onSuccess: () => {
                setTimeout(() => {
                    localStorage.removeItem('job-listing-draft');
                    localStorage.removeItem('job-listing-current-step');
                    localStorage.removeItem('job-listing-completed-steps');
                }, 100);
            }
        });
    };

    // Stepper configuration - Reactive to current step and completed steps
    const stepperSteps = [
        {
            title: 'Job Essentials',
            description: 'Basic job information',
            isCompleted: completedSteps.includes(1) || (currentStep > 1 && isStep1Valid()),
            isCurrent: currentStep === 1,
        },
        {
            title: 'Job Details',
            description: 'Description & requirements',
            isCompleted: completedSteps.includes(2) || (currentStep > 2 && isStep2Valid()),
            isCurrent: currentStep === 2,
        },
        {
            title: 'Job Settings',
            description: 'Salary & preferences',
            isCompleted: completedSteps.includes(3) || (currentStep > 3 && isStep3Valid()),
            isCurrent: currentStep === 3,
        },
        {
            title: 'Screening Questions',
            description: 'Application requirements',
            isCompleted: completedSteps.includes(4),
            isCurrent: currentStep === 4,
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
                                Complete the {currentStep === 1 ? 'essential' : currentStep === 2 ? 'detailed' : 'optional'} information for your job
                                listing. Your progress is automatically saved.
                            </p>
                        </div>

                        {/* Multi-step indicator - Always visible on all steps */}
                        <div className="bg-muted/30 rounded-lg p-6" style={{ display: 'block', minHeight: '100px' }}>
                            <Stepper 
                                key={`stepper-${currentStep}`} 
                                steps={stepperSteps} 
                                className="mx-auto max-w-2xl" 
                                onStepClick={goToStep} 
                            />
                        </div>

                        {/* Progress and Auto-save Status */}
                        <div className="flex items-center justify-between">
                            <FormProgress currentProgress={formProgress} className="max-w-md flex-1" />
                            <AutoSaveStatus status={autoSaveStatus} lastSavedAt={lastSavedAt} />
                        </div>
                    </div>

                    <form onSubmit={currentStep === 4 ? handleSubmit : (e) => e.preventDefault()} className="space-y-6">
                        {/* Step 1: Job Essentials */}
                        {currentStep === 1 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-3">
                                        <Briefcase className="text-primary h-5 w-5" />
                                        Job Essentials
                                    </CardTitle>
                                    <CardDescription>Let's start with the basic information about your job posting</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {/* Job Title */}
                                    <div>
                                        <Label htmlFor="title" className="text-base">
                                            Job Title <span className="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            onBlur={() => currentValidation.markFieldTouched('title')}
                                            className={`mt-1.5 ${
                                                currentValidation.touched.title
                                                    ? currentValidation.errors.title
                                                        ? 'border-red-500 focus-visible:border-red-500'
                                                        : currentValidation.isFieldValid('title')
                                                          ? 'border-green-500'
                                                          : ''
                                                    : ''
                                            }`}
                                            placeholder="e.g., Senior Frontend Developer"
                                            required
                                        />
                                        <FieldHelper>Use a clear, specific title that candidates will search for</FieldHelper>
                                        {currentValidation.errors.title && currentValidation.touched.title && (
                                            <p className="mt-1 text-sm text-red-500">{currentValidation.errors.title}</p>
                                        )}
                                        {errors.title && <p className="mt-1 text-sm text-red-500">{errors.title}</p>}
                                    </div>

                                    {/* Job Categories */}
                                    <div>
                                        <Label htmlFor="categories" className="text-base">
                                            Job Categories <span className="text-red-500">*</span>
                                        </Label>
                                        <div className="mt-1.5">
                                            <MultiSelect
                                                options={Object.entries(categoryOptions).map(([value, label]) => ({
                                                    value,
                                                    label,
                                                }))}
                                                selected={data.categories}
                                                onSelectionChange={(selected) => setData('categories', selected)}
                                                onBlur={() => currentValidation.markFieldTouched('categories')}
                                                placeholder="Select job categories"
                                                className={
                                                    currentValidation.touched.categories
                                                        ? currentValidation.errors.categories
                                                            ? 'border-red-500 focus-visible:border-red-500'
                                                            : currentValidation.isFieldValid('categories')
                                                              ? 'border-green-500'
                                                              : ''
                                                        : ''
                                                }
                                            />
                                        </div>
                                        <FieldHelper>Choose one or more categories that best fit this role</FieldHelper>
                                        {currentValidation.errors.categories && currentValidation.touched.categories && (
                                            <p className="mt-1 text-sm text-red-500">{currentValidation.errors.categories}</p>
                                        )}
                                        {errors.categories && <p className="mt-1 text-sm text-red-500">{errors.categories}</p>}
                                    </div>

                                    {/* Work Arrangement and Location */}
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
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

                                        {/* Conditional location field */}
                                        {(data.workplace === Workplace.ONSITE ||
                                            data.workplace === Workplace.HYBRID ||
                                            data.workplace === Workplace.REMOTE) && (
                                            <div>
                                                <Label htmlFor="office_location" className="text-base">
                                                    {data.workplace === Workplace.REMOTE ? 'Company Location' : 'Office Location'}{' '}
                                                    <span className="text-red-500">*</span>
                                                </Label>
                                                <Input
                                                    id="office_location"
                                                    value={data.office_location}
                                                    onChange={(e) => setData('office_location', e.target.value)}
                                                    onBlur={() => currentValidation.markFieldTouched('office_location')}
                                                    className={`mt-1.5 ${
                                                        currentValidation.touched.office_location
                                                            ? currentValidation.errors.office_location
                                                                ? 'border-red-500 focus-visible:border-red-500'
                                                                : currentValidation.isFieldValid('office_location')
                                                                  ? 'border-green-500'
                                                                  : ''
                                                            : ''
                                                    }`}
                                                    placeholder={
                                                        data.workplace === Workplace.REMOTE
                                                            ? 'Company headquarters location'
                                                            : 'Office city or location'
                                                    }
                                                    required
                                                />
                                                <FieldHelper>
                                                    {data.workplace === Workplace.REMOTE
                                                        ? "Your company's main location (for legal/tax purposes)"
                                                        : 'Specific city, district, or area where the office is located'}
                                                </FieldHelper>
                                                {currentValidation.errors.office_location && currentValidation.touched.office_location && (
                                                    <p className="mt-1 text-sm text-red-500">{currentValidation.errors.office_location}</p>
                                                )}
                                                {errors.office_location && <p className="mt-1 text-sm text-red-500">{errors.office_location}</p>}
                                            </div>
                                        )}
                                    </div>

                                    {/* Employment Type and Workload */}
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                                {/* Quick select buttons for mobile */}
                                                <div className="mt-3 grid grid-cols-4 gap-2 md:hidden">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setData('workload_min', 20);
                                                            setData('workload_max', 40);
                                                        }}
                                                    >
                                                        20-40%
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setData('workload_min', 50);
                                                            setData('workload_max', 70);
                                                        }}
                                                    >
                                                        50-70%
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setData('workload_min', 80);
                                                            setData('workload_max', 100);
                                                        }}
                                                    >
                                                        80-100%
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setData('workload_min', 100);
                                                            setData('workload_max', 100);
                                                        }}
                                                    >
                                                        100%
                                                    </Button>
                                                </div>
                                                <FieldHelper>Specify the percentage of full-time work expected</FieldHelper>
                                                {(errors.workload_min || errors.workload_max) && (
                                                    <p className="mt-1 text-sm text-red-500">{errors.workload_min || errors.workload_max}</p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Step 2: Job Details & Description */}
                        {currentStep === 2 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-3">
                                        <Building2 className="text-primary h-5 w-5" />
                                        Job Details & Description
                                    </CardTitle>
                                    <CardDescription>Now let's add detailed information about the role and your company</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {/* Company Description */}
                                    <div>
                                        <Label htmlFor="company_description" className="text-base">
                                            Company Description
                                        </Label>
                                        <Textarea
                                            id="company_description"
                                            value={data.company_description}
                                            onChange={(e) => setData('company_description', e.target.value)}
                                            className="mt-1.5 min-h-[120px] md:min-h-[150px]"
                                            placeholder="Tell candidates about your company..."
                                        />
                                        <CharacterCount current={data.company_description.length} max={500} />
                                        <FieldHelper>Brief overview of your company, mission, and culture (optional but recommended)</FieldHelper>
                                        {errors.company_description && <p className="mt-1 text-sm text-red-500">{errors.company_description}</p>}
                                    </div>

                                    {/* Job Description */}
                                    <div>
                                        <Label htmlFor="description" className="text-base">
                                            Job Description <span className="text-red-500">*</span>
                                        </Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            onBlur={() => currentValidation.markFieldTouched('description')}
                                            className={`mt-1.5 min-h-[150px] md:min-h-[180px] ${
                                                currentValidation.touched.description
                                                    ? currentValidation.errors.description
                                                        ? 'border-red-500 focus-visible:border-red-500'
                                                        : currentValidation.isFieldValid('description')
                                                          ? 'border-green-500'
                                                          : ''
                                                    : ''
                                            }`}
                                            placeholder="Describe the role, responsibilities, and expectations..."
                                            required
                                        />
                                        <CharacterCount current={data.description.length} max={2000} />
                                        <FieldHelper>Clearly outline the role, key responsibilities, and day-to-day expectations</FieldHelper>
                                        {currentValidation.errors.description && currentValidation.touched.description && (
                                            <p className="mt-1 text-sm text-red-500">{currentValidation.errors.description}</p>
                                        )}
                                        {errors.description && <p className="mt-1 text-sm text-red-500">{errors.description}</p>}
                                    </div>

                                    {/* Requirements */}
                                    <div>
                                        <Label htmlFor="requirements" className="text-base">
                                            Requirements <span className="text-red-500">*</span>
                                        </Label>
                                        <Textarea
                                            id="requirements"
                                            value={data.requirements}
                                            onChange={(e) => setData('requirements', e.target.value)}
                                            onBlur={() => currentValidation.markFieldTouched('requirements')}
                                            className={`mt-1.5 min-h-[150px] md:min-h-[180px] ${
                                                currentValidation.touched.requirements
                                                    ? currentValidation.errors.requirements
                                                        ? 'border-red-500 focus-visible:border-red-500'
                                                        : currentValidation.isFieldValid('requirements')
                                                          ? 'border-green-500'
                                                          : ''
                                                    : ''
                                            }`}
                                            placeholder="List the qualifications, skills, and experience required..."
                                            required
                                        />
                                        <CharacterCount current={data.requirements.length} max={1500} />
                                        <FieldHelper>
                                            List must-have qualifications, skills, and experience. Be specific but not overly restrictive
                                        </FieldHelper>
                                        {currentValidation.errors.requirements && currentValidation.touched.requirements && (
                                            <p className="mt-1 text-sm text-red-500">{currentValidation.errors.requirements}</p>
                                        )}
                                        {errors.requirements && <p className="mt-1 text-sm text-red-500">{errors.requirements}</p>}
                                    </div>

                                    {/* Benefits */}
                                    <div>
                                        <Label htmlFor="benefits" className="text-base">
                                            Benefits & Perks
                                        </Label>
                                        <Textarea
                                            id="benefits"
                                            value={data.benefits}
                                            onChange={(e) => setData('benefits', e.target.value)}
                                            className="mt-1.5 min-h-[120px] md:min-h-[150px]"
                                            placeholder="Describe what you offer (e.g., flexible hours, remote work, health insurance)..."
                                        />
                                        <CharacterCount current={data.benefits.length} max={800} />
                                        <FieldHelper>Highlight competitive benefits, perks, and what makes your company attractive</FieldHelper>
                                        {errors.benefits && <p className="mt-1 text-sm text-red-500">{errors.benefits}</p>}
                                    </div>

                                    {/* Skills */}
                                    <div>
                                        <Label htmlFor="skills" className="text-base">
                                            Required Skills
                                        </Label>
                                        <Textarea
                                            id="skills"
                                            value={data.skills}
                                            onChange={(e) => setData('skills', e.target.value)}
                                            className="mt-1.5 min-h-[100px] md:min-h-[120px]"
                                            placeholder="e.g., JavaScript, React, Node.js, PostgreSQL, AWS"
                                        />
                                        <CharacterCount current={data.skills.length} max={500} />
                                        <FieldHelper>
                                            List the most important skills separated by commas. Focus on the technologies, tools, and competencies
                                            that are essential for success in this role.
                                        </FieldHelper>
                                        {errors.skills && <p className="mt-1 text-sm text-red-500">{errors.skills}</p>}
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Step 3: Optional Enhancements */}
                        {currentStep === 3 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-3">
                                        <Award className="text-primary h-5 w-5" />
                                        Optional Enhancements
                                    </CardTitle>
                                    <CardDescription>Add optional features to make your job listing more attractive</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {/* Salary Information */}
                                    <div className="space-y-4">
                                        <h4 className="text-lg font-medium">Salary Information</h4>
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
                                            <Select
                                                value={data.salary_period}
                                                onValueChange={(value) => setData('salary_period', value as SalaryPeriod)}
                                            >
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
                                            <strong>Pro tip:</strong> Including salary information increases application quality by 40% and attracts
                                            more relevant candidates.
                                        </FieldHelper>
                                    </div>

                                    {/* Experience Level */}
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

                                    {/* Company Branding */}
                                    <div className="space-y-4">
                                        <h4 className="text-lg font-medium">Company Branding</h4>
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
                                    </div>

                                    {/* Final Words */}
                                    <div>
                                        <Label htmlFor="final_words" className="text-base">
                                            Closing Message
                                        </Label>
                                        <Textarea
                                            id="final_words"
                                            value={data.final_words}
                                            onChange={(e) => setData('final_words', e.target.value)}
                                            className="mt-1.5 min-h-[120px] md:min-h-[150px]"
                                            placeholder="Add any closing remarks or application instructions..."
                                        />
                                        <CharacterCount current={data.final_words.length} max={400} />
                                        <FieldHelper>Optional closing message, application tips, or next steps information</FieldHelper>
                                        {errors.final_words && <p className="mt-1 text-sm text-red-500">{errors.final_words}</p>}
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Step 4: Screening Questions */}
                        {currentStep === 4 && (
                            <div className="space-y-6">
                                {/* Application Process */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-3">
                                            <Mail className="text-primary h-5 w-5" />
                                            Application Process
                                        </CardTitle>
                                        <CardDescription>Choose how candidates should apply for this position</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div>
                                            <Label htmlFor="application_process" className="text-base">
                                                Application Method <span className="text-red-500">*</span>
                                            </Label>
                                            <Select
                                                value={data.application_process}
                                                onValueChange={(value) => {
                                                    setData('application_process', value as ApplicationProcess);
                                                    // Clear the fields when switching
                                                    if (value === ApplicationProcess.EMAIL) {
                                                        setData('application_url', '');
                                                    } else if (value === ApplicationProcess.URL) {
                                                        setData('application_email', '');
                                                    }
                                                }}
                                            >
                                                <SelectTrigger id="application_process" className="mt-1.5">
                                                    <SelectValue placeholder="Select application method" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value={ApplicationProcess.EMAIL}>
                                                        <div className="flex items-center gap-2">
                                                            <Mail className="h-4 w-4" />
                                                            Email Application
                                                        </div>
                                                    </SelectItem>
                                                    <SelectItem value={ApplicationProcess.URL}>
                                                        <div className="flex items-center gap-2">
                                                            <ExternalLink className="h-4 w-4" />
                                                            External Website
                                                        </div>
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <FieldHelper>
                                                Choose whether applicants should apply via email or through your company's external application system
                                            </FieldHelper>
                                            {errors.application_process && <p className="mt-1 text-sm text-red-500">{errors.application_process}</p>}
                                        </div>

                                        {/* Conditional Email Field */}
                                        {data.application_process === ApplicationProcess.EMAIL && (
                                            <div>
                                                <Label htmlFor="application_email" className="text-base">
                                                    Application Email <span className="text-red-500">*</span>
                                                </Label>
                                                <Input
                                                    id="application_email"
                                                    type="email"
                                                    value={data.application_email}
                                                    onChange={(e) => setData('application_email', e.target.value)}
                                                    onBlur={() => currentValidation.markFieldTouched('application_email')}
                                                    className={`mt-1.5 ${
                                                        currentValidation.touched.application_email
                                                            ? currentValidation.errors.application_email
                                                                ? 'border-red-500 focus-visible:border-red-500'
                                                                : currentValidation.isFieldValid('application_email')
                                                                  ? 'border-green-500'
                                                                  : ''
                                                            : ''
                                                    }`}
                                                    placeholder="hr@company.com"
                                                    required
                                                />
                                                <FieldHelper>
                                                    Applications will be sent to this email address. Make sure it's actively monitored.
                                                </FieldHelper>
                                                {currentValidation.errors.application_email && currentValidation.touched.application_email && (
                                                    <p className="mt-1 text-sm text-red-500">{currentValidation.errors.application_email}</p>
                                                )}
                                                {errors.application_email && <p className="mt-1 text-sm text-red-500">{errors.application_email}</p>}
                                            </div>
                                        )}

                                        {/* Conditional URL Field */}
                                        {data.application_process === ApplicationProcess.URL && (
                                            <div>
                                                <Label htmlFor="application_url" className="text-base">
                                                    Application URL <span className="text-red-500">*</span>
                                                </Label>
                                                <Input
                                                    id="application_url"
                                                    value={data.application_url}
                                                    onChange={(e) => setData('application_url', e.target.value)}
                                                    onBlur={() => currentValidation.markFieldTouched('application_url')}
                                                    className={`mt-1.5 ${
                                                        currentValidation.touched.application_url
                                                            ? currentValidation.errors.application_url
                                                                ? 'border-red-500 focus-visible:border-red-500'
                                                                : currentValidation.isFieldValid('application_url')
                                                                  ? 'border-green-500'
                                                                  : ''
                                                            : ''
                                                    }`}
                                                    placeholder="https://careers.company.com/apply/job-id"
                                                    required
                                                />
                                                <FieldHelper>
                                                    Applicants will be redirected to this URL to complete their application on your website.
                                                </FieldHelper>
                                                {currentValidation.errors.application_url && currentValidation.touched.application_url && (
                                                    <p className="mt-1 text-sm text-red-500">{currentValidation.errors.application_url}</p>
                                                )}
                                                {errors.application_url && <p className="mt-1 text-sm text-red-500">{errors.application_url}</p>}
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>

                                {/* Application Documents */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-3">
                                            <Building2 className="text-primary h-5 w-5" />
                                            Application Documents
                                        </CardTitle>
                                        <CardDescription>Customize the document requirements for this job</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="cv">CV / Resume</Label>
                                                <Select
                                                    value={data.application_documents.cv}
                                                    onValueChange={(value) =>
                                                        setData('application_documents', {
                                                            ...data.application_documents,
                                                            cv: value,
                                                        })
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select option" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="required">Required</SelectItem>
                                                        <SelectItem value="optional">Optional</SelectItem>
                                                        <SelectItem value="hidden">Hidden</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="cover_letter">Cover Letter</Label>
                                                <Select
                                                    value={data.application_documents.cover_letter}
                                                    onValueChange={(value) =>
                                                        setData('application_documents', {
                                                            ...data.application_documents,
                                                            cover_letter: value,
                                                        })
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select option" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="required">Required</SelectItem>
                                                        <SelectItem value="optional">Optional</SelectItem>
                                                        <SelectItem value="hidden">Hidden</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                {/* Screening Questions */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-3">
                                            <Briefcase className="text-primary h-5 w-5" />
                                            Screening Questions
                                        </CardTitle>
                                        <CardDescription>Add screening questions to find the best candidates for {data.title || 'this position'}</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="text-muted-foreground text-sm">
                                            Start with some pre-configured questions that are commonly asked, or add your own custom questions.
                                        </div>
                                        
                                        {/* Questions List */}
                                        {data.screening_questions.length > 0 && (
                                            <div className="space-y-4">
                                                {data.screening_questions.map((question, index) => (
                                                    <div key={question.id} className="border-border rounded-lg border p-4">
                                                        <div className="flex items-center justify-between">
                                                            <h4 className="font-medium">
                                                                {predefinedQuestions.find((q) => q.id === question.id)?.label || 'Custom Question'}
                                                            </h4>
                                                            <Button
                                                                type="button"
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() => {
                                                                    const updatedQuestions = data.screening_questions.filter((_, i) => i !== index);
                                                                    setData('screening_questions', updatedQuestions);
                                                                }}
                                                            >
                                                                Remove
                                                            </Button>
                                                        </div>
                                                        <p className="text-muted-foreground mt-1 text-sm">{question.text}</p>
                                                        <div className="mt-2 text-xs text-gray-500">
                                                            {question.requirement === 'optional' && 'Optional question'}
                                                            {question.requirement === 'required' && 'Required question'}
                                                            {question.requirement === 'knockout' && 'Knockout question'}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                        {/* Add Questions */}
                                        <div className="space-y-4">
                                            <h4 className="font-medium">Add Common Questions</h4>
                                            <div className="flex flex-wrap gap-2">
                                                {predefinedQuestions
                                                    .filter((question) => !data.screening_questions.some((q) => q.id === question.id))
                                                    .slice(0, 8) // Show first 8 questions
                                                    .map((question) => (
                                                        <Button
                                                            key={question.id}
                                                            type="button"
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() => {
                                                                const newQuestion: Question = {
                                                                    id: question.id,
                                                                    text: question.defaultText,
                                                                    requirement: 'optional',
                                                                    answerType: question.answerType,
                                                                    choices: question.choices,
                                                                };
                                                                setData('screening_questions', [...data.screening_questions, newQuestion]);
                                                            }}
                                                        >
                                                            + {question.label}
                                                        </Button>
                                                    ))}
                                            </div>
                                        </div>

                                        {data.screening_questions.length === 0 && (
                                            <div className="text-muted-foreground py-6 text-center">
                                                No screening questions added yet. Click on a button above to add questions.
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* Step Navigation */}
                        <div className="bg-muted/30 mt-8 rounded-lg p-6">
                            <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
                                <div className="text-muted-foreground text-sm">
                                    <div className="font-medium">
                                        {currentStep === 1 && 'Step 1 of 4: Job Essentials'}
                                        {currentStep === 2 && 'Step 2 of 4: Job Details & Description'}
                                        {currentStep === 3 && 'Step 3 of 4: Job Settings'}
                                        {currentStep === 4 && 'Step 4 of 4: Screening Questions'}
                                    </div>
                                    <div>
                                        {currentStep === 1 && 'Fill in the basic information about your job'}
                                        {currentStep === 2 && 'Add detailed descriptions and requirements'}
                                        {currentStep === 3 && 'Configure salary, experience level and branding'}
                                        {currentStep === 4 && 'Set up application documents and screening questions'}
                                    </div>
                                </div>
                                <div className="flex items-center gap-4">
                                    {/* Previous Button */}
                                    {currentStep > 1 && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={goToPreviousStep}
                                            disabled={processing}
                                            className="min-w-[100px]"
                                        >
                                            Previous
                                        </Button>
                                    )}

                                    {/* Cancel Button */}
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => {
                                            // Navigate to job listings instead of using browser back
                                            window.location.href = route('company.job-listings.index');
                                        }}
                                        disabled={processing}
                                        className="min-w-[100px]"
                                    >
                                        Cancel
                                    </Button>

                                    {/* Next/Submit Button */}
                                    {currentStep < 4 ? (
                                        <Button
                                            type="button"
                                            onClick={(e) => {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                goToNextStep();
                                            }}
                                            disabled={processing || !getCurrentStepValidation().isFormValid()}
                                            className="min-w-[140px]"
                                        >
                                            Next Step
                                        </Button>
                                    ) : (
                                        <Button type="submit" disabled={processing || !allFieldsValidation.isFormValid()} className="min-w-[200px]">
                                            {processing ? (
                                                <>
                                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                    Creating...
                                                </>
                                            ) : (
                                                'Create Job Listing'
                                            )}
                                        </Button>
                                    )}
                                </div>
                            </div>
                            {/* Progress feedback */}
                            {currentStep < 4 && !getCurrentStepValidation().isFormValid() && (
                                <div className="mt-2 text-center text-sm text-orange-600 sm:text-right">
                                    Please complete all required fields to continue
                                </div>
                            )}
                            {currentStep === 4 && !allFieldsValidation.isFormValid() && (
                                <div className="mt-2 text-center text-sm text-orange-600 sm:text-right">
                                    Please complete all required fields from previous steps
                                </div>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </CompanyLayout>
    );
}
