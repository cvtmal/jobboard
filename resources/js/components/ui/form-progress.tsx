import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';

interface FormProgressProps {
    currentProgress: number;
    className?: string;
    showLabel?: boolean;
}

export function FormProgress({ currentProgress, className, showLabel = true }: FormProgressProps) {
    const percentage = Math.min(Math.max(currentProgress, 0), 100);
    const isComplete = percentage === 100;
    
    return (
        <div className={cn('space-y-2', className)}>
            {showLabel && (
                <div className="flex justify-between items-center text-sm">
                    <span className="font-medium">Form Progress</span>
                    <span className={cn(
                        'font-medium',
                        isComplete ? 'text-green-600' : 'text-muted-foreground'
                    )}>
                        {Math.round(percentage)}% {isComplete ? 'Complete' : 'Complete'}
                    </span>
                </div>
            )}
            <Progress 
                value={percentage} 
                className={cn(
                    'h-2',
                    isComplete && 'bg-green-100'
                )}
            />
        </div>
    );
}