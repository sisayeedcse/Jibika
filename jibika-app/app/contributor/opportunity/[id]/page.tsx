"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { useParams, useRouter } from 'next/navigation';
import { useState } from 'react';
import { Sparkles, ShieldCheck, ArrowLeft, CheckCircle2 } from 'lucide-react';
import { use } from 'react'; // React 19 / Next 15 pattern for params

export default function OpportunityDetail({ params }: { params: Promise<{ id: string }> }) {
  const resolvedParams = use(params);
  const { id } = resolvedParams;
  const { fundingPools, assetRequests, workers, aiAssessments, addContribution } = useJibikaStore();
  const { t, language } = useI18n();
  const router = useRouter();

  const pool = fundingPools.find(p => p.id === id);
  const req = assetRequests.find(r => r.id === pool?.assetRequestId);
  const worker = workers.find(w => w.id === req?.workerId);
  const assessment = aiAssessments.find(a => a.assetRequestId === req?.id);

  const [fundAmount, setFundAmount] = useState(5000);
  const [showConfirmation, setShowConfirmation] = useState(false);

  if (!pool || !req || !worker) return <div>Opportunity not found</div>;

  const progress = (pool.currentAmount / pool.targetAmount) * 100;
  const remaining = pool.targetAmount - pool.currentAmount;

  const handleFund = () => {
    addContribution(pool.id, fundAmount);
    router.push('/contributor');
  };

  return (
    <div className="container mx-auto px-4 py-8 max-w-4xl space-y-6">
      <button onClick={() => router.back()} className="text-sm font-medium text-slate-500 hover:text-slate-900 dark:hover:text-slate-50 flex items-center gap-1 mb-4">
        <ArrowLeft className="h-4 w-4" /> {language === 'en' ? 'Back to Opportunities' : 'সুযোগে ফিরে যান'}
      </button>

      <div className="grid md:grid-cols-3 gap-8">
        <div className="md:col-span-2 space-y-6">
          {/* Worker Info */}
          <Card>
            {worker.profileImageUrl ? (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={worker.profileImageUrl} alt="Cover" className="h-48 w-full object-cover rounded-t-xl" />
            ) : (
              <div className="h-48 bg-primary-700/10 dark:bg-primary-600/20" />
            )}
            <CardContent className="pt-0 relative">
              <div className="absolute -top-12 left-6 h-24 w-24 bg-white dark:bg-slate-800 rounded-xl border-4 border-white dark:border-slate-800 shadow flex items-center justify-center text-4xl overflow-hidden">
                {worker.profileImageUrl ? (
                   // eslint-disable-next-line @next/next/no-img-element
                   <img src={worker.profileImageUrl} alt={worker.name} className="w-full h-full object-cover" />
                ) : (
                  "👨🏽‍🔧"
                )}
              </div>
              <div className="pt-14 pb-2">
                <div className="flex justify-between items-start">
                  <div>
                    <h2 className="text-2xl font-bold">{worker.name}</h2>
                    <p className="text-slate-500">{worker.occupation} • {worker.location}</p>
                  </div>
                  <Badge variant="success" className="bg-green-100 text-green-800">
                    <ShieldCheck className="h-3 w-3 mr-1 inline" />
                    {language === 'en' ? 'Community Verified' : 'কমিউনিটি যাচাইকৃত'}
                  </Badge>
                </div>
              </div>
              <div className="mt-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                <h4 className="font-semibold mb-2">{language === 'en' ? 'Worker Story' : 'কর্মীর গল্প'}</h4>
                <p className="text-slate-600 dark:text-slate-400 italic">"{worker.story}"</p>
              </div>
            </CardContent>
          </Card>

          {/* AI Assessment Explanation */}
          {assessment && (
            <Card className="border-accent-200 dark:border-accent-900 bg-accent-50/50 dark:bg-accent-900/10">
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2 text-accent-700 dark:text-accent-500">
                  <Sparkles className="h-5 w-5" />
                  {language === 'en' ? 'AI Suitability Assessment' : 'এআই উপযুক্ততা মূল্যায়ন'}
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ul className="space-y-3">
                  {assessment.explanation.map((reason, i) => (
                    <li key={i} className="flex items-start gap-3 text-slate-700 dark:text-slate-300">
                      <CheckCircle2 className="h-5 w-5 text-accent-500 shrink-0 mt-0.5" />
                      <span>{reason}</span>
                    </li>
                  ))}
                </ul>
                <div className="mt-4 text-xs text-slate-400">
                  {language === 'en' ? 'Note: This is an AI-assisted estimate based on demo data.' : 'দ্রষ্টব্য: এটি ডেমো ডেটার উপর ভিত্তি করে একটি এআই-সহায়তা অনুমান।'}
                </div>
              </CardContent>
            </Card>
          )}
        </div>

        {/* Funding Widget */}
        <div className="md:col-span-1">
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle className="text-xl">{req.requestedAssetCategory}</CardTitle>
              <div className="text-sm text-slate-500">{language === 'en' ? 'Required Amount' : 'প্রয়োজনীয় পরিমাণ'}: ৳ {pool.targetAmount.toLocaleString()}</div>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="space-y-2">
                <div className="flex justify-between text-sm font-bold">
                  <span className="text-primary-600">৳ {pool.currentAmount.toLocaleString()}</span>
                  <span className="text-slate-500">{Math.round(progress)}%</span>
                </div>
                <div className="h-3 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-primary-600 rounded-full transition-all duration-1000"
                    style={{ width: `${progress}%` }}
                  />
                </div>
              </div>

              {!showConfirmation ? (
                <div className="space-y-4">
                  <div>
                    <label className="text-sm font-semibold mb-2 block">{language === 'en' ? 'Contribution Amount (৳)' : 'অবদানের পরিমাণ (৳)'}</label>
                    <input 
                      type="number"
                      className="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 font-bold text-lg focus:ring-2 focus:ring-accent-500 outline-none"
                      value={fundAmount}
                      onChange={(e) => setFundAmount(Math.min(parseInt(e.target.value) || 0, remaining))}
                      max={remaining}
                    />
                  </div>
                  <div className="flex gap-2">
                    {[1000, 2000, 5000].map(amt => (
                      <button 
                        key={amt}
                        onClick={() => setFundAmount(Math.min(amt, remaining))}
                        className="flex-1 py-2 rounded border border-slate-200 dark:border-slate-700 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 font-medium"
                      >
                        ৳{amt}
                      </button>
                    ))}
                  </div>
                  <Button onClick={() => setShowConfirmation(true)} className="w-full h-12 text-lg" variant="accent">
                    {t('contrib.fund')}
                  </Button>
                </div>
              ) : (
                <div className="bg-slate-50 dark:bg-slate-900 p-4 rounded-lg space-y-4 border border-slate-200 dark:border-slate-700">
                  <h4 className="font-bold text-slate-900 dark:text-slate-100">
                    {language === 'en' ? 'Confirm Funding' : 'ফান্ডিং নিশ্চিত করুন'}
                  </h4>
                  <p className="text-sm text-slate-600 dark:text-slate-400">
                    {language === 'en' 
                      ? `You are providing ৳${fundAmount} to help ${worker.name} get a ${req.requestedAssetCategory}. This is an asset-financing contribution.` 
                      : `আপনি ${worker.name} কে একটি ${req.requestedAssetCategory} পেতে ৳${fundAmount} প্রদান করছেন। এটি একটি সম্পদ-অর্থায়ন অবদান।`}
                  </p>
                  <div className="flex gap-2">
                    <Button variant="outline" className="flex-1" onClick={() => setShowConfirmation(false)}>{t('ui.cancel')}</Button>
                    <Button variant="default" className="flex-1 bg-green-600 hover:bg-green-700" onClick={handleFund}>
                      {language === 'en' ? 'Confirm' : 'নিশ্চিত করুন'}
                    </Button>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
