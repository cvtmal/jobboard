import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { Head, Link, router } from '@inertiajs/react';
import { SwissCanton, swissCantonData } from '@/types/enums/SwissCanton';
import { useEffect, useState } from 'react';
import { useAppearance } from '@/hooks/use-appearance';
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
    city: string | null;
    primary_canton_code: string | null;
    has_multiple_locations: boolean;
    allows_remote: boolean;
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
    auth: {
        user: any;
    };
    locale: string;
}

export default function Show({ jobListing, auth, locale }: ShowProps) {
    const { appearance, updateAppearance } = useAppearance();
    const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

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
                    <a 
                        href={`mailto:${jobListing.application_email}`} 
                        className="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        {jobListing.application_email}
                    </a>
                ) : (
                    <p className="text-gray-700 dark:text-gray-300">Email (not specified)</p>
                );
            case 'external':
                return jobListing.application_url ? (
                    <a 
                        href={jobListing.application_url} 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        className="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        Apply on company website
                    </a>
                ) : (
                    <p className="text-gray-700 dark:text-gray-300">External URL (not specified)</p>
                );
            case 'internal':
                return <p className="text-gray-700 dark:text-gray-300">Apply through our platform</p>;
            default:
                return <p className="text-gray-700 dark:text-gray-300">Not specified</p>;
        }
    };

    // Format location information
    const formatLocation = () => {
        const parts = [];
        
        if (jobListing.city) {
            parts.push(jobListing.city);
        }
        
        if (jobListing.primary_canton_code && swissCantonData[jobListing.primary_canton_code as SwissCanton]) {
            parts.push(swissCantonData[jobListing.primary_canton_code as SwissCanton].label);
        }
        
        if (parts.length === 0) {
            return 'Location not specified';
        }
        
        let location = parts.join(', ');
        
        if (jobListing.has_multiple_locations) {
            location += ' + additional locations';
        }
        
        return location;
    };

    return (
        <div className={isDarkMode ? 'dark' : ''}>
            <Head title={jobListing.title} />
            
            <div className="min-h-screen bg-gray-50 dark:bg-gray-950">
                {/* Subtle grid pattern background */}
                <div className="pointer-events-none fixed inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px] dark:bg-[linear-gradient(to_right,#ffffff12_1px,transparent_1px),linear-gradient(to_bottom,#ffffff12_1px,transparent_1px)]" />
                
                <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    {/* Header with navigation */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between">
                            <Link 
                                href={route('home')} 
                                className="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <svg 
                                    xmlns="http://www.w3.org/2000/svg" 
                                    width="24" 
                                    height="24" 
                                    viewBox="0 0 24 24" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    strokeWidth="2" 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    className="mr-2 h-4 w-4"
                                >
                                    <path d="m15 18-6-6 6-6" />
                                </svg>
                                Back to jobs
                            </Link>
                            
                            <div className="flex space-x-4 items-center">
                                {/* Theme toggle button */}
                                <button
                                    onClick={() => updateAppearance(isDarkMode ? 'light' : 'dark')}
                                    className={`flex h-8 w-8 items-center justify-center rounded-full ${isDarkMode ? 'bg-gray-800 text-yellow-300 hover:bg-gray-700' : 'bg-gray-200 text-indigo-700 hover:bg-gray-300'}`}
                                    aria-label="Toggle dark mode"
                                >
                                    {isDarkMode ? (
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={2}
                                            stroke="currentColor"
                                            className="h-4 w-4"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                            />
                                        </svg>
                                    ) : (
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={2}
                                            stroke="currentColor"
                                            className="h-4 w-4"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                                            />
                                        </svg>
                                    )}
                                </button>

                                {!auth.user && (
                                    <>
                                        <Link href={route('login')}>
                                            <Button variant="outline">Log in</Button>
                                        </Link>
                                        <Link href={route('register')}>
                                            <Button>Sign up</Button>
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                    
                    <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        {/* Main job content */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Job header */}
                            <div className="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-800">
                                <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div>
                                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                            {jobListing.title}
                                        </h1>
                                        <div className="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    strokeWidth={1.5}
                                                    stroke="currentColor"
                                                    className="mr-1 h-4 w-4"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                    />
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"
                                                    />
                                                </svg>
                                                {jobListing.employment_type?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) || 'Not specified'}
                                            </span>
                                            <span className="mx-2">•</span>
                                            <span className="flex items-center">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    strokeWidth={1.5}
                                                    stroke="currentColor"
                                                    className="mr-1 h-4 w-4"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                                    />
                                                </svg>
                                                {formatLocation()}
                                            </span>
                                            {jobListing.allows_remote && (
                                                <>
                                                    <span className="mx-2">•</span>
                                                    <span className="flex items-center">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            strokeWidth={1.5}
                                                            stroke="currentColor"
                                                            className="mr-1 h-4 w-4"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"
                                                            />
                                                        </svg>
                                                        Remote possible
                                                    </span>
                                                </>
                                            )}
                                            <span className="mx-2">•</span>
                                            <span className="flex items-center">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    strokeWidth={1.5}
                                                    stroke="currentColor"
                                                    className="mr-1 h-4 w-4"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                                    />
                                                </svg>
                                                Posted {new Date(jobListing.created_at).toLocaleDateString()}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="flex-shrink-0">
                                        <Button size="lg" className="text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500">
                                            Apply Now
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            
                            {/* Job description */}
                            <Card className="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                <CardHeader>
                                    <CardTitle className="text-gray-900 dark:text-white">Job Description</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-gray-900 dark:text-white prose prose-sm max-w-none dark:prose-invert">
                                        <SafeHtml content={jobListing.description} />
                                    </div>
                                </CardContent>
                            </Card>
                            
                            {/* Job details */}
                            <Card className="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                <CardHeader>
                                    <CardTitle className="text-gray-900 dark:text-white">Job Details</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Type</h3>
                                            <p className="mt-1 text-gray-700 dark:text-gray-300">
                                                {jobListing.employment_type?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) || 'Not specified'}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Experience Level</h3>
                                            <p className="mt-1 text-gray-700 dark:text-gray-300">
                                                {jobListing.experience_level?.replace('_', ' ')?.replace(/\b\w/g, (c) => c.toUpperCase()) || 'Not specified'}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Location</h3>
                                            <p className="mt-1 text-gray-700 dark:text-gray-300">
                                                {formatLocation()}
                                                {jobListing.allows_remote && (
                                                    <span className="ml-2 inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                        Remote possible
                                                    </span>
                                                )}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Salary</h3>
                                            <p className="mt-1 text-gray-700 dark:text-gray-300">{formatSalary()}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                            
                            {/* Application method */}
                            <Card className="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                <CardHeader>
                                    <CardTitle className="text-gray-900 dark:text-white">How to Apply</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div>
                                        {formatApplicationMethod()}
                                    </div>
                                </CardContent>
                                <CardFooter className="flex justify-end">
                                    <Button size="lg" className="text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500">
                                        Apply Now
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                        
                        {/* Company sidebar */}
                        <div className="space-y-6">
                            <Card className="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                <CardHeader>
                                    <CardTitle className="text-gray-900 dark:text-white">About the Company</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex items-center">
                                        {jobListing.company.logo ? (
                                            <img 
                                                src={jobListing.company.logo} 
                                                alt={`${jobListing.company.name} logo`} 
                                                className="h-16 w-16 rounded-lg object-contain border border-gray-200 dark:border-gray-800"
                                            />
                                        ) : (
                                            <div className="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    fill="none" 
                                                    viewBox="0 0 24 24" 
                                                    strokeWidth={1.5} 
                                                    stroke="currentColor" 
                                                    className="h-8 w-8"
                                                >
                                                    <path 
                                                        strokeLinecap="round" 
                                                        strokeLinejoin="round" 
                                                        d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" 
                                                    />
                                                </svg>
                                            </div>
                                        )}
                                        <div className="ml-4">
                                            <h3 className="text-lg font-medium text-gray-900 dark:text-white">{jobListing.company.name}</h3>
                                            {jobListing.company.city && (
                                                <p className="text-sm text-gray-700 dark:text-gray-300">
                                                    {jobListing.company.city}
                                                    {jobListing.company.postcode && `, ${jobListing.company.postcode}`}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                    
                                    <Separator />
                                    
                                    <div>
                                        <h4 className="text-sm font-medium text-gray-500 dark:text-gray-400">Company Details</h4>
                                        <div className="mt-3 space-y-3">
                                            {jobListing.company.size && (
                                                <div className="flex items-start">
                                                    <svg 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24" 
                                                        strokeWidth={1.5} 
                                                        stroke="currentColor" 
                                                        className="mt-0.5 h-4 w-4 text-gray-400 dark:text-gray-500"
                                                    >
                                                        <path 
                                                            strokeLinecap="round" 
                                                            strokeLinejoin="round" 
                                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" 
                                                        />
                                                    </svg>
                                                    <span className="ml-2 text-gray-700 dark:text-gray-300">{jobListing.company.size} employees</span>
                                                </div>
                                            )}
                                            
                                            {jobListing.company.type && (
                                                <div className="flex items-start">
                                                    <svg 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24" 
                                                        strokeWidth={1.5} 
                                                        stroke="currentColor" 
                                                        className="mt-0.5 h-4 w-4 text-gray-400 dark:text-gray-500"
                                                    >
                                                        <path 
                                                            strokeLinecap="round" 
                                                            strokeLinejoin="round" 
                                                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m-4.773-4.227-1.591 1.591M5.25 12H3m4.227 4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" 
                                                        />
                                                    </svg>
                                                    <span className="ml-2 text-gray-700 dark:text-gray-300">{jobListing.company.type}</span>
                                                </div>
                                            )}
                                            
                                            {jobListing.company.url && (
                                                <div className="flex items-start">
                                                    <svg 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24" 
                                                        strokeWidth={1.5} 
                                                        stroke="currentColor" 
                                                        className="mt-0.5 h-4 w-4 text-gray-400 dark:text-gray-500"
                                                    >
                                                        <path 
                                                            strokeLinecap="round" 
                                                            strokeLinejoin="round" 
                                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"
                                                        />
                                                    </svg>
                                                    <a 
                                                        href={jobListing.company.url.startsWith('http') ? jobListing.company.url : `https://${jobListing.company.url}`} 
                                                        target="_blank" 
                                                        rel="noopener noreferrer" 
                                                        className="ml-2 text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                    >
                                                        Company Website
                                                    </a>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    
                                    {jobListing.company.description_english || jobListing.company.description_german || 
                                     jobListing.company.description_french || jobListing.company.description_italian ? (
                                        <>
                                            <Separator />
                                            
                                            <div>
                                                <h4 className="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">About</h4>
                                                <div className="prose prose-sm max-w-none dark:prose-invert">
                                                    <SafeHtml 
                                                        content={
                                                            jobListing.company.description_english || 
                                                            jobListing.company.description_german || 
                                                            jobListing.company.description_french || 
                                                            jobListing.company.description_italian || ''
                                                        }
                                                    />
                                                </div>
                                            </div>
                                        </>
                                    ) : null}
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
