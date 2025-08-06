import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useAppearance } from '@/hooks/use-appearance';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns';
import { ArrowUpDown, Eye, Pencil, PlusCircle, Search, Trash2 } from 'lucide-react';

interface JobListing {
    id: number;
    title: string;
    status: string;
    workplace: string;
    city: string;
    created_at: string;
    applications_count?: number;
}

interface Props {
    auth: Auth;
    jobListings: {
        data: JobListing[];
        links: any;
        meta: {
            current_page: number;
            last_page: number;
            from: number;
            to: number;
            total: number;
        };
    };
}

export default function JobListingIndex({ auth, jobListings }: Props) {
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

    const getWorkplaceColor = (workplace: string) => {
        switch (workplace) {
            case 'remote':
                return 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400';
            case 'hybrid':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
            case 'onsite':
                return 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
        }
    };

    return (
        <CompanyLayout>
            <Head title="Job Listings" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl">
                    <div className="mb-8 flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Job Listings</h1>
                            <p className="text-muted-foreground mt-2">Manage your job postings and track applications</p>
                        </div>
                        <Button asChild>
                            <Link href={route('company.job-listings.create')}>
                                <PlusCircle className="mr-2 h-4 w-4" />
                                Create New Job
                            </Link>
                        </Button>
                    </div>

                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Your Job Listings</CardTitle>
                                <div className="relative w-64">
                                    <Search className="text-muted-foreground absolute top-2.5 left-2 h-4 w-4" />
                                    <Input placeholder="Search jobs..." className="pl-8" />
                                </div>
                            </div>
                            <CardDescription>
                                You have {jobListings.meta?.total || 0} job {(jobListings.meta?.total || 0) === 1 ? 'listing' : 'listings'} in total
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {(jobListings.data || []).length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-12">
                                    <p className="text-muted-foreground mb-4 text-lg font-medium">You don't have any job listings yet</p>
                                    <Button asChild>
                                        <Link href={route('company.job-listings.create')}>
                                            <PlusCircle className="mr-2 h-4 w-4" />
                                            Create Your First Job
                                        </Link>
                                    </Button>
                                </div>
                            ) : (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Title</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead>Location</TableHead>
                                                <TableHead>
                                                    <div className="flex items-center">
                                                        Posted
                                                        <ArrowUpDown className="ml-1 h-4 w-4" />
                                                    </div>
                                                </TableHead>
                                                <TableHead>Applications</TableHead>
                                                <TableHead className="text-right">Actions</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {(jobListings.data || []).map((job) => (
                                                <TableRow key={job.id}>
                                                    <TableCell className="font-medium">{job.title}</TableCell>
                                                    <TableCell>
                                                        <Badge variant="outline" className={getStatusColor(job.status)}>
                                                            {job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell>
                                                        <div className="flex flex-col">
                                                            <Badge variant="outline" className={getWorkplaceColor(job.workplace)}>
                                                                {job.workplace.charAt(0).toUpperCase() + job.workplace.slice(1)}
                                                            </Badge>
                                                            {job.city && <span className="text-muted-foreground mt-1 text-sm">{job.city}</span>}
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>{format(new Date(job.created_at), 'MMM d, yyyy')}</TableCell>
                                                    <TableCell>{job.applications_count || 0}</TableCell>
                                                    <TableCell className="text-right">
                                                        <DropdownMenu>
                                                            <DropdownMenuTrigger asChild>
                                                                <Button variant="ghost" size="icon">
                                                                    <span className="sr-only">Open menu</span>
                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        width="16"
                                                                        height="16"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        strokeWidth="2"
                                                                        strokeLinecap="round"
                                                                        strokeLinejoin="round"
                                                                        className="lucide lucide-more-vertical"
                                                                    >
                                                                        <circle cx="12" cy="12" r="1" />
                                                                        <circle cx="12" cy="5" r="1" />
                                                                        <circle cx="12" cy="19" r="1" />
                                                                    </svg>
                                                                </Button>
                                                            </DropdownMenuTrigger>
                                                            <DropdownMenuContent align="end">
                                                                <DropdownMenuItem asChild>
                                                                    <Link href={route('company.job-listings.show', job.id)}>
                                                                        <Eye className="mr-2 h-4 w-4" />
                                                                        View
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem asChild>
                                                                    <Link href={route('company.job-listings.edit', job.id)}>
                                                                        <Pencil className="mr-2 h-4 w-4" />
                                                                        Edit
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem
                                                                    className="text-red-600 focus:text-red-600 dark:text-red-400 dark:focus:text-red-400"
                                                                    onClick={() => {
                                                                        if (confirm('Are you sure you want to delete this job?')) {
                                                                            // Delete logic - will be implemented later
                                                                        }
                                                                    }}
                                                                >
                                                                    <Trash2 className="mr-2 h-4 w-4" />
                                                                    Delete
                                                                </DropdownMenuItem>
                                                            </DropdownMenuContent>
                                                        </DropdownMenu>
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            )}

                            {/* Pagination would go here */}
                            {jobListings.meta?.last_page > 1 && (
                                <div className="mt-6 flex items-center justify-between">
                                    <p className="text-muted-foreground text-sm">
                                        Showing {jobListings.meta?.from} to {jobListings.meta?.to} of {jobListings.meta?.total} results
                                    </p>
                                    <div className="flex items-center gap-2">{/* Pagination controls would go here */}</div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </CompanyLayout>
    );
}
