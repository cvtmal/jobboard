<?php

declare(strict_types=1);

return [
    'listing' => 'Stellenangebot',
    'listings' => 'Stellenangebote',
    'create' => 'Stellenangebot erstellen',
    'edit' => 'Stellenangebot bearbeiten',
    'fields' => [
        'title' => 'Stellenbezeichnung',
        'description' => 'Beschreibung',
        'requirements' => 'Anforderungen',
        'location' => 'Standort',
        'salary' => 'Gehalt',
        'salary_range' => 'Gehaltsrahmen',
        'min_salary' => 'Mindestgehalt',
        'max_salary' => 'Höchstgehalt',
        'job_type' => 'Beschäftigungsart',
        'remote' => 'Remote',
        'application_deadline' => 'Bewerbungsfrist',
        'status' => 'Status',
        'published' => 'Veröffentlicht',
        'draft' => 'Entwurf',
    ],
    'actions' => [
        'view' => 'Job ansehen',
        'edit' => 'Job bearbeiten',
        'delete' => 'Job löschen',
        'apply' => 'Auf Job bewerben',
        'save' => 'Job speichern',
        'publish' => 'Job veröffentlichen',
        'unpublish' => 'Job zurückziehen',
    ],
    'job_types' => [
        'full_time' => 'Vollzeit',
        'part_time' => 'Teilzeit',
        'contract' => 'Befristet',
        'freelance' => 'Freiberuflich',
        'internship' => 'Praktikum',
    ],
    'messages' => [
        'created' => 'Stellenangebot erfolgreich erstellt.',
        'updated' => 'Stellenangebot erfolgreich aktualisiert.',
        'deleted' => 'Stellenangebot erfolgreich gelöscht.',
        'published' => 'Stellenangebot erfolgreich veröffentlicht.',
        'unpublished' => 'Stellenangebot erfolgreich zurückgezogen.',
    ],
];
