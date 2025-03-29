import React from 'react';
import { useForm } from '@inertiajs/react';
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';

type Language = {
  name: string;
  code: string;
  flag: string;
};

const languages: Language[] = [
  { name: 'English', code: 'en', flag: '🇬🇧' },
  { name: 'Deutsch', code: 'de', flag: '🇩🇪' },
];

export default function LanguageSwitcher() {
  const { data, setData, post, processing } = useForm({ 
    locale: '' 
  });

  const setLocale = (locale: string) => {
    router.get(window.location.pathname, { locale }, {
      preserveState: true,
      preserveScroll: true,
      only: [],
    });
  };

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" size="sm" className="h-8 w-8 px-0">
          {languages.find(lang => lang.code === document.documentElement.lang)?.flag || '🌐'}
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        {languages.map((language) => (
          <DropdownMenuItem 
            key={language.code}
            onClick={() => setLocale(language.code)}
            className="cursor-pointer"
          >
            <span className="mr-2">{language.flag}</span>
            {language.name}
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
