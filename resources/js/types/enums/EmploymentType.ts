/**
 * TypeScript enum that mirrors the PHP EmploymentType enum
 */
export enum EmploymentType {
    FULL_TIME = 'full-time',
    PART_TIME = 'part-time',
    FULL_PART_TIME = 'full-part-time',
    CONTRACT = 'contract',
    TEMPORARY = 'temporary',
    INTERNSHIP = 'internship',
    VOLUNTEER = 'volunteer',
}

/**
 * Get the human-readable label for an employment type
 */
export function getEmploymentTypeLabel(type: EmploymentType): string {
    switch (type) {
        case EmploymentType.FULL_TIME:
            return 'Full Time';
        case EmploymentType.PART_TIME:
            return 'Part Time';
        case EmploymentType.FULL_PART_TIME:
            return 'Full/Part Time';
        case EmploymentType.CONTRACT:
            return 'Contract';
        case EmploymentType.TEMPORARY:
            return 'Temporary';
        case EmploymentType.INTERNSHIP:
            return 'Internship';
        case EmploymentType.VOLUNTEER:
            return 'Volunteer';
        default:
            return '';
    }
}
