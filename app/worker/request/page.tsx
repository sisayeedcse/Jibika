"use client";

import { useState } from 'react';
import { useI18n } from "@/lib/i18n";
import { useJibikaStore } from "@/store/jibikaStore";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Stepper } from "@/components/ui/stepper";
import { useRouter } from 'next/navigation';
import { PackageSearch, Briefcase, Calculator } from 'lucide-react';

export default function WorkerRequestForm() {
  const { t, language } = useI18n();
  const { createAssetRequest } = useJibikaStore();
  const router = useRouter();

  const [step, setStep] = useState(1);
  const [formData, setFormData] = useState({
    requestedAssetCategory: 'Industrial Sewing Machine',
    reason: 'To increase production capacity and take bulk orders.',
    requestedAmount: 35000,
  });

  const handleNext = () => setStep(s => Math.min(s + 1, 3));
  const handlePrev = () => setStep(s => Math.max(s - 1, 1));
  const handleSubmit = () => {
    createAssetRequest(formData);
    router.push('/worker');
  };

  return (
    <div className="container mx-auto px-4 py-8 max-w-xl">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50 mb-2">
          {t('worker.req.title')}
        </h1>
        <Stepper currentStep={step} totalSteps={3} />
      </div>

      <Card className="shadow-lg border-0 ring-1 ring-slate-200 dark:ring-slate-800">
        {step === 1 && (
          <>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-xl">
                <PackageSearch className="text-primary-600" />
                {t('worker.req.step1')}
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <p className="text-sm text-slate-500">
                {language === 'en' ? 'Select the type of asset you need for your work.' : 'আপনার কাজের জন্য প্রয়োজনীয় সম্পদের ধরন নির্বাচন করুন।'}
              </p>
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {/* Visual Options - Just one selected for demo simplicity */}
                <div 
                  className="border-2 border-primary-600 bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 cursor-pointer text-center"
                  onClick={() => setFormData({...formData, requestedAssetCategory: 'Industrial Sewing Machine'})}
                >
                  <div className="w-16 h-16 bg-white dark:bg-slate-800 rounded-lg mx-auto mb-3 flex items-center justify-center shadow-sm">
                    {/* Placeholder for actual image */}
                    <span className="text-2xl">🧵</span>
                  </div>
                  <h4 className="font-semibold text-primary-900 dark:text-primary-100">
                    {language === 'en' ? 'Industrial Sewing Machine' : 'শিল্প সেলাই মেশিন'}
                  </h4>
                </div>
                
                <div 
                  className="border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 rounded-xl p-4 cursor-pointer text-center opacity-60"
                >
                  <div className="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <span className="text-2xl">🛒</span>
                  </div>
                  <h4 className="font-semibold text-slate-700 dark:text-slate-300">
                    {language === 'en' ? 'Vending Cart' : 'ভেন্ডিং কার্ট'}
                  </h4>
                </div>
              </div>
            </CardContent>
            <CardFooter>
              <Button onClick={handleNext} className="w-full h-12 text-lg">{t('ui.continue')}</Button>
            </CardFooter>
          </>
        )}

        {step === 2 && (
          <>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-xl">
                <Briefcase className="text-primary-600" />
                {t('worker.req.step2')}
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <label className="text-sm font-medium">
                {language === 'en' ? 'Explain why you need this and how it will improve your income.' : 'ব্যাখ্যা করুন কেন আপনার এটি প্রয়োজন এবং এটি কীভাবে আপনার আয় বাড়াবে।'}
              </label>
              <textarea 
                className="w-full min-h-[120px] p-4 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-primary-500 outline-none"
                value={formData.reason}
                onChange={(e) => setFormData({...formData, reason: e.target.value})}
              />
            </CardContent>
            <CardFooter className="flex gap-4">
              <Button onClick={handlePrev} variant="outline" className="w-1/3 h-12">{t('ui.cancel')}</Button>
              <Button onClick={handleNext} className="w-2/3 h-12">{t('ui.continue')}</Button>
            </CardFooter>
          </>
        )}

        {step === 3 && (
          <>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-xl">
                <Calculator className="text-primary-600" />
                {t('worker.req.amount')}
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="relative">
                <span className="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-slate-400">৳</span>
                <input 
                  type="number" 
                  className="w-full h-16 pl-12 pr-4 text-2xl font-bold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-primary-500 outline-none"
                  value={formData.requestedAmount}
                  onChange={(e) => setFormData({...formData, requestedAmount: parseInt(e.target.value) || 0})}
                />
              </div>
              <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex gap-3 text-sm text-blue-800 dark:text-blue-300">
                <span className="shrink-0 text-blue-600">ℹ️</span>
                <p>
                  {language === 'en' 
                    ? 'This amount will be verified by community members before funding begins.' 
                    : 'ফান্ডিং শুরু হওয়ার আগে এই পরিমাণ কমিউনিটি সদস্যদের দ্বারা যাচাই করা হবে।'}
                </p>
              </div>
            </CardContent>
            <CardFooter className="flex gap-4">
              <Button onClick={handlePrev} variant="outline" className="w-1/3 h-12">{t('ui.cancel')}</Button>
              <Button onClick={handleSubmit} variant="accent" className="w-2/3 h-12 text-lg">{t('worker.req.submit')}</Button>
            </CardFooter>
          </>
        )}
      </Card>
    </div>
  );
}
