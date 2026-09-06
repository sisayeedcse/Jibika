"use client";

import { useI18n } from '@/lib/i18n';
import { useJibikaStore } from '@/store/jibikaStore';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Scissors, Coins, ShieldCheck, ArrowRight, ActivitySquare, Store, Users, Target, CheckCircle2 } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { Role } from '@/types';
import { useEffect, useState } from 'react';

export default function LandingPage() {
  const { t, language } = useI18n();
  const { setActiveRole, workers, fundingPools, assetRequests } = useJibikaStore();
  const router = useRouter();
  const [mounted, setMounted] = useState(false);

  useEffect(() => setMounted(true), []);

  const handleRoleSelect = (role: Role, route: string) => {
    // Automatically login as the first available worker for demo flow
    const demoUserId = role === 'WORKER' ? 'w-1' : 'usr-1';
    setActiveRole(role, demoUserId);
    router.push(route);
  };

  // Derive stats
  const totalWorkers = workers.length;
  const totalFunded = fundingPools.reduce((sum, pool) => sum + pool.currentAmount, 0);
  const deliveredAssets = assetRequests.filter(r => r.status === 'DELIVERED' || r.status === 'MONITORING').length;

  if (!mounted) return null;

  return (
    <div className="flex flex-col min-h-screen bg-slate-50 dark:bg-slate-900">
      {/* Hero Section */}
      <section className="relative px-4 py-20 overflow-hidden bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
        <div className="absolute inset-0 bg-gradient-to-br from-primary-50 to-transparent dark:from-primary-900/10 z-0" />
        <div className="container mx-auto max-w-5xl relative z-10 text-center space-y-8">
          <Badge variant="success" className="mb-4 px-3 py-1 animate-in fade-in slide-in-from-bottom-4 duration-500">
            {t('ui.demo_data')} - Innovation Fair 2026
          </Badge>
          <h1 className="text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight animate-in fade-in slide-in-from-bottom-6 duration-700">
            {t('app.tagline')}
          </h1>
          <p className="text-xl md:text-2xl text-slate-600 dark:text-slate-400 font-medium max-w-3xl mx-auto leading-relaxed animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100">
            {language === 'en' 
              ? 'Connecting verified livelihood needs with small-scale capital through trust and AI.' 
              : 'বিশ্বাস এবং কৃত্রিম বুদ্ধিমত্তার মাধ্যমে যাচাইকৃত কর্মীদের সাথে ক্ষুদ্র মূলধনের সংযোগ স্থাপন।'}
          </p>
        </div>
      </section>

      {/* Live Stats Strip */}
      <section className="bg-primary-700 text-white py-8 border-y border-primary-800 shadow-inner">
        <div className="container mx-auto max-w-5xl px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-primary-600">
          <div className="flex flex-col items-center justify-center space-y-2 pt-4 md:pt-0">
            <Users className="h-8 w-8 text-primary-300 opacity-80" />
            <span className="text-4xl font-black">{totalWorkers}</span>
            <span className="text-primary-200 font-medium tracking-wide uppercase text-sm">Workers Supported</span>
          </div>
          <div className="flex flex-col items-center justify-center space-y-2 pt-4 md:pt-0">
            <Target className="h-8 w-8 text-primary-300 opacity-80" />
            <span className="text-4xl font-black">৳{totalFunded.toLocaleString()}</span>
            <span className="text-primary-200 font-medium tracking-wide uppercase text-sm">Capital Deployed</span>
          </div>
          <div className="flex flex-col items-center justify-center space-y-2 pt-4 md:pt-0">
            <CheckCircle2 className="h-8 w-8 text-primary-300 opacity-80" />
            <span className="text-4xl font-black">{deliveredAssets}</span>
            <span className="text-primary-200 font-medium tracking-wide uppercase text-sm">Assets Delivered</span>
          </div>
        </div>
      </section>

      {/* Role Selectors */}
      <section className="container mx-auto px-4 py-16 max-w-6xl">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-bold mb-4">{language === 'en' ? 'Join the Ecosystem' : 'ইকোসিস্টেমে যোগ দিন'}</h2>
          <p className="text-slate-500 max-w-xl mx-auto">Experience the JIBIKA platform from the perspective of our key participants.</p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          {[
            { role: 'WORKER' as Role, route: '/worker', icon: Scissors, colors: { bg: 'bg-primary-100 dark:bg-primary-900/30', text: 'text-primary-600', border: 'hover:border-primary-500', iconText: 'text-primary-600' }, title: t('role.worker'), desc: 'Access productive assets to grow your livelihood.' },
            { role: 'COMMUNITY' as Role, route: '/community', icon: ShieldCheck, colors: { bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-600', border: 'hover:border-blue-500', iconText: 'text-blue-600' }, title: t('role.community'), desc: 'Verify workers and build community trust.' },
            { role: 'CONTRIBUTOR' as Role, route: '/contributor', icon: Coins, colors: { bg: 'bg-accent-100 dark:bg-accent-900/30', text: 'text-accent-600', border: 'hover:border-accent-500', iconText: 'text-accent-600' }, title: t('role.contributor'), desc: 'Fund verified livelihood opportunities.' },
            { role: 'VENDOR' as Role, route: '/vendor', icon: Store, colors: { bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-600', border: 'hover:border-orange-500', iconText: 'text-orange-600' }, title: 'I am a Vendor', desc: 'Fulfill procurement orders and deliver assets.' }
          ].map((item) => (
            <Card 
              key={item.role}
              className={`border-2 ${item.colors.border} transition-all duration-300 cursor-pointer hover:shadow-xl group bg-white dark:bg-slate-900`}
              onClick={() => handleRoleSelect(item.role, item.route)}
            >
              <CardContent className="p-8 flex flex-col h-full text-center space-y-4">
                <div className={`h-16 w-16 ${item.colors.bg} rounded-2xl flex items-center justify-center ${item.colors.iconText} mx-auto group-hover:scale-110 transition-transform`}>
                  <item.icon className="h-8 w-8" />
                </div>
                <h3 className="text-xl font-bold text-slate-900 dark:text-slate-100">{item.title}</h3>
                <p className="text-slate-500 dark:text-slate-400 text-sm flex-grow">
                  {language === 'en' ? item.desc : (item.role === 'WORKER' ? 'আপনার জীবনযাত্রা উন্নত করতে সম্পদ গ্রহণ করুন।' : 'ইকোসিস্টেমে অংশগ্রহণ করুন।')}
                </p>
                <div className={`mt-4 ${item.colors.text} font-semibold flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity`}>
                  {t('ui.continue')} <ArrowRight className="ml-1 h-4 w-4" />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Impact Stories Preview */}
        <div className="bg-white dark:bg-slate-900 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 shadow-sm mb-16">
          <h2 className="text-2xl font-bold mb-6 flex items-center gap-2">
            <span className="text-xl">🌟</span> {language === 'en' ? 'Real Impact (Demo Data)' : 'প্রকৃত প্রভাব (ডেমো ডেটা)'}
          </h2>
          <div className="grid md:grid-cols-2 gap-6">
            <div className="flex gap-4 items-start p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800">
              <div className="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 shrink-0 flex items-center justify-center text-2xl">🧵</div>
              <div>
                <h4 className="font-bold">Rina Akter, Cumilla</h4>
                <p className="text-sm text-slate-600 dark:text-slate-400 mt-1">"The embroidery machine arrived last month. I have already hired two assistants from my village."</p>
                <Badge variant="success" className="mt-2 scale-90 origin-left">Delivered</Badge>
              </div>
            </div>
            <div className="flex gap-4 items-start p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800">
              <div className="w-16 h-16 rounded-full bg-accent-100 dark:bg-accent-900/30 shrink-0 flex items-center justify-center text-2xl">🔧</div>
              <div>
                <h4 className="font-bold">Jamal Hossain, Khulna</h4>
                <p className="text-sm text-slate-600 dark:text-slate-400 mt-1">"Modern power tools have doubled my production speed. I can finally accept commercial contracts."</p>
                <Badge variant="success" className="mt-2 scale-90 origin-left">Monitoring</Badge>
              </div>
            </div>
          </div>
        </div>

        {/* Admin Link */}
        <div className="flex justify-center pb-8 border-t border-slate-200 dark:border-slate-800 pt-8">
          <Button variant="ghost" className="text-slate-500 hover:text-slate-900 dark:hover:text-white" onClick={() => handleRoleSelect('ADMIN', '/admin')}>
            <ActivitySquare className="mr-2 h-4 w-4" /> Go to Admin Dashboard
          </Button>
        </div>
      </section>
    </div>
  );
}
