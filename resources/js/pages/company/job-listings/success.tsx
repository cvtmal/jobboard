import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import { type Auth, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { 
    ArrowRight, 
    Building2, 
    Calendar, 
    CheckCircle2,
    Clock,
    Download,
    Eye,
    ExternalLink,
    Linkedin,
    MapPin,
    Package,
    Share2,
    Sparkles,
    Users,
    Zap
} from 'lucide-react';
import { useState, useEffect } from 'react';

interface JobTier {
    id: number;
    name: string;
    price: number;
    duration_days: number;
    features?: string[];
}

interface JobListing {
    id: number;
    title: string;
    description: string;
    workplace: string;
    city?: string;
    employment_type: {
        value: string;
        label: string;
    };
    experience_level: {
        value: string;
        label: string;
    };
    status: {
        value: string;
        label: string;
    };
    company: {
        id: number;
        name: string;
        logo_url?: string;
    };
    job_tier?: JobTier;
    created_at: string;
    updated_at: string;
    active_until?: string;
}

interface SuccessData {
    tier_id?: number;
    published_at?: string;
}

interface Props {
    auth: Auth;
    jobListing: JobListing;
    successData?: SuccessData;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings',
        href: '/company/job-listings',
    },
    {
        title: 'Success',
        href: '#',
    },
];

