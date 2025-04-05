import CompanySidebarLayout from '@/layouts/company/company-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';

interface CompanyLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: CompanyLayoutProps) => (
    <CompanySidebarLayout breadcrumbs={breadcrumbs} {...props}>
        {children}
    </CompanySidebarLayout>
);
