import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type RegisterForm = {
    name: string;
    first_name: string;
    last_name: string;
    email: string;
    phone_number: string;
    password: string;
    password_confirmation: string;
    url?: string;
    address?: string;
    postcode?: string;
    city?: string;
};

export default function CompanyRegister() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<RegisterForm>>({
        name: '',
        first_name: '',
        last_name: '',
        email: '',
        phone_number: '',
        password: '',
        password_confirmation: '',
        url: '',
        address: '',
        postcode: '',
        city: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('company.register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout title="Create a company account" description="Enter your details below to create your company account">
            <Head title="Register Company" />
            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    {/* Company Details Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium">Company Details</h3>
                        
                        <div className="grid gap-2">
                            <Label htmlFor="name">Company Name</Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="organization"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                disabled={processing}
                                placeholder="Company name"
                            />
                            <InputError message={errors.name} className="mt-2" />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="url">Company Website</Label>
                            <Input
                                id="url"
                                type="url"
                                tabIndex={2}
                                autoComplete="url"
                                value={data.url}
                                onChange={(e) => setData('url', e.target.value)}
                                disabled={processing}
                                placeholder="https://example.com"
                            />
                            <InputError message={errors.url} className="mt-2" />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="address">Address</Label>
                            <Input
                                id="address"
                                type="text"
                                tabIndex={3}
                                autoComplete="street-address"
                                value={data.address}
                                onChange={(e) => setData('address', e.target.value)}
                                disabled={processing}
                                placeholder="123 Main St"
                            />
                            <InputError message={errors.address} className="mt-2" />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="postcode">Postal Code</Label>
                                <Input
                                    id="postcode"
                                    type="text"
                                    tabIndex={4}
                                    autoComplete="postal-code"
                                    value={data.postcode}
                                    onChange={(e) => setData('postcode', e.target.value)}
                                    disabled={processing}
                                    placeholder="12345"
                                />
                                <InputError message={errors.postcode} className="mt-2" />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="city">City</Label>
                                <Input
                                    id="city"
                                    type="text"
                                    tabIndex={5}
                                    autoComplete="address-level2"
                                    value={data.city}
                                    onChange={(e) => setData('city', e.target.value)}
                                    disabled={processing}
                                    placeholder="City"
                                />
                                <InputError message={errors.city} className="mt-2" />
                            </div>
                        </div>
                    </div>

                    {/* Contact Person Details Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium">Contact Person</h3>
                        
                        <div className="grid grid-cols-2 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="first_name">First Name</Label>
                                <Input
                                    id="first_name"
                                    type="text"
                                    required
                                    tabIndex={6}
                                    autoComplete="given-name"
                                    value={data.first_name}
                                    onChange={(e) => setData('first_name', e.target.value)}
                                    disabled={processing}
                                    placeholder="First name"
                                />
                                <InputError message={errors.first_name} className="mt-2" />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="last_name">Last Name</Label>
                                <Input
                                    id="last_name"
                                    type="text"
                                    required
                                    tabIndex={7}
                                    autoComplete="family-name"
                                    value={data.last_name}
                                    onChange={(e) => setData('last_name', e.target.value)}
                                    disabled={processing}
                                    placeholder="Last name"
                                />
                                <InputError message={errors.last_name} className="mt-2" />
                            </div>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="email">Business Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                tabIndex={8}
                                autoComplete="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                disabled={processing}
                                placeholder="email@example.com"
                            />
                            <InputError message={errors.email} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                type="tel"
                                required
                                tabIndex={9}
                                autoComplete="tel"
                                value={data.phone_number}
                                onChange={(e) => setData('phone_number', e.target.value)}
                                disabled={processing}
                                placeholder="+1 234 567 890"
                            />
                            <InputError message={errors.phone_number} />
                        </div>
                    </div>

                    {/* Password Section */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium">Set Password</h3>
                        
                        <div className="grid gap-2">
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                required
                                tabIndex={10}
                                autoComplete="new-password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                disabled={processing}
                                placeholder="Password"
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                tabIndex={11}
                                autoComplete="new-password"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                disabled={processing}
                                placeholder="Confirm password"
                            />
                            <InputError message={errors.password_confirmation} />
                        </div>
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={12} disabled={processing}>
                        {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                        Create Company Account
                    </Button>
                </div>

                <div className="text-muted-foreground text-center text-sm">
                    Already have a company account?{' '}
                    <TextLink href={route('company.login')}>
                        Log in
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}
