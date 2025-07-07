<?php

declare(strict_types=1);

namespace App\Enums;

enum EmploymentType: string
{
    case PERMANENT = 'permanent';
    case TEMPORARY = 'temporary';
    case FREELANCE = 'freelance';
    case INTERNSHIP = 'internship';
    case SIDE_JOB = 'side-job';
    case APPRENTICESHIP = 'apprenticeship';
    case WORKING_STUDENT = 'working-student';
    case INTERIM = 'interim';

    /**
     * Get all available values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the human-readable name of the employment type.
     */
    public function label(): string
    {
        return match ($this) {
            self::PERMANENT => 'Permanent position',
            self::TEMPORARY => 'Temporary employment',
            self::FREELANCE => 'Freelance',
            self::INTERNSHIP => 'Internship',
            self::SIDE_JOB => 'Side job',
            self::APPRENTICESHIP => 'Apprenticeship',
            self::WORKING_STUDENT => 'Working student',
            self::INTERIM => 'Interim',
        };
    }
}
