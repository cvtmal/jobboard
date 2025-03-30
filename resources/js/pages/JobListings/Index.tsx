import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Pagination } from '@/components/ui/pagination';
import CompanyLayout from '@/layouts/company/CompanyLayout';
import { Head, Link } from '@inertiajs/react';

interface JobListing {
    id: number;
    title: string;
    description: string;
    location: string | null;
    status: string;
    created_at: string;
    employment_type: string | null;
    experience_level: string | null;
}

interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

interface JobListingsProps {
    jobListings: {
        data: JobListing[];
        links: PaginationLinks;
        current_page: number;
        last_page: number;
    };
}

export default function Index({ jobListings }: JobListingsProps) {
    return (
        <CompanyLayout>
            <Head title="Job Listings" />

            <div className="py-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Job Listings</h1>
                    <Link href={route('company.job-listings.create')}>
                        <Button>Create Job Listing</Button>
                    </Link>
                </div>

                {jobListings.data.length === 0 ? (
                    <Card>
                        <CardContent className="pt-6">
                            <div className="py-12 text-center">
                                <h3 className="mb-2 text-lg font-medium text-gray-500">No job listings yet</h3>
                                <p className="mb-6 text-sm text-gray-400">Create your first job listing to get started</p>
                                <Link href={route('company.job-listings.create')}>
                                    <Button>Create Job Listing</Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4">
                        {jobListings.data.map((jobListing) => (
                            <Card key={jobListing.id} className="overflow-hidden">
                                <CardHeader className="pb-3">
                                    <div className="flex items-start justify-between">
                                        <div>
                                            <CardTitle className="text-xl">{jobListing.title}</CardTitle>
                                            <CardDescription>{jobListing.location}</CardDescription>
                                        </div>
                                        <Badge>{jobListing.status}</Badge>
                                    </div>
                                </CardHeader>
                                <CardContent className="pb-3">
                                    <p className="line-clamp-2 text-sm text-gray-600">{jobListing.description}</p>
                                </CardContent>
                                <CardFooter className="flex justify-between border-t pt-3">
                                    <div className="flex space-x-2 text-sm text-gray-500">
                                        <span>{jobListing.employment_type}</span>
                                        <span>•</span>
                                        <span>{jobListing.experience_level}</span>
                                    </div>
                                    <div className="flex space-x-2">
                                        <Link href={route('company.job-listings.show', jobListing.id)}>
                                            <Button variant="ghost" size="sm">
                                                View
                                            </Button>
                                        </Link>
                                        <Link href={route('company.job-listings.edit', jobListing.id)}>
                                            <Button variant="outline" size="sm">
                                                Edit
                                            </Button>
                                        </Link>
                                    </div>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}

                {jobListings.data.length > 0 && (
                    <div className="mt-6 flex justify-center">
                        <Pagination currentPage={jobListings.current_page} lastPage={jobListings.last_page} links={jobListings.links} />
                    </div>
                )}
            </div>
        </CompanyLayout>
    );
}
