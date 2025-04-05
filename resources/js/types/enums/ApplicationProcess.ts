/**
 * TypeScript enum that mirrors the PHP ApplicationProcess enum
 */
export enum ApplicationProcess {
    EMAIL = 'email',
    URL = 'url',
    BOTH = 'both',
}

/**
 * Get the human-readable label for an application process
 */
export function getApplicationProcessLabel(process: ApplicationProcess): string {
    switch (process) {
        case ApplicationProcess.EMAIL:
            return 'Email';
        case ApplicationProcess.URL:
            return 'External Website';
        case ApplicationProcess.BOTH:
            return 'Both Email and External Website';
        default:
            return 'Unknown';
    }
}
