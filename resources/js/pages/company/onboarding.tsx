import { Head, Link } from '@inertiajs/react';
import { Upload, HelpCircle, LifeBuoy, Mail, PlusCircle } from 'lucide-react';

import CompanyLayout from '@/layouts/company-layout';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';
import { type Auth } from '@/types';

export default function CompanyOnboarding({ auth }: { auth: Auth }) {
    const { appearance } = useAppearance();
    const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    return (
        <CompanyLayout>
            <Head title="Onboarding" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex flex-col space-y-8">
                        <div className="space-y-4">
                            <h1 className="text-3xl font-bold tracking-tight">Welcome to JobBoard</h1>
                            <p className="text-muted-foreground">
                                Let's get your company set up and ready to start hiring the best talent!
                            </p>
                        </div>

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            {/* Post a Job Card */}
                            <Card className={`border-2 ${isDarkMode ? 'border-blue-600/40 bg-blue-950/10' : 'border-blue-500/20 bg-blue-50'}`}>
                                <CardHeader>
                                    <div className="flex items-center gap-3">
                                        <div className={`rounded-full p-2 ${isDarkMode ? 'bg-blue-900/40' : 'bg-blue-100'}`}>
                                            <PlusCircle className={`h-6 w-6 ${isDarkMode ? 'text-blue-400' : 'text-blue-600'}`} />
                                        </div>
                                        <CardTitle>Publish Your First Job</CardTitle>
                                    </div>
                                    <CardDescription>
                                        Create your first job listing and start receiving applications
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="ml-6 list-disc space-y-2 text-sm text-muted-foreground">
                                        <li>Define job requirements and responsibilities</li>
                                        <li>Set up application process</li>
                                        <li>Reach qualified candidates</li>
                                    </ul>
                                </CardContent>
                                <CardFooter>
                                    <Button asChild className="w-full">
                                        <Link href="/company/job-listings/create">
                                            <PlusCircle className="mr-2 h-4 w-4" />
                                            Create Job Listing
                                        </Link>
                                    </Button>
                                </CardFooter>
                            </Card>

                            {/* Upload Logo Card */}
                            <Card>
                                <CardHeader>
                                    <div className="flex items-center gap-3">
                                        <div className={`rounded-full p-2 ${isDarkMode ? 'bg-slate-800' : 'bg-slate-100'}`}>
                                            <Upload className="h-6 w-6" />
                                        </div>
                                        <CardTitle>Upload Your Logo</CardTitle>
                                    </div>
                                    <CardDescription>
                                        Add your company branding to make your listings stand out
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="ml-6 list-disc space-y-2 text-sm text-muted-foreground">
                                        <li>Enhance company recognition</li>
                                        <li>Improve applicant experience</li>
                                        <li>Build your employer brand</li>
                                    </ul>
                                </CardContent>
                                <CardFooter>
                                    <Button asChild variant="outline" className="w-full">
                                        <Link href="/company/settings/profile">
                                            <Upload className="mr-2 h-4 w-4" />
                                            Upload Logo
                                        </Link>
                                    </Button>
                                </CardFooter>
                            </Card>

                            {/* Support Card */}
                            <Card>
                                <CardHeader>
                                    <div className="flex items-center gap-3">
                                        <div className={`rounded-full p-2 ${isDarkMode ? 'bg-slate-800' : 'bg-slate-100'}`}>
                                            <LifeBuoy className="h-6 w-6" />
                                        </div>
                                        <CardTitle>Contact & Support</CardTitle>
                                    </div>
                                    <CardDescription>
                                        Get help with your account or job listings
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="flex items-start gap-2">
                                            <HelpCircle className="mt-0.5 h-4 w-4 text-muted-foreground" />
                                            <div>
                                                <p className="text-sm font-medium">Help Center</p>
                                                <p className="text-xs text-muted-foreground">Guidelines and FAQs</p>
                                                <Link href="/help" className="text-xs text-primary hover:underline">
                                                    Visit Help Center
                                                </Link>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-2">
                                            <Mail className="mt-0.5 h-4 w-4 text-muted-foreground" />
                                            <div>
                                                <p className="text-sm font-medium">Email Support</p>
                                                <p className="text-xs text-muted-foreground">Get help from our team</p>
                                                <a href="mailto:support@jobboard.com" className="text-xs text-primary hover:underline">
                                                    support@jobboard.com
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter>
                                    <Button asChild variant="outline" className="w-full">
                                        <Link href="/contact">
                                            <LifeBuoy className="mr-2 h-4 w-4" />
                                            Contact Us
                                        </Link>
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
