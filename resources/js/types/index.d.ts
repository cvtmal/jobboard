import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';
import { SwissCanton } from './enums/SwissCanton';
import { SwissRegion } from './enums/SwissRegion';
import { SwissSubRegion } from './enums/SwissSubRegion';

export interface Auth {
    user: User;
    company: Company | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Company {
    id: number;
    name: string;
    email: string;
    address?: string;
    postcode?: string;
    city?: string;
    canton_code?: SwissCanton;
    latitude?: number;
    longitude?: number;
    url?: string;
    size?: string;
    type?: string;
    description_german?: string;
    description_english?: string;
    description_french?: string;
    description_italian?: string;
    logo?: string;
    cover?: string;
    video?: string;
    newsletter?: boolean;
    internal_notes?: string;
    active: boolean;
    blocked: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface JobListing {
    id: number;
    company_id: number;
    title: string;
    slug: string;
    description: string;
    requirements: string;
    we_offer: string;
    address?: string;
    postcode?: string;
    city?: string;
    primary_canton_code?: SwissCanton;
    primary_sub_region?: SwissSubRegion;
    primary_latitude?: number;
    primary_longitude?: number;
    has_multiple_locations: boolean;
    is_remote: boolean;
    additionalLocations?: JobListingAdditionalLocation[];
    company: Company;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface JobListingAdditionalLocation {
    id: number;
    job_listing_id: number;
    canton_code?: SwissCanton;
    sub_region?: SwissSubRegion;
    city?: string;
    postcode?: string;
    latitude?: number;
    longitude?: number;
    created_at: string;
    updated_at: string;
}
