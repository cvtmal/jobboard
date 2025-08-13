import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { 
    ArrowLeft, 
    CheckCircle, 
    Mail, 
    Phone,
    ExternalLink
} from 'lucide-react';

interface JobListing {
    id: number;
    title: string;
    company: {
        id: number;
        name: string;
    };
}

interface Props {
    auth: Auth;
    jobListing: JobListing;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings / Already Published',
        href: '/company/job-listings',
    },
];

export default function AlreadyPublished({ auth, jobListing }: Props) {
    const handleBackToJobListing = () => {
        router.visit(route('company.job-listings.show', jobListing.id));
    };

    const handleBackToListings = () => {
        router.visit(route('company.job-listings.index'));
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title={`Already Published: ${jobListing.title}`} />

            <div className="py-8">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardHeader className="text-center pb-4">
                            <div className="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                <CheckCircle className="h-8 w-8 text-green-600" />
                            </div>
                            <CardTitle className="text-2xl">Job Listing Already Published</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="text-center">
                                <p className="text-lg text-muted-foreground mb-2">
                                    <strong>"{jobListing.title}"</strong> is already published and active.
                                </p>
                                <p className="text-muted-foreground">
                                    If you want to make changes to a currently published job listing, 
                                    please contact our support team for assistance.
                                </p>
                            </div>

                            {/* Support Contact Information */}
                            <div className="bg-blue-50 rounded-lg p-6 border border-blue-200">
                                <h3 className="font-semibold text-blue-900 mb-4 text-center">
                                    Need Help? Contact Support
                                </h3>
                                <div className="space-y-3">
                                    <div className="flex items-center justify-center gap-3 text-blue-800">
                                        <Mail className="h-4 w-4" />
                                        <a 
                                            href="mailto:support@jobboard.com" 
                                            className="hover:underline"
                                        >
                                            support@jobboard.com
                                        </a>
                                    </div>
                                    <div className="flex items-center justify-center gap-3 text-blue-800">
                                        <Phone className="h-4 w-4" />
                                        <a 
                                            href="tel:+41123456789" 
                                            className="hover:underline"
                                        >
                                            +41 12 345 67 89
                                        </a>
                                    </div>
                                    <div className="flex items-center justify-center gap-3 text-blue-800">
                                        <ExternalLink className="h-4 w-4" />
                                        <a 
                                            href="/help" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            className="hover:underline"
                                        >
                                            Visit Help Center
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {/* What you can do */}
                            <div className="bg-gray-50 rounded-lg p-4 border">
                                <h4 className="font-medium mb-3">What you can do:</h4>
                                <ul className="text-sm text-muted-foreground space-y-2">
                                    <li>• View your published job listing and track applications</li>
                                    <li>• Create a new job listing for different positions</li>
                                    <li>• Contact support for major changes to published jobs</li>
                                    <li>• Manage your other draft job listings</li>
                                </ul>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex flex-col sm:flex-row gap-3 pt-4">
                                <Button
                                    onClick={handleBackToJobListing}
                                    className="flex-1"
                                >
                                    <CheckCircle className="h-4 w-4 mr-2" />
                                    View Published Job
                                </Button>
                                <Button
                                    variant="outline"
                                    onClick={handleBackToListings}
                                    className="flex-1"
                                >
                                    <ArrowLeft className="h-4 w-4 mr-2" />
                                    Back to All Listings
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </CompanyLayout>
    );
}