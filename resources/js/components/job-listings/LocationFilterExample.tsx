import React, { useState } from 'react';
import { SwissRegion } from '../../types/enums/SwissRegion';
import { SwissCanton } from '../../types/enums/SwissCanton';
import { SwissSubRegion } from '../../types/enums/SwissSubRegion';
import { isJobInRegion, isJobInCanton, isJobInSubRegion } from '../../utils/locationFilters';
import LocationFilter from './LocationFilter';
import type { JobListing } from '../../types';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

interface LocationFilterExampleProps {
  jobListings: JobListing[];
}

const LocationFilterExample: React.FC<LocationFilterExampleProps> = ({ jobListings }) => {
  // State for selected location filters
  const [selectedRegions, setSelectedRegions] = useState<SwissRegion[]>([]);
  const [selectedCantons, setSelectedCantons] = useState<SwissCanton[]>([]);
  const [selectedSubRegions, setSelectedSubRegions] = useState<SwissSubRegion[]>([]);
  
  // Handler for region selection changes
  const handleRegionChange = (region: SwissRegion, isSelected: boolean) => {
    setSelectedRegions(prev => 
      isSelected 
        ? [...prev, region] 
        : prev.filter(r => r !== region)
    );
  };
  
  // Handler for canton selection changes
  const handleCantonChange = (canton: SwissCanton, isSelected: boolean) => {
    setSelectedCantons(prev => 
      isSelected 
        ? [...prev, canton] 
        : prev.filter(c => c !== canton)
    );
  };
  
  // Handler for sub-region selection changes
  const handleSubRegionChange = (subRegion: SwissSubRegion, isSelected: boolean) => {
    setSelectedSubRegions(prev => 
      isSelected 
        ? [...prev, subRegion] 
        : prev.filter(sr => sr !== subRegion)
    );
  };
  
  // Filter jobs based on selected locations
  const filteredJobs = jobListings.filter(job => {
    // If no filters are selected, show all jobs
    if (
      selectedRegions.length === 0 && 
      selectedCantons.length === 0 && 
      selectedSubRegions.length === 0
    ) {
      return true;
    }
    
    // Check if job matches any selected sub-region (highest priority)
    if (selectedSubRegions.length > 0) {
      return selectedSubRegions.some(subRegion => isJobInSubRegion(job, subRegion));
    }
    
    // Check if job matches any selected canton (medium priority)
    if (selectedCantons.length > 0) {
      return selectedCantons.some(canton => isJobInCanton(job, canton));
    }
    
    // Check if job matches any selected region (lowest priority)
    if (selectedRegions.length > 0) {
      return selectedRegions.some(region => isJobInRegion(job, region));
    }
    
    return false;
  });
  
  return (
    <div className="flex flex-col md:flex-row gap-6">
      {/* Filters sidebar */}
      <div className="w-full md:w-80 flex-shrink-0">
        <Card>
          <CardHeader>
            <CardTitle>Filter Jobs</CardTitle>
          </CardHeader>
          <CardContent>
            <LocationFilter 
              selectedRegions={selectedRegions}
              selectedCantons={selectedCantons}
              selectedSubRegions={selectedSubRegions}
              onRegionChange={handleRegionChange}
              onCantonChange={handleCantonChange}
              onSubRegionChange={handleSubRegionChange}
              showSubRegions={true}
            />
          </CardContent>
        </Card>
      </div>
      
      {/* Job listings */}
      <div className="flex-grow">
        <div className="mb-4">
          <p className="text-sm text-muted-foreground">
            {filteredJobs.length} job{filteredJobs.length !== 1 ? 's' : ''} found
          </p>
        </div>
        
        <div className="space-y-4">
          {filteredJobs.map(job => (
            <Card key={job.id}>
              <CardContent className="pt-6">
                <h3 className="text-xl font-semibold mb-2">{job.title}</h3>
                <div className="text-sm text-muted-foreground mb-2">
                  {job.company.name}
                </div>
                <div className="flex flex-wrap gap-2 mb-4">
                  {job.primary_canton_code && (
                    <span className="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                      {job.city || ''} {job.city ? ',' : ''} {job.primary_canton_code}
                    </span>
                  )}
                  {job.is_remote && (
                    <span className="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                      Remote
                    </span>
                  )}
                  {job.has_multiple_locations && (
                    <span className="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20">
                      Multiple Locations
                    </span>
                  )}
                </div>
              </CardContent>
            </Card>
          ))}
          
          {filteredJobs.length === 0 && (
            <div className="text-center py-10">
              <h3 className="text-lg font-medium">No jobs match your filters</h3>
              <p className="text-sm text-muted-foreground mt-2">
                Try adjusting your location filters to see more results
              </p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default LocationFilterExample;
