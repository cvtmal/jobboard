import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import CompanyLayout from '@/layouts/company-layout';
import { useAppearance } from '@/hooks/use-appearance';
import { type Auth } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Edit, Share, Download, MessageSquare, CalendarDays, MapPin, Briefcase, Clock, DollarSign } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { format } from 'date-fns';
import { SafeHtml } from '@/components/ui/safe-html';

interface JobListing {
  id: number;
  title: string;
  description: string;
  status: string;
  workplace: string;
  employment_type: string;
  city: string;
  created_at: string;
  updated_at: string;
  salary_min: number | null;
  salary_max: number | null;
  salary_type: string | null;
  applications_count: number;
  experience_level: string | null;
}

interface Props {
  auth: Auth;
  jobListing: JobListing;
  categoryLabel: string | null;
}

export default function JobListingShow({ auth, jobListing, categoryLabel }: Props) {
  const { appearance } = useAppearance();
  const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'published':
        return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
      case 'draft':
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
      case 'closed':
        return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
      default:
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
    }
  };

  const formatSalary = () => {
    if (!jobListing.salary_min && !jobListing.salary_max) {
      return 'Not specified';
    }

    const formatNumber = (num: number) => {
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'CHF', maximumFractionDigits: 0 }).format(num);
    };

    const period = jobListing.salary_type ? ` ${jobListing.salary_type.toLowerCase()}` : '';

    if (jobListing.salary_min && jobListing.salary_max) {
      return `${formatNumber(jobListing.salary_min)} - ${formatNumber(jobListing.salary_max)}${period}`;
    } else if (jobListing.salary_min) {
      return `From ${formatNumber(jobListing.salary_min)}${period}`;
    } else if (jobListing.salary_max) {
      return `Up to ${formatNumber(jobListing.salary_max)}${period}`;
    }
  };

  const formatEmploymentType = (type: string) => {
    return type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
  };

  const formatExperienceLevel = (level: string | null) => {
    if (!level) return 'Not specified';
    return level.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
  };

  const formatWorkplace = (workplace: string) => {
    return workplace.charAt(0).toUpperCase() + workplace.slice(1);
  };

  return (
    <CompanyLayout>
      <Head title={jobListing.title} />

      <div className="py-8">
        <div className="mx-auto max-w-5xl">
          <div className="mb-6">
            <Button variant="outline" asChild>
              <Link href={route('company.job-listings.index')}>
                <ArrowLeft className="mr-2 h-4 w-4" />
                Back to Listings
              </Link>
            </Button>
          </div>

          <div className="mb-8 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
              <h1 className="text-3xl font-bold tracking-tight">{jobListing.title}</h1>
              <div className="mt-2 flex flex-wrap items-center gap-2">
                <Badge variant="outline" className={getStatusColor(jobListing.status)}>
                  {jobListing.status.charAt(0).toUpperCase() + jobListing.status.slice(1)}
                </Badge>
                {categoryLabel && (
                  <Badge variant="outline">{categoryLabel}</Badge>
                )}
              </div>
            </div>
            <div className="flex flex-wrap gap-2">
              <Button asChild variant="outline">
                <Link href={route('company.job-listings.edit', jobListing.id)}>
                  <Edit className="mr-2 h-4 w-4" />
                  Edit
                </Link>
              </Button>
              <Button variant="outline">
                <Share className="mr-2 h-4 w-4" />
                Share
              </Button>
              <Button variant="outline">
                <Download className="mr-2 h-4 w-4" />
                Export
              </Button>
            </div>
          </div>

          <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div className="md:col-span-2">
              <Card>
                <CardHeader>
                  <CardTitle>Job Description</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="prose dark:prose-invert max-w-none">
                    <SafeHtml content={jobListing.description} preserveLineBreaks />
                  </div>
                </CardContent>
              </Card>
            </div>

            <div className="space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle>Job Details</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex items-start">
                    <CalendarDays className="mr-3 mt-0.5 h-5 w-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">Posted</p>
                      <p className="text-sm text-muted-foreground">
                        {format(new Date(jobListing.created_at), 'MMMM d, yyyy')}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <MapPin className="mr-3 mt-0.5 h-5 w-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">Location</p>
                      <p className="text-sm text-muted-foreground">
                        {formatWorkplace(jobListing.workplace)}
                        {jobListing.city && ` • ${jobListing.city}`}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <Briefcase className="mr-3 mt-0.5 h-5 w-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">Employment</p>
                      <p className="text-sm text-muted-foreground">
                        {jobListing.employment_type 
                          ? formatEmploymentType(jobListing.employment_type) 
                          : 'Not specified'}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <Clock className="mr-3 mt-0.5 h-5 w-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">Experience</p>
                      <p className="text-sm text-muted-foreground">
                        {formatExperienceLevel(jobListing.experience_level)}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <DollarSign className="mr-3 mt-0.5 h-5 w-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">Salary</p>
                      <p className="text-sm text-muted-foreground">
                        {formatSalary()}
                      </p>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Applications</CardTitle>
                  <CardDescription>
                    {jobListing.applications_count} applications received
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <Button className="w-full" asChild>
                    <Link href={`/company/job-listings/${jobListing.id}/applications`}>
                      <MessageSquare className="mr-2 h-4 w-4" />
                      View Applications
                    </Link>
                  </Button>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </CompanyLayout>
  );
}
