/**
 * TypeScript enum that mirrors the PHP ExperienceLevel enum
 */
export enum ExperienceLevel {
    ENTRY = 'entry',
    JUNIOR = 'junior',
    MID_LEVEL = 'mid-level',
    SENIOR = 'senior',
    EXECUTIVE = 'executive',
}

/**
 * Get the human-readable label for an experience level
 */
export function getExperienceLevelLabel(level: ExperienceLevel): string {
    switch (level) {
        case ExperienceLevel.ENTRY:
            return 'Entry Level';
        case ExperienceLevel.JUNIOR:
            return 'Junior';
        case ExperienceLevel.MID_LEVEL:
            return 'Mid Level';
        case ExperienceLevel.SENIOR:
            return 'Senior';
        case ExperienceLevel.EXECUTIVE:
            return 'Executive';
        default:
            return '';
    }
}
