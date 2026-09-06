"use client";

import { useState } from 'react';
import { useJibikaStore } from '@/store/jibikaStore';
import { useRouter } from 'next/navigation';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/lib/i18n';

export default function RegisterPage() {
  const { registerWorker } = useJibikaStore();
  const router = useRouter();
  const { language } = useI18n();
  
  const [formData, setFormData] = useState({
    name: '',
    occupation: '',
    location: '',
    phone: '',
    experience: '',
    story: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    registerWorker(formData);
    router.push('/worker');
  };

  return (
    <div className="container mx-auto px-4 py-16 max-w-xl">
      <Card className="border-0 shadow-2xl rounded-3xl overflow-hidden bg-white/80 backdrop-blur-xl">
        <CardHeader className="bg-gradient-to-br from-primary-50 to-white pb-8 pt-10 border-b border-primary-100">
          <CardTitle className="text-3xl font-extrabold text-center text-slate-900">
            {language === 'en' ? 'Create a Worker Profile' : 'কর্মীর প্রোফাইল তৈরি করুন'}
          </CardTitle>
        </CardHeader>
        <CardContent className="p-8">
          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <label className="text-sm font-bold text-slate-700">Full Name</label>
                <input 
                  type="text" 
                  required 
                  className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                  value={formData.name}
                  onChange={e => setFormData({...formData, name: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-bold text-slate-700">Phone Number</label>
                <input 
                  type="tel" 
                  required 
                  className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                  value={formData.phone}
                  onChange={e => setFormData({...formData, phone: e.target.value})}
                />
              </div>
            </div>

            <div className="grid md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <label className="text-sm font-bold text-slate-700">Occupation</label>
                <input 
                  type="text" 
                  required 
                  placeholder="e.g. Tailor, Farmer"
                  className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                  value={formData.occupation}
                  onChange={e => setFormData({...formData, occupation: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-bold text-slate-700">Experience (Years)</label>
                <input 
                  type="text" 
                  required 
                  placeholder="e.g. 5"
                  className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                  value={formData.experience}
                  onChange={e => setFormData({...formData, experience: e.target.value})}
                />
              </div>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-bold text-slate-700">Location / District</label>
              <input 
                type="text" 
                required 
                className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                value={formData.location}
                onChange={e => setFormData({...formData, location: e.target.value})}
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-bold text-slate-700">Your Story & Needs</label>
              <textarea 
                required 
                rows={4}
                className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none"
                placeholder="Tell us about your work and why you need capital..."
                value={formData.story}
                onChange={e => setFormData({...formData, story: e.target.value})}
              ></textarea>
            </div>
            
            <Button type="submit" size="lg" className="w-full text-lg h-14 mt-4 shadow-xl">
              Create Account
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
