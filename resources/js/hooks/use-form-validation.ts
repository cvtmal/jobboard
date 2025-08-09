import { useEffect, useState } from 'react';

interface ValidationRule {
    required?: boolean;
    minLength?: number;
    maxLength?: number;
    pattern?: RegExp;
    custom?: (value: any) => string | null;
}

interface ValidationRules {
    [key: string]: ValidationRule;
}

interface ValidationErrors {
    [key: string]: string;
}

export function useFormValidation<T extends Record<string, any>>(data: T, rules: ValidationRules) {
    const [errors, setErrors] = useState<ValidationErrors>({});
    const [touched, setTouched] = useState<Record<string, boolean>>({});

    const validateField = (field: string, value: any): string | null => {
        const rule = rules[field];
        if (!rule) return null;

        if (rule.required && (!value || (typeof value === 'string' && !value.trim()))) {
            return 'This field is required';
        }

        if (typeof value === 'string') {
            if (rule.minLength && value.length < rule.minLength) {
                return `Minimum length is ${rule.minLength} characters`;
            }

            if (rule.maxLength && value.length > rule.maxLength) {
                return `Maximum length is ${rule.maxLength} characters`;
            }

            if (rule.pattern && !rule.pattern.test(value)) {
                return 'Invalid format';
            }
        }

        if (rule.custom) {
            return rule.custom(value);
        }

        return null;
    };

    const validateAll = (): ValidationErrors => {
        const newErrors: ValidationErrors = {};

        Object.keys(rules).forEach((field) => {
            const error = validateField(field, data[field as keyof T]);
            if (error) {
                newErrors[field] = error;
            }
        });

        return newErrors;
    };

    const validateSingle = (field: string): string | null => {
        return validateField(field, data[field as keyof T]);
    };

    const markFieldTouched = (field: string) => {
        setTouched((prev) => ({ ...prev, [field]: true }));
    };

    const isFieldValid = (field: string): boolean => {
        return !errors[field] && touched[field];
    };

    const isFormValid = (): boolean => {
        const allErrors = validateAll();
        return Object.keys(allErrors).length === 0;
    };

    // Validate on data changes for touched fields
    useEffect(() => {
        const newErrors: ValidationErrors = {};

        Object.keys(touched).forEach((field) => {
            if (touched[field]) {
                const error = validateField(field, data[field as keyof T]);
                if (error) {
                    newErrors[field] = error;
                }
            }
        });

        setErrors(newErrors);
    }, [data, touched]);

    return {
        errors,
        touched,
        validateAll,
        validateSingle,
        markFieldTouched,
        isFieldValid,
        isFormValid,
        setErrors,
    };
}
