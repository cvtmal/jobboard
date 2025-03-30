// Type declarations for components
declare module '@/layouts/company/CompanyLayout' {
    import { ReactNode } from 'react';

    interface CompanyLayoutProps {
        children: ReactNode;
    }

    const CompanyLayout: React.FC<CompanyLayoutProps>;
    export default CompanyLayout;
}

declare module '@/components/ui/alert-dialog' {
    import { ReactNode } from 'react';

    interface AlertDialogProps {
        children: ReactNode;
    }

    interface AlertDialogTriggerProps {
        asChild?: boolean;
        children: ReactNode;
    }

    interface AlertDialogActionProps {
        children: ReactNode;
        className?: string;
        onClick?: () => void;
    }

    interface AlertDialogCancelProps {
        children: ReactNode;
    }

    interface AlertDialogContentProps {
        children: ReactNode;
    }

    interface AlertDialogDescriptionProps {
        children: ReactNode;
    }

    interface AlertDialogFooterProps {
        children: ReactNode;
    }

    interface AlertDialogHeaderProps {
        children: ReactNode;
    }

    interface AlertDialogTitleProps {
        children: ReactNode;
    }

    export const AlertDialog: React.FC<AlertDialogProps>;
    export const AlertDialogTrigger: React.FC<AlertDialogTriggerProps>;
    export const AlertDialogAction: React.FC<AlertDialogActionProps>;
    export const AlertDialogCancel: React.FC<AlertDialogCancelProps>;
    export const AlertDialogContent: React.FC<AlertDialogContentProps>;
    export const AlertDialogDescription: React.FC<AlertDialogDescriptionProps>;
    export const AlertDialogFooter: React.FC<AlertDialogFooterProps>;
    export const AlertDialogHeader: React.FC<AlertDialogHeaderProps>;
    export const AlertDialogTitle: React.FC<AlertDialogTitleProps>;
}

// Additional component type declarations
declare module '@/components/ui/radio-group' {
    import { ReactNode } from 'react';

    interface RadioGroupProps {
        value: string;
        onValueChange: (value: string) => void;
        className?: string;
        children: ReactNode;
    }

    interface RadioGroupItemProps {
        value: string;
        id: string;
    }

    export const RadioGroup: React.FC<RadioGroupProps>;
    export const RadioGroupItem: React.FC<RadioGroupItemProps>;
}

// Inertia type declarations
declare module '@inertiajs/react' {
    import { ReactNode } from 'react';

    // Head Component
    interface HeadProps {
        title: string;
    }
    export const Head: React.FC<HeadProps>;

    // Link Component
    interface LinkProps {
        href: string;
        className?: string;
        preserveScroll?: boolean;
        preserveState?: boolean;
        only?: string[];
        data?: Record<string, any>;
        replace?: boolean;
        method?: string;
        as?: string;
        headers?: Record<string, string>;
        children: ReactNode;
    }
    export const Link: React.FC<LinkProps>;

    // useForm hook
    interface UseFormOptions {
        forceFormData?: boolean;
        resetOnSuccess?: boolean;
        onBefore?: () => void;
        onStart?: () => void;
        onProgress?: (progress: ProgressEvent) => void;
        onSuccess?: (page: any) => void;
        onError?: (errors: Record<string, string>) => void;
        onCancel?: () => void;
        onFinish?: () => void;
    }

    // FormDataType for the useForm hook
    interface FormDataType {
        [key: string]: any;
    }

    interface InertiaFormProps<TForm> {
        data: TForm;
        errors: Record<string, string>;
        hasErrors: boolean;
        processing: boolean;
        progress: ProgressEvent | null;
        wasSuccessful: boolean;
        recentlySuccessful: boolean;
        isDirty: boolean;

        setData: (key: keyof TForm | Record<string, any>, value?: any) => InertiaFormProps<TForm>;
        transform: (callback: (data: TForm) => any) => InertiaFormProps<TForm>;
        setDefaults: () => InertiaFormProps<TForm>;
        reset: (...fields: string[]) => InertiaFormProps<TForm>;
        clearErrors: (...fields: string[]) => InertiaFormProps<TForm>;
        submit: (method: string, url: string, options?: UseFormOptions) => void;
        get: (url: string, options?: UseFormOptions) => void;
        post: (url: string, options?: UseFormOptions) => void;
        put: (url: string, options?: UseFormOptions) => void;
        patch: (url: string, options?: UseFormOptions) => void;
        delete: (url: string, options?: UseFormOptions) => void;
        cancel: () => void;
    }

    export function useForm<TForm = FormDataType>(initialValues?: TForm): InertiaFormProps<TForm>;

    // Router
    export const router: {
        visit: (
            url: string,
            options?: {
                method?: string;
                data?: Record<string, any>;
                replace?: boolean;
                preserveState?: boolean;
                preserveScroll?: boolean;
                only?: string[];
                headers?: Record<string, string>;
                errorBag?: string;
                forceFormData?: boolean;
                onBefore?: () => void;
                onStart?: () => void;
                onProgress?: (progress: ProgressEvent) => void;
                onSuccess?: (page: any) => void;
                onError?: (errors: Record<string, string>) => void;
                onCancel?: () => void;
                onFinish?: () => void;
            },
        ) => void;
        reload: (options?: { only?: string[] }) => void;
        get: (url: string, data?: Record<string, any>, options?: any) => void;
        post: (url: string, data?: Record<string, any>, options?: any) => void;
        put: (url: string, data?: Record<string, any>, options?: any) => void;
        patch: (url: string, data?: Record<string, any>, options?: any) => void;
        delete: (url: string, options?: any) => void;
    };
}

// Global declarations
declare global {
    function route(name: string, params?: Record<string, any> | any, absolute?: boolean): string;
}
