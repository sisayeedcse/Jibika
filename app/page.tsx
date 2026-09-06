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
    <div className="flex flex-col min-h-screen bg-white">
      {/* Hero Section */}
      <section className="relative px-4 py-24 overflow-hidden bg-gradient-to-br from-green-50 via-white to-orange-50">
        {/* Soft Decorative Blobs */}
        <div className="absolute top-0 left-0 w-96 h-96 bg-primary-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -translate-x-1/2 -translate-y-1/2 animate-blob" />
        <div className="absolute top-0 right-0 w-96 h-96 bg-accent-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 translate-x-1/3 -translate-y-1/4 animate-blob animation-delay-2000" />
        <div className="absolute -bottom-32 left-1/2 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -translate-x-1/2 animate-blob animation-delay-4000" />
        
        <div className="container mx-auto max-w-5xl relative z-10 text-center space-y-10">
          
          <h1 className="text-5xl md:text-7xl font-extrabold text-slate-900 tracking-tight leading-tight animate-in fade-in slide-in-from-bottom-6 duration-700">
            {t('app.tagline')}
          </h1>
          
          <p className="text-xl md:text-3xl text-slate-600 font-medium max-w-4xl mx-auto leading-relaxed animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100">
            {language === 'en' 
              ? 'Connecting verified livelihood needs with small-scale capital through trust and AI.' 
              : 'বিশ্বাস এবং কৃত্রিম বুদ্ধিমত্তার মাধ্যমে যাচাইকৃত কর্মীদের সাথে ক্ষুদ্র মূলধনের সংযোগ স্থাপন।'}
          </p>
          
          <div className="pt-8 flex justify-center animate-in fade-in slide-in-from-bottom-10 duration-700 delay-200">
            <Button size="lg" className="bg-primary-600 hover:bg-primary-700 text-white font-bold px-10 py-7 rounded-2xl text-xl shadow-xl hover:shadow-2xl transition-all" onClick={() => router.push('/register')}>
              {language === 'en' ? 'Create a Worker Profile' : 'কর্মীর প্রোফাইল তৈরি করুন'}
            </Button>
          </div>
        </div>
      </section>

      {/* Live Stats Strip */}
      <section className="bg-primary-700 text-white py-12 shadow-inner">
        <div className="container mx-auto max-w-6xl px-4 grid grid-cols-1 md:grid-cols-3 gap-10 text-center divide-y md:divide-y-0 md:divide-x divide-primary-600/50">
          <div className="flex flex-col items-center justify-center space-y-3 pt-6 md:pt-0">
            <div className="p-3 bg-white/10 rounded-2xl mb-2">
              <Users className="h-8 w-8 text-primary-100" />
            </div>
            <span className="text-5xl font-black tracking-tight">{totalWorkers}</span>
            <span className="text-primary-200 font-bold tracking-widest uppercase text-sm">Workers Supported</span>
          </div>
          <div className="flex flex-col items-center justify-center space-y-3 pt-6 md:pt-0">
            <div className="p-3 bg-white/10 rounded-2xl mb-2">
              <Target className="h-8 w-8 text-primary-100" />
            </div>
            <span className="text-5xl font-black tracking-tight">৳{totalFunded.toLocaleString()}</span>
            <span className="text-primary-200 font-bold tracking-widest uppercase text-sm">Capital Deployed</span>
          </div>
          <div className="flex flex-col items-center justify-center space-y-3 pt-6 md:pt-0">
            <div className="p-3 bg-white/10 rounded-2xl mb-2">
              <CheckCircle2 className="h-8 w-8 text-primary-100" />
            </div>
            <span className="text-5xl font-black tracking-tight">{deliveredAssets}</span>
            <span className="text-primary-200 font-bold tracking-widest uppercase text-sm">Assets Delivered</span>
          </div>
        </div>
      </section>

      {/* Role Selectors */}
      <section className="container mx-auto px-4 py-24 max-w-6xl">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-extrabold text-slate-900 mb-6">
            {language === 'en' ? 'What brings you to JIBIKA?' : 'আপনি জীবিকায় কেন এসেছেন?'}
          </h2>
          <p className="text-xl text-slate-500 max-w-2xl mx-auto">
            {language === 'en' 
              ? 'Select your role to explore the ecosystem and see how we build trust at every step.' 
              : 'ইকোসিস্টেম অন্বেষণ করতে আপনার ভূমিকা নির্বাচন করুন।'}
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-24">
          {[
            { role: 'WORKER' as Role, route: '/worker', icon: Scissors, colors: { bg: 'bg-primary-50', text: 'text-primary-700', border: 'hover:border-primary-500 hover:ring-primary-100' }, title: t('role.worker'), desc: 'Access productive assets to grow your livelihood.' },
            { role: 'COMMUNITY' as Role, route: '/community', icon: ShieldCheck, colors: { bg: 'bg-blue-50', text: 'text-blue-700', border: 'hover:border-blue-500 hover:ring-blue-100' }, title: t('role.community'), desc: 'Verify workers and build community trust.' },
            { role: 'CONTRIBUTOR' as Role, route: '/contributor', icon: Coins, colors: { bg: 'bg-accent-50', text: 'text-accent-700', border: 'hover:border-accent-500 hover:ring-accent-100' }, title: t('role.contributor'), desc: 'Fund verified livelihood opportunities.' },
            { role: 'VENDOR' as Role, route: '/vendor', icon: Store, colors: { bg: 'bg-orange-50', text: 'text-orange-700', border: 'hover:border-orange-500 hover:ring-orange-100' }, title: 'I am a Vendor', desc: 'Fulfill procurement orders and deliver assets.' }
          ].map((item) => (
            <Card 
              key={item.role}
              className={`border-2 border-slate-100 ${item.colors.border} hover:ring-4 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:-translate-y-2 group bg-white`}
              onClick={() => handleRoleSelect(item.role, item.route)}
            >
              <CardContent className="p-8 flex flex-col h-full text-center space-y-6">
                <div className={`h-24 w-24 ${item.colors.bg} rounded-[2rem] flex items-center justify-center ${item.colors.text} mx-auto group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-sm`}>
                  <item.icon className="h-12 w-12" />
                </div>
                <div>
                  <h3 className="text-2xl font-bold text-slate-900 mb-3">{item.title}</h3>
                  <p className="text-slate-600 font-medium text-base leading-relaxed">
                    {language === 'en' ? item.desc : (item.role === 'WORKER' ? 'আপনার জীবিকা বাড়াতে সম্পদের অ্যাক্সেস নিন।' : 'সম্প্রদায়ের বিশ্বাস তৈরি করুন।')}
                  </p>
                </div>
                <div className={`mt-auto pt-4 ${item.colors.text} font-bold text-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity`}>
                  {t('ui.continue')} <ArrowRight className="ml-2 h-5 w-5" />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Impact Stories Preview */}
        <div className="bg-slate-50 rounded-[3rem] p-10 md:p-16 border border-slate-200 shadow-sm mb-16 relative overflow-hidden">
          <div className="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
            <CheckCircle2 className="w-64 h-64" />
          </div>
          <h2 className="text-3xl md:text-4xl font-extrabold text-slate-900 mb-10 flex items-center gap-3 relative z-10">
            <span className="text-3xl">🌟</span> {language === 'en' ? 'Real Impact' : 'প্রকৃত প্রভাব'}
          </h2>
          <div className="grid md:grid-cols-2 gap-8 relative z-10">
            <div className="flex flex-col md:flex-row gap-6 items-start p-8 rounded-3xl bg-white border border-slate-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="w-20 h-20 rounded-full bg-primary-50 shrink-0 flex items-center justify-center text-3xl shadow-inner border border-primary-100">🧵</div>
              <div>
                <h4 className="text-xl font-bold text-slate-900">Rina Akter, Cumilla</h4>
                <p className="text-base text-slate-600 font-medium mt-3 italic leading-relaxed">"The embroidery machine arrived last month. I have already hired two assistants from my village."</p>
                <Badge variant="success" className="mt-4 px-3 py-1">Delivered</Badge>
              </div>
            </div>
            <div className="flex flex-col md:flex-row gap-6 items-start p-8 rounded-3xl bg-white border border-slate-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="w-20 h-20 rounded-full bg-accent-50 shrink-0 flex items-center justify-center text-3xl shadow-inner border border-accent-100">🔨</div>
              <div>
                <h4 className="text-xl font-bold text-slate-900">Jamal Hossain, Khulna</h4>
                <p className="text-base text-slate-600 font-medium mt-3 italic leading-relaxed">"Modern power tools have doubled my production speed. I can finally accept commercial contracts."</p>
                <Badge variant="success" className="mt-4 px-3 py-1">Monitoring</Badge>
              </div>
            </div>
          </div>
        </div>

        {/* Admin Link */}
        <div className="flex justify-center pb-8 pt-8">
          <Button variant="ghost" className="text-slate-500 hover:text-slate-900 font-medium text-lg px-6 py-6 rounded-xl bg-slate-100 hover:bg-slate-200 transition-all" onClick={() => handleRoleSelect('ADMIN', '/admin')}>
            <ActivitySquare className="mr-3 h-5 w-5" /> Go to Admin Dashboard
          </Button>
        </div>
      </section>
    </div>
  );
}
