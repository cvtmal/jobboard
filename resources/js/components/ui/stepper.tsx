import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';

interface StepperStep {
    title: string;
    description?: string;
    isCompleted?: boolean;
    isCurrent?: boolean;
}

interface StepperProps {
    steps: StepperStep[];
    className?: string;
    onStepClick?: (stepIndex: number) => void;
}

export function Stepper({ steps, className, onStepClick }: StepperProps) {
    return (
        <div className={cn('flex items-center justify-between', className)}>
            {steps.map((step, index) => (
                <div key={index} className="flex items-center flex-1">
                    <div 
                        className={cn(
                            "flex items-center",
                            onStepClick && (step.isCompleted || step.isCurrent) && "cursor-pointer"
                        )}
                        onClick={() => {
                            if (onStepClick && (step.isCompleted || step.isCurrent)) {
                                onStepClick(index + 1);
                            }
                        }}
                    >
                        {/* Step Circle */}
                        <div
                            className={cn(
                                'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-medium transition-colors',
                                step.isCompleted
                                    ? 'border-green-500 bg-green-500 text-white'
                                    : step.isCurrent
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-muted-foreground/20 bg-muted text-muted-foreground',
                                onStepClick && (step.isCompleted || step.isCurrent) && "hover:scale-105"
                            )}
                        >
                            {step.isCompleted ? (
                                <Check className="h-4 w-4" />
                            ) : (
                                <span>{index + 1}</span>
                            )}
                        </div>

                        {/* Step Text */}
                        <div className="ml-3">
                            <div
                                className={cn(
                                    'text-sm font-medium',
                                    step.isCompleted
                                        ? 'text-green-700'
                                        : step.isCurrent
                                        ? 'text-foreground'
                                        : 'text-muted-foreground'
                                )}
                            >
                                {step.title}
                            </div>
                            {step.description && (
                                <div className="text-xs text-muted-foreground">
                                    {step.description}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Connector Line */}
                    {index < steps.length - 1 && (
                        <div
                            className={cn(
                                'mx-4 h-px flex-1 transition-colors',
                                step.isCompleted
                                    ? 'bg-green-500'
                                    : 'bg-muted-foreground/20'
                            )}
                        />
                    )}
                </div>
            ))}
        </div>
    );
}