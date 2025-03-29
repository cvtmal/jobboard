import { router } from '@inertiajs/react';

type PageProps = {
  locale: {
    current: string;
    available: string[];
  };
};

type TranslationDictionary = {
  [key: string]: string;
};

/**
 * Load translations on demand
 */
export const loadTranslations = async (locale: string): Promise<TranslationDictionary> => {
  try {
    return await import(`../../../lang/${locale}.json`);
  } catch (error) {
    console.error(`Failed to load translations for ${locale}:`, error);
    return {};
  }
};

/**
 * Get current locale from page props
 */
export const getCurrentLocale = (): string => {
  return document.documentElement.lang || 'en';
};

/**
 * Translation function that falls back to the key if translation is not found
 */
export const __ = (key: string, replacements: Record<string, string> = {}): string => {
  try {
    // Access the global translations object that will be populated at runtime
    const translations = (window as any).__translations || {};
    
    let translation = translations[key] || key;
    
    // Replace any placeholders
    Object.entries(replacements).forEach(([placeholder, value]) => {
      translation = translation.replace(`:${placeholder}`, value);
    });
    
    return translation;
  } catch (error) {
    console.error('Translation error:', error);
    return key;
  }
};
