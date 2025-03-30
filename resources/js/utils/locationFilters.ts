import { SwissCanton, swissCantonData } from '../types/enums/SwissCanton';
import { SwissRegion, swissRegionData } from '../types/enums/SwissRegion';
import { SwissSubRegion, swissSubRegionData } from '../types/enums/SwissSubRegion';
import type { JobListing, JobListingAdditionalLocation } from '../types';

/**
 * Get the raw string value of a SwissCanton enum
 * This helps us compare enum values with database string values
 */
export function getCantonCode(canton: SwissCanton): string {
  return canton; // The enum value is already the canton code string (ZH, BE, etc.)
}

/**
 * Check if a job listing is in a specific region
 */
export function isJobInRegion(job: JobListing, region: SwissRegion): boolean {
  // Check primary location
  if (job.primary_canton_code) {
    const canton = swissCantonData[job.primary_canton_code];
    if (canton && canton.region === region) {
      return true;
    }
  }

  // Check additional locations
  if (job.additionalLocations?.length) {
    return job.additionalLocations.some(location => {
      if (location.canton_code) {
        const canton = swissCantonData[location.canton_code];
        return canton && canton.region === region;
      }
      return false;
    });
  }

  return false;
}

/**
 * Check if a job listing is in a specific canton
 */
export function isJobInCanton(job: JobListing, canton: SwissCanton): boolean {
  // Get the raw string value to compare with database values
  const cantonCode = getCantonCode(canton);
  
  // Check primary location against the raw string value
  if (job.primary_canton_code === cantonCode) {
    return true;
  }

  // Check additional locations
  if (job.additionalLocations?.length) {
    return job.additionalLocations.some(location => location.canton_code === cantonCode);
  }

  return false;
}

/**
 * Check if a job listing is in a specific sub-region
 */
export function isJobInSubRegion(job: JobListing, subRegion: SwissSubRegion): boolean {
  // Check primary location
  if (job.primary_sub_region === subRegion) {
    return true;
  }

  // Check additional locations
  if (job.additionalLocations?.length) {
    return job.additionalLocations.some(location => location.sub_region === subRegion);
  }

  return false;
}

/**
 * Get all sub-regions for a specific canton
 */
export function getSubRegionsForCanton(canton: SwissCanton): SwissSubRegion[] {
  return Object.values(swissSubRegionData)
    .filter(subRegion => subRegion.cantons.includes(canton))
    .map(subRegion => subRegion.id);
}

/**
 * Get all sub-regions for a specific region
 */
export function getSubRegionsForRegion(region: SwissRegion): SwissSubRegion[] {
  return Object.values(swissSubRegionData)
    .filter(subRegion => subRegion.region === region)
    .map(subRegion => subRegion.id);
}

/**
 * Get human-readable location label for a job listing's primary location
 */
export function getJobLocationLabel(job: JobListing): string {
  const parts: string[] = [];
  
  if (job.city) {
    parts.push(job.city);
  }
  
  if (job.primary_sub_region && job.primary_sub_region !== getDefaultSubRegionForCanton(job.primary_canton_code as SwissCanton | null)) {
    parts.push(swissSubRegionData[job.primary_sub_region].label);
  }
  
  if (job.primary_canton_code) {
    parts.push(swissCantonData[job.primary_canton_code].label);
  }
  
  return parts.join(', ');
}

/**
 * Get the default sub-region for a canton
 */
export function getDefaultSubRegionForCanton(cantonCode: SwissCanton | null): SwissSubRegion | null {
  if (!cantonCode) return null;
  
  const cantonSubRegions = getSubRegionsForCanton(cantonCode);
  if (cantonSubRegions.length === 0) return null;
  
  // Find a sub-region with the same name as the canton
  const defaultSubRegion = cantonSubRegions.find(sr => {
    const subRegionName = swissSubRegionData[sr].label;
    const cantonName = swissCantonData[cantonCode].label;
    return subRegionName.includes(cantonName);
  });
  
  return defaultSubRegion || cantonSubRegions[0];
}
