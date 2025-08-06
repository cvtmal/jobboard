import { Transition } from '@headlessui/react';
import { Head, useForm } from '@inertiajs/react';
import { Building, CheckCircle, MapPin, Plus, Target, Upload, Users, X } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

import { CompanyImageUploader } from '@/components/company';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import CompanyLayout from '@/layouts/company-layout';
import type { Auth, BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company Details',
        href: '/company/details',
    },
];

type ProfileForm = {
    name: string;
    email: string;
    first_name: string;
    last_name: string;
    phone_number: string;
    address: string;
    postcode: string;
    city: string;
    url: string;
    size: string;
    type: string;
    industry: string;
    founded_year: string;
    description_english: string;
    mission_statement: string;
    benefits: string[];
    company_culture: string[];
};

export default function CompanyDetails({
    auth,
    company,
    shouldShowOnboarding,
    status,
}: {
    auth: Auth;
    company: any;
    shouldShowOnboarding: boolean;
    status?: string;
}) {
    const [benefits, setBenefits] = useState<string[]>(company.benefits || []);
    const [companyCulture, setCompanyCulture] = useState<string[]>(company.company_culture || []);
    const [newBenefit, setNewBenefit] = useState('');
    const [newCultureItem, setNewCultureItem] = useState('');

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Partial<ProfileForm>>({
        name: company.name || '',
        email: company.email || '',
        first_name: company.first_name || '',
        last_name: company.last_name || '',
        phone_number: company.phone_number || '',
        address: company.address || '',
        postcode: company.postcode || '',
        city: company.city || '',
        url: company.url || '',
        size: company.size || '',
        type: company.type || '',
        industry: company.industry || '',
        founded_year: company.founded_year?.toString() || '',
        description_english: company.description_english || '',
        mission_statement: company.mission_statement || '',
        benefits,
        company_culture: companyCulture,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('company.details.update'), {
            preserveScroll: true,
        });
    };

    const addBenefit = () => {
        if (newBenefit.trim() && !benefits.includes(newBenefit.trim())) {
            const updatedBenefits = [...benefits, newBenefit.trim()];
            setBenefits(updatedBenefits);
            setData('benefits', updatedBenefits);
            setNewBenefit('');
        }
    };

    const removeBenefit = (index: number) => {
        const updatedBenefits = benefits.filter((_, i) => i !== index);
        setBenefits(updatedBenefits);
        setData('benefits', updatedBenefits);
    };

    const addCultureItem = () => {
        if (newCultureItem.trim() && !companyCulture.includes(newCultureItem.trim())) {
            const updatedCulture = [...companyCulture, newCultureItem.trim()];
            setCompanyCulture(updatedCulture);
            setData('company_culture', updatedCulture);
            setNewCultureItem('');
        }
    };

    const removeCultureItem = (index: number) => {
        const updatedCulture = companyCulture.filter((_, i) => i !== index);
        setCompanyCulture(updatedCulture);
        setData('company_culture', updatedCulture);
    };

    return (
        <CompanyLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Details" />

            <div className="space-y-8">
                {shouldShowOnboarding && (
                    <Card className="border-primary/20 bg-primary/5">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Building className="text-primary h-5 w-5" />
                                Welcome! Complete Your Company Details
                            </CardTitle>
                            <CardDescription>
                                Add your company details and branding to create better job listings and attract quality candidates.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                )}

                <div className="grid gap-8 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        {/* Company Images - Outside of form */}
                        <Card className="mb-8">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Upload className="h-5 w-5" />
                                    Company Branding (optional)
                                </CardTitle>
                                <CardDescription>
                                    Upload your company logo and banner. These will be used as defaults for your job listings.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <CompanyImageUploader mode="direct" currentBannerUrl={company.banner_url} currentLogoUrl={company.logo_url} />
                            </CardContent>
                        </Card>

                        <form onSubmit={submit} className="space-y-8">
                            {/* Basic Information */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Building className="h-5 w-5" />
                                        Basic Information
                                    </CardTitle>
                                    <CardDescription>Essential company information that identifies your business.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="name">Company Name *</Label>
                                        <Input
                                            id="name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                            autoComplete="organization"
                                            placeholder="Your company name"
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="email">Email Address *</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                            autoComplete="email"
                                            placeholder="company@example.com"
                                        />
                                        <InputError message={errors.email} />
                                    </div>

                                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="first_name">Contact First Name</Label>
                                            <Input
                                                id="first_name"
                                                value={data.first_name}
                                                onChange={(e) => setData('first_name', e.target.value)}
                                                autoComplete="given-name"
                                                placeholder="John"
                                            />
                                            <InputError message={errors.first_name} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="last_name">Contact Last Name</Label>
                                            <Input
                                                id="last_name"
                                                value={data.last_name}
                                                onChange={(e) => setData('last_name', e.target.value)}
                                                autoComplete="family-name"
                                                placeholder="Doe"
                                            />
                                            <InputError message={errors.last_name} />
                                        </div>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="phone_number">Phone Number</Label>
                                        <Input
                                            id="phone_number"
                                            type="tel"
                                            value={data.phone_number}
                                            onChange={(e) => setData('phone_number', e.target.value)}
                                            autoComplete="tel"
                                            placeholder="+41 xx xxx xx xx"
                                        />
                                        <InputError message={errors.phone_number} />
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Contact Information */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <MapPin className="h-5 w-5" />
                                        Contact Information
                                    </CardTitle>
                                    <CardDescription>Location and contact details for your company.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="address">Address</Label>
                                        <Input
                                            id="address"
                                            value={data.address}
                                            onChange={(e) => setData('address', e.target.value)}
                                            placeholder="Street address"
                                        />
                                        <InputError message={errors.address} />
                                    </div>

                                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="postcode">Postal Code</Label>
                                            <Input
                                                id="postcode"
                                                value={data.postcode}
                                                onChange={(e) => setData('postcode', e.target.value)}
                                                placeholder="8000"
                                            />
                                            <InputError message={errors.postcode} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="city">City</Label>
                                            <Input
                                                id="city"
                                                value={data.city}
                                                onChange={(e) => setData('city', e.target.value)}
                                                placeholder="Zurich"
                                            />
                                            <InputError message={errors.city} />
                                        </div>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="url">Website URL</Label>
                                        <Input
                                            id="url"
                                            type="url"
                                            value={data.url}
                                            onChange={(e) => setData('url', e.target.value)}
                                            placeholder="https://example.com"
                                        />
                                        <InputError message={errors.url} />
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Company Details */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Users className="h-5 w-5" />
                                        Company Details (optional)
                                    </CardTitle>
                                    <CardDescription>Information about your company size, industry, and type.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="size">Company Size</Label>
                                            <Input
                                                id="size"
                                                value={data.size}
                                                onChange={(e) => setData('size', e.target.value)}
                                                placeholder="e.g. 10-50 employees"
                                            />
                                            <InputError message={errors.size} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="type">Company Type</Label>
                                            <Input
                                                id="type"
                                                value={data.type}
                                                onChange={(e) => setData('type', e.target.value)}
                                                placeholder="e.g. Private, Public, Startup"
                                            />
                                            <InputError message={errors.type} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="industry">Industry</Label>
                                            <Input
                                                id="industry"
                                                value={data.industry}
                                                onChange={(e) => setData('industry', e.target.value)}
                                                placeholder="e.g. Technology, Finance, Healthcare"
                                            />
                                            <InputError message={errors.industry} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="founded_year">Founded Year</Label>
                                            <Input
                                                id="founded_year"
                                                type="number"
                                                value={data.founded_year}
                                                onChange={(e) => setData('founded_year', e.target.value)}
                                                min="1800"
                                                max={new Date().getFullYear()}
                                                placeholder="2020"
                                            />
                                            <InputError message={errors.founded_year} />
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Company Description */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Target className="h-5 w-5" />
                                        Company Description (optional)
                                    </CardTitle>
                                    <CardDescription>Tell potential candidates about your company and mission.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="description_english">Company Description (English)</Label>
                                        <Textarea
                                            id="description_english"
                                            value={data.description_english}
                                            onChange={(e) => setData('description_english', e.target.value)}
                                            rows={6}
                                            placeholder="Describe your company, what you do, and what makes you unique..."
                                        />
                                        <InputError message={errors.description_english} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="mission_statement">Mission Statement</Label>
                                        <Textarea
                                            id="mission_statement"
                                            value={data.mission_statement}
                                            onChange={(e) => setData('mission_statement', e.target.value)}
                                            rows={3}
                                            placeholder="What is your company's mission and vision?"
                                        />
                                        <InputError message={errors.mission_statement} />
                                    </div>

                                    {/* Benefits */}
                                    <div className="grid gap-2">
                                        <Label>Employee Benefits</Label>
                                        <div className="space-y-2">
                                            <div className="flex gap-2">
                                                <Input
                                                    value={newBenefit}
                                                    onChange={(e) => setNewBenefit(e.target.value)}
                                                    placeholder="Add a benefit (e.g. Health insurance, Remote work)"
                                                    onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addBenefit())}
                                                />
                                                <Button type="button" onClick={addBenefit} size="sm">
                                                    <Plus className="h-4 w-4" />
                                                </Button>
                                            </div>
                                            <div className="flex flex-wrap gap-2">
                                                {benefits.map((benefit, index) => (
                                                    <div key={index} className="bg-secondary flex items-center gap-1 rounded-md px-2 py-1 text-sm">
                                                        {benefit}
                                                        <button
                                                            type="button"
                                                            onClick={() => removeBenefit(index)}
                                                            className="text-muted-foreground hover:text-foreground"
                                                        >
                                                            <X className="h-3 w-3" />
                                                        </button>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Company Culture */}
                                    <div className="grid gap-2">
                                        <Label>Company Culture</Label>
                                        <div className="space-y-2">
                                            <div className="flex gap-2">
                                                <Input
                                                    value={newCultureItem}
                                                    onChange={(e) => setNewCultureItem(e.target.value)}
                                                    placeholder="Add a culture value (e.g. Innovation, Collaboration)"
                                                    onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addCultureItem())}
                                                />
                                                <Button type="button" onClick={addCultureItem} size="sm">
                                                    <Plus className="h-4 w-4" />
                                                </Button>
                                            </div>
                                            <div className="flex flex-wrap gap-2">
                                                {companyCulture.map((culture, index) => (
                                                    <div key={index} className="bg-secondary flex items-center gap-1 rounded-md px-2 py-1 text-sm">
                                                        {culture}
                                                        <button
                                                            type="button"
                                                            onClick={() => removeCultureItem(index)}
                                                            className="text-muted-foreground hover:text-foreground"
                                                        >
                                                            <X className="h-3 w-3" />
                                                        </button>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <div className="flex items-center gap-4">
                                <Button disabled={processing}>{processing ? 'Saving...' : 'Save Details'}</Button>

                                <Transition
                                    show={recentlySuccessful}
                                    enter="transition ease-in-out"
                                    enterFrom="opacity-0"
                                    leave="transition ease-in-out"
                                    leaveTo="opacity-0"
                                >
                                    <p className="text-sm text-green-600">Details saved successfully!</p>
                                </Transition>
                            </div>
                        </form>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Profile Tips</CardTitle>
                                <CardDescription>Make your company profile stand out</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm">
                                <div className="flex items-start gap-2">
                                    <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                    <p>Add a clear, professional company description that highlights what makes you unique.</p>
                                </div>
                                <div className="flex items-start gap-2">
                                    <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                    <p>Upload a high-quality logo and banner to build brand recognition.</p>
                                </div>
                                <div className="flex items-start gap-2">
                                    <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                    <p>List employee benefits and company culture values to attract the right candidates.</p>
                                </div>
                                <div className="flex items-start gap-2">
                                    <CheckCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" />
                                    <p>Keep your contact information up-to-date for easy communication.</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </CompanyLayout>
    );
}