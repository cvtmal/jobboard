import { CompanyNavMain } from '@/components/company-nav-main';
import { CompanyNavUser } from '@/components/company-nav-user';
import { NavFooter } from '@/components/nav-footer';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { useAppearance } from '@/hooks/use-appearance';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BarChart3, Briefcase, Building2, Folder, Home, LayoutDashboard, Settings, Users } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/company/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'Profile',
        href: '/company/profile',
        icon: Building2,
        subItems: [
            {
                title: 'Company Details',
                href: '/company/details',
            },
            {
                title: 'Career Page',
                href: '/company/career-page',
            },
        ],
    },
    {
        title: 'Job Listings',
        href: '/company/job-listings',
        icon: Briefcase,
    },
    {
        title: 'Applications',
        href: '/company/applications',
        icon: Users,
    },
    {
        title: 'Analytics',
        href: '/company/analytics',
        icon: BarChart3,
    },
    {
        title: 'Settings',
        href: '/company/settings',
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'Help Center',
        href: '/help',
        icon: Folder,
    },
];

export function CompanySidebar() {
    const { appearance } = useAppearance();

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/company/dashboard">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <CompanyNavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <CompanyNavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
