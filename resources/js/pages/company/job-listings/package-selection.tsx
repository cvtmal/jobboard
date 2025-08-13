import { Button } from '@/components/ui/button';
import { PackageSelector, type JobTier } from './components/PackageSelector';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Package } from 'lucide-react';
import { useState, useEffect } from 'react';

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
    jobTiers: JobTier[];
    currentSubscription?: {
        id: number;
        job_tier_id: number;
        expires_at: string;
        payment_status: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings / Package Selection',
        href: '/company/job-listings',
    },
];

export default function PackageSelection({ auth, jobListing, jobTiers, currentSubscription }: Props) {
    const [selectedTier, setSelectedTier] = useState<JobTier | null>(
        currentSubscription ? jobTiers.find(t => t.id === currentSubscription.job_tier_id) || null : null
    );
    const [isProcessing, setIsProcessing] = useState(false);

    // Load previously selected tier from localStorage on mount
    useEffect(() => {
        const savedTier = localStorage.getItem(`job-listing-package-selection-${jobListing.id}`);
        if (savedTier) {
            try {
                const tierData = JSON.parse(savedTier);
                const foundTier = jobTiers.find(tier => tier.id === tierData.id);
                if (foundTier) {
                    setSelectedTier(foundTier);
                }
            } catch (error) {
                console.warn('Failed to load saved tier selection:', error);
            }
        }
    }, [jobListing.id, jobTiers]);

    // Save selected tier to localStorage whenever it changes
    useEffect(() => {
        if (selectedTier) {
            localStorage.setItem(
                `job-listing-package-selection-${jobListing.id}`, 
                JSON.stringify(selectedTier)
            );
        }
    }, [selectedTier, jobListing.id]);

    const handleBack = () => {
        router.visit(route('company.job-listings.preview', jobListing.id));
    };

    const handleContinueToOrderSummary = () => {
        if (!selectedTier) {
            return;
        }

        router.visit(route('company.job-listings.order-summary', jobListing.id), {
            data: {
                selected_tier_id: selectedTier.id
            }
        });
    };

    const handleTierSelect = (tier: JobTier) => {
        setSelectedTier(tier);
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title={`Package Selection: ${jobListing.title}`} />

            <div className="py-6">
                <div className="mx-auto max-w-6xl">
                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center gap-3 mb-4">
                            <Package className="h-6 w-6 text-blue-600" />
                            <h1 className="text-2xl font-bold">Choose Your Package</h1>
                        </div>
                        <p className="text-muted-foreground">
                            Select the package that best fits your hiring needs for "{jobListing.title}".
                            {currentSubscription && (
                                <span className="ml-2 text-blue-600">
                                    You currently have an active {jobTiers.find(t => t.id === currentSubscription.job_tier_id)?.name || 'Unknown'} package.
                                </span>
                            )}
                        </p>
                    </div>

                    {/* Current Job Info */}
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="font-medium text-blue-900">{jobListing.title}</h3>
                                <p className="text-sm text-blue-700">{jobListing.company.name}</p>
                            </div>
                            <div className="text-right">
                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {jobListing.status.label}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Package Selector Component */}
                    <div className="mb-8">
                        <PackageSelector
                            tiers={jobTiers}
                            selectedTier={selectedTier}
                            onSelect={handleTierSelect}
                            disabled={isProcessing}
                        />
                    </div>

                    {/* Action Buttons */}
                    <div className="flex items-center justify-between">
                        <Button
                            variant="outline"
                            onClick={handleBack}
                            disabled={isProcessing}
                            className="gap-2"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back to Preview
                        </Button>

                        <Button
                            onClick={handleContinueToOrderSummary}
                            disabled={!selectedTier || isProcessing}
                            className="gap-2"
                        >
                            Continue to Order Summary
                            <ArrowRight className="h-4 w-4" />
                        </Button>
                    </div>

                    {/* Help Text */}
                    {!selectedTier && (
                        <div className="mt-4 text-center text-sm text-orange-600">
                            Please select a package to continue
                        </div>
                    )}
                </div>
            </div>
        </CompanyLayout>
    );
}