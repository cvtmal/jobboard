import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Building, CheckCircle, Globe, Star, Upload, Users } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import type { Auth, BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile',
        href: '/company/profile',
    },
];

export default function CompanyProfileOverview({
    auth,
    company,
    shouldShowOnboarding,
}: {
    auth: Auth;
    company: any;
    shouldShowOnboarding: boolean;
}) {
    const profileCompletion = calculateProfileCompletion(company);

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Profile" />

            <div className="space-y-8">
                {shouldShowOnboarding && (
                    <Card className="border-primary/20 bg-primary/5">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Building className="text-primary h-5 w-5" />
                                Welcome! Set Up Your Company Profile
                            </CardTitle>
                            <CardDescription>
                                Complete your profile to create better job listings and attract quality candidates. Start with your company details below.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Link href="/company/details">
                                <Button>Complete Company Details</Button>
                            </Link>
                        </CardContent>
                    </Card>
                )}

                {/* Profile Overview Cards */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Company Details Card */}
                    <Card className="transition-shadow hover:shadow-md">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Building className="h-5 w-5" />
                                Company Details
                            </CardTitle>
                            <CardDescription>
                                Manage your company information, branding, and contact details
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-sm">
                                    <span>Profile Completion</span>
                                    <span className="font-medium">{profileCompletion.percentage}%</span>
                                </div>
                                <div className="h-2 bg-gray-200 rounded-full">
                                    <div 
                                        className="h-2 bg-primary rounded-full transition-all duration-300"
                                        style={{ width: `${profileCompletion.percentage}%` }}
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                {profileCompletion.completed.map((item, index) => (
                                    <div key={index} className="flex items-center gap-2 text-sm text-green-600">
                                        <CheckCircle className="h-4 w-4" />
                                        <span>{item}</span>
                                    </div>
                                ))}
                                {profileCompletion.missing.slice(0, 2).map((item, index) => (
                                    <div key={index} className="flex items-center gap-2 text-sm text-gray-500">
                                        <div className="h-4 w-4 border rounded-full border-gray-300" />
                                        <span>{item}</span>
                                    </div>
                                ))}
                                {profileCompletion.missing.length > 2 && (
                                    <div className="text-sm text-gray-500">
                                        +{profileCompletion.missing.length - 2} more to complete
                                    </div>
                                )}
                            </div>

                            <Link href="/company/details">
                                <Button variant="outline" className="w-full">
                                    Manage Details
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>

                    {/* Career Page Card */}
                    <Card className="transition-shadow hover:shadow-md">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Globe className="h-5 w-5" />
                                Career Page
                            </CardTitle>
                            <CardDescription>
                                Customize your public career page and company showcase
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex items-center gap-2 text-sm">
                                <div className="h-2 w-2 bg-yellow-500 rounded-full"></div>
                                <span className="text-gray-600">Coming Soon</span>
                            </div>

                            <p className="text-sm text-gray-600">
                                Create a branded career page where candidates can learn about your company culture, view open positions, and apply directly.
                            </p>

                            <Link href="/company/career-page">
                                <Button variant="outline" className="w-full" disabled>
                                    Customize Career Page
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                </div>

                {/* Quick Stats */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Star className="h-5 w-5" />
                            Profile Impact
                        </CardTitle>
                        <CardDescription>
                            See how your profile affects your job listing performance
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-6 md:grid-cols-3">
                            <div className="text-center">
                                <div className="text-2xl font-bold text-primary">{company.logo_url ? '✓' : '!'}</div>
                                <div className="text-sm font-medium">Company Logo</div>
                                <div className="text-xs text-gray-600">
                                    {company.logo_url ? 'Increases trust by 40%' : 'Upload for better visibility'}
                                </div>
                            </div>
                            
                            <div className="text-center">
                                <div className="text-2xl font-bold text-primary">
                                    {company.description_english ? '✓' : '!'}
                                </div>
                                <div className="text-sm font-medium">Company Description</div>
                                <div className="text-xs text-gray-600">
                                    {company.description_english ? 'Attracts quality candidates' : 'Add to improve applications'}
                                </div>
                            </div>
                            
                            <div className="text-center">
                                <div className="text-2xl font-bold text-primary">
                                    {(company.benefits && company.benefits.length > 0) ? '✓' : '!'}
                                </div>
                                <div className="text-sm font-medium">Benefits Listed</div>
                                <div className="text-xs text-gray-600">
                                    {(company.benefits && company.benefits.length > 0) 
                                        ? 'Increases application rate' 
                                        : 'Add benefits to stand out'}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </CompanyLayout>
    );
}

function calculateProfileCompletion(company: any) {
    const requiredFields = [
        { field: 'name', label: 'Company Name' },
        { field: 'email', label: 'Email Address' },
        { field: 'description_english', label: 'Company Description' },
        { field: 'address', label: 'Address' },
        { field: 'city', label: 'City' },
        { field: 'url', label: 'Website' },
        { field: 'logo_url', label: 'Company Logo' },
        { field: 'size', label: 'Company Size' },
        { field: 'industry', label: 'Industry' },
    ];

    const completed = requiredFields.filter(({ field }) => {
        const value = company[field];
        return value && value !== '' && value !== null;
    });

    const missing = requiredFields.filter(({ field }) => {
        const value = company[field];
        return !value || value === '' || value === null;
    });

    return {
        percentage: Math.round((completed.length / requiredFields.length) * 100),
        completed: completed.map(({ label }) => label),
        missing: missing.map(({ label }) => label),
    };
}