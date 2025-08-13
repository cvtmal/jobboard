import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type JobTier } from './components/PackageSelector';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/react';
import { 
    ArrowLeft, 
    CheckCircle2, 
    CreditCard, 
    Package, 
    Calendar,
    TrendingUp,
    Star,
    Zap
} from 'lucide-react';
import { useState } from 'react';

interface JobListing {
    id: number;
    title: string;
    company: {
        id: number;
        name: string;
    };
    status: {
        value: string;
        label: string;
    };
}

interface Props {
    auth: Auth;
    jobListing: JobListing;
    selectedTier: JobTier;
    currentSubscription?: {
        id: number;
        job_tier_id: number;
        expires_at: string;
        payment_status: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings / Order Summary',
        href: '/company/job-listings',
    },
];

export default function OrderSummary({ auth, jobListing, selectedTier, currentSubscription }: Props) {
    const [isPublishing, setIsPublishing] = useState(false);
    
    const { post } = useForm({
        selected_tier_id: selectedTier.id,
        status: 'published', // Ensure the job is marked as published
    });

    const handleBack = () => {
        router.visit(route('company.job-listings.package-selection', jobListing.id));
    };

    const handlePublish = () => {
        if (isPublishing) return;
        
        setIsPublishing(true);

        // Use the new publish-with-subscription route
        post(route('company.job-listings.publish-with-subscription', jobListing.id), {
            onSuccess: () => {
                // Clear any localStorage data
                localStorage.removeItem(`job-listing-package-selection-${jobListing.id}`);
                localStorage.removeItem(`job-listing-draft`);
                localStorage.removeItem(`job-listing-edit-draft`);
                localStorage.removeItem(`job-listing-edit-draft-${jobListing.id}`);
                localStorage.removeItem(`job-listing-draft-${jobListing.id}`);
                localStorage.removeItem(`job-listing-current-step-${jobListing.id}`);
                localStorage.removeItem(`job-listing-completed-steps-${jobListing.id}`);
                
                // Let the backend handle the redirect
                // Backend will redirect to either 'already-published' page or 'show' page
            },
            onError: (errors) => {
                console.error('Publication failed:', errors);
                setIsPublishing(false);
            },
            onFinish: () => {
                setIsPublishing(false);
            }
        });
    };

    const getTierIcon = (tierName: string) => {
        switch (tierName) {
            case 'Basic':
                return <Zap className="w-5 h-5 text-blue-600" />;
            case 'Professional':
                return <TrendingUp className="w-5 h-5 text-orange-600" />;
            case 'Premium':
                return <Star className="w-5 h-5 text-purple-600" />;
            default:
                return <Package className="w-5 h-5 text-gray-600" />;
        }
    };

    const getTierFeatures = (tier: JobTier) => {
        const features = [
            `Job active for ${tier.duration_days} days`,
            `Up to ${tier.max_active_jobs} active job${tier.max_active_jobs > 1 ? 's' : ''}`,
        ];

        if (tier.max_applications) {
            features.push(`Up to ${tier.max_applications} applications`);
        } else {
            features.push('Unlimited applications');
        }

        if (tier.featured) {
            features.push('Featured placement');
            features.push('Priority in search results');
        }

        if (tier.has_analytics) {
            features.push('Advanced analytics dashboard');
            features.push('Applicant insights & reports');
        }

        return features;
    };

    const isUpgrade = currentSubscription && currentSubscription.job_tier_id !== selectedTier.id;
    const isDowngrade = currentSubscription && currentSubscription.job_tier_id > selectedTier.id;

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title={`Order Summary: ${jobListing.title}`} />

            <div className="py-6">
                <div className="mx-auto max-w-4xl">
                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center gap-3 mb-4">
                            <CreditCard className="h-6 w-6 text-blue-600" />
                            <h1 className="text-2xl font-bold">Order Summary</h1>
                        </div>
                        <p className="text-muted-foreground">
                            Review your order details and publish your job listing
                        </p>
                    </div>

                    <div className="grid gap-6 lg:grid-cols-3">
                        {/* Order Details */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Job Listing Summary */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Package className="h-5 w-5" />
                                        Job Listing
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <h3 className="font-medium">{jobListing.title}</h3>
                                        <p className="text-sm text-muted-foreground">{jobListing.company.name}</p>
                                        <div className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {jobListing.status.label}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Selected Package */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        {getTierIcon(selectedTier.name)}
                                        Selected Package
                                    </CardTitle>
                                    {selectedTier.name === 'Professional' && (
                                        <CardDescription className="flex items-center gap-1">
                                            <Star className="h-3 w-3" />
                                            Most Popular Choice
                                        </CardDescription>
                                    )}
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="font-medium">{selectedTier.name} Package</h3>
                                                <p className="text-sm text-muted-foreground">{selectedTier.description}</p>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-2xl font-bold">
                                                    CHF {selectedTier.price.toFixed(0)}
                                                </div>
                                                <div className="text-sm text-muted-foreground">
                                                    {selectedTier.duration_days} days
                                                </div>
                                            </div>
                                        </div>

                                        <div className="border-t pt-4">
                                            <h4 className="font-medium mb-2">Package includes:</h4>
                                            <div className="space-y-2">
                                                {getTierFeatures(selectedTier).map((feature, index) => (
                                                    <div key={index} className="flex items-center gap-2">
                                                        <CheckCircle2 className="w-4 h-4 text-green-500 flex-shrink-0" />
                                                        <span className="text-sm">{feature}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Current Subscription Info (if applicable) */}
                            {currentSubscription && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Calendar className="h-5 w-5" />
                                            {isUpgrade ? 'Package Upgrade' : isDowngrade ? 'Package Change' : 'Current Package'}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="space-y-2">
                                            {isUpgrade && (
                                                <div className="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <p className="text-sm text-blue-800">
                                                        <strong>Upgrading:</strong> Your job listing will immediately benefit from the enhanced features of the {selectedTier.name} package.
                                                    </p>
                                                </div>
                                            )}
                                            {isDowngrade && (
                                                <div className="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <p className="text-sm text-yellow-800">
                                                        <strong>Package Change:</strong> Your current package will be replaced with the {selectedTier.name} package.
                                                    </p>
                                                </div>
                                            )}
                                            {!isUpgrade && !isDowngrade && (
                                                <div className="p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <p className="text-sm text-green-800">
                                                        <strong>Renewal:</strong> Extending your current {selectedTier.name} package.
                                                    </p>
                                                </div>
                                            )}
                                            <p className="text-sm text-muted-foreground">
                                                Current subscription expires: {new Date(currentSubscription.expires_at).toLocaleDateString()}
                                            </p>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}
                        </div>

                        {/* Order Summary Sidebar */}
                        <div className="space-y-6">
                            {/* Price Summary */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Order Total</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span>{selectedTier.name} Package</span>
                                            <span>CHF {selectedTier.price.toFixed(0)}</span>
                                        </div>
                                        <div className="flex justify-between text-sm text-muted-foreground">
                                            <span>Duration</span>
                                            <span>{selectedTier.duration_days} days</span>
                                        </div>
                                        <div className="border-t pt-3">
                                            <div className="flex justify-between font-bold text-lg">
                                                <span>Total</span>
                                                <span>CHF {selectedTier.price.toFixed(0)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Trust Signals */}
                            <Card>
                                <CardContent className="pt-6">
                                    <div className="space-y-3 text-center">
                                        <CheckCircle2 className="h-8 w-8 text-green-500 mx-auto" />
                                        <div className="space-y-1">
                                            <p className="font-medium">30-day money-back guarantee</p>
                                            <p className="text-xs text-muted-foreground">
                                                Cancel anytime • Secure payment • Instant activation
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Action Buttons */}
                            <div className="space-y-3">
                                <Button
                                    onClick={handlePublish}
                                    disabled={isPublishing}
                                    className="w-full h-12 text-base"
                                    size="lg"
                                >
                                    {isPublishing ? (
                                        <>
                                            <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" />
                                            Publishing...
                                        </>
                                    ) : (
                                        <>
                                            <CheckCircle2 className="h-5 w-5 mr-2" />
                                            Publish Job Listing
                                        </>
                                    )}
                                </Button>
                                
                                <Button
                                    variant="outline"
                                    onClick={handleBack}
                                    disabled={isPublishing}
                                    className="w-full gap-2"
                                >
                                    <ArrowLeft className="h-4 w-4" />
                                    Back to Package Selection
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}