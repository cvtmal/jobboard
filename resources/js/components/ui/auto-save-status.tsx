import { cn } from '@/lib/utils';
import { Check, Loader2, AlertCircle } from 'lucide-react';

type SaveStatus = 'idle' | 'saving' | 'saved' | 'error';

interface AutoSaveStatusProps {
    status: SaveStatus;
    className?: string;
    lastSavedAt?: Date;
}

export function AutoSaveStatus({ status, className, lastSavedAt }: AutoSaveStatusProps) {
    const getStatusConfig = (status: SaveStatus) => {
        switch (status) {
            case 'saving':
                return {
                    icon: <Loader2 className="h-4 w-4 animate-spin" />,
                    text: 'Saving...',
                    className: 'text-blue-600',
                };
            case 'saved':
                return {
                    icon: <Check className="h-4 w-4" />,
                    text: lastSavedAt 
                        ? `Saved at ${lastSavedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
                        : 'All changes saved',
                    className: 'text-green-600',
                };
            case 'error':
                return {
                    icon: <AlertCircle className="h-4 w-4" />,
                    text: 'Failed to save',
                    className: 'text-red-600',
                };
            case 'idle':
            default:
                return null;
        }
    };

    const config = getStatusConfig(status);

    if (!config) {
        return null;
    }

    return (
        <div className={cn('flex items-center gap-2 text-sm', config.className, className)}>
            {config.icon}
            <span>{config.text}</span>
        </div>
    );
}