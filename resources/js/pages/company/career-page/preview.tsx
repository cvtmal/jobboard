import { Head } from '@inertiajs/react';
import { ArrowLeft, Briefcase, Clock, Globe, MapPin, Users } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import type { BreadcrumbItem, Company, JobListing } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Career page',
        href: '/company/career-page',
    },
    {
        title: 'Preview',
        href: '/company/career-page/preview',
    },
];

interface Props {
    company: Company & {
        jobListings: JobListing[];
    };
    jobListings: JobListing[];
}

export default function CareerPagePreview({ company, jobListings }: Props) {
    const backToEdit = () => {
        window.location.href = route('company.career-page.edit');
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title={`${company.name} - Career Page Preview`} />

            {/* Header */}
            <div className="flex items-center justify-between mb-8">
                <div className="flex items-center gap-4">
                    <Button variant="outline" onClick={backToEdit}>
                        <ArrowLeft className="h-4 w-4 mr-2" />
                        Back to Edit
                    </Button>
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Career Page Preview</h1>
                        <p className="text-gray-600 mt-1">
                            This is how your career page will look to visitors
                        </p>
                    </div>
                </div>
            </div>

            {/* Preview Container */}
            <div className="bg-white border rounded-lg shadow-sm">
                {/* Company Header */}
                <div className="bg-gradient-to-r from-blue-50 to-indigo-50 p-8">
                    <div className="max-w-4xl mx-auto">
                        <div className="flex items-start gap-6">
                            {company.logo_url && (
                                <div className="flex-shrink-0">
                                    <img
                                        src={company.logo_url}
                                        alt={`${company.name} logo`}
                                        className="h-20 w-20 rounded-lg object-cover"
                                    />
                                </div>
                            )}
                            <div className="flex-1">
                                <h1 className="text-3xl font-bold text-gray-900 mb-2">
                                    {company.name}
                                </h1>
                                {company.tagline && (
                                    <p className="text-lg text-gray-700 mb-4">
                                        {company.tagline}
                                    </p>
                                )}
                                <div className="flex flex-wrap gap-4 text-sm text-gray-600">
                                    {company.location && (
                                        <div className="flex items-center gap-1">
                                            <MapPin className="h-4 w-4" />
                                            <span>{company.location}</span>
                                        </div>
                                    )}
                                    {company.company_size && (
                                        <div className="flex items-center gap-1">
                                            <Users className="h-4 w-4" />
                                            <span>{company.company_size} employees</span>
                                        </div>
                                    )}
                                    {company.website && (
                                        <div className="flex items-center gap-1">
                                            <Globe className="h-4 w-4" />
                                            <a 
                                                href={company.website}
                                                className="text-blue-600 hover:underline"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                Visit Website
                                            </a>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Company Banner/Images */}
                {company.career_page_images && company.career_page_images.length > 0 && (
                    <div className="px-8 py-6">
                        <div className="max-w-4xl mx-auto">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {company.career_page_images.slice(0, 5).map((image, index) => (
                                    <div key={index} className="aspect-video rounded-lg overflow-hidden">
                                        <img
                                            src={image.url}
                                            alt={`${company.name} workplace`}
                                            className="w-full h-full object-cover"
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {/* Company Description */}
                <div className="px-8 py-6 border-t">
                    <div className="max-w-4xl mx-auto">
                        <h2 className="text-2xl font-semibold text-gray-900 mb-4">About {company.name}</h2>
                        {company.description ? (
                            <div className="prose max-w-none text-gray-700">
                                {company.description.split('\\n').map((paragraph, index) => (
                                    <p key={index} className="mb-4">
                                        {paragraph}
                                    </p>
                                ))}
                            </div>
                        ) : (
                            <p className="text-gray-500 italic">
                                No company description provided yet.
                            </p>
                        )}
                    </div>
                </div>

                {/* Videos Section */}
                {company.career_page_videos && company.career_page_videos.length > 0 && (
                    <div className="px-8 py-6 border-t">
                        <div className="max-w-4xl mx-auto">
                            <h2 className="text-2xl font-semibold text-gray-900 mb-6">
                                Get to Know Us
                            </h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {company.career_page_videos.map((video, index) => (
                                    <div key={index} className="aspect-video rounded-lg overflow-hidden bg-gray-100">
                                        <iframe
                                            src={video.url.replace('watch?v=', 'embed/')}
                                            title={video.title || 'Company Video'}
                                            className="w-full h-full"
                                            frameBorder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {/* Job Listings */}
                <div className="px-8 py-6 border-t">
                    <div className="max-w-4xl mx-auto">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-2xl font-semibold text-gray-900">
                                Open Positions
                            </h2>
                            <span className="text-sm text-gray-500">
                                {jobListings.length} {jobListings.length === 1 ? 'position' : 'positions'} available
                            </span>
                        </div>

                        {jobListings.length > 0 ? (
                            <div className="space-y-4">
                                {jobListings.map((job) => (
                                    <Card key={job.id} className="hover:shadow-md transition-shadow">
                                        <CardContent className="p-6">
                                            <div className="flex items-start justify-between">
                                                <div className="flex-1">
                                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                                        {job.title}
                                                    </h3>
                                                    <div className="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                                                        <div className="flex items-center gap-1">
                                                            <Briefcase className="h-4 w-4" />
                                                            <span>{job.employment_type}</span>
                                                        </div>
                                                        {job.location && (
                                                            <div className="flex items-center gap-1">
                                                                <MapPin className="h-4 w-4" />
                                                                <span>{job.location}</span>
                                                            </div>
                                                        )}
                                                        {job.created_at && (
                                                            <div className="flex items-center gap-1">
                                                                <Clock className="h-4 w-4" />
                                                                <span>
                                                                    Posted {new Date(job.created_at).toLocaleDateString()}
                                                                </span>
                                                            </div>
                                                        )}
                                                    </div>
                                                    {job.summary && (
                                                        <p className="text-gray-700 text-sm line-clamp-2">
                                                            {job.summary}
                                                        </p>
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm">
                                                    View Details
                                                </Button>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12">
                                <Briefcase className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-medium text-gray-900 mb-2">
                                    No open positions
                                </h3>
                                <p className="text-gray-500">
                                    There are currently no open positions at this company.
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Spontaneous Applications */}
                {company.spontaneous_application_enabled && (
                    <div className="px-8 py-6 border-t bg-gray-50">
                        <div className="max-w-4xl mx-auto text-center">
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                Don't see the right position?
                            </h3>
                            <p className="text-gray-600 mb-4">
                                We're always looking for talented people to join our team. 
                                Send us your application and we'll keep you in mind for future opportunities.
                            </p>
                            <Button>
                                Apply Spontaneously
                            </Button>
                        </div>
                    </div>
                )}

                {/* Footer */}
                <div className="px-8 py-6 border-t bg-gray-50 text-center text-sm text-gray-500">
                    <p>
                        This is a preview of your career page. 
                        <a href={route('company.career-page.edit')} className="text-blue-600 hover:underline ml-1">
                            Go back to edit
                        </a>
                    </p>
                </div>
            </div>
        </CompanyLayout>
    );
}