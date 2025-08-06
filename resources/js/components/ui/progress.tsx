import { cn } from '@/lib/utils';

interface ProgressProps {
    value: number;
    className?: string;
}

export function Progress({ value, className }: ProgressProps) {
    const percentage = Math.min(Math.max(value, 0), 100);
    
    return (
        <div className={cn('relative h-2 w-full overflow-hidden rounded-full bg-secondary', className)}>
            <div
                className="h-full bg-primary transition-all duration-300 ease-in-out"
                style={{ width: `${percentage}%` }}
            />
        </div>
    );
}