import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { type Company } from '@/types';
import { Link, router } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';

interface CompanyMenuContentProps {
    company: Company;
}

export function CompanyMenuContent({ company }: CompanyMenuContentProps) {
    const cleanup = useMobileNavigation();

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <div className="flex flex-col items-start gap-1 overflow-hidden">
                        <div className="truncate font-medium">{company.name}</div>
                        <div className="text-muted-foreground w-full truncate text-xs">{company.email}</div>
                    </div>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem asChild>
                    <Link className="block w-full" href={route('company.settings.profile')} as="button" prefetch onClick={cleanup}>
                        <Settings className="mr-2" />
                        Settings
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem 
                onClick={() => {
                    localStorage.removeItem('job-listing-draft');
                    localStorage.removeItem('job-listing-current-step');
                    localStorage.removeItem('job-listing-completed-steps');
                    cleanup();
                    router.post(route('company.logout'));
                }}
                className="cursor-pointer"
            >
                <LogOut className="mr-2" />
                Log out
            </DropdownMenuItem>
        </>
    );
}
