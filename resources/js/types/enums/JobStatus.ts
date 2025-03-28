/**
 * TypeScript enum that mirrors the PHP JobStatus enum
 */
export enum JobStatus {
  DRAFT = 'draft',
  PUBLISHED = 'published',
  CLOSED = 'closed',
}

/**
 * Get the human-readable label for a job status
 */
export function getJobStatusLabel(status: JobStatus): string {
  switch (status) {
    case JobStatus.DRAFT:
      return 'Draft';
    case JobStatus.PUBLISHED:
      return 'Published';
    case JobStatus.CLOSED:
      return 'Closed';
    default:
      return 'Unknown';
  }
}
