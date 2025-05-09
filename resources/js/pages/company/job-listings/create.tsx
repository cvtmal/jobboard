import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import { useAppearance } from '@/hooks/use-appearance';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';
import { ApplicationLanguage } from '@/types/enums/ApplicationLanguage';
import { CustomEmploymentType } from '@/types/enums/CustomEmploymentType';
import { JobStatus } from '@/types/enums/JobStatus';
import { SalaryPeriod } from '@/types/enums/SalaryPeriod';
import { SeniorityLevel } from '@/types/enums/SeniorityLevel';
import { Workplace } from '@/types/enums/Workplace';
import { type Auth } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

interface Props {
  auth: Auth;
  errors: Record<string, string>;
  categoryOptions: Record<string, string>;
}

export default function CreateJobListing({ auth, errors, categoryOptions }: Props) {
  const { appearance } = useAppearance();

  const { data, setData, post, processing } = useForm({
    // Basic job information
    title: '',
    company_description: '',
    description: '',
    requirements: '',
    benefits: '',
    final_words: '',

    // Location information
    workplace: Workplace.ONSITE,
    office_location: auth.company?.city || '',

    // Application details
    application_language: ApplicationLanguage.ENGLISH,
    category: '',
    employment_type: CustomEmploymentType.EMPLOYEE,
    seniority_level: SeniorityLevel.MID_LEVEL,

    // Salary information
    salary_min: '',
    salary_max: '',
    salary_period: SalaryPeriod.YEARLY,

    // Additional fields
    skills: '',

    // Hidden fields for form processing
    application_process: ApplicationProcess.EMAIL,
    status: JobStatus.PUBLISHED,
    company_id: auth.company?.id || '',
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();

    if (!auth.company) {
      window.location.href = route('company.login');
      return;
    }

    post(route('company.job-listings.store'), {
      onSuccess: () => {
        window.location.href = route('company.job-listings.index');
      },
    });
  };

  return (
    <CompanyLayout>
      <Head title="Create Job Listing" />

      <div className="py-8">
        <div className="mx-auto max-w-5xl">
          <div className="mb-8">
            <h1 className="text-3xl font-bold tracking-tight">Create New Job Listing</h1>
            <p className="mt-2 text-muted-foreground">
              Fill in the details below to create your job listing and start attracting candidates.
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-8">
            {/* Job Information Section */}
            <Card>
              <CardHeader>
                <CardTitle>Job Information</CardTitle>
                <CardDescription>
                  Basic information about the job position
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
                  <Label htmlFor="company_description" className="text-base">
                    Company Description
                  </Label>
                  <Textarea
                    id="company_description"
                    value={data.company_description}
                    onChange={(e) => setData('company_description', e.target.value)}
                    className="mt-1.5 min-h-[100px]"
                    placeholder="Tell candidates about your company..."
                  />
                  {errors.company_description && <p className="mt-1 text-sm text-red-500">{errors.company_description}</p>}
                </div>

                <div>
                  <Label htmlFor="description" className="text-base">
                    Job Description <span className="text-red-500">*</span>
                  </Label>
                  <Textarea
                    id="description"
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    className="mt-1.5 min-h-[150px]"
                    placeholder="Describe the role, responsibilities, and expectations..."
                    required
                  />
                  {errors.description && <p className="mt-1 text-sm text-red-500">{errors.description}</p>}
                </div>

                <div>
                  <Label htmlFor="requirements" className="text-base">
                    Requirements <span className="text-red-500">*</span>
                  </Label>
                  <Textarea
                    id="requirements"
                    value={data.requirements}
                    onChange={(e) => setData('requirements', e.target.value)}
                    className="mt-1.5 min-h-[150px]"
                    placeholder="List the qualifications, skills, and experience required..."
                    required
                  />
                  {errors.requirements && <p className="mt-1 text-sm text-red-500">{errors.requirements}</p>}
                </div>

                <div>
                  <Label htmlFor="benefits" className="text-base">
                    Benefits
                  </Label>
                  <Textarea
                    id="benefits"
                    value={data.benefits}
                    onChange={(e) => setData('benefits', e.target.value)}
                    className="mt-1.5 min-h-[100px]"
                    placeholder="Describe what you offer (e.g., flexible hours, remote work, health insurance)..."
                  />
                  {errors.benefits && <p className="mt-1 text-sm text-red-500">{errors.benefits}</p>}
                </div>

                <div>
                  <Label htmlFor="final_words" className="text-base">
                    A Few Final Words
                  </Label>
                  <Textarea
                    id="final_words"
                    value={data.final_words}
                    onChange={(e) => setData('final_words', e.target.value)}
                    className="mt-1.5 min-h-[100px]"
                    placeholder="Add any closing remarks or application instructions..."
                  />
                  {errors.final_words && <p className="mt-1 text-sm text-red-500">{errors.final_words}</p>}
                </div>
              </CardContent>
            </Card>

            {/* Job Location Section */}
            <Card>
              <CardHeader>
                <CardTitle>Job Location</CardTitle>
                <CardDescription>
                  Where the job will be performed
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <Label htmlFor="workplace" className="text-base">
                    Job Location Type <span className="text-red-500">*</span>
                  </Label>
                  <Select
                    value={data.workplace}
                    onValueChange={(value) => setData('workplace', value as Workplace)}
                  >
                    <SelectTrigger id="workplace" className="mt-1.5">
                      <SelectValue placeholder="Select location type" />
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
                  <Label htmlFor="office_location" className="text-base">
                    Office Location <span className="text-red-500">*</span>
                  </Label>
                  <Input
                    id="office_location"
                    value={data.office_location}
                    onChange={(e) => setData('office_location', e.target.value)}
                    className="mt-1.5"
                    placeholder="City or location"
                    required
                  />
                  {errors.office_location && <p className="mt-1 text-sm text-red-500">{errors.office_location}</p>}
                </div>
              </CardContent>
            </Card>

            {/* Job Details Section */}
            <Card>
              <CardHeader>
                <CardTitle>Job Details</CardTitle>
                <CardDescription>
                  Categorization and specifics of the position
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <Label htmlFor="application_language" className="text-base">
                    Application Form Language <span className="text-red-500">*</span>
                  </Label>
                  <Select
                    value={data.application_language}
                    onValueChange={(value) => setData('application_language', value as ApplicationLanguage)}
                  >
                    <SelectTrigger id="application_language" className="mt-1.5">
                      <SelectValue placeholder="Select language" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={ApplicationLanguage.ENGLISH}>English</SelectItem>
                      <SelectItem value={ApplicationLanguage.GERMAN}>German</SelectItem>
                      <SelectItem value={ApplicationLanguage.FRENCH}>French</SelectItem>
                      <SelectItem value={ApplicationLanguage.ITALIAN}>Italian</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.application_language && <p className="mt-1 text-sm text-red-500">{errors.application_language}</p>}
                </div>

                <div>
                  <Label htmlFor="category" className="text-base">
                    Job Category <span className="text-red-500">*</span>
                  </Label>
                  <Select
                    value={data.category}
                    onValueChange={(value) => setData('category', value)}
                  >
                    <SelectTrigger id="category" className="mt-1.5">
                      <SelectValue placeholder="Select job category" />
                    </SelectTrigger>
                    <SelectContent>
                      {Object.entries(categoryOptions).map(([value, label]) => (
                        <SelectItem key={value} value={value}>{label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {errors.category && <p className="mt-1 text-sm text-red-500">{errors.category}</p>}
                </div>

                <div>
                  <Label htmlFor="employment_type" className="text-base">
                    Employment Type <span className="text-red-500">*</span>
                  </Label>
                  <Select
                    value={data.employment_type}
                    onValueChange={(value) => setData('employment_type', value as CustomEmploymentType)}
                  >
                    <SelectTrigger id="employment_type" className="mt-1.5">
                      <SelectValue placeholder="Select employment type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={CustomEmploymentType.EMPLOYEE}>Employee</SelectItem>
                      <SelectItem value={CustomEmploymentType.INTERIM}>Interim</SelectItem>
                      <SelectItem value={CustomEmploymentType.APPRENTICESHIP}>Apprenticeship</SelectItem>
                      <SelectItem value={CustomEmploymentType.INTERNSHIP}>Internship</SelectItem>
                      <SelectItem value={CustomEmploymentType.WORKING_STUDENT}>Working Student</SelectItem>
                      <SelectItem value={CustomEmploymentType.TRAINEESHIP}>Traineeship</SelectItem>
                      <SelectItem value={CustomEmploymentType.SIDE_JOB}>Side Job</SelectItem>
                      <SelectItem value={CustomEmploymentType.FREELANCE}>Freelance</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.employment_type && <p className="mt-1 text-sm text-red-500">{errors.employment_type}</p>}
                </div>

                <div>
                  <Label htmlFor="seniority_level" className="text-base">
                    Seniority Level
                  </Label>
                  <Select
                    value={data.seniority_level}
                    onValueChange={(value) => setData('seniority_level', value as SeniorityLevel)}
                  >
                    <SelectTrigger id="seniority_level" className="mt-1.5">
                      <SelectValue placeholder="Select seniority level" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={SeniorityLevel.NO_EXPERIENCE}>No Experience/Student</SelectItem>
                      <SelectItem value={SeniorityLevel.JUNIOR}>Junior</SelectItem>
                      <SelectItem value={SeniorityLevel.MID_LEVEL}>Mid-Level</SelectItem>
                      <SelectItem value={SeniorityLevel.PROFESSIONAL}>Professional/Experienced</SelectItem>
                      <SelectItem value={SeniorityLevel.SENIOR}>Senior</SelectItem>
                      <SelectItem value={SeniorityLevel.LEAD}>Lead</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.seniority_level && <p className="mt-1 text-sm text-red-500">{errors.seniority_level}</p>}
                </div>
              </CardContent>
            </Card>

            {/* Salary Section */}
            <Card>
              <CardHeader>
                <CardTitle>Salary Information</CardTitle>
                <CardDescription>
                  Optional compensation details
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label htmlFor="salary_min" className="text-base">
                      Minimum Salary
                    </Label>
                    <Input
                      id="salary_min"
                      type="number"
                      value={data.salary_min}
                      onChange={(e) => setData('salary_min', e.target.value)}
                      className="mt-1.5"
                      placeholder="Optional"
                    />
                    {errors.salary_min && <p className="mt-1 text-sm text-red-500">{errors.salary_min}</p>}
                  </div>

                  <div>
                    <Label htmlFor="salary_max" className="text-base">
                      Maximum Salary
                    </Label>
                    <Input
                      id="salary_max"
                      type="number"
                      value={data.salary_max}
                      onChange={(e) => setData('salary_max', e.target.value)}
                      className="mt-1.5"
                      placeholder="Optional"
                    />
                    {errors.salary_max && <p className="mt-1 text-sm text-red-500">{errors.salary_max}</p>}
                  </div>
                </div>

                <div>
                  <Label htmlFor="salary_period" className="text-base">
                    Salary Period
                  </Label>
                  <Select
                    value={data.salary_period}
                    onValueChange={(value) => setData('salary_period', value as SalaryPeriod)}
                  >
                    <SelectTrigger id="salary_period" className="mt-1.5">
                      <SelectValue placeholder="Select salary period" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={SalaryPeriod.HOURLY}>Per Hour</SelectItem>
                      <SelectItem value={SalaryPeriod.DAILY}>Per Day</SelectItem>
                      <SelectItem value={SalaryPeriod.WEEKLY}>Per Week</SelectItem>
                      <SelectItem value={SalaryPeriod.MONTHLY}>Per Month</SelectItem>
                      <SelectItem value={SalaryPeriod.YEARLY}>Per Year</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.salary_period && <p className="mt-1 text-sm text-red-500">{errors.salary_period}</p>}
                </div>
              </CardContent>
            </Card>

            {/* Skills Section */}
            <Card>
              <CardHeader>
                <CardTitle>Skills</CardTitle>
                <CardDescription>
                  Required or preferred skills for the position (optional)
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div>
                  <Label htmlFor="skills" className="text-base">
                    Skills
                  </Label>
                  <Textarea
                    id="skills"
                    value={data.skills}
                    onChange={(e) => setData('skills', e.target.value)}
                    className="mt-1.5"
                    placeholder="Enter skills separated by commas (e.g., JavaScript, React, PHP, Laravel)"
                  />
                  <p className="mt-1.5 text-sm text-muted-foreground">
                    This is a temporary implementation. Skill selection will be improved in a future update.
                  </p>
                  {errors.skills && <p className="mt-1 text-sm text-red-500">{errors.skills}</p>}
                </div>
              </CardContent>
            </Card>

            {/* Submit Section */}
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
                Create Job Listing
              </Button>
            </div>
          </form>
        </div>
      </div>
    </CompanyLayout>
  );
}
