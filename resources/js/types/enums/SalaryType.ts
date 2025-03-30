/**
 * TypeScript enum that mirrors the PHP SalaryType enum
 */
export enum SalaryType {
    HOURLY = 'hourly',
    DAILY = 'daily',
    MONTHLY = 'monthly',
    YEARLY = 'yearly',
}

/**
 * Get the human-readable label for a salary type
 */
export function getSalaryTypeLabel(type: SalaryType): string {
    switch (type) {
        case SalaryType.HOURLY:
            return 'Hourly';
        case SalaryType.DAILY:
            return 'Daily';
        case SalaryType.MONTHLY:
            return 'Monthly';
        case SalaryType.YEARLY:
            return 'Yearly';
        default:
            return '';
    }
}
