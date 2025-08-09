import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { SafeHtml } from '@/components/ui/safe-html';
import { useAppearance } from '@/hooks/use-appearance';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns';
import { ArrowLeft, Briefcase, CalendarDays, Clock, DollarSign, Download, Edit, ExternalLink, Gauge, Mail, MapPin, MessageSquare, Share } from 'lucide-react';

interface JobListing {
    id: number;
    title: string;
    description: string;
    status: string;
    workplace: string;
    employment_type: string;
    city: string;
    created_at: string;
    updated_at: string;
    salary_min: number | null;
    salary_max: number | null;
    salary_type: string | null;
    applications_count: number;
    experience_level: string | null;
    requirements: string | null;
    benefits: string | null;
    company_description: string | null;
    final_words: string | null;
    skills: string | null;
    workload_min: number | null;
    workload_max: number | null;
    seniority_level: string | null;
    application_process: string | null;
    application_email: string | null;
    application_url: string | null;
    application_documents: { cv: string; cover_letter: string; } | null;
    screening_questions: Array<{id: string; text: string; requirement: string; answerType: string; choices?: string[];}> | null;
    company: { name: string; };
}

interface Props {
    auth: Auth;
    jobListing: JobListing;
    categoryLabels: string[];
}

export default function JobListingShow({ auth, jobListing, categoryLabels }: Props) {
    const { appearance } = useAppearance();
    const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'published':
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            case 'draft':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            case 'closed':
                return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
            default:
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
        }
    };

    const formatSalary = () => {
        if (!jobListing.salary_min && !jobListing.salary_max) {
            return 'Not specified';
        }

        const formatNumber = (num: number) => {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'CHF', maximumFractionDigits: 0 }).format(num);
        };

        const period = jobListing.salary_type ? ` ${jobListing.salary_type.toLowerCase()}` : '';

        if (jobListing.salary_min && jobListing.salary_max) {
            return `${formatNumber(jobListing.salary_min)} - ${formatNumber(jobListing.salary_max)}${period}`;
        } else if (jobListing.salary_min) {
            return `From ${formatNumber(jobListing.salary_min)}${period}`;
        } else if (jobListing.salary_max) {
            return `Up to ${formatNumber(jobListing.salary_max)}${period}`;
        }
    };

    const formatEmploymentType = (type: string) => {
        return type
            .split('-')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const formatExperienceLevel = (level: string | null, seniorityLevel?: string | null) => {
        if (seniorityLevel) {
            return seniorityLevel
                .split('-')
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
        if (!level) return 'Not specified';
        return level
            .split('-')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const formatWorkload = () => {
        if (!jobListing.workload_min && !jobListing.workload_max) {
            return 'Not specified';
        }
        
        if (jobListing.workload_min && jobListing.workload_max) {
            if (jobListing.workload_min === jobListing.workload_max) {
                return `${jobListing.workload_min}%`;
            }
            return `${jobListing.workload_min}% - ${jobListing.workload_max}%`;
        } else if (jobListing.workload_min) {
            return `From ${jobListing.workload_min}%`;
        } else if (jobListing.workload_max) {
            return `Up to ${jobListing.workload_max}%`;
        }
    };

    const parseSkills = (skillsString: string | null) => {
        if (!skillsString) return [];
        return skillsString.split(',').map(skill => skill.trim()).filter(skill => skill.length > 0);
    };

    const getDocumentRequirementColor = (requirement: string) => {
        switch (requirement.toLowerCase()) {
            case 'required':
                return 'bg-red-500';
            case 'optional':
                return 'bg-yellow-500';
            default:
                return 'bg-gray-500';
        }
    };

    const formatWorkplace = (workplace: string) => {
        return workplace.charAt(0).toUpperCase() + workplace.slice(1);
    };

    return (
        <CompanyLayout>
            <Head title={jobListing.title} />

            <div className="py-8">
                <div className="mx-auto max-w-5xl">
                    <div className="mb-6">
                        <Button variant="outline" asChild>
                            <Link href={route('company.job-listings.index')}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Listings
                            </Link>
                        </Button>
                    </div>

                    <div className="mb-8 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{jobListing.title}</h1>
                            <div className="mt-2 flex flex-wrap items-center gap-2">
                                <Badge variant="outline" className={getStatusColor(jobListing.status)}>
                                    {jobListing.status.charAt(0).toUpperCase() + jobListing.status.slice(1)}
                                </Badge>
                                {categoryLabels.map((category, index) => (
                                    <Badge key={index} variant="outline">{category}</Badge>
                                ))}
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Button asChild variant="outline">
                                <Link href={route('company.job-listings.edit', jobListing.id)}>
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                            <Button variant="outline">
                                <Share className="mr-2 h-4 w-4" />
                                Share
                            </Button>
                            <Button variant="outline">
                                <Download className="mr-2 h-4 w-4" />
                                Export
                            </Button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div className="md:col-span-2 space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Job Description</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="prose dark:prose-invert max-w-none">
                                        <SafeHtml content={jobListing.description} preserveLineBreaks />
                                    </div>
                                </CardContent>
                            </Card>

                            {jobListing.requirements && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Job Requirements</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="prose dark:prose-invert max-w-none">
                                            <SafeHtml content={jobListing.requirements} preserveLineBreaks />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {jobListing.skills && parseSkills(jobListing.skills).length > 0 && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Required Skills</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="flex flex-wrap gap-2">
                                            {parseSkills(jobListing.skills).map((skill, index) => (
                                                <Badge key={index} variant="secondary">
                                                    {skill}
                                                </Badge>
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {jobListing.benefits && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>What We Offer</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="prose dark:prose-invert max-w-none">
                                            <SafeHtml content={jobListing.benefits} preserveLineBreaks />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {jobListing.company_description && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>About This Role at {jobListing.company?.name || 'Our Company'}</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="prose dark:prose-invert max-w-none">
                                            <SafeHtml content={jobListing.company_description} preserveLineBreaks />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {jobListing.final_words && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Additional Information</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="prose dark:prose-invert max-w-none">
                                            <SafeHtml content={jobListing.final_words} preserveLineBreaks />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}
                        </div>

                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Job Details</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex items-start">
                                        <CalendarDays className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                        <div>
                                            <p className="font-medium">Posted</p>
                                            <p className="text-muted-foreground text-sm">{format(new Date(jobListing.created_at), 'MMMM d, yyyy')}</p>
                                        </div>
                                    </div>

                                    <div className="flex items-start">
                                        <MapPin className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                        <div>
                                            <p className="font-medium">Location</p>
                                            <p className="text-muted-foreground text-sm">
                                                {formatWorkplace(jobListing.workplace)}
                                                {jobListing.city && ` • ${jobListing.city}`}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex items-start">
                                        <Briefcase className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                        <div>
                                            <p className="font-medium">Employment</p>
                                            <p className="text-muted-foreground text-sm">
                                                {jobListing.employment_type ? formatEmploymentType(jobListing.employment_type) : 'Not specified'}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex items-start">
                                        <Clock className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                        <div>
                                            <p className="font-medium">Experience</p>
                                            <p className="text-muted-foreground text-sm">{formatExperienceLevel(jobListing.experience_level, jobListing.seniority_level)}</p>
                                        </div>
                                    </div>

                                    {(jobListing.workload_min || jobListing.workload_max) && (
                                        <div className="flex items-start">
                                            <Gauge className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                            <div>
                                                <p className="font-medium">Workload</p>
                                                <p className="text-muted-foreground text-sm">{formatWorkload()}</p>
                                            </div>
                                        </div>
                                    )}

                                    <div className="flex items-start">
                                        <DollarSign className="text-muted-foreground mt-0.5 mr-3 h-5 w-5" />
                                        <div>
                                            <p className="font-medium">Salary</p>
                                            <p className="text-muted-foreground text-sm">{formatSalary()}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {(jobListing.application_process || jobListing.application_email || jobListing.application_url || jobListing.application_documents || jobListing.screening_questions) && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Application Process</CardTitle>
                                        <CardDescription>How candidates can apply for this position</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {(jobListing.application_email || jobListing.application_url) && (
                                            <div>
                                                <p className="font-medium mb-2">Application Method</p>
                                                <div className="space-y-2">
                                                    {jobListing.application_email && (
                                                        <div className="flex items-center space-x-2">
                                                            <Mail className="h-4 w-4 text-muted-foreground" />
                                                            <a href={`mailto:${jobListing.application_email}`} className="text-primary hover:underline text-sm">
                                                                {jobListing.application_email}
                                                            </a>
                                                        </div>
                                                    )}
                                                    {jobListing.application_url && (
                                                        <div className="flex items-center space-x-2">
                                                            <ExternalLink className="h-4 w-4 text-muted-foreground" />
                                                            <a href={jobListing.application_url} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline text-sm">
                                                                Apply via external website
                                                            </a>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        )}

                                        {jobListing.application_documents && (
                                            <div>
                                                <p className="font-medium mb-2">Required Documents</p>
                                                <ul className="space-y-1">
                                                    <li className="flex items-center space-x-2 text-sm">
                                                        <div className={`w-2 h-2 rounded-full ${getDocumentRequirementColor(jobListing.application_documents.cv)}`}></div>
                                                        <span>CV/Resume ({jobListing.application_documents.cv.toLowerCase()})</span>
                                                    </li>
                                                    <li className="flex items-center space-x-2 text-sm">
                                                        <div className={`w-2 h-2 rounded-full ${getDocumentRequirementColor(jobListing.application_documents.cover_letter)}`}></div>
                                                        <span>Cover Letter ({jobListing.application_documents.cover_letter.toLowerCase()})</span>
                                                    </li>
                                                </ul>
                                                <div className="mt-2 text-xs text-muted-foreground">
                                                    <span className="inline-flex items-center space-x-1"><div className="w-2 h-2 rounded-full bg-red-500"></div><span>Required</span></span>
                                                    <span className="inline-flex items-center space-x-1 ml-4"><div className="w-2 h-2 rounded-full bg-yellow-500"></div><span>Optional</span></span>
                                                </div>
                                            </div>
                                        )}

                                        {jobListing.screening_questions && jobListing.screening_questions.length > 0 && (
                                            <div>
                                                <p className="font-medium mb-2">Screening Questions</p>
                                                <div className="flex items-center space-x-2 text-sm text-muted-foreground">
                                                    <MessageSquare className="h-4 w-4" />
                                                    <span>Applicants will answer {jobListing.screening_questions.length} screening question{jobListing.screening_questions.length !== 1 ? 's' : ''}</span>
                                                </div>
                                                <Button variant="outline" size="sm" className="mt-2">
                                                    <MessageSquare className="mr-2 h-4 w-4" />
                                                    View Questions
                                                </Button>
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>
                            )}

                            <Card>
                                <CardHeader>
                                    <CardTitle>Applications</CardTitle>
                                    <CardDescription>{jobListing.applications_count} applications received</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <Button className="w-full" asChild>
                                        <Link href={`/company/job-listings/${jobListing.id}/applications`}>
                                            <MessageSquare className="mr-2 h-4 w-4" />
                                            View Applications
                                        </Link>
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
