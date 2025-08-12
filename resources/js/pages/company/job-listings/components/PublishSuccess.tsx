import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle2, ExternalLink, Share2 } from 'lucide-react';

interface PublishSuccessProps {
    jobTitle: string;
    jobId: number;
    companyName: string;
    selectedTier: {
        name: string;
        duration_days: number;
    };
}

export function PublishSuccess({ jobTitle, jobId, companyName, selectedTier }: PublishSuccessProps) {
    const jobUrl = `/jobs/${jobId}`;
    const shareUrl = `${window.location.origin}${jobUrl}`;
    
    const handleShare = () => {
        if (navigator.share) {
            navigator.share({
                title: `${jobTitle} at ${companyName}`,
                text: `Check out this job opportunity: ${jobTitle} at ${companyName}`,
                url: shareUrl,
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(shareUrl);
            // You might want to show a toast notification here
        }
    };

    return (
        <Card className="max-w-2xl mx-auto">
            <CardHeader className="text-center">
                <div className="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <CheckCircle2 className="w-8 h-8 text-green-600" />
                </div>
                <CardTitle className="text-2xl text-green-600">Job Published Successfully! 🎉</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6 text-center">
                {/* Job Details */}
                <div className="bg-gray-50 rounded-lg p-4">
                    <h3 className="font-semibold text-lg mb-2">{jobTitle}</h3>
                    <p className="text-muted-foreground">{companyName}</p>
                    <div className="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                        {selectedTier.name} Package - Active for {selectedTier.duration_days} days
                    </div>
                </div>

                {/* What happens next */}
                <div className="text-left space-y-3">
                    <h4 className="font-medium">What happens next:</h4>
                    <div className="space-y-2 text-sm text-muted-foreground">
                        <div className="flex items-center gap-2">
                            <CheckCircle2 className="w-4 h-4 text-green-500" />
                            Your job is now live and searchable
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckCircle2 className="w-4 h-4 text-green-500" />
                            Candidates can apply immediately
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckCircle2 className="w-4 h-4 text-green-500" />
                            You'll receive email notifications for new applications
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckCircle2 className="w-4 h-4 text-green-500" />
                            Job will be featured in Monday's newsletter (15,000+ subscribers)
                        </div>
                    </div>
                </div>

                {/* Actions */}
                <div className="flex flex-col sm:flex-row gap-3 pt-4">
                    <Button 
                        asChild 
                        className="flex-1"
                    >
                        <a href={jobUrl} target="_blank" rel="noopener noreferrer">
                            <ExternalLink className="w-4 h-4 mr-2" />
                            View Live Job Posting
                        </a>
                    </Button>
                    
                    <Button 
                        variant="outline" 
                        onClick={handleShare}
                        className="flex-1"
                    >
                        <Share2 className="w-4 h-4 mr-2" />
                        Share Job
                    </Button>
                </div>

                <div className="pt-4 border-t">
                    <Button 
                        asChild 
                        variant="outline" 
                        className="w-full"
                    >
                        <a href="/company/job-listings">
                            Back to Job Listings
                        </a>
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
}