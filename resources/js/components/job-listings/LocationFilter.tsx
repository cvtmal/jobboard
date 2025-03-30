import React, { useState } from 'react';
import { SwissCanton, swissCantonData } from '../../types/enums/SwissCanton';
import { SwissRegion, swissRegionData } from '../../types/enums/SwissRegion';
import { SwissSubRegion, swissSubRegionData } from '../../types/enums/SwissSubRegion';
import { getSubRegionsForCanton, getSubRegionsForRegion } from '../../utils/locationFilters';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '../ui/accordion';
import { Checkbox } from '../ui/checkbox';
import { Label } from '../ui/label';

interface LocationFilterProps {
    selectedRegions: SwissRegion[];
    selectedCantons: SwissCanton[];
    selectedSubRegions: SwissSubRegion[];
    onRegionChange: (region: SwissRegion, isSelected: boolean) => void;
    onCantonChange: (canton: SwissCanton, isSelected: boolean) => void;
    onSubRegionChange: (subRegion: SwissSubRegion, isSelected: boolean) => void;
    showSubRegions?: boolean;
}

const LocationFilter: React.FC<LocationFilterProps> = ({
    selectedRegions,
    selectedCantons,
    selectedSubRegions,
    onRegionChange,
    onCantonChange,
    onSubRegionChange,
    showSubRegions = true,
}) => {
    // Keep track of expanded accordion sections
    const [expandedRegions, setExpandedRegions] = useState<Record<string, boolean>>({});

    const toggleRegionExpansion = (region: SwissRegion) => {
        setExpandedRegions((prev) => ({
            ...prev,
            [region]: !prev[region],
        }));
    };

    // Get cantons for a region
    const getCantonsForRegion = (region: SwissRegion): SwissCanton[] => {
        return Object.values(swissCantonData)
            .filter((canton) => canton.region === region)
            .map((canton) => canton.id);
    };

    // Check if all cantons in a region are selected
    const isRegionFullySelected = (region: SwissRegion): boolean => {
        const cantons = getCantonsForRegion(region);
        return cantons.every((canton) => selectedCantons.includes(canton));
    };

    // Check if some but not all cantons in a region are selected
    const isRegionPartiallySelected = (region: SwissRegion): boolean => {
        const cantons = getCantonsForRegion(region);
        const selected = cantons.filter((canton) => selectedCantons.includes(canton));
        return selected.length > 0 && selected.length < cantons.length;
    };

    // Handle click on a region checkbox
    const handleRegionClick = (region: SwissRegion) => {
        const shouldSelect = !isRegionFullySelected(region);

        // Update the region state
        onRegionChange(region, shouldSelect);

        // Update all cantons in this region
        const cantons = getCantonsForRegion(region);
        cantons.forEach((canton) => {
            onCantonChange(canton, shouldSelect);
        });

        // If we're selecting and showing sub-regions, expand the section
        if (shouldSelect && showSubRegions) {
            setExpandedRegions((prev) => ({
                ...prev,
                [region]: true,
            }));
        }

        // Update all sub-regions in this region if we're showing them
        if (showSubRegions) {
            const subRegions = getSubRegionsForRegion(region);
            subRegions.forEach((subRegion) => {
                onSubRegionChange(subRegion, shouldSelect);
            });
        }
    };

    // Handle click on a canton checkbox
    const handleCantonClick = (canton: SwissCanton) => {
        const shouldSelect = !selectedCantons.includes(canton);

        // Update the canton state
        onCantonChange(canton, shouldSelect);

        // Update all sub-regions in this canton if we're showing them
        if (showSubRegions) {
            const subRegions = getSubRegionsForCanton(canton);
            subRegions.forEach((subRegion) => {
                onSubRegionChange(subRegion, shouldSelect);
            });
        }

        // Check if we need to update the region state
        const cantonRegion = swissCantonData[canton].region;
        const regionCantons = getCantonsForRegion(cantonRegion);

        if (shouldSelect) {
            // If all cantons in the region are now selected, select the region
            const otherCantons = regionCantons.filter((c) => c !== canton);
            if (otherCantons.every((c) => selectedCantons.includes(c))) {
                onRegionChange(cantonRegion, true);
            }
        } else {
            // If the region was fully selected, it's now partially selected
            if (isRegionFullySelected(cantonRegion)) {
                onRegionChange(cantonRegion, false);
            }
        }
    };

    // Handle click on a sub-region checkbox
    const handleSubRegionClick = (subRegion: SwissSubRegion) => {
        const shouldSelect = !selectedSubRegions.includes(subRegion);
        onSubRegionChange(subRegion, shouldSelect);
    };

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-semibold">Location</h3>

            <Accordion type="multiple" className="w-full">
                {Object.values(SwissRegion).map((region) => {
                    const isFullySelected = isRegionFullySelected(region);
                    const isPartiallySelected = isRegionPartiallySelected(region);
                    const regionData = swissRegionData[region];

                    return (
                        <AccordionItem value={region} key={region} className="border-b">
                            <div className="flex items-center space-x-2 py-2">
                                <Checkbox
                                    id={`region-${region}`}
                                    checked={isFullySelected}
                                    data-state={isPartiallySelected ? 'indeterminate' : isFullySelected ? 'checked' : 'unchecked'}
                                    onCheckedChange={() => handleRegionClick(region)}
                                    className="data-[state=indeterminate]:bg-primary data-[state=indeterminate]:opacity-70"
                                />
                                <Label htmlFor={`region-${region}`} className="flex-grow cursor-pointer text-base font-medium">
                                    {regionData.label}
                                </Label>
                                <AccordionTrigger onClick={() => toggleRegionExpansion(region)} className="pr-0 hover:no-underline" />
                            </div>

                            <AccordionContent>
                                <div className="ml-6 space-y-3">
                                    {getCantonsForRegion(region).map((canton) => {
                                        const cantonData = swissCantonData[canton];
                                        const cantonSubRegions = getSubRegionsForCanton(canton);
                                        const hasSubRegions = showSubRegions && cantonSubRegions.length > 1;

                                        return (
                                            <div key={canton} className="space-y-2">
                                                <div className="flex items-center space-x-2">
                                                    <Checkbox
                                                        id={`canton-${canton}`}
                                                        checked={selectedCantons.includes(canton)}
                                                        onCheckedChange={() => handleCantonClick(canton)}
                                                    />
                                                    <Label htmlFor={`canton-${canton}`} className="cursor-pointer">
                                                        {cantonData.label}
                                                    </Label>
                                                </div>

                                                {hasSubRegions && (
                                                    <div className="ml-6 space-y-2">
                                                        {cantonSubRegions.map((subRegion) => {
                                                            const subRegionData = swissSubRegionData[subRegion];

                                                            return (
                                                                <div key={subRegion} className="flex items-center space-x-2">
                                                                    <Checkbox
                                                                        id={`subregion-${subRegion}`}
                                                                        checked={selectedSubRegions.includes(subRegion)}
                                                                        onCheckedChange={() => handleSubRegionClick(subRegion)}
                                                                    />
                                                                    <Label htmlFor={`subregion-${subRegion}`} className="cursor-pointer text-sm">
                                                                        {subRegionData.label}
                                                                    </Label>
                                                                </div>
                                                            );
                                                        })}
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    );
                })}
            </Accordion>
        </div>
    );
};

export default LocationFilter;
