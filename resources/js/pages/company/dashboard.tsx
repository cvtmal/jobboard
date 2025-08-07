import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { AlertCircle, CheckCircle, Mail, Phone, HelpCircle, ChevronRight } from 'lucide-react';
import { AvatarGroup } from '@/components/ui/avatar-group';
import HeadingSmall from '@/components/heading-small';
import avatar1 from '@images/avatar1.png';
import avatar2 from '@images/avatar2.png';
import avatar3 from '@images/avatar3.png';
import avatar4 from '@images/avatar4.png';
import Heading from '@/components/heading';

type ProfileCompletion = {
    percentage: number;
    steps: Record<string, boolean>;
    missingSteps: string[];
};

export default function CompanyDashboard({
    auth,
    company,
    profileCompletion,
    shouldShowOnboarding,
    hasJobListings,
}: {
    auth: Auth;
    company?: any;
    profileCompletion?: ProfileCompletion;
    shouldShowOnboarding?: boolean;
    hasJobListings?: boolean;
}) {
    return (
        <CompanyLayout>
            <Head title="Dashboard" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex flex-col space-y-6">
                        <div className="flex items-center justify-between">
                            <Heading title="Dashboard" description="Welcome to your company dashboard" />
                            {shouldShowOnboarding && profileCompletion && (
                                <Button asChild variant="outline" size="sm">
                                    <Link href={route('company.profile')}>Complete Profile ({profileCompletion.percentage}%)</Link>
                                </Button>
                            )}
                        </div>

                        {/* Onboarding Section */}
                        {shouldShowOnboarding && profileCompletion && (
                            <Card className="border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-950/20">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <AlertCircle className="h-5 w-5 text-orange-600" />
                                        Complete Your Company Profile
                                    </CardTitle>
                                    <CardDescription className="text-orange-700 dark:text-orange-300">
                                        Complete your profile to attract better candidates and improve your hiring success.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <div className="mb-2 flex items-center justify-between text-sm">
                                            <span className="font-medium">Profile Completion</span>
                                            <span className="text-muted-foreground">{profileCompletion.percentage}% complete</span>
                                        </div>
                                        <Progress value={profileCompletion.percentage} className="h-3" />
                                    </div>

                                    <div className="grid grid-cols-2 gap-4 text-sm">
                                        {Object.entries(profileCompletion.steps).map(([step, completed]) => (
                                            <div key={step} className="flex items-center gap-2">
                                                {completed ? (
                                                    <CheckCircle className="h-4 w-4 text-green-600" />
                                                ) : (
                                                    <div className="border-muted-foreground h-4 w-4 rounded-full border-2" />
                                                )}
                                                <span className={completed ? 'text-green-700 dark:text-green-400' : 'text-muted-foreground'}>
                                                    {step.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())}
                                                </span>
                                            </div>
                                        ))}
                                    </div>

                                    <div className="flex gap-2 pt-2">
                                        <Button asChild size="sm">
                                            <Link href={route('company.profile')}>Complete Profile</Link>
                                        </Button>
                                        <Button asChild variant="outline" size="sm">
                                            <Link href={route('company.profile.skip')} method="post">
                                                Skip for now
                                            </Link>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Main Dashboard Content */}
                        {/* Onboarding Steps */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-start gap-4">
                                    <Avatar className="h-10 w-10">
                                        <AvatarImage src={avatar1} alt="Lukas - Onboarding Assistant" />
                                        <AvatarFallback>LM</AvatarFallback>
                                    </Avatar>
                                    <div className="flex-1">
                                        <CardTitle>Welcome</CardTitle>
                                        <CardDescription>
                                            I'm Lukas, your onboarding assistant, and I'm here to help you set up your account.
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* Step 1 - Account Created */}
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            1
                                        </div>
                                        <HeadingSmall title="Account Created" description="Your account has been successfully created." />
                                    </div>
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                        <CheckCircle className="text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>

                                {/* Step 2 - Fill out company details */}
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            2
                                        </div>
                                        <HeadingSmall title="Fill out company details" description="Complete your company profile to attract better candidates." />
                                    </div>
                                    <Button asChild size="default">
                                        <Link href={route('company.details')}>Complete Profile</Link>
                                    </Button>
                                </div>

                                {/* Step 3 - Create your first Job */}
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            3
                                        </div>
                                        <HeadingSmall title="Create your first Job" description="Post a job to start attracting candidates." />
                                    </div>
                                    {hasJobListings ? (
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                            <CheckCircle className="text-blue-600 dark:text-blue-400" />
                                        </div>
                                    ) : (
                                        <Button asChild size="default">
                                            <Link href={route('company.job-listings.create')}>Create Job</Link>
                                        </Button>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Contact Us Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Contact Us</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* Avatar Stack - Professional Overlapping Design */}
                                <div className="flex justify-center">
                                    <AvatarGroup
                                        avatars={[
                                            { name: "Lukas Meier", src: avatar1 },
                                            { name: "Lara Schmid", src: avatar2 },
                                            { name: "Céline Rochat", src: avatar3 },
                                            { name: "Matteo Bernasconi", src: avatar4 },
                                        ]}
                                        max={4}
                                        size="xl"
                                        overlap="sm"
                                        showTooltip={true}
                                    />
                                </div>

                                {/* Customer Service Team Info */}
                                <div className="text-center space-y-3">
                                    <h3 className="text-lg font-semibold">Customer Service Team</h3>
                                    <div className="space-y-2">
                                        <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                                            <Mail className="h-4 w-4" />
                                            <a href="mailto:info@superjobs.ch" className="hover:text-primary transition-colors">
                                                info@superjobs.ch
                                            </a>
                                        </div>
                                        <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                                            <Phone className="h-4 w-4" />
                                            <a href="tel:+41441234567" className="hover:text-primary transition-colors">
                                                +41 44 123 45 67
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Support Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Support</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-0">
                                {/* Help center item */}
                                <Link
                                    href={route('company.dashboard')}
                                    className="flex items-center justify-between p-4 -m-4 mb-2 rounded-lg transition-all duration-200 hover:bg-muted/50 group"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 transition-transform group-hover:scale-105 dark:bg-blue-900/30">
                                            <HelpCircle className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <HeadingSmall title="Help center" description="See guides and tutorials" />
                                    </div>
                                    <ChevronRight className="h-5 w-5 text-muted-foreground transition-all group-hover:translate-x-1 group-hover:text-foreground" />
                                </Link>

                                {/* Email us item */}
                                <Link
                                    href={route('company.dashboard')}
                                    className="flex items-center justify-between p-4 -m-4 rounded-lg transition-all duration-200 hover:bg-muted/50 group"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 transition-transform group-hover:scale-105 dark:bg-green-900/30">
                                            <Mail className="h-5 w-5 text-green-600 dark:text-green-400" />
                                        </div>
                                        <HeadingSmall title="Email us" description="Get support from our experts" />
                                    </div>
                                    <ChevronRight className="h-5 w-5 text-muted-foreground transition-all group-hover:translate-x-1 group-hover:text-foreground" />
                                </Link>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}
