import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { cn } from '@/lib/utils';
import { ChevronDown, Check } from 'lucide-react';
import { ReactNode, useState } from 'react';

interface FormSectionProps {
    title: string;
    description?: string;
    icon?: ReactNode;
    children: ReactNode;
    isCompleted?: boolean;
    isRequired?: boolean;
    className?: string;
    defaultOpen?: boolean;
}

export function FormSection({
    title,
    description,
    icon,
    children,
    isCompleted = false,
    isRequired = false,
    className,
    defaultOpen = true,
}: FormSectionProps) {
    const [isOpen, setIsOpen] = useState(defaultOpen);

    return (
        <Card className={cn('transition-all duration-200', className)}>
            <Collapsible open={isOpen} onOpenChange={setIsOpen}>
                <CollapsibleTrigger asChild>
                    <CardHeader className="cursor-pointer transition-colors hover:bg-muted/50">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                {icon && <div className="text-primary">{icon}</div>}
                                <div>
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        {title}
                                        {isRequired && <span className="text-red-500 text-sm">*</span>}
                                        {isCompleted && (
                                            <Check className="h-4 w-4 text-green-500" />
                                        )}
                                    </CardTitle>
                                    {description && (
                                        <CardDescription className="mt-1">
                                            {description}
                                        </CardDescription>
                                    )}
                                </div>
                            </div>
                            <ChevronDown
                                className={cn(
                                    'h-5 w-5 transition-transform duration-200',
                                    isOpen && 'rotate-180'
                                )}
                            />
                        </div>
                    </CardHeader>
                </CollapsibleTrigger>
                <CollapsibleContent>
                    <CardContent className="space-y-6 pt-0">
                        {children}
                    </CardContent>
                </CollapsibleContent>
            </Collapsible>
        </Card>
    );
}