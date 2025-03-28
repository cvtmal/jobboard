import React, { ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

interface CompanyLayoutProps {
  children: ReactNode;
}

export default function CompanyLayout({ children }: CompanyLayoutProps) {
  return (
    <div className="container mx-auto py-8">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Company Dashboard</h1>
        <div className="flex space-x-2">
          <Button variant="outline" asChild>
            <Link href={route('company.dashboard')}>Dashboard</Link>
          </Button>
          <Button variant="outline" asChild>
            <Link href={route('company.job-listings.index')}>Job Listings</Link>
          </Button>
          <Button variant="outline" asChild>
            <Link href={route('company.settings.profile')}>Settings</Link>
          </Button>
        </div>
      </div>
      
      <Card>
        <CardContent className="p-6">
          {children}
        </CardContent>
      </Card>
    </div>
  );
}
