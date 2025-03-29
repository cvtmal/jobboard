<?php

declare(strict_types=1);

return [
    'company' => 'Company',
    'companies' => 'Companies',
    'profile' => 'Company Profile',
    'dashboard' => 'Company Dashboard',
    'fields' => [
        'name' => 'Company Name',
        'email' => 'Email Address',
        'password' => 'Password',
        'address' => 'Address',
        'postcode' => 'Postal Code',
        'city' => 'City',
        'size' => 'Company Size',
        'type' => 'Company Type',
        'url' => 'Website URL',
        'description' => 'Description',
        'logo' => 'Company Logo',
        'cover' => 'Cover Image',
        'video' => 'Company Video',
        'newsletter' => 'Subscribe to Newsletter',
        'internal_notes' => 'Internal Notes',
        'active' => 'Active',
        'blocked' => 'Blocked',
    ],
    'sizes' => [
        'small' => '1-10 employees',
        'medium' => '11-50 employees',
        'large' => '51-200 employees',
        'enterprise' => '201+ employees',
    ],
    'types' => [
        'startup' => 'Startup',
        'smb' => 'Small/Medium Business',
        'enterprise' => 'Enterprise',
        'nonprofit' => 'Non-profit',
        'government' => 'Government',
        'education' => 'Education',
    ],
    'actions' => [
        'view' => 'View Company',
        'edit' => 'Edit Company',
        'save' => 'Save Company',
    ],
    'messages' => [
        'profile_updated' => 'Company profile updated successfully.',
        'verification_sent' => 'A verification link has been sent to your email address.',
    ],
];
