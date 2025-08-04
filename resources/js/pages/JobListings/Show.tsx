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
import { Separator } from '@/components/ui/separator';
import CompanyLayout from '@/layouts/company/CompanyLayout';
import { Head, Link, router } from '@inertiajs/react';
import { SafeHtml } from '@/components/ui/safe-html';

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
    location: string | null;
    salary_min: number | null;
    salary_max: number | null;
    salary_type: string | null;
    employment_type: string | null;
    experience_level: string | null;
    application_process: string;
    application_email: string | null;
    application_url: string | null;
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
                        <Card>
                            <CardHeader className="flex flex-row items-start justify-between pb-3">
                                <div>
                                    <CardTitle className="text-xl">Job Details</CardTitle>
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
                                        <h3 className="text-sm font-medium text-gray-500">Location</h3>
                                        <p className="mt-1">{jobListing.location || 'Not specified'}</p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Salary</h3>
                                        <p className="mt-1">{formatSalary()}</p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Employment Type</h3>
                                        <p className="mt-1">
                                            {jobListing.employment_type?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) ||
                                                'Not specified'}
                                        </p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Experience Level</h3>
                                        <p className="mt-1">
                                            {jobListing.experience_level?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) ||
                                                'Not specified'}
                                        </p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Application Method</h3>
                                        <div className="mt-1">{formatApplicationMethod()}</div>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Date Posted</h3>
                                        <p className="mt-1">{jobListing.created_at ? new Date(jobListing.created_at).toLocaleDateString() : 'N/A'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
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
