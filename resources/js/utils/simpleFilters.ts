/**
 * Simple direct filter utility functions that work with raw string values
 * rather than enums to ensure consistent behavior.
 */
export function filterJobsByCantonCode(jobs: any[], cantonCode: string): any[] {
  return jobs.filter(job => job.primary_canton_code === cantonCode);
}

export function filterJobsByCanton(jobs: any[], cantonCodes: string[]): any[] {
  if (cantonCodes.length === 0) {
    return jobs;
  }
  
  return jobs.filter(job => 
    cantonCodes.includes(job.primary_canton_code)
  );
}

export function filterJobsByRegion(jobs: any[], regionMapping: Record<string, string>, regions: string[]): any[] {
  if (regions.length === 0) {
    return jobs;
  }
  
  return jobs.filter(job => {
    if (!job.primary_canton_code) return false;
    const jobRegion = regionMapping[job.primary_canton_code];
    return jobRegion && regions.includes(jobRegion);
  });
}

export function filterJobsBySubRegion(jobs: any[], subRegions: string[]): any[] {
  if (subRegions.length === 0) {
    return jobs;
  }
  
  return jobs.filter(job => 
    job.primary_sub_region && subRegions.includes(job.primary_sub_region)
  );
}
