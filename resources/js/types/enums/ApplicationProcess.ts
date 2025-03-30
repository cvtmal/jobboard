/**
 * TypeScript enum that mirrors the PHP ApplicationProcess enum
 */
export enum ApplicationProcess {
    EMAIL = 'email',
    EXTERNAL = 'external',
    INTERNAL = 'internal',
}

/**
 * Get the human-readable label for an application process
 */
export function getApplicationProcessLabel(process: ApplicationProcess): string {
    switch (process) {
        case ApplicationProcess.EMAIL:
            return 'Email';
        case ApplicationProcess.EXTERNAL:
            return 'External Website';
        case ApplicationProcess.INTERNAL:
            return 'Internal Application Form';
        default:
            return 'Unknown';
    }
}
