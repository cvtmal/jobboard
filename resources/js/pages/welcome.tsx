import { LocationFilter } from '@/components/job-listings';
import { SwissCanton, swissCantonData } from '@/types/enums/SwissCanton';
import { SwissRegion } from '@/types/enums/SwissRegion';
import { SwissSubRegion } from '@/types/enums/SwissSubRegion';
import { filterJobsByCanton, filterJobsByRegion, filterJobsBySubRegion } from '@/utils/simpleFilters';
import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useAppearance } from '@/hooks/use-appearance';

interface WelcomeProps {
    auth: any;
    jobListings: any[];
}

export default function Welcome({ auth, jobListings = [] }: WelcomeProps) {
    const currentYear = new Date().getFullYear();

    // State for location filters
    const [selectedRegions, setSelectedRegions] = useState<SwissRegion[]>([]);
    const [selectedCantons, setSelectedCantons] = useState<SwissCanton[]>([]);
    const [selectedSubRegions, setSelectedSubRegions] = useState<SwissSubRegion[]>([]);

    // Create a mapping of canton codes to regions for filtering
    const cantonToRegion: Record<string, string> = {};

    Object.values(swissCantonData).forEach((canton) => {
        cantonToRegion[canton.id] = canton.region;
    });

    // Handler for region selection changes
    const handleRegionChange = (region: SwissRegion, isSelected: boolean) => {
        setSelectedRegions((prev) => (isSelected ? [...prev, region] : prev.filter((r) => r !== region)));
    };

    // Handler for canton selection changes
    const handleCantonChange = (canton: SwissCanton, isSelected: boolean) => {
        setSelectedCantons((prev) => (isSelected ? [...prev, canton] : prev.filter((c) => c !== canton)));
    };

    // Handler for sub-region selection changes
    const handleSubRegionChange = (subRegion: SwissSubRegion, isSelected: boolean) => {
        setSelectedSubRegions((prev) => (isSelected ? [...prev, subRegion] : prev.filter((sr) => sr !== subRegion)));
    };

    // Filter jobs using our simple filter functions
    const filteredJobs = (() => {
        // If no filters are selected, show all jobs
        if (selectedRegions.length === 0 && selectedCantons.length === 0 && selectedSubRegions.length === 0) {
            return jobListings;
        }

        // Apply canton filter if selected
        if (selectedCantons.length > 0) {
            // Convert enum values to string codes
            const cantonCodes = selectedCantons.map((c) => c);
            return filterJobsByCanton(jobListings, cantonCodes);
        }

        // Apply sub-region filter if selected
        if (selectedSubRegions.length > 0) {
            // Convert enum values to string codes
            const subRegionCodes = selectedSubRegions.map((sr) => sr);
            return filterJobsBySubRegion(jobListings, subRegionCodes);
        }

        // Apply region filter if selected
        if (selectedRegions.length > 0) {
            // Convert enum values to string codes
            const regionCodes = selectedRegions.map((r) => r);
            return filterJobsByRegion(jobListings, cantonToRegion, regionCodes);
        }

        return jobListings;
    })();

    const { appearance, updateAppearance } = useAppearance();
    const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    return (
        <div className={isDarkMode ? 'dark' : ''}>
            <div className={`relative min-h-screen antialiased overflow-hidden ${isDarkMode ? 'bg-gray-950 text-gray-100' : 'bg-gray-50 text-gray-900'}`}>
                {/* Highly visible grid pattern across the entire screen in dark mode */}
                <div className="pointer-events-none fixed inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px] dark:bg-[linear-gradient(to_right,#ffffff12_1px,transparent_1px),linear-gradient(to_bottom,#ffffff12_1px,transparent_1px)]" />

                {/* Dark strong vignette effect with very dark edges */}
                <div className="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_center,transparent_10%,rgba(0,0,0,0.85)_90%)] opacity-0 dark:opacity-100"></div>

                {/* Bright center spotlight */}
                <div className="pointer-events-none fixed inset-0">
                    <div className="absolute left-1/2 top-1/2 h-[900px] w-[1400px] -translate-x-1/2 -translate-y-1/2 bg-[radial-gradient(circle_at_center,rgba(130,100,255,0.25)_0%,rgba(120,80,255,0.15)_20%,transparent_60%)] opacity-0 dark:opacity-100 dark:mix-blend-lighten"></div>

                    {/* Extra brighter spotlight in the center for more contrast */}
                    <div className="absolute left-1/2 top-1/2 h-[600px] w-[800px] -translate-x-1/2 -translate-y-1/2 bg-[radial-gradient(circle_at_center,rgba(170,140,255,0.3)_0%,transparent_70%)] opacity-0 dark:opacity-100 dark:mix-blend-lighten"></div>
                </div>

                <div className="relative mx-auto max-w-7xl">
                    <header className="px-4 pt-8 pb-6 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between">
                            {/* Logo */}
                            <div className="flex items-center">
                                <a href="/" className="flex items-center">
                                    <svg
                                        className="h-10 w-10 text-indigo-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                                        />
                                    </svg>
                                    <span className="ml-2 text-xl font-bold">DevJobs</span>
                                </a>
                            </div>

                            {/* Navigation */}
                            <div className="flex items-center gap-4">
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

                                {/* Auth links */}
                                {auth.user ? (
                                    <a
                                        href="/dashboard"
                                        className="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    >
                                        Dashboard
                                    </a>
                                ) : (
                                    <div className="flex items-center gap-2">
                                        <a
                                            href="/login"
                                            className="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-indigo-600 shadow-sm ring-1 ring-gray-300 ring-inset hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700"
                                        >
                                            Sign in
                                        </a>
                                        <a
                                            href="/register"
                                            className="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                        >
                                            Sign up
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>
                    </header>

                    <main className="px-4 pb-12 sm:px-6 lg:px-8">
                        {/* Main hero section */}
                        <main className="mx-auto max-w-6xl pb-16 relative">
                            <div className="mb-16 flex flex-col items-center justify-center text-center">
                                {/* Focal point for vignette effect */}
                                <div
                                    className={`relative z-10 mb-8 inline-flex items-center rounded-full ${isDarkMode ? 'bg-gray-800/50 text-indigo-400' : 'bg-indigo-50 text-indigo-600'
                                        } px-3 py-1 text-sm backdrop-blur-sm`}
                                >
                                    <span
                                        className={`mr-2 inline-block h-2 w-2 rounded-full ${isDarkMode ? 'bg-indigo-400' : 'bg-indigo-600'}`}
                                    ></span>
                                    Now hiring developers across all tech stacks
                                </div>
                                <h1
                                    className={`mb-6 text-4xl leading-tight font-bold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'} sm:text-5xl md:text-6xl`}
                                >
                                    Where{' '}
                                    <span className="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">tech talent</span>{' '}
                                    meets opportunity
                                </h1>
                                <p className={`mb-10 max-w-2xl text-lg ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                    Connect with forward-thinking companies looking for developers like you. Find your next role in the tech industry
                                    with our curated job board.
                                </p>
                                <div className="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
                                    <Link
                                        href={route('job-listings.index')}
                                        className="flex items-center justify-center rounded-md bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 text-base font-medium text-white shadow-lg shadow-indigo-700/20 transition-all hover:from-indigo-500 hover:to-purple-500"
                                    >
                                        Browse Jobs
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className={`flex items-center justify-center rounded-md ${isDarkMode
                                                ? 'border border-gray-700 bg-gray-800/40 text-white hover:bg-gray-800/60'
                                                : 'border border-gray-300 bg-white text-gray-800 hover:bg-gray-50'
                                            } px-8 py-3 text-base font-medium shadow-lg backdrop-blur-sm transition-all`}
                                    >
                                        Post a Job
                                    </Link>
                                </div>
                            </div>

                            {/* Featured job categories */}
                            <div className="mb-16">
                                <h2
                                    className={`mb-8 text-center text-2xl font-semibold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'}`}
                                >
                                    Popular Tech Categories
                                </h2>
                                <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                                    {[
                                        { name: 'Full-Stack', icon: '⚛️' },
                                        { name: 'Frontend', icon: '🎨' },
                                        { name: 'Backend', icon: '⚙️' },
                                        { name: 'DevOps', icon: '🚀' },
                                        { name: 'AI/ML', icon: '🧠' },
                                        { name: 'Mobile', icon: '📱' },
                                    ].map((category) => (
                                        <div
                                            key={category.name}
                                            className={`group cursor-pointer rounded-xl ${isDarkMode
                                                    ? 'border border-gray-800 bg-gray-900/30 hover:border-indigo-500/30 hover:bg-gray-800/50'
                                                    : 'border border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/50'
                                                } p-4 text-center shadow-lg transition-all`}
                                        >
                                            <div className="mb-2 text-2xl">{category.icon}</div>
                                            <span
                                                className={`text-sm font-medium ${isDarkMode
                                                        ? 'text-gray-300 group-hover:text-indigo-400'
                                                        : 'text-gray-700 group-hover:text-indigo-600'
                                                    } transition-all`}
                                            >
                                                {category.name}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Job stats section */}
                            <div
                                className={`rounded-2xl ${isDarkMode ? 'border border-gray-800 bg-gray-900/30' : 'border border-gray-200 bg-white'
                                    } p-8 shadow-lg`}
                            >
                                <div className="mb-8 text-center">
                                    <h2 className={`mb-3 text-2xl font-semibold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'}`}>
                                        Why Developers Choose Us
                                    </h2>
                                    <p className={`mx-auto max-w-2xl ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                        Our platform connects top tech talent with innovative companies
                                    </p>
                                </div>
                                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                                    <div
                                        className={`rounded-xl ${isDarkMode ? 'border border-gray-800 bg-gray-800/20' : 'border border-gray-200 bg-gray-50'
                                            } p-6 shadow-lg`}
                                    >
                                        <div className="mb-3 text-3xl font-bold text-indigo-600">1,200+</div>
                                        <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Active Job Listings</div>
                                    </div>
                                    <div
                                        className={`rounded-xl ${isDarkMode ? 'border border-gray-800 bg-gray-800/20' : 'border border-gray-200 bg-gray-50'
                                            } p-6 shadow-lg`}
                                    >
                                        <div className="mb-3 text-3xl font-bold text-purple-600">650+</div>
                                        <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Tech Companies</div>
                                    </div>
                                    <div
                                        className={`rounded-xl ${isDarkMode ? 'border border-gray-800 bg-gray-800/20' : 'border border-gray-200 bg-gray-50'
                                            } p-6 shadow-lg`}
                                    >
                                        <div className="mb-3 text-3xl font-bold text-blue-600">25k+</div>
                                        <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Registered Developers</div>
                                    </div>
                                    <div
                                        className={`rounded-xl ${isDarkMode ? 'border border-gray-800 bg-gray-800/20' : 'border border-gray-200 bg-gray-50'
                                            } p-6 shadow-lg`}
                                    >
                                        <div className="mb-3 text-3xl font-bold text-indigo-600">98%</div>
                                        <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Hiring Success Rate</div>
                                    </div>
                                </div>
                            </div>
                        </main>

                        {/* Filters and job listings */}
                        <div className="grid grid-cols-1 gap-8 lg:grid-cols-4">
                            {/* Filters sidebar */}
                            <div className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:col-span-1 dark:border-gray-800 dark:bg-gray-900">
                                <h2 className="mb-4 text-lg font-medium">Filters</h2>

                                {/* Location filter */}
                                <div className="mb-6">
                                    <LocationFilter
                                        selectedRegions={selectedRegions}
                                        selectedCantons={selectedCantons}
                                        selectedSubRegions={selectedSubRegions}
                                        onRegionChange={handleRegionChange}
                                        onCantonChange={handleCantonChange}
                                        onSubRegionChange={handleSubRegionChange}
                                    />
                                </div>
                            </div>

                            {/* Job listings */}
                            <div className="lg:col-span-3">
                                <div className="mb-4 flex items-center justify-between">
                                    <h2 className="text-lg font-medium">{filteredJobs.length} Job Listings</h2>

                                    {/* Sort dropdown would go here */}
                                </div>

                                {/* Job cards */}
                                <div className="space-y-4">
                                    {filteredJobs.length > 0 ? (
                                        filteredJobs.map((job) => (
                                            <div
                                                key={job.id}
                                                className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                                            >
                                                <div className="flex items-start justify-between">
                                                    <div>
                                                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{job.title}</h3>
                                                        <p className="text-sm text-gray-500 dark:text-gray-400">{job.company?.name}</p>
                                                    </div>

                                                    {/* Company logo placeholder */}
                                                    <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                                        {job.company?.logo ? (
                                                            <img
                                                                src={job.company.logo}
                                                                alt={`${job.company.name} logo`}
                                                                className="h-8 w-8 rounded-full"
                                                            />
                                                        ) : (
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                strokeWidth={1.5}
                                                                stroke="currentColor"
                                                                className="h-6 w-6 text-gray-400 dark:text-gray-500"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21m-4.773-4.227-1.591 1.591M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                                                />
                                                            </svg>
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Job metadata */}
                                                <div className="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                                    {/* Location */}
                                                    {(job.city || job.primary_canton_code) && (
                                                        <div className="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                strokeWidth={1.5}
                                                                stroke="currentColor"
                                                                className="mr-1 h-4 w-4 text-gray-400 dark:text-gray-500"
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
                                                            <span>
                                                                {job.city}
                                                                {job.city && job.primary_canton_code && ', '}
                                                                {job.primary_canton_code && (swissCantonData as any)[job.primary_canton_code]?.label}
                                                                {job.has_multiple_locations && ' + weitere Standorte'}
                                                            </span>
                                                        </div>
                                                    )}

                                                    {/* Remote */}
                                                    {job.allows_remote && (
                                                        <div className="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                strokeWidth={1.5}
                                                                stroke="currentColor"
                                                                className="mr-1 h-4 w-4 text-gray-400 dark:text-gray-500"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"
                                                                />
                                                            </svg>
                                                            <span>Remote möglich</span>
                                                        </div>
                                                    )}
                                                </div>

                                                {/* Job description (truncated) */}
                                                <p className="mt-4 line-clamp-3 text-sm text-gray-600 dark:text-gray-300">{job.description}</p>

                                                {/* Apply button */}
                                                <div className="mt-4 flex justify-end">
                                                    <a
                                                        href={`/jobs/${job.id}`}
                                                        className="inline-flex items-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50"
                                                    >
                                                        Mehr erfahren
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20"
                                                            fill="currentColor"
                                                            className="ml-1 h-4 w-4"
                                                        >
                                                            <path
                                                                fillRule="evenodd"
                                                                d="M5 10a.75.75 0 0 1 .75-.75h6.638L10.23 7.29a.75.75 0 1 1 1.04-1.08l3.5 3.25a.75.75 0 0 1 0 1.08l-3.5 3.25a.75.75 0 1 1-1.04-1.08l2.158-1.96H5.75A.75.75 0 0 1 5 10.172V5L8 4z"
                                                                clipRule="evenodd"
                                                            />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="py-12 text-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                strokeWidth={1.5}
                                                stroke="currentColor"
                                                className="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    d="M21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                                                />
                                            </svg>
                                            <h3 className="mt-4 text-lg font-medium text-gray-900 dark:text-white">Keine Jobs gefunden</h3>
                                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Probiere andere Filterkriterien oder schau später wieder vorbei.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </main>

                    <footer className="border-t border-gray-200 px-4 py-8 sm:px-6 lg:px-8 dark:border-gray-800">
                        <div className="flex flex-col items-center justify-between md:flex-row">
                            <div className="flex items-center">
                                <svg
                                    className="h-8 w-8 text-indigo-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                                    />
                                </svg>
                                <span className="ml-2 text-lg font-semibold">DevJobs</span>
                            </div>

                            <div className="mt-4 text-sm text-gray-500 md:mt-0 dark:text-gray-400">
                                &copy; {currentYear} DevJobs. All rights reserved.
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    );
}
