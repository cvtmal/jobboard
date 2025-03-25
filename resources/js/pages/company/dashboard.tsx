import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';

export default function CompanyDashboard({ auth }: PageProps) {
  return (
    <>
      <Head title="Company Dashboard" />
      
      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="flex flex-col space-y-4">
            <h1 className="text-2xl font-bold">Company Dashboard</h1>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle>Company Profile</CardTitle>
                  <CardDescription>Manage your company information</CardDescription>
                </CardHeader>
                <CardContent>
                  <p>Update your company details, logo, and other information.</p>
                </CardContent>
                <CardFooter>
                  <Button>Edit Profile</Button>
                </CardFooter>
              </Card>
              
              <Card>
                <CardHeader>
                  <CardTitle>Job Listings</CardTitle>
                  <CardDescription>Manage your job listings</CardDescription>
                </CardHeader>
                <CardContent>
                  <p>Create, edit, and manage all your job listings in one place.</p>
                </CardContent>
                <CardFooter>
                  <Button>View Job Listings</Button>
                </CardFooter>
              </Card>
              
              <Card>
                <CardHeader>
                  <CardTitle>Applications</CardTitle>
                  <CardDescription>Review candidate applications</CardDescription>
                </CardHeader>
                <CardContent>
                  <p>View and manage all applications to your job listings.</p>
                </CardContent>
                <CardFooter>
                  <Button>View Applications</Button>
                </CardFooter>
              </Card>
              
              <Card>
                <CardHeader>
                  <CardTitle>Analytics</CardTitle>
                  <CardDescription>View performance metrics</CardDescription>
                </CardHeader>
                <CardContent>
                  <p>See how your job listings are performing and track applicant engagement.</p>
                </CardContent>
                <CardFooter>
                  <Button>View Analytics</Button>
                </CardFooter>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
