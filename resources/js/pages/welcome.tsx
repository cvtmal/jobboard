import { type SharedData } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@inertiajs/core';
import { useState, useEffect } from 'react';

interface WelcomeProps {
  auth: SharedData['auth'];
}

export default function Welcome({ auth }: WelcomeProps) {
    const currentYear = new Date().getFullYear();
    const [isDarkMode, setIsDarkMode] = useState(true);
    
    // Initialize theme based on system preference
    useEffect(() => {
        if (typeof window !== 'undefined') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            setIsDarkMode(prefersDark);
            
            // Listen for changes in system preference
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleChange = (e: MediaQueryListEvent) => {
                setIsDarkMode(e.matches);
            };
            
            mediaQuery.addEventListener('change', handleChange);
            return () => mediaQuery.removeEventListener('change', handleChange);
        }
    }, []);
    
    const toggleTheme = () => {
        setIsDarkMode(!isDarkMode);
    };

    return (
        <>
            <Head title="DevJobs | Your Next Tech Career" />
            <div className={`relative min-h-screen antialiased ${isDarkMode ? 'bg-gray-950 text-gray-100' : 'bg-gray-50 text-gray-900'}`}>
                {/* Cyber grid background */}
                <div className="absolute inset-0 z-0 opacity-10">
                    <div className="absolute inset-0" style={{ 
                        backgroundImage: `linear-gradient(to right, ${isDarkMode ? 'rgba(75, 85, 99, 0.1)' : 'rgba(75, 85, 99, 0.15)'} 1px, transparent 1px), 
                                          linear-gradient(to bottom, ${isDarkMode ? 'rgba(75, 85, 99, 0.1)' : 'rgba(75, 85, 99, 0.15)'} 1px, transparent 1px)`,
                        backgroundSize: '40px 40px' 
                    }}></div>
                </div>
                
                {/* Accent lights */}
                <div className="absolute bottom-0 left-0 right-0 top-0 z-0">
                    <div className={`absolute -left-40 top-20 h-96 w-96 rounded-full ${isDarkMode ? 'bg-purple-700 opacity-10' : 'bg-purple-400 opacity-15'} blur-[120px]`}></div>
                    <div className={`absolute -right-40 bottom-20 h-96 w-96 rounded-full ${isDarkMode ? 'bg-blue-600 opacity-10' : 'bg-blue-400 opacity-15'} blur-[120px]`}></div>
                    <div className={`absolute left-1/3 top-1/3 h-72 w-72 rounded-full ${isDarkMode ? 'bg-indigo-500 opacity-10' : 'bg-indigo-300 opacity-15'} blur-[100px]`}></div>
                </div>
                
                <div className="relative z-10 mx-auto max-w-7xl px-6 py-8 lg:px-8">
                    {/* Header */}
                    <header className="mx-auto mb-12 max-w-6xl">
                        <div className="flex items-center justify-between py-3">
                            <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-md bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-lg shadow-indigo-700/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="m18 16 4-4-4-4"></path>
                                        <path d="m6 8-4 4 4 4"></path>
                                        <path d="m14.5 4-5 16"></path>
                                    </svg>
                                </div>
                                <span className={`text-xl font-semibold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'}`}>DevJobs</span>
                            </div>
                            <div className="flex items-center gap-4">
                                {/* Theme toggle button */}
                                <button 
                                    onClick={toggleTheme}
                                    className={`flex h-8 w-8 items-center justify-center rounded-full ${isDarkMode ? 'bg-gray-800 text-yellow-300 hover:bg-gray-700' : 'bg-gray-200 text-indigo-700 hover:bg-gray-300'}`}
                                >
                                    {isDarkMode ? (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <circle cx="12" cy="12" r="5"/>
                                            <line x1="12" y1="1" x2="12" y2="3"/>
                                            <line x1="12" y1="21" x2="12" y2="23"/>
                                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                            <line x1="1" y1="12" x2="3" y2="12"/>
                                            <line x1="21" y1="12" x2="23" y2="12"/>
                                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                        </svg>
                                    ) : (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                        </svg>
                                    )}
                                </button>
                                <nav className="flex items-center gap-4">
                                    {auth.user ? (
                                        <Link
                                            href={route('dashboard')}
                                            className={`rounded-md ${isDarkMode 
                                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-700/20 hover:bg-indigo-500' 
                                                : 'bg-indigo-600 text-white shadow-md hover:bg-indigo-700'} 
                                                px-4 py-2 text-sm font-medium transition-all`}
                                        >
                                            Dashboard
                                        </Link>
                                    ) : (
                                        <>
                                            <Link
                                                href={route('login')}
                                                className={`rounded-md px-4 py-2 text-sm font-medium ${isDarkMode 
                                                    ? 'text-gray-300 hover:text-white' 
                                                    : 'text-gray-600 hover:text-gray-900'} transition-all`}
                                            >
                                                Log in
                                            </Link>
                                            <Link
                                                href={route('register')}
                                                className={`rounded-md ${isDarkMode 
                                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-700/20 hover:bg-indigo-500' 
                                                    : 'bg-indigo-600 text-white shadow-md hover:bg-indigo-700'} 
                                                    px-4 py-2 text-sm font-medium transition-all`}
                                            >
                                                Register
                                            </Link>
                                        </>
                                    )}
                                </nav>
                            </div>
                        </div>
                    </header>

                    {/* Main hero section */}
                    <main className="mx-auto max-w-6xl pb-16">
                        <div className="mb-16 flex flex-col items-center justify-center text-center">
                            <div className={`mb-8 inline-flex items-center rounded-full ${isDarkMode 
                                ? 'bg-gray-800/50 text-indigo-400' 
                                : 'bg-indigo-50 text-indigo-600'} px-3 py-1 text-sm backdrop-blur-sm`}>
                                <span className={`mr-2 inline-block h-2 w-2 rounded-full ${isDarkMode ? 'bg-indigo-400' : 'bg-indigo-600'}`}></span>
                                Now hiring developers across all tech stacks
                            </div>
                            <h1 className={`mb-6 text-4xl font-bold leading-tight tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'} sm:text-5xl md:text-6xl`}>
                                Where <span className="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">tech talent</span> meets opportunity
                            </h1>
                            <p className={`mb-10 max-w-2xl text-lg ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                Connect with forward-thinking companies looking for developers like you. 
                                Find your next role in the tech industry with our curated job board.
                            </p>
                            <div className="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
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
                                        : 'border border-gray-300 bg-white text-gray-800 hover:bg-gray-50'} 
                                        px-8 py-3 text-base font-medium shadow-lg backdrop-blur-sm transition-all`}
                                >
                                    Post a Job
                                </Link>
                            </div>
                        </div>

                        {/* Featured job categories */}
                        <div className="mb-16">
                            <h2 className={`mb-8 text-center text-2xl font-semibold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'}`}>Popular Tech Categories</h2>
                            <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                                {[
                                    { name: 'Full-Stack', icon: '⚛️' },
                                    { name: 'Frontend', icon: '🎨' },
                                    { name: 'Backend', icon: '⚙️' },
                                    { name: 'DevOps', icon: '🚀' },
                                    { name: 'AI/ML', icon: '🧠' },
                                    { name: 'Mobile', icon: '📱' }
                                ].map((category) => (
                                    <div key={category.name} 
                                        className={`group cursor-pointer rounded-xl ${isDarkMode 
                                            ? 'border border-gray-800 bg-gray-900/30 hover:border-indigo-500/30 hover:bg-gray-800/50' 
                                            : 'border border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/50'} 
                                            p-4 text-center shadow-lg transition-all`}
                                    >
                                        <div className="mb-2 text-2xl">{category.icon}</div>
                                        <span className={`text-sm font-medium ${isDarkMode 
                                            ? 'text-gray-300 group-hover:text-indigo-400' 
                                            : 'text-gray-700 group-hover:text-indigo-600'} transition-all`}>{category.name}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Job stats section */}
                        <div className={`rounded-2xl ${isDarkMode 
                            ? 'border border-gray-800 bg-gray-900/30' 
                            : 'border border-gray-200 bg-white'} p-8 shadow-lg`}
                        >
                            <div className="mb-8 text-center">
                                <h2 className={`mb-3 text-2xl font-semibold tracking-tight ${isDarkMode ? 'text-white' : 'text-gray-900'}`}>Why Developers Choose Us</h2>
                                <p className={`mx-auto max-w-2xl ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Our platform connects top tech talent with innovative companies</p>
                            </div>
                            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                                <div className={`rounded-xl ${isDarkMode 
                                    ? 'border border-gray-800 bg-gray-800/20' 
                                    : 'border border-gray-200 bg-gray-50'} p-6 shadow-lg`}
                                >
                                    <div className="mb-3 text-3xl font-bold text-indigo-600">1,200+</div>
                                    <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Active Job Listings</div>
                                </div>
                                <div className={`rounded-xl ${isDarkMode 
                                    ? 'border border-gray-800 bg-gray-800/20' 
                                    : 'border border-gray-200 bg-gray-50'} p-6 shadow-lg`}
                                >
                                    <div className="mb-3 text-3xl font-bold text-purple-600">650+</div>
                                    <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Tech Companies</div>
                                </div>
                                <div className={`rounded-xl ${isDarkMode 
                                    ? 'border border-gray-800 bg-gray-800/20' 
                                    : 'border border-gray-200 bg-gray-50'} p-6 shadow-lg`}
                                >
                                    <div className="mb-3 text-3xl font-bold text-blue-600">25k+</div>
                                    <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Registered Developers</div>
                                </div>
                                <div className={`rounded-xl ${isDarkMode 
                                    ? 'border border-gray-800 bg-gray-800/20' 
                                    : 'border border-gray-200 bg-gray-50'} p-6 shadow-lg`}
                                >
                                    <div className="mb-3 text-3xl font-bold text-indigo-600">98%</div>
                                    <div className={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-600'}`}>Hiring Success Rate</div>
                                </div>
                            </div>
                        </div>
                    </main>

                    {/* Featured tech stack logos */}
                    <div className="mx-auto max-w-6xl py-12">
                        <p className={`mb-8 text-center text-sm font-medium ${isDarkMode ? 'text-gray-500' : 'text-gray-400'}`}>COMPANIES USING OUR PLATFORM</p>
                        <div className="flex flex-wrap items-center justify-center gap-8 opacity-50">
                            {/* Sample company logos would go here */}
                            <div className={`h-6 w-24 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                            <div className={`h-6 w-16 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                            <div className={`h-6 w-20 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                            <div className={`h-6 w-16 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                            <div className={`h-6 w-24 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                            <div className={`h-6 w-20 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-300'}`}></div>
                        </div>
                    </div>

                    {/* Footer */}
                    <footer className={`mx-auto max-w-6xl ${isDarkMode ? 'border-gray-800' : 'border-gray-200'} border-t pt-12 text-center`}>
                        <div className="flex justify-center space-x-6 pb-8">
                            <a href="#" className={`${isDarkMode ? 'text-gray-500 hover:text-gray-400' : 'text-gray-400 hover:text-gray-600'}`}>
                                <span className="sr-only">GitHub</span>
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fillRule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clipRule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" className={`${isDarkMode ? 'text-gray-500 hover:text-gray-400' : 'text-gray-400 hover:text-gray-600'}`}>
                                <span className="sr-only">Twitter</span>
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" className={`${isDarkMode ? 'text-gray-500 hover:text-gray-400' : 'text-gray-400 hover:text-gray-600'}`}>
                                <span className="sr-only">LinkedIn</span>
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg>
                            </a>
                        </div>
                        <p className={`pb-8 text-sm ${isDarkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                            &copy; {currentYear} DevJobs. All rights reserved.
                        </p>
                    </footer>
                </div>
            </div>
        </>
    );
}
