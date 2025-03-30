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
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company/CompanyLayout';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { EmploymentType } from '@/types/enums/EmploymentType';
import { ExperienceLevel } from '@/types/enums/ExperienceLevel';
import { JobStatus } from '@/types/enums/JobStatus';
import { SalaryType } from '@/types/enums/SalaryType';
import { PageProps } from '@inertiajs/core';
import { Head, Link, useForm } from '@inertiajs/react';

interface Company {
    id: number;
    name: string;
}

interface JobListing {
    id: number;
    company_id: number;
    title: string;
    description: string;
    address: string | null;
    postcode: string | null;
    city: string | null;
    salary_min: number | null;
    salary_max: number | null;
    salary_type: string | null;
    employment_type: string | null;
    experience_level: string | null;
    application_process: string;
    application_email: string | null;
    application_url: string | null;
    status: string;
    no_salary: boolean;
    created_at: string;
    updated_at: string;
    company: Company;
}

interface EditProps extends PageProps {
    jobListing: JobListing;
    errors: Record<string, string>;
    auth: {
        company: Company | null;
    };
}

export default function Edit({ jobListing, errors, auth }: EditProps) {
    const {
        data,
        setData,
        patch,
        processing,
        delete: destroy,
    } = useForm({
        title: jobListing.title || '',
        description: jobListing.description || '',
        address: jobListing.address || '',
        postcode: jobListing.postcode || '',
        city: jobListing.city || '',
        salary_min: jobListing.salary_min ? String(jobListing.salary_min) : '',
        salary_max: jobListing.salary_max ? String(jobListing.salary_max) : '',
        salary_type: jobListing.salary_type || SalaryType.YEARLY,
        employment_type: jobListing.employment_type || EmploymentType.FULL_TIME,
        experience_level: jobListing.experience_level || ExperienceLevel.MID_LEVEL,
        application_process: (jobListing.application_process as ApplicationProcess) || ApplicationProcess.EMAIL,
        application_email: jobListing.application_email || '',
        application_url: jobListing.application_url || '',
        status: (jobListing.status as JobStatus) || JobStatus.PUBLISHED,
        no_salary: jobListing.no_salary ?? false,
        company_id: auth.company?.id || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Check if company is authenticated
        if (!auth.company) {
            // Redirect to company login if not authenticated
            window.location.href = route('company.login');
            return;
        }

        // Include the company ID from auth
        patch(route('company.job-listings.update', jobListing.id), {
            onSuccess: () => {
                // Redirect to the job listings index on success
                window.location.href = route('company.job-listings.show', jobListing.id);
            },
        });
    };

    const handleDelete = () => {
        // Check if company is authenticated
        if (!auth.company) {
            // Redirect to company login if not authenticated
            window.location.href = route('company.login');
            return;
        }

        destroy(route('company.job-listings.destroy', jobListing.id), {
            onSuccess: () => {
                window.location.href = route('company.job-listings.index');
            },
        });
    };

    const handleApplicationProcessChange = (value: string) => {
        setData('application_process', value as ApplicationProcess);
    };

    return (
        <CompanyLayout>
            <Head title={`Edit ${jobListing.title}`} />

            <div className="py-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-bold">Edit Job Listing</h1>
                    <div className="flex gap-2">
                        <Link href={route('company.job-listings.show', jobListing.id)}>
                            <Button variant="outline">View Listing</Button>
                        </Link>
                        <Link href={route('company.job-listings.index')}>
                            <Button variant="outline">Back to Listings</Button>
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Job Details</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="title">Title</Label>
                                    <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} required />
                                    {errors.title && <p className="text-sm text-red-500">{errors.title}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="description">Description</Label>
                                    <Textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        rows={6}
                                        required
                                    />
                                    {errors.description && <p className="text-sm text-red-500">{errors.description}</p>}
                                </div>

                                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div className="space-y-2">
                                        <Label htmlFor="address">Address</Label>
                                        <Input
                                            id="address"
                                            value={data.address}
                                            onChange={(e) => setData('address', e.target.value)}
                                            placeholder="Street address"
                                        />
                                        {errors.address && <p className="text-sm text-red-500">{errors.address}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="postcode">Postal Code</Label>
                                        <Input
                                            id="postcode"
                                            value={data.postcode}
                                            onChange={(e) => setData('postcode', e.target.value)}
                                            placeholder="Postal/ZIP code"
                                        />
                                        {errors.postcode && <p className="text-sm text-red-500">{errors.postcode}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="city">City</Label>
                                        <Input id="city" value={data.city} onChange={(e) => setData('city', e.target.value)} placeholder="City" />
                                        {errors.city && <p className="text-sm text-red-500">{errors.city}</p>}
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label htmlFor="salary_min">Minimum Salary</Label>
                                        <Input
                                            id="salary_min"
                                            type="number"
                                            value={data.salary_min}
                                            onChange={(e) => setData('salary_min', e.target.value)}
                                        />
                                        {errors.salary_min && <p className="text-sm text-red-500">{errors.salary_min}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="salary_max">Maximum Salary</Label>
                                        <Input
                                            id="salary_max"
                                            type="number"
                                            value={data.salary_max}
                                            onChange={(e) => setData('salary_max', e.target.value)}
                                        />
                                        {errors.salary_max && <p className="text-sm text-red-500">{errors.salary_max}</p>}
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div className="space-y-2">
                                        <Label htmlFor="salary_type">Salary Type</Label>
                                        <Select value={data.salary_type} onValueChange={(value) => setData('salary_type', value as SalaryType)}>
                                            <SelectTrigger id="salary_type">
                                                <SelectValue placeholder="Select salary type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={SalaryType.HOURLY}>Hourly</SelectItem>
                                                <SelectItem value={SalaryType.DAILY}>Daily</SelectItem>
                                                <SelectItem value={SalaryType.MONTHLY}>Monthly</SelectItem>
                                                <SelectItem value={SalaryType.YEARLY}>Yearly</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.salary_type && <p className="text-sm text-red-500">{errors.salary_type}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="employment_type">Employment Type</Label>
                                        <Select
                                            value={data.employment_type}
                                            onValueChange={(value) => setData('employment_type', value as EmploymentType)}
                                        >
                                            <SelectTrigger id="employment_type">
                                                <SelectValue placeholder="Select employment type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={EmploymentType.FULL_TIME}>Full Time</SelectItem>
                                                <SelectItem value={EmploymentType.PART_TIME}>Part Time</SelectItem>
                                                <SelectItem value={EmploymentType.FULL_PART_TIME}>Full/Part Time</SelectItem>
                                                <SelectItem value={EmploymentType.CONTRACT}>Contract</SelectItem>
                                                <SelectItem value={EmploymentType.TEMPORARY}>Temporary</SelectItem>
                                                <SelectItem value={EmploymentType.INTERNSHIP}>Internship</SelectItem>
                                                <SelectItem value={EmploymentType.VOLUNTEER}>Volunteer</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.employment_type && <p className="text-sm text-red-500">{errors.employment_type}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="experience_level">Experience Level</Label>
                                        <Select
                                            value={data.experience_level}
                                            onValueChange={(value) => setData('experience_level', value as ExperienceLevel)}
                                        >
                                            <SelectTrigger id="experience_level">
                                                <SelectValue placeholder="Select experience level" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={ExperienceLevel.ENTRY}>Entry Level</SelectItem>
                                                <SelectItem value={ExperienceLevel.JUNIOR}>Junior</SelectItem>
                                                <SelectItem value={ExperienceLevel.MID_LEVEL}>Mid Level</SelectItem>
                                                <SelectItem value={ExperienceLevel.SENIOR}>Senior</SelectItem>
                                                <SelectItem value={ExperienceLevel.EXECUTIVE}>Executive</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.experience_level && <p className="text-sm text-red-500">{errors.experience_level}</p>}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="application_process">Application Process</Label>
                                    <Select value={data.application_process} onValueChange={handleApplicationProcessChange}>
                                        <SelectTrigger id="application_process">
                                            <SelectValue placeholder="Select application process" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value={ApplicationProcess.EMAIL}>Email</SelectItem>
                                            <SelectItem value={ApplicationProcess.EXTERNAL}>External Website</SelectItem>
                                            <SelectItem value={ApplicationProcess.INTERNAL}>Internal Application Form</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.application_process && <p className="text-sm text-red-500">{errors.application_process}</p>}
                                </div>

                                {data.application_process === ApplicationProcess.EMAIL && (
                                    <div className="space-y-2">
                                        <Label htmlFor="application_email">Application Email</Label>
                                        <Input
                                            id="application_email"
                                            type="email"
                                            value={data.application_email}
                                            onChange={(e) => setData('application_email', e.target.value)}
                                        />
                                        {errors.application_email && <p className="text-sm text-red-500">{errors.application_email}</p>}
                                    </div>
                                )}

                                {data.application_process === ApplicationProcess.EXTERNAL && (
                                    <div className="space-y-2">
                                        <Label htmlFor="application_url">Application URL</Label>
                                        <Input
                                            id="application_url"
                                            type="url"
                                            value={data.application_url}
                                            onChange={(e) => setData('application_url', e.target.value)}
                                        />
                                        {errors.application_url && <p className="text-sm text-red-500">{errors.application_url}</p>}
                                    </div>
                                )}

                                <div className="space-y-2">
                                    <Label htmlFor="status">Status</Label>
                                    <Select value={data.status} onValueChange={(value) => setData('status', value as JobStatus)}>
                                        <SelectTrigger id="status">
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value={JobStatus.DRAFT}>Draft</SelectItem>
                                            <SelectItem value={JobStatus.PUBLISHED}>Published</SelectItem>
                                            <SelectItem value={JobStatus.CLOSED}>Closed</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.status && <p className="text-sm text-red-500">{errors.status}</p>}
                                </div>
                            </CardContent>
                            <CardFooter className="flex justify-between">
                                <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                        <Button variant="destructive" type="button">
                                            Delete Listing
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Delete Job Listing</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                Are you sure you want to delete this job listing? This action cannot be undone.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction onClick={handleDelete}>Delete</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                                <Button type="submit" disabled={processing}>
                                    Save Changes
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </form>
            </div>
        </CompanyLayout>
    );
}
