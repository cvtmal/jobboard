import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { AlertCircle, CheckCircle } from 'lucide-react';

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
}: {
    auth: Auth;
    company?: any;
    profileCompletion?: ProfileCompletion;
    shouldShowOnboarding?: boolean;
}) {
    return (
        <CompanyLayout>
            <Head title="Company Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex flex-col space-y-6">
                        <div className="flex items-center justify-between">
                            <h1 className="text-2xl font-bold">Company Dashboard</h1>
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

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <Card>
                                <CardHeader>
                                    <CardTitle>{auth.company?.name}</CardTitle>
                                    <CardDescription>Manage your company information</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p>Update your company details, logo, and other information.</p>
                                </CardContent>
                                <CardFooter>
                                    <Button asChild>
                                        <Link href={route('company.profile')}>Edit Profile</Link>
                                    </Button>
                                </CardFooter>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Job Listings</CardTitle>
                                    <CardDescription>Manage your job listings</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p>Create, edit, and manage all your job listings in one place.</p>
                                </CardContent>
                                <CardFooter>
                                    <Button>View Job Listings</Button>
                                </CardFooter>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Applications</CardTitle>
                                    <CardDescription>Review candidate applications</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p>View and manage all applications to your job listings.</p>
                                </CardContent>
                                <CardFooter>
                                    <Button>View Applications</Button>
                                </CardFooter>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Analytics</CardTitle>
                                    <CardDescription>View performance metrics</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p>See how your job listings are performing and track applicant engagement.</p>
                                </CardContent>
                                <CardFooter>
                                    <Button>View Analytics</Button>
                                </CardFooter>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Account Settings</CardTitle>
                                    <CardDescription>Manage account security</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p>Update your password and appearance preferences.</p>
                                </CardContent>
                                <CardFooter className="flex gap-2">
                                    <Button asChild variant="outline">
                                        <Link href={route('company.settings.password')}>Password</Link>
                                    </Button>
                                    <Button asChild variant="outline">
                                        <Link href={route('company.settings.appearance')}>Appearance</Link>
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
