import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Pagination } from '@/components/ui/pagination';
import CompanyLayout from '@/layouts/company/CompanyLayout';

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
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold tracking-tight">Job Listings</h1>
          <Link href={route('company.job-listings.create')}>
            <Button>Create Job Listing</Button>
          </Link>
        </div>

        {jobListings.data.length === 0 ? (
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12">
                <h3 className="text-lg font-medium text-gray-500 mb-2">No job listings yet</h3>
                <p className="text-sm text-gray-400 mb-6">Create your first job listing to get started</p>
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
                  <div className="flex justify-between items-start">
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
                <CardFooter className="border-t pt-3 flex justify-between">
                  <div className="flex space-x-2 text-sm text-gray-500">
                    <span>{jobListing.employment_type}</span>
                    <span>•</span>
                    <span>{jobListing.experience_level}</span>
                  </div>
                  <div className="flex space-x-2">
                    <Link href={route('company.job-listings.show', jobListing.id)}>
                      <Button variant="ghost" size="sm">View</Button>
                    </Link>
                    <Link href={route('company.job-listings.edit', jobListing.id)}>
                      <Button variant="outline" size="sm">Edit</Button>
                    </Link>
                  </div>
                </CardFooter>
              </Card>
            ))}
          </div>
        )}

        {jobListings.data.length > 0 && (
          <div className="mt-6 flex justify-center">
            <Pagination
              currentPage={jobListings.current_page}
              lastPage={jobListings.last_page}
              links={jobListings.links}
            />
          </div>
        )}
      </div>
    </CompanyLayout>
  );
}
