import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface CompanyLayoutProps {
    children: ReactNode;
}

export default function CompanyLayout({ children }: CompanyLayoutProps) {
    return (
        <div className="container mx-auto py-8">
            <div className="mb-6 flex items-center justify-between">
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
                <CardContent className="p-6">{children}</CardContent>
            </Card>
        </div>
    );
}
