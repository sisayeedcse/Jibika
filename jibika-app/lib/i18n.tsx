"use client";

import React, { createContext, useContext, useState, ReactNode, useEffect } from 'react';

type Language = 'en' | 'bn';

interface I18nContextType {
  language: Language;
  setLanguage: (lang: Language) => void;
  t: (key: string) => string;
}

const translations: Record<Language, Record<string, string>> = {
  en: {
    // Navigation
    'app.title': 'JIBIKA',
    'app.tagline': 'From Small Capital to Productive Livelihood',
    
    // Roles
    'role.worker': 'I am a Worker',
    'role.contributor': 'I want to Contribute',
    'role.community': 'I am a Community Verifier',
    'role.admin': 'Admin Dashboard',
    
    // Worker Onboarding
    'worker.welcome': 'Welcome to JIBIKA',
    'worker.req.title': 'Request an Asset',
    'worker.req.step1': 'What do you need?',
    'worker.req.step2': 'How will this help you?',
    'worker.req.amount': 'Estimated Cost (৳)',
    'worker.req.submit': 'Submit Request',
    
    // Status
    'status.requested': 'Requested',
    'status.verified': 'Verified',
    'status.ai_assessed': 'AI Matched',
    'status.funding': 'Funding',
    'status.funded': 'Funded',
    'status.procured': 'Procured',
    'status.delivered': 'Delivered',
    'status.monitoring': 'Monitoring',
    
    // Contributor
    'contrib.discover': 'Discover Opportunities',
    'contrib.suggested': 'Suggested for you',
    'contrib.fund': 'Fund this Asset',
    'contrib.amount': 'Amount to contribute',
    
    // UI Elements
    'ui.continue': 'Continue',
    'ui.cancel': 'Cancel',
    'ui.approve': 'Approve',
    'ui.flag': 'Flag',
    'ui.demo_data': 'DEMO DATA',
  },
  bn: {
    // Navigation
    'app.title': 'জীবিকা',
    'app.tagline': 'ক্ষুদ্র মূলধন থেকে উৎপাদনশীল জীবনযাত্রা',
    
    // Roles
    'role.worker': 'আমি একজন কর্মী',
    'role.contributor': 'আমি অনুদান দিতে চাই',
    'role.community': 'আমি একজন যাচাইকারী',
    'role.admin': 'অ্যাডমিন ড্যাশবোর্ড',
    
    // Worker Onboarding
    'worker.welcome': 'জীবিকাতে স্বাগতম',
    'worker.req.title': 'সম্পদ অনুরোধ করুন',
    'worker.req.step1': 'আপনার কী প্রয়োজন?',
    'worker.req.step2': 'এটি আপনাকে কীভাবে সাহায্য করবে?',
    'worker.req.amount': 'আনুমানিক খরচ (৳)',
    'worker.req.submit': 'অনুরোধ জমা দিন',
    
    // Status
    'status.requested': 'অনুরোধ করা হয়েছে',
    'status.verified': 'যাচাইকৃত',
    'status.ai_assessed': 'এআই দ্বারা মূল্যায়িত',
    'status.funding': 'ফান্ডিং চলছে',
    'status.funded': 'সম্পূর্ণ ফান্ডেড',
    'status.procured': 'ক্রয় করা হয়েছে',
    'status.delivered': 'হস্তান্তর করা হয়েছে',
    'status.monitoring': 'পর্যবেক্ষণ চলছে',
    
    // Contributor
    'contrib.discover': 'সুযোগগুলো দেখুন',
    'contrib.suggested': 'আপনার জন্য প্রস্তাবিত',
    'contrib.fund': 'এই সম্পদে ফান্ড দিন',
    'contrib.amount': 'ফান্ডের পরিমাণ',
    
    // UI Elements
    'ui.continue': 'চালিয়ে যান',
    'ui.cancel': 'বাতিল করুন',
    'ui.approve': 'অনুমোদন করুন',
    'ui.flag': 'ফ্ল্যাগ করুন',
    'ui.demo_data': 'ডেমো ডেটা',
  }
};

const I18nContext = createContext<I18nContextType | undefined>(undefined);

export const I18nProvider = ({ children }: { children: ReactNode }) => {
  const [language, setLanguageState] = useState<Language>('en');

  useEffect(() => {
    const saved = localStorage.getItem('jibika_lang') as Language;
    if (saved && (saved === 'en' || saved === 'bn')) {
      setLanguageState(saved);
    }
  }, []);

  const setLanguage = (lang: Language) => {
    setLanguageState(lang);
    localStorage.setItem('jibika_lang', lang);
  };

  const t = (key: string): string => {
    return translations[language][key] || key;
  };

  return (
    <I18nContext.Provider value={{ language, setLanguage, t }}>
      {children}
    </I18nContext.Provider>
  );
};

export const useI18n = () => {
  const context = useContext(I18nContext);
  if (context === undefined) {
    throw new Error('useI18n must be used within an I18nProvider');
  }
  return context;
};
