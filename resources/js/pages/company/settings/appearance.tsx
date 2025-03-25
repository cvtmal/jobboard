import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import CompanySettingsLayout from '@/layouts/company/settings-layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company Appearance',
        href: '/company/settings/appearance',
    },
];

export default function CompanyAppearance() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Appearance" />

            <CompanySettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall 
                        title="Appearance settings" 
                        description="Update your company account's appearance settings" 
                    />
                    <AppearanceTabs />
                </div>
            </CompanySettingsLayout>
        </AppLayout>
    );
}
