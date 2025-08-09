import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { SafeHtml } from '@/components/ui/safe-html';
import { Separator } from '@/components/ui/separator';
import CompanyLayout from '@/layouts/company/CompanyLayout';
import { Head, Link, router } from '@inertiajs/react';

interface Company {
    id: number;
    name: string;
    address: string | null;
    postcode: string | null;
    city: string | null;
    latitude: number | null;
    longitude: number | null;
    url: string | null;
    size: string | null;
    type: string | null;
    description_english: string | null;
    description_german: string | null;
    description_french: string | null;
    description_italian: string | null;
    logo: string | null;
    cover: string | null;
    video: string | null;
    email: string;
}

interface JobListing {
    id: number;
    company_id: number;
    title: string;
    description: string;
    requirements?: string;
    benefits?: string;
    company_description?: string;
    skills?: string;
    final_words?: string;
    location: string | null;
    city?: string | null;
    primary_canton_code?: string | null;
    primary_sub_region?: string | null;
    workplace?: string | null;
    workload_min?: number | null;
    workload_max?: number | null;
    categories?: string[] | null;
    salary_min: number | null;
    salary_max: number | null;
    salary_type: string | null;
    employment_type: string | null;
    experience_level: string | null;
    seniority_level?: string | null;
    application_process: string;
    application_email: string | null;
    application_url: string | null;
    application_documents?: {
        cv?: string;
        cover_letter?: string;
    } | null;
    screening_questions?: Array<{
        id: string;
        text: string;
        requirement: 'optional' | 'required' | 'knockout';
        answerType: string;
        choices?: string[];
    }> | null;
    status: string;
    created_at: string;
    updated_at: string;
    company: Company;
}

interface ShowProps {
    jobListing: JobListing;
}

