import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
import { loadTranslations } from './utils/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

interface Page {
    component: string;
    props: Record<string, any>;
    url: string;
    version: string | null;
}

interface InertiaProps {
    initialPage: {
        component: string;
        props: {
            locale: {
                current: string;
                available: string[];
            };
            [key: string]: any;
        };
        url: string;
        version: string | null;
    };
    initialComponent: React.ComponentType<any>;
    resolveComponent: (name: string) => Promise<React.ComponentType<any>>;
}

createInertiaApp({
    title: (title: string) => `${title} - ${appName}`,
    resolve: (name: string) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }: { el: HTMLElement; App: React.ComponentType<any>; props: InertiaProps }) {
        const root = createRoot(el);

        // Load translations based on current locale
        const locale = props.initialPage.props.locale.current;

        // Load translations and store them globally
        loadTranslations(locale)
            .then((translations) => {
                (window as any).__translations = translations;

                // Set HTML lang attribute
                document.documentElement.lang = locale;

                // Render the app after translations are loaded
                root.render(<App {...props} />);
            })
            .catch((error) => {
                console.error('Failed to load translations:', error);
                // Still render the app even if translations failed to load
                root.render(<App {...props} />);
            });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
