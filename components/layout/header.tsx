"use client";

import Link from 'next/link';
import { useI18n } from '@/lib/i18n';
import { Button } from '@/components/ui/button';
import { Globe, Menu, Bell, HelpCircle, RotateCcw } from 'lucide-react';
import { useJibikaStore } from '@/store/jibikaStore';
import { Logo } from '@/components/ui/logo';
import { useState, useEffect } from 'react';

export function Header() {
  const { language, setLanguage, t } = useI18n();
  const { activeRole, activeUserId, lifecycleLogs, resetStore } = useJibikaStore();
  const [mounted, setMounted] = useState(false);
  const [showNotifications, setShowNotifications] = useState(false);

  useEffect(() => setMounted(true), []);

  const toggleLanguage = () => {
    setLanguage(language === 'en' ? 'bn' : 'en');
  };

  // Mock notifications based on recent logs for the active role (Simplified logic)
  const recentLogs = lifecycleLogs.slice(-3).reverse();
  const unreadCount = activeRole === 'WORKER' ? recentLogs.length : 0; // Fake count for demo feel

  return (
    <header className="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100">
      <div className="container mx-auto flex h-20 items-center justify-between px-4">
        <div className="flex items-center gap-3">
          <Link href="/" className="flex items-center space-x-3 group">
            <Logo className="h-10 w-10 shadow-md group-hover:shadow-xl group-hover:scale-105 transition-all" />
            <span className="font-extrabold text-2xl tracking-tight text-primary-700 hidden sm:inline-block group-hover:text-primary-600 transition-colors">
              {t('app.title')}
            </span>
          </Link>
          
          <div className="ml-6 flex items-center gap-2">
            <span className="rounded-full bg-slate-100 px-4 py-1.5 text-xs font-bold text-slate-800 border border-slate-200">
              {activeRole}
            </span>
          </div>
        </div>

        <div className="flex items-center space-x-1 sm:space-x-2">
          {/* Reset Button */}
          <Button variant="ghost" size="sm" onClick={resetStore} className="hidden md:flex text-slate-500 hover:text-red-600 transition-colors" title="Reset Data">
            <RotateCcw className="h-4 w-4 mr-2" />
            <span className="text-xs font-semibold uppercase tracking-wider">{t('ui.reset')}</span>
          </Button>

          {/* Help & Support */}
          <Button variant="ghost" size="icon" className="text-slate-500" title="Help & Support">
            <HelpCircle className="h-5 w-5" />
          </Button>

          {/* Notifications */}
          <div className="relative">
            <Button 
              variant="ghost" 
              size="icon" 
              className="text-slate-500 relative"
              onClick={() => setShowNotifications(!showNotifications)}
            >
              <Bell className="h-5 w-5" />
              {unreadCount > 0 && (
                <span className="absolute top-1 right-1 flex h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900" />
              )}
            </Button>
            
            {showNotifications && (
              <div className="absolute right-0 mt-2 w-80 rounded-2xl border border-slate-100 bg-white p-5 shadow-2xl">
                <h4 className="font-bold text-sm mb-3 text-slate-800">Notifications</h4>
                <div className="space-y-3">
                  {recentLogs.map(log => (
                    <div key={log.id} className="text-sm pb-3 border-b border-slate-100 last:border-0 last:pb-0">
                      <p className="font-semibold text-slate-900">{language === 'en' ? log.event : log.eventBn}</p>
                      <p className="text-xs text-slate-500 mt-1">{new Date(log.timestamp).toLocaleString()}</p>
                    </div>
                  ))}
                  {recentLogs.length === 0 && <p className="text-sm text-slate-500">No new notifications.</p>}
                </div>
              </div>
            )}
          </div>

          <div className="h-8 w-px bg-slate-200 mx-2" />

          {/* Language Toggle */}
          <Button variant="ghost" size="icon" onClick={toggleLanguage} aria-label="Toggle language" className="relative group hover:bg-slate-100 rounded-full h-10 w-10">
            <Globe className="h-5 w-5 text-slate-600 group-hover:text-primary-600 transition-colors" />
            <span className="absolute -bottom-1 -right-1 text-[10px] font-bold bg-white text-primary-700 rounded-full px-1.5 shadow-sm border border-slate-200">
              {language === 'en' ? 'EN' : 'বাং'}
            </span>
          </Button>

          <Button variant="ghost" size="icon" className="md:hidden">
            <Menu className="h-6 w-6 text-slate-700" />
          </Button>
        </div>
      </div>
    </header>
  );
}
