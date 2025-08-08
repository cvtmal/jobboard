import { cn } from '@/lib/utils';
import { HelpCircle } from 'lucide-react';
import { ReactNode } from 'react';

interface FieldHelperProps {
    children: ReactNode;
    className?: string;
    showIcon?: boolean;
}

export function FieldHelper({ children, className, showIcon = true }: FieldHelperProps) {
    return (
        <div className={cn('flex items-start gap-2 text-sm text-muted-foreground mt-1', className)}>
            {showIcon && <HelpCircle className="h-4 w-4 mt-0.5 flex-shrink-0" />}
            <div>{children}</div>
        </div>
    );
}

interface CharacterCountProps {
    current: number;
    max?: number;
    className?: string;
}

export function CharacterCount({ current, max, className }: CharacterCountProps) {
    const isOverLimit = max && current > max;
    
    return (
        <div className={cn(
            'text-xs text-right mt-1',
            isOverLimit ? 'text-red-500' : 'text-muted-foreground',
            className
        )}>
            {current}{max && `/${max}`}
            {isOverLimit && ' (over limit)'}
        </div>
    );
}