import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { PageProps } from '@inertiajs/core';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { FormEvent } from 'react';
import CompanyLayout from '@/layouts/company/CompanyLayout';
import { SalaryType } from '@/types/enums/SalaryType';
import { EmploymentType } from '@/types/enums/EmploymentType';
import { ExperienceLevel } from '@/types/enums/ExperienceLevel';
import { JobStatus } from '@/types/enums/JobStatus';
import { ApplicationProcess } from '@/types/enums/ApplicationProcess';

interface CreateProps extends PageProps {
  errors: Record<string, string>;
  auth: {
    company: {
      id: number;
      name: string;
    } | null;
  };
}

export default function Create({ errors, auth }: CreateProps) {
  const { data, setData, post, processing } = useForm({
    title: '',
    description: '',
    address: '',
    postcode: '',
    city: '',
    salary_min: '',
    salary_max: '',
    salary_type: SalaryType.YEARLY, 
    employment_type: EmploymentType.FULL_TIME, 
    experience_level: ExperienceLevel.MID_LEVEL, 
    application_process: ApplicationProcess.EMAIL,
    application_email: '',
    application_url: '',
    status: JobStatus.PUBLISHED, 
    no_salary: false,
    company_id: auth.company?.id || '',
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    
    // Check if company is authenticated
    if (!auth.company) {
      // Redirect to company login if not authenticated
      window.location.href = route('company.login');
      return;
    }
    
    // Include the company ID from auth
    post(route('company.job-listings.store'), {
      onSuccess: () => {
        // Redirect to the job listings index on success
        window.location.href = route('company.job-listings.index');
      }
    });
  };

  const handleApplicationProcessChange = (value: string) => {
    setData('application_process', value as ApplicationProcess);
  };

  return (
    <CompanyLayout>
      <Head title="Create Job Listing" />
      
      <div className="py-6">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold tracking-tight">Create Job Listing</h1>
          <Link href={route('company.job-listings.index')}>
            <Button variant="outline">Back to Listings</Button>
          </Link>
        </div>
        
        <form onSubmit={handleSubmit}>
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Basic Information</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 gap-4">
                  <div>
                    <Label htmlFor="title">Job Title</Label>
                    <Input
                      id="title"
                      type="text"
                      value={data.title}
                      onChange={e => setData('title', e.target.value)}
                      className="mt-1"
                    />
                    {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
                  </div>
                  
                  <div>
                    <Label htmlFor="description">Job Description</Label>
                    <Textarea
                      id="description"
                      value={data.description}
                      onChange={e => setData('description', e.target.value)}
                      className="mt-1 h-32"
                    />
                    {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <Label htmlFor="address">Address</Label>
                      <Input
                        id="address"
                        type="text"
                        value={data.address}
                        onChange={e => setData('address', e.target.value)}
                        className="mt-1"
                        placeholder="Street address"
                      />
                      {errors.address && <p className="text-red-500 text-sm mt-1">{errors.address}</p>}
                    </div>
                    
                    <div>
                      <Label htmlFor="postcode">Postal Code</Label>
                      <Input
                        id="postcode"
                        type="text"
                        value={data.postcode}
                        onChange={e => setData('postcode', e.target.value)}
                        className="mt-1"
                        placeholder="Postal/ZIP code"
                      />
                      {errors.postcode && <p className="text-red-500 text-sm mt-1">{errors.postcode}</p>}
                    </div>
                    
                    <div>
                      <Label htmlFor="city">City</Label>
                      <Input
                        id="city"
                        type="text"
                        value={data.city}
                        onChange={e => setData('city', e.target.value)}
                        className="mt-1"
                        placeholder="City"
                      />
                      {errors.city && <p className="text-red-500 text-sm mt-1">{errors.city}</p>}
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader>
                <CardTitle>Job Details</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="salary_type">Salary Type</Label>
                    <Select 
                      value={data.salary_type}
                      onValueChange={value => setData('salary_type', value as SalaryType)}
                    >
                      <SelectTrigger id="salary_type" className="mt-1">
                        <SelectValue placeholder="Select salary type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value={SalaryType.HOURLY}>Hourly</SelectItem>
                        <SelectItem value={SalaryType.DAILY}>Daily</SelectItem>
                        <SelectItem value={SalaryType.MONTHLY}>Monthly</SelectItem>
                        <SelectItem value={SalaryType.YEARLY}>Yearly</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.salary_type && <p className="text-red-500 text-sm mt-1">{errors.salary_type}</p>}
                  </div>
                  
                  <div className="grid grid-cols-2 gap-2">
                    <div>
                      <Label htmlFor="salary_min">Minimum Salary</Label>
                      <Input
                        id="salary_min"
                        type="number"
                        value={data.salary_min}
                        onChange={e => setData('salary_min', e.target.value)}
                        className="mt-1"
                        placeholder="Optional"
                      />
                      {errors.salary_min && <p className="text-red-500 text-sm mt-1">{errors.salary_min}</p>}
                    </div>
                    
                    <div>
                      <Label htmlFor="salary_max">Maximum Salary</Label>
                      <Input
                        id="salary_max"
                        type="number"
                        value={data.salary_max}
                        onChange={e => setData('salary_max', e.target.value)}
                        className="mt-1"
                        placeholder="Optional"
                      />
                      {errors.salary_max && <p className="text-red-500 text-sm mt-1">{errors.salary_max}</p>}
                    </div>
                  </div>
                  
                  <div>
                    <Label htmlFor="employment_type">Employment Type</Label>
                    <Select 
                      value={data.employment_type}
                      onValueChange={value => setData('employment_type', value as EmploymentType)}
                    >
                      <SelectTrigger id="employment_type" className="mt-1">
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
                    {errors.employment_type && <p className="text-red-500 text-sm mt-1">{errors.employment_type}</p>}
                  </div>
                  
                  <div>
                    <Label htmlFor="experience_level">Experience Level</Label>
                    <Select 
                      value={data.experience_level}
                      onValueChange={value => setData('experience_level', value as ExperienceLevel)}
                    >
                      <SelectTrigger id="experience_level" className="mt-1">
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
                    {errors.experience_level && <p className="text-red-500 text-sm mt-1">{errors.experience_level}</p>}
                  </div>
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader>
                <CardTitle>Application Process</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label>How should candidates apply?</Label>
                  <RadioGroup value={data.application_process} onValueChange={handleApplicationProcessChange} className="mt-2">
                    <div className="flex items-center space-x-2">
                      <RadioGroupItem value={ApplicationProcess.EMAIL} id="application_process_email" />
                      <Label htmlFor="application_process_email">Email</Label>
                    </div>
                    <div className="flex items-center space-x-2">
                      <RadioGroupItem value={ApplicationProcess.EXTERNAL} id="application_process_external" />
                      <Label htmlFor="application_process_external">External Website</Label>
                    </div>
                    <div className="flex items-center space-x-2">
                      <RadioGroupItem value={ApplicationProcess.INTERNAL} id="application_process_internal" />
                      <Label htmlFor="application_process_internal">Internal Application Form</Label>
                    </div>
                  </RadioGroup>
                  {errors.application_process && <p className="text-red-500 text-sm mt-1">{errors.application_process}</p>}
                </div>
                
                {data.application_process === ApplicationProcess.EMAIL && (
                  <div>
                    <Label htmlFor="application_email">Application Email</Label>
                    <Input
                      id="application_email"
                      type="email"
                      value={data.application_email}
                      onChange={e => setData('application_email', e.target.value)}
                      className="mt-1"
                    />
                    {errors.application_email && <p className="text-red-500 text-sm mt-1">{errors.application_email}</p>}
                  </div>
                )}
                
                {data.application_process === ApplicationProcess.EXTERNAL && (
                  <div>
                    <Label htmlFor="application_url">Application URL</Label>
                    <Input
                      id="application_url"
                      type="url"
                      value={data.application_url}
                      onChange={e => setData('application_url', e.target.value)}
                      className="mt-1"
                      placeholder="https://"
                    />
                    {errors.application_url && <p className="text-red-500 text-sm mt-1">{errors.application_url}</p>}
                  </div>
                )}
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader>
                <CardTitle>Publishing</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label htmlFor="status">Status</Label>
                  <Select 
                    value={data.status}
                    onValueChange={value => setData('status', value as JobStatus)}
                  >
                    <SelectTrigger id="status" className="mt-1">
                      <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={JobStatus.DRAFT}>Draft</SelectItem>
                      <SelectItem value={JobStatus.PUBLISHED}>Published</SelectItem>
                      <SelectItem value={JobStatus.CLOSED}>Closed</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.status && <p className="text-red-500 text-sm mt-1">{errors.status}</p>}
                </div>
              </CardContent>
            </Card>
            
            <div className="flex justify-end space-x-2">
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
          </div>
        </form>
      </div>
    </CompanyLayout>
  );
}
