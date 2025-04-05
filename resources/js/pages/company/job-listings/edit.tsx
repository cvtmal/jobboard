import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import { useAppearance } from '@/hooks/use-appearance';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { JobStatus } from '@/types/enums/JobStatus';
import { Workplace } from '@/types/enums/Workplace';
import { EmploymentType } from '@/types/enums/EmploymentType';
import { ExperienceLevel } from '@/types/enums/ExperienceLevel';
import { type Auth } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { ArrowLeft } from 'lucide-react';

interface JobListing {
  id: number;
  title: string;
  description: string;
  status: string;
  workplace: string;
  employment_type: string;
  city: string;
  address: string;
  postcode: string;
  salary_min: string | null;
  salary_max: string | null;
  salary_type: string | null;
  experience_level: string | null;
  application_process: string;
  application_email: string | null;
  application_url: string | null;
}

interface Props {
  auth: Auth;
  jobListing: JobListing;
  categoryOptions: Record<string, string>;
  errors: Record<string, string>;
}

export default function EditJobListing({ auth, jobListing, categoryOptions, errors }: Props) {
  const { appearance } = useAppearance();
  const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

  const { data, setData, put, processing } = useForm({
    title: jobListing.title,
    description: jobListing.description,
    status: jobListing.status,
    workplace: jobListing.workplace,
    employment_type: jobListing.employment_type,
    city: jobListing.city,
    address: jobListing.address || '',
    postcode: jobListing.postcode || '',
    salary_min: jobListing.salary_min || '',
    salary_max: jobListing.salary_max || '',
    salary_type: jobListing.salary_type || '',
    experience_level: jobListing.experience_level || '',
    application_process: jobListing.application_process,
    application_email: jobListing.application_email || '',
    application_url: jobListing.application_url || '',
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    put(route('company.job-listings.update', jobListing.id), {
      onSuccess: () => {
        window.location.href = route('company.job-listings.show', jobListing.id);
      },
    });
  };

  return (
    <CompanyLayout>
      <Head title={`Edit: ${jobListing.title}`} />

      <div className="py-8">
        <div className="mx-auto max-w-5xl">
          <div className="mb-6">
            <Button variant="outline" onClick={() => window.history.back()}>
              <ArrowLeft className="mr-2 h-4 w-4" />
              Back
            </Button>
          </div>

          <div className="mb-8">
            <h1 className="text-3xl font-bold tracking-tight">Edit Job Listing</h1>
            <p className="mt-2 text-muted-foreground">
              Update the details of your job listing.
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-8">
            <Card>
              <CardHeader>
                <CardTitle>Basic Information</CardTitle>
                <CardDescription>
                  Update the general information about this position
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <Label htmlFor="title" className="text-base">
                    Job Title <span className="text-red-500">*</span>
                  </Label>
                  <Input
                    id="title"
                    value={data.title}
                    onChange={(e) => setData('title', e.target.value)}
                    className="mt-1.5"
                    required
                  />
                  {errors.title && <p className="mt-1 text-sm text-red-500">{errors.title}</p>}
                </div>

                <div>
                  <Label htmlFor="description" className="text-base">
                    Job Description <span className="text-red-500">*</span>
                  </Label>
                  <Textarea
                    id="description"
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    className="mt-1.5 min-h-[200px]"
                    required
                  />
                  {errors.description && <p className="mt-1 text-sm text-red-500">{errors.description}</p>}
                </div>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div>
                    <Label htmlFor="workplace">Workplace Type</Label>
                    <Select
                      value={data.workplace}
                      onValueChange={(value) => setData('workplace', value)}
                    >
                      <SelectTrigger id="workplace" className="mt-1.5">
                        <SelectValue placeholder="Select workplace type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value={Workplace.ONSITE}>On-site</SelectItem>
                        <SelectItem value={Workplace.HYBRID}>Hybrid</SelectItem>
                        <SelectItem value={Workplace.REMOTE}>Remote</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.workplace && <p className="mt-1 text-sm text-red-500">{errors.workplace}</p>}
                  </div>

                  <div>
                    <Label htmlFor="employment_type">Employment Type</Label>
                    <Select
                      value={data.employment_type}
                      onValueChange={(value) => setData('employment_type', value)}
                    >
                      <SelectTrigger id="employment_type" className="mt-1.5">
                        <SelectValue placeholder="Select employment type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value={EmploymentType.FULL_TIME}>Full Time</SelectItem>
                        <SelectItem value={EmploymentType.PART_TIME}>Part Time</SelectItem>
                        <SelectItem value={EmploymentType.FULL_PART_TIME}>Full/Part Time</SelectItem>
                        <SelectItem value={EmploymentType.CONTRACT}>Contract</SelectItem>
                        <SelectItem value={EmploymentType.TEMPORARY}>Temporary</SelectItem>
                        <SelectItem value={EmploymentType.INTERNSHIP}>Internship</SelectItem>
                        <SelectItem value={EmploymentType.VOLUNTEER}>Volunteer</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.employment_type && <p className="mt-1 text-sm text-red-500">{errors.employment_type}</p>}
                  </div>

                  <div>
                    <Label htmlFor="experience_level">Experience Level</Label>
                    <Select
                      value={data.experience_level}
                      onValueChange={(value) => setData('experience_level', value)}
                    >
                      <SelectTrigger id="experience_level" className="mt-1.5">
                        <SelectValue placeholder="Select experience level" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value={ExperienceLevel.ENTRY}>Entry Level</SelectItem>
                        <SelectItem value={ExperienceLevel.JUNIOR}>Junior</SelectItem>
                        <SelectItem value={ExperienceLevel.MID_LEVEL}>Mid Level</SelectItem>
                        <SelectItem value={ExperienceLevel.SENIOR}>Senior</SelectItem>
                        <SelectItem value={ExperienceLevel.EXECUTIVE}>Executive</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.experience_level && <p className="mt-1 text-sm text-red-500">{errors.experience_level}</p>}
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Location</CardTitle>
                <CardDescription>
                  Update where the job will be performed
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div>
                    <Label htmlFor="address">Address</Label>
                    <Input
                      id="address"
                      value={data.address}
                      onChange={(e) => setData('address', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.address && <p className="mt-1 text-sm text-red-500">{errors.address}</p>}
                  </div>

                  <div>
                    <Label htmlFor="postcode">Postal Code</Label>
                    <Input
                      id="postcode"
                      value={data.postcode}
                      onChange={(e) => setData('postcode', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.postcode && <p className="mt-1 text-sm text-red-500">{errors.postcode}</p>}
                  </div>

                  <div>
                    <Label htmlFor="city">City</Label>
                    <Input
                      id="city"
                      value={data.city}
                      onChange={(e) => setData('city', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.city && <p className="mt-1 text-sm text-red-500">{errors.city}</p>}
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Compensation</CardTitle>
                <CardDescription>
                  Update salary information for this position
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div>
                    <Label htmlFor="salary_type">Salary Type</Label>
                    <Select
                      value={data.salary_type}
                      onValueChange={(value) => setData('salary_type', value)}
                    >
                      <SelectTrigger id="salary_type" className="mt-1.5">
                        <SelectValue placeholder="Select salary type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="hourly">Hourly</SelectItem>
                        <SelectItem value="daily">Daily</SelectItem>
                        <SelectItem value="monthly">Monthly</SelectItem>
                        <SelectItem value="yearly">Yearly</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.salary_type && <p className="mt-1 text-sm text-red-500">{errors.salary_type}</p>}
                  </div>

                  <div>
                    <Label htmlFor="salary_min">Minimum Salary</Label>
                    <Input
                      id="salary_min"
                      type="number"
                      value={data.salary_min}
                      onChange={(e) => setData('salary_min', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.salary_min && <p className="mt-1 text-sm text-red-500">{errors.salary_min}</p>}
                  </div>

                  <div>
                    <Label htmlFor="salary_max">Maximum Salary</Label>
                    <Input
                      id="salary_max"
                      type="number"
                      value={data.salary_max}
                      onChange={(e) => setData('salary_max', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.salary_max && <p className="mt-1 text-sm text-red-500">{errors.salary_max}</p>}
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Application Details</CardTitle>
                <CardDescription>
                  Update how candidates can apply for this position
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <Label htmlFor="application_process">Application Process</Label>
                  <Select
                    value={data.application_process}
                    onValueChange={(value) => setData('application_process', value)}
                  >
                    <SelectTrigger id="application_process" className="mt-1.5">
                      <SelectValue placeholder="Select application process" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={ApplicationProcess.EMAIL}>Email</SelectItem>
                      <SelectItem value={ApplicationProcess.URL}>External Website</SelectItem>
                      <SelectItem value={ApplicationProcess.BOTH}>Both Email and External Website</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.application_process && <p className="mt-1 text-sm text-red-500">{errors.application_process}</p>}
                </div>

                {data.application_process === ApplicationProcess.EMAIL && (
                  <div>
                    <Label htmlFor="application_email">Application Email</Label>
                    <Input
                      id="application_email"
                      type="email"
                      value={data.application_email}
                      onChange={(e) => setData('application_email', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.application_email && <p className="mt-1 text-sm text-red-500">{errors.application_email}</p>}
                  </div>
                )}

                {data.application_process === ApplicationProcess.URL && (
                  <div>
                    <Label htmlFor="application_url">Application URL</Label>
                    <Input
                      id="application_url"
                      type="url"
                      value={data.application_url}
                      onChange={(e) => setData('application_url', e.target.value)}
                      className="mt-1.5"
                    />
                    {errors.application_url && <p className="mt-1 text-sm text-red-500">{errors.application_url}</p>}
                  </div>
                )}
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Publishing</CardTitle>
                <CardDescription>
                  Update the status of this job listing
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div>
                  <Label htmlFor="status">Status</Label>
                  <Select
                    value={data.status}
                    onValueChange={(value) => setData('status', value)}
                  >
                    <SelectTrigger id="status" className="mt-1.5">
                      <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={JobStatus.DRAFT}>Draft</SelectItem>
                      <SelectItem value={JobStatus.PUBLISHED}>Published</SelectItem>
                      <SelectItem value={JobStatus.CLOSED}>Closed</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.status && <p className="mt-1 text-sm text-red-500">{errors.status}</p>}
                </div>
              </CardContent>
            </Card>

            <div className="flex items-center justify-end gap-4">
              <Button
                type="button"
                variant="outline"
                onClick={() => window.history.back()}
                disabled={processing}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={processing}>
                Update Job Listing
              </Button>
            </div>
          </form>
        </div>
      </div>
    </CompanyLayout>
  );
}
