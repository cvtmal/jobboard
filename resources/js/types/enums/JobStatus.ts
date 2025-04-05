/**
 * TypeScript enum that mirrors the PHP JobStatus enum
 */
export enum JobStatus {
    DRAFT = 'draft',
    PENDING = 'pending',
    PUBLISHED = 'published',
    EXPIRED = 'expired',
    CLOSED = 'closed',
}

/**
 * Get the human-readable label for a job status
 */
export function getJobStatusLabel(status: JobStatus): string {
    switch (status) {
        case JobStatus.DRAFT:
            return 'Draft';
        case JobStatus.PENDING:
            return 'Pending';
        case JobStatus.PUBLISHED:
            return 'Published';
        case JobStatus.EXPIRED:
            return 'Expired';
        case JobStatus.CLOSED:
            return 'Closed';
        default:
            return 'Unknown';
    }
}
