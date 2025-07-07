/**
 * TypeScript enum that mirrors the PHP EmploymentType enum
 */
export enum EmploymentType {
    PERMANENT = 'permanent',
    TEMPORARY = 'temporary',
    FREELANCE = 'freelance',
    INTERNSHIP = 'internship',
    SIDE_JOB = 'side-job',
    APPRENTICESHIP = 'apprenticeship',
    WORKING_STUDENT = 'working-student',
    INTERIM = 'interim',
}

/**
 * Get the human-readable label for an employment type
 */
export function getEmploymentTypeLabel(type: EmploymentType): string {
    switch (type) {
        case EmploymentType.PERMANENT:
            return 'Permanent position';
        case EmploymentType.TEMPORARY:
            return 'Temporary employment';
        case EmploymentType.FREELANCE:
            return 'Freelance';
        case EmploymentType.INTERNSHIP:
            return 'Internship';
        case EmploymentType.SIDE_JOB:
            return 'Side job';
        case EmploymentType.APPRENTICESHIP:
            return 'Apprenticeship';
        case EmploymentType.WORKING_STUDENT:
            return 'Working student';
        case EmploymentType.INTERIM:
            return 'Interim';
        default:
            return '';
    }
}
