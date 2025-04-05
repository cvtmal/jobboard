import { CompanyNavUser } from '@/components/company-nav-user';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    BookOpen,
    Castle,
    CreditCard,
    Folder,
    Gem,
    LayoutGrid,
    Pickaxe,
    Rocket,
    Shapes,
    Tags,
    Users
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Invoices',
        href: '/invoices',
        icon: CreditCard,
    },
    {
        title: 'Jobs',
        href: '/jobs',
        icon: Pickaxe,
    },
    {
        title: 'Applications',
        href: '/applications',
        icon: Rocket,
    },
    {
        title: 'Companies',
        href: '/companies',
        icon: Castle,
    },
    {
        title: 'Applicants',
        href: '/applicants',
        icon: Users,
    },
    {
        title: 'Job Tiers',
        href: '/job-tiers',
        icon: Gem,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: Shapes,
    },
    {
        title: 'Skills',
        href: '/skills',
        icon: Tags,
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/cvtmal/jobboard',
        icon: Folder,
    },
    {
        title: 'Home',
        href: 'http://jobboard.test',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
