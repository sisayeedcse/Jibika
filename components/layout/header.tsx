"use client";

import Link from 'next/link';
import { useI18n } from '@/lib/i18n';
import { Button } from '@/components/ui/button';
import { Globe, Menu, Bell, HelpCircle, RotateCcw } from 'lucide-react';
import { useJibikaStore } from '@/store/jibikaStore';
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
    <header className="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:border-slate-800 dark:bg-slate-900/95 shadow-sm">
      <div className="container mx-auto flex h-16 items-center justify-between px-4">
        <div className="flex items-center gap-2">
          <Link href="/" className="flex items-center space-x-2 group">
            <div className="h-9 w-9 rounded-xl bg-gradient-to-br from-primary-700 to-primary-500 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-all">
              J
            </div>
            <span className="font-bold text-xl tracking-tight text-primary-700 dark:text-primary-500 hidden sm:inline-block">
              {t('app.title')}
            </span>
          </Link>
          
          <div className="ml-4 flex items-center gap-2">
            <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
              {activeRole}
            </span>
            <span className="rounded-full bg-accent-100 px-2 py-0.5 text-[10px] font-bold text-accent-700 border border-accent-200 uppercase tracking-widest hidden sm:inline-block">
              {t('ui.demo_data')}
            </span>
          </div>
        </div>

        <div className="flex items-center space-x-1 sm:space-x-2">
          {/* Reset Demo Button */}
          <Button variant="ghost" size="sm" onClick={resetStore} className="hidden md:flex text-slate-500 hover:text-red-600 transition-colors" title="Reset Demo Data">
            <RotateCcw className="h-4 w-4 mr-1" />
            <span className="text-xs">Reset</span>
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
              <div className="absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                <h4 className="font-semibold text-sm mb-3">Notifications</h4>
                <div className="space-y-3">
                  {recentLogs.map(log => (
                    <div key={log.id} className="text-sm pb-3 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                      <p className="font-medium">{language === 'en' ? log.event : log.eventBn}</p>
                      <p className="text-xs text-slate-500 mt-1">{new Date(log.timestamp).toLocaleString()}</p>
                    </div>
                  ))}
                  {recentLogs.length === 0 && <p className="text-sm text-slate-500">No new notifications.</p>}
                </div>
              </div>
            )}
          </div>

          <div className="h-6 w-px bg-slate-200 dark:bg-slate-800 mx-1" />

          {/* Language Toggle */}
          <Button variant="ghost" size="icon" onClick={toggleLanguage} aria-label="Toggle language" className="relative group">
            <Globe className="h-5 w-5 text-slate-600 dark:text-slate-400 group-hover:text-primary-600 transition-colors" />
            <span className="absolute -bottom-1 -right-1 text-[10px] font-bold bg-white dark:bg-slate-900 rounded-full px-1 shadow-sm border border-slate-200 dark:border-slate-700">
              {language === 'en' ? 'EN' : 'বাং'}
            </span>
          </Button>

          <Button variant="ghost" size="icon" className="md:hidden">
            <Menu className="h-5 w-5" />
          </Button>
        </div>
      </div>
    </header>
  );
}
