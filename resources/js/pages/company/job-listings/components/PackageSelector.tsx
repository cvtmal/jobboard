import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle2, Star, TrendingUp, Zap } from 'lucide-react';

export interface JobTier {
    id: number;
    name: string;
    description: string | null;
    price: number;
    duration_days: number;
    featured: boolean;
    max_applications: number | null;
    max_active_jobs: number;
    has_analytics: boolean;
}

interface PackageSelectorProps {
    tiers: JobTier[];
    selectedTier: JobTier | null;
    onSelect: (tier: JobTier) => void;
    disabled?: boolean;
}

export function PackageSelector({ tiers, selectedTier, onSelect, disabled = false }: PackageSelectorProps) {
    const getFeatures = (tier: JobTier) => {
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

    const getPopularityBadge = (tierName: string) => {
        if (tierName === 'Professional') {
            return (
                <Badge className="absolute -top-3 left-1/2 -translate-x-1/2 bg-orange-500 hover:bg-orange-600">
                    <Star className="w-3 h-3 mr-1" />
                    Most Popular
                </Badge>
            );
        }
        return null;
    };

    const getIcon = (tierName: string) => {
        switch (tierName) {
            case 'Basic':
                return <Zap className="w-5 h-5" />;
            case 'Professional':
                return <TrendingUp className="w-5 h-5" />;
            case 'Premium':
                return <Star className="w-5 h-5" />;
            default:
                return <Zap className="w-5 h-5" />;
        }
    };

    const getSocialProof = (tierName: string) => {
        switch (tierName) {
            case 'Basic':
                return '76% of startups choose this';
            case 'Professional':
                return '87% of companies choose this';
            case 'Premium':
                return '92% of enterprise companies choose this';
            default:
                return '';
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="text-center space-y-2">
                <h2 className="text-2xl font-bold">Choose Your Job Ad Package</h2>
                <p className="text-muted-foreground">
                    Select the package that best fits your hiring needs. You can upgrade anytime.
                </p>
            </div>

            {/* Trust signals */}
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div className="flex items-center justify-center gap-2 text-blue-700">
                    <CheckCircle2 className="w-5 h-5" />
                    <span className="font-medium">30-day money-back guarantee • Publish instantly • Cancel anytime</span>
                </div>
            </div>

            {/* Pricing cards */}
            <div className="grid gap-6 md:grid-cols-3">
                {tiers.map((tier) => {
                    const isSelected = selectedTier?.id === tier.id;
                    const isProfessional = tier.name === 'Professional';
                    
                    return (
                        <Card 
                            key={tier.id}
                            className={`relative transition-all duration-200 ${
                                isSelected 
                                    ? 'ring-2 ring-blue-500 scale-105' 
                                    : isProfessional
                                        ? 'ring-2 ring-orange-200 scale-105'
                                        : 'hover:shadow-lg'
                            } ${disabled ? 'opacity-50' : ''}`}
                        >
                            {getPopularityBadge(tier.name)}
                            
                            <CardHeader className="text-center">
                                <div className={`w-12 h-12 mx-auto rounded-full flex items-center justify-center ${
                                    isProfessional ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600'
                                }`}>
                                    {getIcon(tier.name)}
                                </div>
                                <CardTitle className="text-xl">{tier.name}</CardTitle>
                                <CardDescription className="min-h-[40px]">{tier.description}</CardDescription>
                                <div className="space-y-2">
                                    <div className="text-3xl font-bold">
                                        CHF {tier.price.toFixed(0)}
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {getSocialProof(tier.name)}
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="space-y-4">
                                <div className="space-y-3">
                                    {getFeatures(tier).map((feature, index) => (
                                        <div key={index} className="flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 text-green-500 flex-shrink-0" />
                                            <span className="text-sm">{feature}</span>
                                        </div>
                                    ))}
                                </div>

                                <Button
                                    onClick={() => onSelect(tier)}
                                    disabled={disabled}
                                    variant={isSelected ? 'default' : 'outline'}
                                    className={`w-full ${
                                        isProfessional && !isSelected 
                                            ? 'border-orange-300 hover:border-orange-400 hover:bg-orange-50' 
                                            : ''
                                    }`}
                                >
                                    {isSelected ? 'Selected' : `Choose ${tier.name}`}
                                </Button>

                                {isProfessional && (
                                    <div className="text-center text-xs text-orange-600 font-medium">
                                        ⚡ Recommended for most companies
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    );
                })}
            </div>

            {/* Additional benefits */}
            <div className="bg-gray-50 rounded-lg p-4">
                <div className="text-center text-sm text-muted-foreground">
                    <strong>All packages include:</strong> Mobile-optimized job listings, social media sharing, 
                    email notifications, and priority customer support
                </div>
            </div>
        </div>
    );
}