export default function PublishSuccess({ auth, jobListing, successData }: Props) {
    const [showConfetti, setShowConfetti] = useState(true);

    useEffect(() => {
        // Hide confetti animation after 3 seconds
        const timer = setTimeout(() => {
            setShowConfetti(false);
        }, 3000);
        return () => clearTimeout(timer);
    }, []);

    const handleViewListing = () => {
        router.visit(route('company.job-listings.show', jobListing.id));
    };

    const handleViewPublicListing = () => {
        window.open(route('jobs.show', jobListing.id), '_blank');
    };

    const handleBackToDashboard = () => {
        router.visit(route('company.job-listings.index'));
    };

    const handleShareLinkedIn = () => {
        const url = route('jobs.show', jobListing.id);
        const text = `We're hiring! Check out our new ${jobListing.title} position at ${jobListing.company.name}.`;
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&title=${encodeURIComponent(text)}`, '_blank');
    };

    const handleCopyLink = () => {
        const url = route('jobs.show', jobListing.id);
        navigator.clipboard.writeText(url);
        // You could add a toast notification here
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return 'Just now';
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getDaysActive = () => {
        return jobListing.job_tier?.duration_days || 30;
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Successfully Published!" />

            <div className="py-6">
                <div className="mx-auto max-w-4xl">
                    {/* Success Hero Section */}
                    <Card className="border-green-200 bg-gradient-to-br from-green-50 to-white mb-8 relative overflow-hidden">
                        {showConfetti && (
                            <div className="absolute inset-0 pointer-events-none">
                                <Sparkles className="absolute top-4 left-8 h-6 w-6 text-yellow-400 animate-pulse" />
                                <Sparkles className="absolute top-12 right-12 h-4 w-4 text-green-400 animate-pulse delay-150" />
                                <Sparkles className="absolute bottom-8 left-1/3 h-5 w-5 text-blue-400 animate-pulse delay-300" />
                            </div>
                        )}
                        
                        <CardHeader className="text-center pb-8">
                            <div className="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6 animate-scale-in">
                                <CheckCircle2 className="h-10 w-10 text-green-600" />
                            </div>
                            
                            <CardTitle className="text-3xl font-bold mb-3">
                                Congratulations! Your Job is Live! 🎉
                            </CardTitle>
                            
                            <CardDescription className="text-lg">
                                "{jobListing.title}" is now visible to thousands of qualified candidates
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    {/* Quick Stats Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <Card className="border-blue-100">
                            <CardContent className="pt-6">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 bg-blue-100 rounded-lg">
                                        <Eye className="h-5 w-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Expected Views</p>
                                        <p className="text-lg font-semibold">5,000+</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-purple-100">
                            <CardContent className="pt-6">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 bg-purple-100 rounded-lg">
                                        <Users className="h-5 w-5 text-purple-600" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Candidate Reach</p>
                                        <p className="text-lg font-semibold">10,000+</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-green-100">
                            <CardContent className="pt-6">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 bg-green-100 rounded-lg">
                                        <Calendar className="h-5 w-5 text-green-600" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Active For</p>
                                        <p className="text-lg font-semibold">{getDaysActive()} days</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-amber-100">
                            <CardContent className="pt-6">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 bg-amber-100 rounded-lg">
                                        <Zap className="h-5 w-5 text-amber-600" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Package</p>
                                        <p className="text-lg font-semibold">{jobListing.job_tier?.name || 'Standard'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Details and Next Steps */}
                    <div className="grid md:grid-cols-2 gap-6 mb-8">
                        {/* Purchase Confirmation */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Package className="h-5 w-5" />
                                    Purchase Confirmation
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-3">
                                    <div className="flex justify-between items-center py-2 border-b">
                                        <span className="text-sm text-muted-foreground">Job Title</span>
                                        <span className="text-sm font-medium">{jobListing.title}</span>
                                    </div>
                                    <div className="flex justify-between items-center py-2 border-b">
                                        <span className="text-sm text-muted-foreground">Package</span>
                                        <span className="text-sm font-medium">{jobListing.job_tier?.name || 'Standard'}</span>
                                    </div>
                                    {jobListing.job_tier?.price && (
                                        <div className="flex justify-between items-center py-2 border-b">
                                            <span className="text-sm text-muted-foreground">Amount Paid</span>
                                            <span className="text-sm font-medium">CHF {jobListing.job_tier.price}</span>
                                        </div>
                                    )}
                                    <div className="flex justify-between items-center py-2 border-b">
                                        <span className="text-sm text-muted-foreground">Published At</span>
                                        <span className="text-sm font-medium">{formatDate(successData?.published_at)}</span>
                                    </div>
                                    <div className="flex justify-between items-center py-2">
                                        <span className="text-sm text-muted-foreground">Active Until</span>
                                        <span className="text-sm font-medium">
                                            {jobListing.active_until ? formatDate(jobListing.active_until) : `${getDaysActive()} days from now`}
                                        </span>
                                    </div>
                                </div>
                                
                                <Button variant="outline" className="w-full gap-2" disabled>
                                    <Download className="h-4 w-4" />
                                    Download Receipt
                                </Button>
                            </CardContent>
                        </Card>

                        {/* What Happens Next */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="h-5 w-5" />
                                    What Happens Next?
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="flex gap-3">
                                        <div className="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span className="text-xs font-semibold text-blue-600">1</span>
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium">Within 5 minutes</p>
                                            <p className="text-sm text-muted-foreground">Your listing appears in search results</p>
                                        </div>
                                    </div>
                                    
                                    <div className="flex gap-3">
                                        <div className="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span className="text-xs font-semibold text-blue-600">2</span>
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium">Within 24 hours</p>
                                            <p className="text-sm text-muted-foreground">First applications expected to arrive</p>
                                        </div>
                                    </div>
                                    
                                    <div className="flex gap-3">
                                        <div className="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span className="text-xs font-semibold text-blue-600">3</span>
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium">Within 48 hours</p>
                                            <p className="text-sm text-muted-foreground">Peak visibility period begins</p>
                                        </div>
                                    </div>
                                    
                                    <div className="flex gap-3">
                                        <div className="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <CheckCircle2 className="h-4 w-4 text-green-600" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium">Ongoing</p>
                                            <p className="text-sm text-muted-foreground">We'll notify you of new applications via email</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-6 p-4 bg-blue-50 rounded-lg">
                                    <p className="text-sm font-medium text-blue-900 mb-1">💡 Pro Tip</p>
                                    <p className="text-sm text-blue-700">
                                        Share your job listing on social media to increase visibility and attract more qualified candidates.
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Action Buttons */}
                    <div className="space-y-4">
                        <div className="flex flex-col sm:flex-row gap-4">
                            <Button 
                                size="lg" 
                                className="flex-1 gap-2"
                                onClick={handleViewListing}
                            >
                                View Job Details
                                <ArrowRight className="h-4 w-4" />
                            </Button>
                            
                            <Button 
                                size="lg" 
                                variant="outline"
                                className="flex-1 gap-2"
                                onClick={handleViewPublicListing}
                            >
                                <Eye className="h-4 w-4" />
                                View Live Listing
                                <ExternalLink className="h-3 w-3" />
                            </Button>
                        </div>

                        <div className="flex flex-col sm:flex-row gap-4">
                            <Button 
                                variant="secondary"
                                className="flex-1 gap-2"
                                onClick={handleShareLinkedIn}
                            >
                                <Linkedin className="h-4 w-4" />
                                Share on LinkedIn
                            </Button>
                            
                            <Button 
                                variant="secondary"
                                className="flex-1 gap-2"
                                onClick={handleCopyLink}
                            >
                                <Share2 className="h-4 w-4" />
                                Copy Link
                            </Button>
                        </div>

                        <div className="text-center pt-4">
                            <Button 
                                variant="ghost"
                                onClick={handleBackToDashboard}
                                className="text-muted-foreground"
                            >
                                Back to Dashboard
                            </Button>
                        </div>
                    </div>

                    {/* Footer Message */}
                    <div className="mt-12 text-center">
                        <p className="text-sm text-muted-foreground">
                            Join 500+ companies successfully hiring through our platform
                        </p>
                    </div>
                </div>
            </div>

            <style>{`
                @keyframes scale-in {
                    0% {
                        transform: scale(0);
                        opacity: 0;
                    }
                    50% {
                        transform: scale(1.1);
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
                
                .animate-scale-in {
                    animation: scale-in 0.5s ease-out;
                }
            `}</style>
        </CompanyLayout>
    );
}