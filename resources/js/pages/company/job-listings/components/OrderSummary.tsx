import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { CustomEmploymentType } from '@/types/enums/CustomEmploymentType';
import { Workplace } from '@/types/enums/Workplace';
import { 
    Calendar, 
    CheckCircle2, 
    CreditCard, 
    Eye, 
    Loader2, 
    MapPin, 
    Briefcase, 
    Clock,
    TrendingUp
} from 'lucide-react';
import type { JobTier } from './PackageSelector';

interface JobPreview {
    title: string;
    description_and_requirements: string;
    benefits: string;
    workplace: string;
    office_location: string;
    employment_type: string;
    workload_min: number;
    workload_max: number;
    skills: string;
    salary_min?: string;
    salary_max?: string;
    salary_period?: string;
}

interface OrderSummaryProps {
    jobData: JobPreview;
    selectedTier: JobTier;
    companyName: string;
    onPublish: () => void;
    onGoBack: () => void;
    isProcessing?: boolean;
    discount?: number;
    promoCode?: string;
}

export function OrderSummary({ 
    jobData, 
    selectedTier, 
    companyName,
    onPublish, 
    onGoBack, 
    isProcessing = false,
    discount = 0,
    promoCode 
}: OrderSummaryProps) {
    const subtotal = selectedTier.price;
    const discountAmount = discount > 0 ? (subtotal * discount) / 100 : 0;
    const total = subtotal - discountAmount;

    const formatEmploymentType = (type: string) => {
        const typeMap: Record<string, string> = {
            [CustomEmploymentType.PERMANENT]: 'Permanent position',
            [CustomEmploymentType.TEMPORARY]: 'Temporary employment',
            [CustomEmploymentType.FREELANCE]: 'Freelance',
            [CustomEmploymentType.INTERNSHIP]: 'Internship',
            [CustomEmploymentType.SIDE_JOB]: 'Side job',
            [CustomEmploymentType.APPRENTICESHIP]: 'Apprenticeship',
            [CustomEmploymentType.WORKING_STUDENT]: 'Working student',
            [CustomEmploymentType.INTERIM]: 'Interim',
        };
        return typeMap[type] || type;
    };

    const formatWorkplace = (workplace: string) => {
        const workplaceMap: Record<string, string> = {
            [Workplace.ONSITE]: 'On-site',
            [Workplace.HYBRID]: 'Hybrid',
            [Workplace.REMOTE]: 'Remote',
        };
        return workplaceMap[workplace] || workplace;
    };

    const getPackageFeatures = () => {
        const features = [
            `Active for ${selectedTier.duration_days} days`,
            `Up to ${selectedTier.max_active_jobs} active job${selectedTier.max_active_jobs > 1 ? 's' : ''}`,
        ];

        if (selectedTier.max_applications) {
            features.push(`Up to ${selectedTier.max_applications} applications`);
        } else {
            features.push('Unlimited applications');
        }

        if (selectedTier.featured) {
            features.push('Featured placement');
        }

        if (selectedTier.has_analytics) {
            features.push('Analytics dashboard');
        }

        return features;
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="text-center space-y-2">
                <h2 className="text-2xl font-bold">Review & Publish Your Job</h2>
                <p className="text-muted-foreground">
                    Review your job listing and complete your order to publish
                </p>
            </div>

            {/* Urgency indicator */}
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div className="flex items-center justify-center gap-2 text-blue-700">
                    <TrendingUp className="w-5 h-5" />
                    <span className="font-medium">Publish now to appear in Monday's job newsletter to 15,000+ candidates</span>
                </div>
            </div>

            <div className="grid gap-6 lg:grid-cols-2">
                {/* Job Preview */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Eye className="w-5 h-5" />
                            Job Preview
                        </CardTitle>
                        <CardDescription>How your job will appear to candidates</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {/* Job Title & Company */}
                        <div>
                            <h3 className="text-xl font-semibold">{jobData.title}</h3>
                            <p className="text-muted-foreground">{companyName}</p>
                        </div>

                        {/* Key details */}
                        <div className="flex flex-wrap gap-2">
                            <Badge variant="secondary" className="flex items-center gap-1">
                                <Briefcase className="w-3 h-3" />
                                {formatEmploymentType(jobData.employment_type)}
                            </Badge>
                            <Badge variant="secondary" className="flex items-center gap-1">
                                <MapPin className="w-3 h-3" />
                                {formatWorkplace(jobData.workplace)}
                            </Badge>
                            <Badge variant="secondary" className="flex items-center gap-1">
                                <Clock className="w-3 h-3" />
                                {jobData.workload_min}%-{jobData.workload_max}%
                            </Badge>
                            {selectedTier.featured && (
                                <Badge className="bg-orange-500 hover:bg-orange-600">
                                    Featured
                                </Badge>
                            )}
                        </div>

                        {/* Location */}
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <MapPin className="w-4 h-4" />
                            {jobData.office_location}
                        </div>

                        {/* Salary (if provided) */}
                        {(jobData.salary_min || jobData.salary_max) && (
                            <div className="flex items-center gap-2 text-sm font-medium">
                                <CreditCard className="w-4 h-4" />
                                {jobData.salary_min && jobData.salary_max 
                                    ? `CHF ${jobData.salary_min} - ${jobData.salary_max}` 
                                    : jobData.salary_min 
                                        ? `From CHF ${jobData.salary_min}`
                                        : `Up to CHF ${jobData.salary_max}`
                                }
                                {jobData.salary_period && ` ${jobData.salary_period.toLowerCase()}`}
                            </div>
                        )}

                        {/* Description preview */}
                        <div>
                            <h4 className="font-medium mb-2">Job Description</h4>
                            <div className="text-sm text-muted-foreground line-clamp-4 bg-gray-50 rounded p-3">
                                {jobData.description_and_requirements.substring(0, 200)}
                                {jobData.description_and_requirements.length > 200 && '...'}
                            </div>
                        </div>

                        {/* Skills */}
                        {jobData.skills && (
                            <div>
                                <h4 className="font-medium mb-2">Required Skills</h4>
                                <p className="text-sm text-muted-foreground">{jobData.skills}</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Order Summary */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <CreditCard className="w-5 h-5" />
                            Order Summary
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {/* Selected Package */}
                        <div className="space-y-3">
                            <div className="flex items-center justify-between">
                                <span className="font-medium">{selectedTier.name} Package</span>
                                <span className="font-medium">CHF {selectedTier.price.toFixed(2)}</span>
                            </div>
                            
                            {/* Package features */}
                            <div className="space-y-2">
                                {getPackageFeatures().map((feature, index) => (
                                    <div key={index} className="flex items-center gap-2 text-sm text-muted-foreground">
                                        <CheckCircle2 className="w-3 h-3 text-green-500" />
                                        {feature}
                                    </div>
                                ))}
                            </div>
                        </div>

                        <Separator />

                        {/* Pricing breakdown */}
                        <div className="space-y-2">
                            <div className="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span>CHF {subtotal.toFixed(2)}</span>
                            </div>
                            
                            {discount > 0 && (
                                <div className="flex justify-between text-sm text-green-600">
                                    <span>
                                        Discount {promoCode && `(${promoCode})`} -{discount}%
                                    </span>
                                    <span>-CHF {discountAmount.toFixed(2)}</span>
                                </div>
                            )}
                            
                            <div className="flex justify-between text-sm">
                                <span>VAT (8.1%)</span>
                                <span>CHF {(total * 0.081).toFixed(2)}</span>
                            </div>
                        </div>

                        <Separator />

                        <div className="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>CHF {(total + (total * 0.081)).toFixed(2)}</span>
                        </div>

                        {/* Publication timeline */}
                        <div className="bg-green-50 border border-green-200 rounded p-3 space-y-2">
                            <div className="flex items-center gap-2 text-green-700 font-medium">
                                <Calendar className="w-4 h-4" />
                                Publication Timeline
                            </div>
                            <div className="text-sm text-green-600">
                                <div>• Job goes live immediately after payment</div>
                                <div>• Appears in search results within 5 minutes</div>
                                <div>• Included in next newsletter (Monday mornings)</div>
                            </div>
                        </div>

                        {/* Action buttons */}
                        <div className="space-y-3 pt-4">
                            <Button 
                                onClick={onPublish}
                                disabled={isProcessing}
                                size="lg"
                                className="w-full bg-green-600 hover:bg-green-700"
                            >
                                {isProcessing ? (
                                    <>
                                        <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                        Publishing...
                                    </>
                                ) : (
                                    <>
                                        <CreditCard className="mr-2 h-4 w-4" />
                                        Publish Job & Pay CHF {(total + (total * 0.081)).toFixed(2)}
                                    </>
                                )}
                            </Button>

                            <Button
                                variant="outline"
                                onClick={onGoBack}
                                disabled={isProcessing}
                                className="w-full"
                            >
                                Back to Package Selection
                            </Button>
                        </div>

                        {/* Trust signals */}
                        <div className="text-center text-xs text-muted-foreground pt-2">
                            <div>🔒 Secure payment • 30-day money-back guarantee</div>
                            <div>Cancel anytime • Priority customer support</div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}