export default function Show({ jobListing }: ShowProps) {
    const handleDelete = () => {
        router.delete(route('company.job-listings.destroy', jobListing.id));
    };

    // Format salary information for display
    const formatSalary = () => {
        if (!jobListing.salary_min && !jobListing.salary_max) return 'Not specified';

        let salaryText = '';
        if (jobListing.salary_min && jobListing.salary_max) {
            salaryText = `${jobListing.salary_min.toLocaleString()} - ${jobListing.salary_max.toLocaleString()}`;
        } else if (jobListing.salary_min) {
            salaryText = `From ${jobListing.salary_min.toLocaleString()}`;
        } else if (jobListing.salary_max) {
            salaryText = `Up to ${jobListing.salary_max.toLocaleString()}`;
        }

        const suffixes: Record<string, string> = {
            annual: ' per year',
            monthly: ' per month',
            hourly: ' per hour',
        };

        return salaryText + ((jobListing.salary_type && suffixes[jobListing.salary_type]) || '');
    };

    // Format application method for display
    const formatApplicationMethod = () => {
        switch (jobListing.application_process) {
            case 'email':
                return jobListing.application_email ? (
                    <a href={`mailto:${jobListing.application_email}`} className="text-blue-600 hover:underline">
                        {jobListing.application_email}
                    </a>
                ) : (
                    'Email (not specified)'
                );
            case 'external':
                return jobListing.application_url ? (
                    <a href={jobListing.application_url} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">
                        External application link
                    </a>
                ) : (
                    'External URL (not specified)'
                );
            case 'internal':
                return 'Internal application form';
            default:
                return 'Not specified';
        }
    };

    // Format workload range for display
    const formatWorkload = () => {
        if (!jobListing.workload_min && !jobListing.workload_max) return 'Not specified';
        
        if (jobListing.workload_min === jobListing.workload_max) {
            return `${jobListing.workload_min}%`;
        }
        
        return `${jobListing.workload_min || 0}% - ${jobListing.workload_max || 100}%`;
    };

    // Format workplace type for display
    const formatWorkplace = (workplace: string | null) => {
        if (!workplace) return 'Not specified';
        
        const workplaceMap: Record<string, string> = {
            'onsite': 'On-site',
            'hybrid': 'Hybrid',
            'remote': 'Remote'
        };
        
        return workplaceMap[workplace.toLowerCase()] || workplace;
    };

    // Format categories for display
    const formatCategories = (categories: string[] | null) => {
        if (!categories || categories.length === 0) return [];
        
        return categories.map(category => 
            category.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
        );
    };

    // Format document requirements for display
    const formatDocumentRequirement = (requirement: string | undefined): { text: string; color: string } => {
        if (!requirement) return { text: 'Not specified', color: 'bg-gray-100 text-gray-600' };
        
        const requirementMap: Record<string, { text: string; color: string }> = {
            'required': { text: 'Required', color: 'bg-red-100 text-red-800' },
            'optional': { text: 'Optional', color: 'bg-blue-100 text-blue-800' },
            'hidden': { text: 'Not required', color: 'bg-gray-100 text-gray-600' }
        };
        
        return requirementMap[requirement] || { text: requirement, color: 'bg-gray-100 text-gray-600' };
    };

    return (
        <CompanyLayout>
            <Head title={jobListing.title} />

            <div className="py-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">{jobListing.title}</h1>
                    <div className="flex gap-3">
                        <Link href={route('company.job-listings.edit', jobListing.id)}>
                            <Button>Edit Job</Button>
                        </Link>
                        <Link href={route('company.job-listings.index')}>
                            <Button variant="outline">Back to Listings</Button>
                        </Link>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        {/* Job Overview Card */}
                        <Card>
                            <CardHeader className="flex flex-row items-start justify-between pb-3">
                                <div>
                                    <CardTitle className="text-xl">Job Overview</CardTitle>
                                </div>
                                <Badge
                                    className={
                                        jobListing.status === 'published'
                                            ? 'bg-green-100 text-green-800'
                                            : jobListing.status === 'draft'
                                              ? 'bg-yellow-100 text-yellow-800'
                                              : 'bg-gray-100 text-gray-800'
                                    }
                                >
                                    {jobListing.status?.toUpperCase()}
                                </Badge>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium">Description</h3>
                                    <div className="prose prose-sm mt-2 max-w-none">
                                        <SafeHtml content={jobListing.description} />
                                    </div>
                                </div>

                                <Separator />

                                <div className="grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Workload</h3>
                                        <p className="mt-1 font-medium">{formatWorkload()}</p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Work Arrangement</h3>
                                        <p className="mt-1 font-medium">{formatWorkplace(jobListing.workplace || null)}</p>
                                    </div>

                                    <div className="col-span-full">
                                        <h3 className="text-sm font-medium text-gray-500">Job Categories</h3>
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            {formatCategories(jobListing.categories || null).length > 0 ? (
                                                formatCategories(jobListing.categories || null).map((category, index) => (
                                                    <Badge key={index} variant="outline" className="text-xs">
                                                        {category}
                                                    </Badge>
                                                ))
                                            ) : (
                                                <span className="text-gray-500">No categories specified</span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Requirements & Skills Card */}
                        {(jobListing.requirements || jobListing.skills) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-xl">Requirements & Skills</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {jobListing.requirements && (
                                        <div>
                                            <h3 className="text-lg font-medium">Requirements</h3>
                                            <div className="prose prose-sm mt-2 max-w-none">
                                                <SafeHtml content={jobListing.requirements} />
                                            </div>
                                        </div>
                                    )}

                                    {jobListing.requirements && jobListing.skills && <Separator />}

                                    {jobListing.skills && (
                                        <div>
                                            <h3 className="text-lg font-medium">Required Skills</h3>
                                            <div className="mt-2 flex flex-wrap gap-2">
                                                {jobListing.skills.split(',').map((skill, index) => (
                                                    <Badge key={index} variant="secondary" className="text-sm">
                                                        {skill.trim()}
                                                    </Badge>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    <div className="grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500">Experience Level</h3>
                                            <p className="mt-1">
                                                {jobListing.experience_level?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) ||
                                                    jobListing.seniority_level?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) ||
                                                    'Not specified'}
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Job Details Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl">Job Details</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Employment Type</h3>
                                        <p className="mt-1">
                                            {jobListing.employment_type?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) ||
                                                'Not specified'}
                                        </p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Salary</h3>
                                        <p className="mt-1">{formatSalary()}</p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Location</h3>
                                        <p className="mt-1">
                                            {jobListing.location || jobListing.city || 'Not specified'}
                                            {jobListing.primary_canton_code && (
                                                <span className="text-gray-500 ml-2">
                                                    ({jobListing.primary_canton_code})
                                                </span>
                                            )}
                                        </p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Date Posted</h3>
                                        <p className="mt-1">{jobListing.created_at ? new Date(jobListing.created_at).toLocaleDateString() : 'N/A'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Benefits & Perks Card */}
                        {(jobListing.benefits || jobListing.company_description || jobListing.final_words) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-xl">Benefits & Company Culture</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    {jobListing.company_description && (
                                        <div>
                                            <h3 className="text-lg font-medium">About This Role at Our Company</h3>
                                            <div className="prose prose-sm mt-2 max-w-none">
                                                <SafeHtml content={jobListing.company_description} />
                                            </div>
                                        </div>
                                    )}

                                    {jobListing.company_description && jobListing.benefits && <Separator />}

                                    {jobListing.benefits && (
                                        <div>
                                            <h3 className="text-lg font-medium">Benefits & Perks</h3>
                                            <div className="prose prose-sm mt-2 max-w-none">
                                                <SafeHtml content={jobListing.benefits} />
                                            </div>
                                        </div>
                                    )}

                                    {(jobListing.company_description || jobListing.benefits) && jobListing.final_words && <Separator />}

                                    {jobListing.final_words && (
                                        <div>
                                            <h3 className="text-lg font-medium">Final Words</h3>
                                            <div className="prose prose-sm mt-2 max-w-none">
                                                <SafeHtml content={jobListing.final_words} />
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Application Process Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl">Application Process</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">How to Apply</h3>
                                    <div className="mt-1">{formatApplicationMethod()}</div>
                                </div>

                                {jobListing.application_documents && (
                                    <>
                                        <Separator />
                                        <div>
                                            <h3 className="text-lg font-medium mb-4">Required Documents</h3>
                                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-sm font-medium">CV / Resume</span>
                                                    <Badge className={formatDocumentRequirement(jobListing.application_documents.cv).color}>
                                                        {formatDocumentRequirement(jobListing.application_documents.cv).text}
                                                    </Badge>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-sm font-medium">Cover Letter</span>
                                                    <Badge className={formatDocumentRequirement(jobListing.application_documents.cover_letter).color}>
                                                        {formatDocumentRequirement(jobListing.application_documents.cover_letter).text}
                                                    </Badge>
                                                </div>
                                            </div>
                                        </div>
                                    </>
                                )}

                                {jobListing.screening_questions && jobListing.screening_questions.length > 0 && (
                                    <>
                                        <Separator />
                                        <div>
                                            <h3 className="text-lg font-medium mb-4">Screening Questions</h3>
                                            <p className="text-sm text-gray-600 mb-4">
                                                You will be asked to answer these questions during the application process:
                                            </p>
                                            <div className="space-y-3">
                                                {jobListing.screening_questions.map((question, index) => (
                                                    <div key={question.id || index} className="border border-gray-200 rounded-lg p-3">
                                                        <div className="flex items-start justify-between">
                                                            <p className="text-sm font-medium flex-1">{question.text}</p>
                                                            <Badge 
                                                                variant="outline" 
                                                                className={`ml-2 text-xs ${
                                                                    question.requirement === 'required' 
                                                                        ? 'border-red-300 text-red-700' 
                                                                        : question.requirement === 'knockout'
                                                                        ? 'border-orange-300 text-orange-700'
                                                                        : 'border-gray-300 text-gray-600'
                                                                }`}
                                                            >
                                                                {question.requirement}
                                                            </Badge>
                                                        </div>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            Answer type: {question.answerType.replace(/[-_]/g, ' ')}
                                                        </p>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        {/* Application Summary */}
                        <Card className="bg-blue-50">
                            <CardHeader>
                                <CardTitle className="text-lg">Application Summary</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="flex items-center justify-between text-sm">
                                    <span>Workload:</span>
                                    <span className="font-medium">{formatWorkload()}</span>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span>Work Style:</span>
                                    <span className="font-medium">{formatWorkplace(jobListing.workplace || null)}</span>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span>Employment:</span>
                                    <span className="font-medium">
                                        {jobListing.employment_type?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) || 'Not specified'}
                                    </span>
                                </div>
                                {jobListing.screening_questions && jobListing.screening_questions.length > 0 && (
                                    <div className="flex items-center justify-between text-sm">
                                        <span>Screening Questions:</span>
                                        <span className="font-medium">{jobListing.screening_questions.length}</span>
                                    </div>
                                )}
                                <Separator />
                                <div className="text-center">
                                    <div className="text-sm text-gray-600 mb-2">Ready to apply?</div>
                                    {formatApplicationMethod()}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Company Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {jobListing.company.logo && (
                                    <div className="mb-4">
                                        <img
                                            src={jobListing.company.logo}
                                            alt={`${jobListing.company.name} Logo`}
                                            className="h-32 w-32 object-contain"
                                        />
                                    </div>
                                )}

                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Company Name</h3>
                                    <p className="mt-1 font-medium">{jobListing.company.name}</p>
                                </div>

                                {jobListing.company.url && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Website</h3>
                                        <a
                                            href={
                                                jobListing.company.url.startsWith('http')
                                                    ? jobListing.company.url
                                                    : `https://${jobListing.company.url}`
                                            }
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="mt-1 block text-blue-600 hover:underline"
                                        >
                                            {jobListing.company.url}
                                        </a>
                                    </div>
                                )}

                                {(jobListing.company.address || jobListing.company.city) && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Location</h3>
                                        <p className="mt-1">
                                            {[jobListing.company.address, jobListing.company.postcode, jobListing.company.city]
                                                .filter(Boolean)
                                                .join(', ')}
                                        </p>
                                    </div>
                                )}

                                {jobListing.company.size && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Company Size</h3>
                                        <p className="mt-1">{jobListing.company.size}</p>
                                    </div>
                                )}

                                {jobListing.company.type && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Company Type</h3>
                                        <p className="mt-1">{jobListing.company.type}</p>
                                    </div>
                                )}

                                {jobListing.company.description_english && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">About the Company</h3>
                                        <p className="mt-1 text-sm">{jobListing.company.description_english}</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card className="bg-red-50">
                            <CardContent className="pt-6">
                                <p className="mb-4 text-sm text-gray-700">Danger Zone</p>
                                <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                        <Button variant="destructive" className="w-full">
                                            Delete Job Listing
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                This action cannot be undone. This will permanently delete this job listing.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction onClick={handleDelete} className="bg-red-500 hover:bg-red-600">
                                                Delete
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
