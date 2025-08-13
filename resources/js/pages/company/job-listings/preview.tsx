import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { Head, router } from '@inertiajs/react';
import { 
    ArrowLeft, 
    ArrowRight, 
    Building2, 
    Calendar, 
    DollarSign, 
    Eye, 
    MapPin, 
    Users, 
    Briefcase,
    Clock,
    Edit3
} from 'lucide-react';

interface JobListing {
    id: number;
    title: string;
    description: string;
    workplace: string;
    city?: string;
    employment_type: {
        value: string;
        label: string;
    };
    experience_level: {
        value: string;
        label: string;
    };
    salary_min?: number;
    salary_max?: number;
    salary_type?: {
        value: string;
        label: string;
    } | null;
    workload_min: number;
    workload_max: number;
    application_process: ApplicationProcess;
    application_email?: string;
    application_url?: string;
    contact_person?: string;
    status: {
        value: string;
        label: string;
    };
    company: {
        id: number;
        name: string;
        logo_url?: string;
        banner_url?: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    auth: Auth;
    jobListing: JobListing;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings / Preview',
        href: '/company/job-listings',
    },
];

export default function PreviewJobListing({ auth, jobListing }: Props) {
    const handleEditJobListing = () => {
        router.visit(route('company.job-listings.edit', jobListing.id));
    };

    const handleContinueToPackageSelection = () => {
        router.visit(route('company.job-listings.package-selection', jobListing.id));
    };

    const handleBack = () => {
        router.visit(route('company.job-listings.index'));
    };

    const formatSalary = () => {
        if (!jobListing.salary_min && !jobListing.salary_max) {
            return 'Salary not specified';
        }

        const formatAmount = (amount: number) => {
            return new Intl.NumberFormat('en-CH', {
                style: 'currency',
                currency: 'CHF',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount);
        };

        const salaryPeriod = jobListing.salary_type?.label || '';
        
        if (jobListing.salary_min && jobListing.salary_max) {
            return `${formatAmount(jobListing.salary_min)} - ${formatAmount(jobListing.salary_max)} ${salaryPeriod}`.trim();
        } else if (jobListing.salary_min) {
            return `From ${formatAmount(jobListing.salary_min)} ${salaryPeriod}`.trim();
        } else if (jobListing.salary_max) {
            return `Up to ${formatAmount(jobListing.salary_max)} ${salaryPeriod}`.trim();
        }

        return '';
    };

    const formatWorkload = () => {
        if (jobListing.workload_min === jobListing.workload_max) {
            return `${jobListing.workload_min}%`;
        }
        return `${jobListing.workload_min}% - ${jobListing.workload_max}%`;
    };

    const formatLocation = () => {
        if (jobListing.workplace === 'remote') {
            return 'Remote';
        } else if (jobListing.workplace === 'hybrid') {
            return `Hybrid${jobListing.city ? ` (${jobListing.city})` : ''}`;
        } else {
            return jobListing.city || 'On-site';
        }
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title={`Preview: ${jobListing.title}`} />

            <div className="py-6">
                <div className="mx-auto max-w-4xl">
                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between">
                            <div>
                                <div className="flex items-center gap-3 mb-4">
                                    <Eye className="h-6 w-6 text-blue-600" />
                                    <h1 className="text-2xl font-bold">Preview Job Listing</h1>
                                </div>
                                <p className="text-muted-foreground">
                                    Review your job listing before selecting a package and publishing
                                </p>
                            </div>
                            <div className="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    onClick={handleEditJobListing}
                                    className="gap-2"
                                >
                                    <Edit3 className="h-4 w-4" />
                                    Edit
                                </Button>
                            </div>
                        </div>
                    </div>

                    {/* Job Listing Preview */}
                    <Card className="mb-8">
                        <CardHeader>
                            <div className="flex items-start justify-between">
                                <div className="flex items-start gap-4">
                                    {jobListing.company.logo_url && (
                                        <img
                                            src={jobListing.company.logo_url}
                                            alt={`${jobListing.company.name} logo`}
                                            className="h-16 w-16 rounded-lg object-cover border"
                                        />
                                    )}
                                    <div>
                                        <CardTitle className="text-xl mb-2">{jobListing.title}</CardTitle>
                                        <div className="flex items-center gap-2 text-muted-foreground mb-2">
                                            <Building2 className="h-4 w-4" />
                                            <span>{jobListing.company.name}</span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                            <div className="flex items-center gap-1">
                                                <MapPin className="h-4 w-4" />
                                                <span>{formatLocation()}</span>
                                            </div>
                                            <div className="flex items-center gap-1">
                                                <Briefcase className="h-4 w-4" />
                                                <span>{jobListing.employment_type.label}</span>
                                            </div>
                                            <div className="flex items-center gap-1">
                                                <Clock className="h-4 w-4" />
                                                <span>{formatWorkload()}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <div className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {jobListing.status.label}
                                    </div>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Key Details */}
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <DollarSign className="h-5 w-5 text-green-600" />
                                    <div>
                                        <p className="text-sm font-medium">Salary</p>
                                        <p className="text-sm text-muted-foreground">{formatSalary()}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <Users className="h-5 w-5 text-blue-600" />
                                    <div>
                                        <p className="text-sm font-medium">Experience Level</p>
                                        <p className="text-sm text-muted-foreground">{jobListing.experience_level.label}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <Calendar className="h-5 w-5 text-purple-600" />
                                    <div>
                                        <p className="text-sm font-medium">Application Method</p>
                                        <p className="text-sm text-muted-foreground">
                                            {jobListing.application_process === ApplicationProcess.EMAIL ? 'Email Application' : 'External Website'}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Job Description */}
                            <div>
                                <h3 className="text-lg font-semibold mb-3">Job Description</h3>
                                <div className="prose max-w-none">
                                    <div 
                                        dangerouslySetInnerHTML={{ 
                                            __html: jobListing.description.replace(/\n/g, '<br />') 
                                        }} 
                                        className="text-sm leading-relaxed whitespace-pre-wrap"
                                    />
                                </div>
                            </div>

                            {/* Application Details */}
                            <div className="border-t pt-6">
                                <h3 className="text-lg font-semibold mb-3">Application Information</h3>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {jobListing.contact_person && (
                                        <div>
                                            <p className="text-sm font-medium text-muted-foreground">Contact Person</p>
                                            <p className="text-sm">{jobListing.contact_person}</p>
                                        </div>
                                    )}
                                    {jobListing.application_process === ApplicationProcess.EMAIL && jobListing.application_email && (
                                        <div>
                                            <p className="text-sm font-medium text-muted-foreground">Application Email</p>
                                            <p className="text-sm">{jobListing.application_email}</p>
                                        </div>
                                    )}
                                    {jobListing.application_process === ApplicationProcess.URL && jobListing.application_url && (
                                        <div>
                                            <p className="text-sm font-medium text-muted-foreground">Application URL</p>
                                            <p className="text-sm break-all">{jobListing.application_url}</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Action Buttons */}
                    <div className="flex items-center justify-between">
                        <Button
                            variant="outline"
                            onClick={handleBack}
                            className="gap-2"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back to Listings
                        </Button>

                        <div className="flex items-center gap-4">
                            <Button
                                variant="outline"
                                onClick={handleEditJobListing}
                                className="gap-2"
                            >
                                <Edit3 className="h-4 w-4" />
                                Edit Job
                            </Button>
                            
                            <Button
                                onClick={handleContinueToPackageSelection}
                                className="gap-2"
                            >
                                Continue to Package Selection
                                <ArrowRight className="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}