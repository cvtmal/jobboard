import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { useEffect, useState } from 'react';

export function CompanyNavMain({ items = [] }: { items: NavItem[] }) {
    const { url } = usePage();

    // Helper functions
    const isCurrentPath = (href: string) => url === href;
    const isParentActive = (item: NavItem) => {
        if (isCurrentPath(item.href)) return true;
        return item.subItems?.some((subItem) => isCurrentPath(subItem.href)) || false;
    };

    // Initialize expanded state based on current active routes
    const getInitialExpandedItems = () => {
        const initialExpanded = new Set<string>();
        items.forEach((item) => {
            if (item.subItems && item.subItems.length > 0 && isParentActive(item)) {
                initialExpanded.add(item.title);
            }
        });
        return initialExpanded;
    };

    const [expandedItems, setExpandedItems] = useState<Set<string>>(getInitialExpandedItems);

    // Update expanded state when route changes (for parent items with active children)
    useEffect(() => {
        const newExpanded = new Set(expandedItems);
        items.forEach((item) => {
            if (item.subItems && item.subItems.length > 0) {
                const shouldBeExpanded = isParentActive(item);
                if (shouldBeExpanded && !newExpanded.has(item.title)) {
                    newExpanded.add(item.title);
                }
            }
        });

        // Only update state if there are changes to prevent unnecessary re-renders
        if (newExpanded.size !== expandedItems.size || Array.from(newExpanded).some((item) => !expandedItems.has(item))) {
            setExpandedItems(newExpanded);
        }
    }, [url]);

    const toggleExpanded = (title: string) => {
        const newExpanded = new Set(expandedItems);
        if (newExpanded.has(title)) {
            newExpanded.delete(title);
        } else {
            newExpanded.add(title);
        }
        setExpandedItems(newExpanded);
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Company</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    const hasSubItems = item.subItems && item.subItems.length > 0;
                    const isExpanded = expandedItems.has(item.title);
                    const isActive = isParentActive(item);

                    return (
                        <SidebarMenuItem key={item.title}>
                            {hasSubItems ? (
                                <>
                                    <SidebarMenuButton
                                        onClick={() => toggleExpanded(item.title)}
                                        isActive={isActive}
                                        tooltip={{ children: item.title }}
                                        className="w-full"
                                    >
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                        <ChevronRight className={`ml-auto h-4 w-4 transition-transform ${isExpanded ? 'rotate-90' : ''}`} />
                                    </SidebarMenuButton>
                                    {isExpanded && (
                                        <SidebarMenuSub>
                                            {item.subItems!.map((subItem) => (
                                                <SidebarMenuSubItem key={subItem.title}>
                                                    <SidebarMenuSubButton asChild isActive={isCurrentPath(subItem.href)}>
                                                        <Link href={subItem.href}>
                                                            {subItem.icon && <subItem.icon />}
                                                            <span>{subItem.title}</span>
                                                        </Link>
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            ))}
                                        </SidebarMenuSub>
                                    )}
                                </>
                            ) : (
                                <SidebarMenuButton asChild isActive={isCurrentPath(item.href)} tooltip={{ children: item.title }}>
                                    <Link href={item.href}>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            )}
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
