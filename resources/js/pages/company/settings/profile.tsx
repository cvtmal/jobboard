import { Transition } from '@headlessui/react';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import CompanySettingsLayout from '@/layouts/company/settings-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company Profile',
        href: '/company/settings/profile',
    },
];

type ProfileForm = {
    name: string;
    email: string;
    address: string;
    postcode: string;
    city: string;
    url: string;
    size: string;
    type: string;
    description_english: string;
};

export default function CompanyProfile({ auth, mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Partial<ProfileForm>>({
        name: auth.company.name,
        email: auth.company.email,
        address: auth.company.address || '',
        postcode: auth.company.postcode || '',
        city: auth.company.city || '',
        url: auth.company.url || '',
        size: auth.company.size || '',
        type: auth.company.type || '',
        description_english: auth.company.description_english || '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('company.profile.update'), {
            preserveScroll: true,
        });
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Profile" />

            <CompanySettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Company Information" description="Update your company profile details" />

                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Company Name</Label>
                            <Input
                                id="name"
                                className="mt-1 block w-full"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                                autoComplete="organization"
                                placeholder="Company name"
                            />
                            <InputError className="mt-2" message={errors.name} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="email">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                className="mt-1 block w-full"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoComplete="email"
                                placeholder="Email address"
                            />
                            <InputError className="mt-2" message={errors.email} />
                        </div>

                        {mustVerifyEmail && auth.company.email_verified_at === null && (
                            <div>
                                <p className="text-muted-foreground -mt-4 text-sm">
                                    Your email address is unverified.{' '}
                                    <Link
                                        href={route('company.verification.send')}
                                        method="post"
                                        as="button"
                                        className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                    >
                                        Click here to resend the verification email.
                                    </Link>
                                </p>

                                {status === 'verification-link-sent' && (
                                    <div className="mt-2 text-sm font-medium text-green-600">
                                        A new verification link has been sent to your email address.
                                    </div>
                                )}
                            </div>
                        )}

                        <div className="grid gap-2">
                            <Label htmlFor="address">Address</Label>
                            <Input
                                id="address"
                                className="mt-1 block w-full"
                                value={data.address}
                                onChange={(e) => setData('address', e.target.value)}
                                placeholder="Street address"
                            />
                            <InputError className="mt-2" message={errors.address} />
                        </div>

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="postcode">Postal Code</Label>
                                <Input
                                    id="postcode"
                                    className="mt-1 block w-full"
                                    value={data.postcode}
                                    onChange={(e) => setData('postcode', e.target.value)}
                                    placeholder="Postal code"
                                />
                                <InputError className="mt-2" message={errors.postcode} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="city">City</Label>
                                <Input
                                    id="city"
                                    className="mt-1 block w-full"
                                    value={data.city}
                                    onChange={(e) => setData('city', e.target.value)}
                                    placeholder="City"
                                />
                                <InputError className="mt-2" message={errors.city} />
                            </div>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="url">Website URL</Label>
                            <Input
                                id="url"
                                type="url"
                                className="mt-1 block w-full"
                                value={data.url}
                                onChange={(e) => setData('url', e.target.value)}
                                placeholder="https://example.com"
                            />
                            <InputError className="mt-2" message={errors.url} />
                        </div>

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="size">Company Size</Label>
                                <Input
                                    id="size"
                                    className="mt-1 block w-full"
                                    value={data.size}
                                    onChange={(e) => setData('size', e.target.value)}
                                    placeholder="e.g. 10-50 employees"
                                />
                                <InputError className="mt-2" message={errors.size} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="type">Company Type</Label>
                                <Input
                                    id="type"
                                    className="mt-1 block w-full"
                                    value={data.type}
                                    onChange={(e) => setData('type', e.target.value)}
                                    placeholder="e.g. IT, Finance, Healthcare"
                                />
                                <InputError className="mt-2" message={errors.type} />
                            </div>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="description_english">Company Description (English)</Label>
                            <Textarea
                                id="description_english"
                                className="mt-1 block w-full"
                                value={data.description_english}
                                onChange={(e) => setData('description_english', e.target.value)}
                                rows={6}
                                placeholder="Describe your company..."
                            />
                            <InputError className="mt-2" message={errors.description_english} />
                        </div>

                        <div className="flex items-center gap-4">
                            <Button disabled={processing}>Save</Button>

                            <Transition
                                show={recentlySuccessful}
                                enter="transition ease-in-out"
                                enterFrom="opacity-0"
                                leave="transition ease-in-out"
                                leaveTo="opacity-0"
                            >
                                <p className="text-sm text-neutral-600">Saved</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </CompanySettingsLayout>
        </CompanyLayout>
    );
}
