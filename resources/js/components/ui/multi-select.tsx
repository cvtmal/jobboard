import { CheckIcon, ChevronDownIcon, XIcon } from 'lucide-react';
import React, { useState } from 'react';
import { cn } from '@/lib/utils';
import { Button } from './button';
import { Badge } from './badge';
import { Popover, PopoverContent, PopoverTrigger } from './popover';

interface Option {
    value: string;
    label: string;
}

interface MultiSelectProps {
    options: Option[];
    selected: string[];
    onSelectionChange: (selected: string[]) => void;
    placeholder?: string;
    className?: string;
    disabled?: boolean;
    onBlur?: () => void;
}

export function MultiSelect({
    options,
    selected,
    onSelectionChange,
    placeholder = 'Select items...',
    className,
    disabled = false,
    onBlur,
}: MultiSelectProps) {
    const [open, setOpen] = useState(false);

    const toggleSelection = (value: string) => {
        if (selected.includes(value)) {
            onSelectionChange(selected.filter(item => item !== value));
        } else {
            onSelectionChange([...selected, value]);
        }
    };

    const removeItem = (value: string) => {
        onSelectionChange(selected.filter(item => item !== value));
    };

    const getSelectedLabels = () => {
        return selected.map(value => {
            const option = options.find(opt => opt.value === value);
            return option ? option.label : value;
        });
    };

    return (
        <Popover open={open} onOpenChange={(newOpen) => {
            setOpen(newOpen);
            if (!newOpen && onBlur) {
                onBlur();
            }
        }}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    disabled={disabled}
                    className={cn(
                        "w-full justify-between min-h-9 h-auto py-2 px-3",
                        className
                    )}
                >
                    <div className="flex flex-wrap gap-1 min-h-5">
                        {selected.length === 0 ? (
                            <span className="text-muted-foreground">{placeholder}</span>
                        ) : (
                            getSelectedLabels().map((label, index) => (
                                <Badge
                                    key={selected[index]}
                                    variant="secondary"
                                    className="text-xs"
                                >
                                    {label}
                                    <button
                                        type="button"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            removeItem(selected[index]);
                                        }}
                                        className="ml-1 hover:bg-secondary-foreground/20 rounded-full p-0.5"
                                    >
                                        <XIcon className="h-3 w-3" />
                                    </button>
                                </Badge>
                            ))
                        )}
                    </div>
                    <ChevronDownIcon className="h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-full p-0" align="start">
                <div className="max-h-60 overflow-auto">
                    <div className="p-1">
                        {options.map((option) => (
                            <div
                                key={option.value}
                                onClick={() => toggleSelection(option.value)}
                                className={cn(
                                    "flex cursor-pointer items-center justify-between rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground",
                                    selected.includes(option.value) && "bg-accent text-accent-foreground"
                                )}
                            >
                                <span>{option.label}</span>
                                {selected.includes(option.value) && (
                                    <CheckIcon className="h-4 w-4" />
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </PopoverContent>
        </Popover>
    );
}