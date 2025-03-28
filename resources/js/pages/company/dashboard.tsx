import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type Auth } from '@/types';
import { Head, Link } from '@inertiajs/react';

export default function CompanyDashboard({ auth }: { auth: Auth }) {
    return (
        <AppLayout>
            <Head title="Company Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex flex-col space-y-4">
                        <h1 className="text-2xl font-bold">Company Dashboard</h1>

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
                                        <Link href={route('company.settings.profile')}>Edit Profile</Link>
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
        </AppLayout>
    );
}
