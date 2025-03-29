import { type SharedData } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@inertiajs/core';
import { useState, useEffect } from 'react';

interface WelcomeProps {
  auth: SharedData['auth'];
}

export default function Welcome({ auth }: WelcomeProps) {
    const currentYear = new Date().getFullYear();

    return (
        <>
            <Head title="Job Board | Find Your Next Career" />
            <div className="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-950 dark:via-purple-950 dark:to-pink-950">
                {/* Background decorative elements */}
                <div className="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-blue-300 opacity-20 blur-3xl dark:bg-blue-600" />
                <div className="absolute top-1/2 -right-24 h-96 w-96 rounded-full bg-purple-300 opacity-20 blur-3xl dark:bg-purple-600" />
                <div className="absolute -bottom-24 left-1/3 h-96 w-96 rounded-full bg-pink-300 opacity-20 blur-3xl dark:bg-pink-600" />
                
                <div className="relative z-10 mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-8">
                    {/* Header with glassmorphism */}
                    <header className="mb-12 w-full">
                        <div className="flex items-center justify-between rounded-xl bg-white bg-opacity-20 p-4 backdrop-blur-lg dark:bg-gray-900 dark:bg-opacity-20">
                            <div className="flex items-center gap-2">
                                <div className="flex h-10 w-10 items-center justify-center rounded-md bg-indigo-600 text-white shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                        <line x1="9" x2="15" y1="15" y2="15" />
                                        <line x1="9" x2="15" y1="9" y2="9" />
                                        <line x1="9" x2="9" y1="9" y2="15" />
                                    </svg>
                                </div>
                                <span className="text-xl font-semibold text-gray-800 dark:text-white">Career Hub</span>
                            </div>
                            <nav className="flex items-center gap-4">
                                {auth.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="rounded-md bg-white bg-opacity-30 px-5 py-2 text-sm font-medium text-gray-800 shadow-sm transition-all hover:bg-opacity-40 dark:bg-gray-800 dark:bg-opacity-30 dark:text-white dark:hover:bg-opacity-40"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="rounded-md px-5 py-2 text-sm font-medium text-gray-700 transition-all hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                                        >
                                            Log in
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            className="rounded-md bg-white bg-opacity-30 px-5 py-2 text-sm font-medium text-gray-800 shadow-sm transition-all hover:bg-opacity-40 dark:bg-gray-800 dark:bg-opacity-30 dark:text-white dark:hover:bg-opacity-40"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </header>

                    {/* Main content with glassmorphism */}
                    <main className="flex flex-1 flex-col items-center justify-center">
                        <div className="rounded-2xl bg-white bg-opacity-20 p-1 shadow-xl backdrop-blur-lg dark:bg-gray-900 dark:bg-opacity-20">
                            <div className="grid overflow-hidden rounded-xl lg:grid-cols-2">
                                {/* Left side - content */}
                                <div className="flex flex-col justify-center p-8 lg:p-12">
                                    <h1 className="mb-6 text-4xl font-bold leading-tight text-gray-800 dark:text-white lg:text-5xl">
                                        Find Your <span className="text-indigo-600 dark:text-indigo-400">Dream Job</span> Today
                                    </h1>
                                    <p className="mb-8 text-lg text-gray-600 dark:text-gray-300">
                                        Connect with top employers and discover opportunities that match your skills and aspirations. Your next career move starts here.
                                    </p>
                                    <div className="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                                        <Link
                                            href={route('job-listings.index')}
                                            className="flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-md transition-all hover:bg-indigo-700"
                                        >
                                            Explore Jobs
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            className="flex items-center justify-center rounded-md bg-white bg-opacity-50 px-6 py-3 text-base font-medium text-gray-800 shadow-md transition-all hover:bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-50 dark:text-white dark:hover:bg-opacity-70"
                                        >
                                            Post a Job
                                        </Link>
                                    </div>
                                </div>

                                {/* Right side - stats */}
                                <div className="hidden bg-white bg-opacity-10 p-12 backdrop-blur-sm dark:bg-black dark:bg-opacity-10 lg:block">
                                    <div className="grid grid-cols-2 gap-8">
                                        <div className="rounded-xl bg-white bg-opacity-20 p-6 shadow-sm backdrop-blur-sm dark:bg-gray-800 dark:bg-opacity-20">
                                            <div className="mb-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">1000+</div>
                                            <div className="text-sm text-gray-600 dark:text-gray-300">Active Job Listings</div>
                                        </div>
                                        <div className="rounded-xl bg-white bg-opacity-20 p-6 shadow-sm backdrop-blur-sm dark:bg-gray-800 dark:bg-opacity-20">
                                            <div className="mb-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">500+</div>
                                            <div className="text-sm text-gray-600 dark:text-gray-300">Companies Hiring</div>
                                        </div>
                                        <div className="rounded-xl bg-white bg-opacity-20 p-6 shadow-sm backdrop-blur-sm dark:bg-gray-800 dark:bg-opacity-20">
                                            <div className="mb-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">50k+</div>
                                            <div className="text-sm text-gray-600 dark:text-gray-300">Registered Job Seekers</div>
                                        </div>
                                        <div className="rounded-xl bg-white bg-opacity-20 p-6 shadow-sm backdrop-blur-sm dark:bg-gray-800 dark:bg-opacity-20">
                                            <div className="mb-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">98%</div>
                                            <div className="text-sm text-gray-600 dark:text-gray-300">Satisfaction Rate</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Featured categories */}
                        <div className="mt-12 w-full rounded-xl bg-white bg-opacity-20 p-8 backdrop-blur-lg dark:bg-gray-900 dark:bg-opacity-20">
                            <h2 className="mb-6 text-center text-2xl font-semibold text-gray-800 dark:text-white">Popular Categories</h2>
                            <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                                {['Technology', 'Marketing', 'Design', 'Finance', 'Healthcare', 'Education'].map((category) => (
                                    <div key={category} className="flex flex-col items-center rounded-lg bg-white bg-opacity-30 p-4 text-center transition-all hover:bg-opacity-40 dark:bg-gray-800 dark:bg-opacity-30 dark:hover:bg-opacity-40">
                                        <span className="text-sm font-medium text-gray-800 dark:text-white">{category}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </main>

                    {/* Footer */}
                    <footer className="mt-12 rounded-xl bg-white bg-opacity-20 p-6 text-center backdrop-blur-lg dark:bg-gray-900 dark:bg-opacity-20">
                        <p className="text-sm text-gray-600 dark:text-gray-300">
                            &copy; {currentYear} Career Hub. All rights reserved.
                        </p>
                    </footer>
                </div>
            </div>
        </>
    );
}
