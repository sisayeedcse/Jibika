"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ShieldCheck, CheckCircle, AlertTriangle } from 'lucide-react';
import { useState } from 'react';

export default function CommunityDashboard() {
  const { verifications, assetRequests, workers, verifyRequest, runAIAssessment } = useJibikaStore();
  const { t, language } = useI18n();
  const [notes, setNotes] = useState('');

  const pendingVerifications = verifications.filter(v => v.status === 'PENDING');

  const handleApprove = (verificationId: string, requestId: string) => {
    verifyRequest(verificationId, notes || 'Verified by community leader.');
    // Automatically trigger AI Assessment for Demo purposes
    runAIAssessment(requestId);
  };

  return (
    <div className="container mx-auto px-4 py-8 max-w-4xl space-y-8">
      <div className="flex items-center gap-3 mb-8">
        <div className="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600">
          <ShieldCheck className="h-6 w-6" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50">
            {language === 'en' ? 'Community Verification' : 'কমিউনিটি যাচাইকরণ'}
          </h1>
          <p className="text-slate-500">
            {language === 'en' ? 'Review and verify requests from your local community.' : 'আপনার স্থানীয় কমিউনিটির অনুরোধগুলো পর্যালোচনা এবং যাচাই করুন।'}
          </p>
        </div>
      </div>

      {pendingVerifications.length === 0 ? (
        <Card className="border-dashed">
          <CardContent className="p-12 text-center text-slate-500">
            {language === 'en' ? 'No pending verifications.' : 'কোন অপেক্ষমাণ যাচাইকরণ নেই।'}
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-6">
          {pendingVerifications.map(v => {
            const req = assetRequests.find(r => r.id === v.assetRequestId);
            const worker = workers.find(w => w.id === req?.workerId);
            if (!req || !worker) return null;

            return (
              <Card key={v.id} className="overflow-hidden">
                <CardHeader className="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle className="text-xl mb-1">{worker.name}</CardTitle>
                      <p className="text-slate-500">{worker.occupation} • {worker.location}</p>
                    </div>
                    <Badge variant="warning">{t('status.requested')}</Badge>
                  </div>
                </CardHeader>
                <CardContent className="p-6 space-y-6">
                  <div className="grid md:grid-cols-2 gap-6">
                    <div>
                      <h4 className="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">
                        {language === 'en' ? 'Requested Asset' : 'অনুরোধকৃত সম্পদ'}
                      </h4>
                      <p className="font-medium text-lg">{req.requestedAssetCategory}</p>
                      <p className="text-2xl font-bold text-primary-600 mt-1">৳ {req.requestedAmount.toLocaleString()}</p>
                    </div>
                    <div>
                      <h4 className="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">
                        {language === 'en' ? 'Worker Story' : 'কর্মীর গল্প'}
                      </h4>
                      <p className="text-slate-700 dark:text-slate-300 italic">"{worker.story}"</p>
                    </div>
                  </div>

                  <div className="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label className="text-sm font-medium block mb-2">
                      {language === 'en' ? 'Verification Notes (Optional)' : 'যাচাইকরণ নোট (ঐচ্ছিক)'}
                    </label>
                    <textarea 
                      className="w-full p-3 rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 outline-none focus:border-blue-500"
                      rows={2}
                      placeholder={language === 'en' ? "I confirm this person's identity and need..." : "আমি এই ব্যক্তির পরিচয় এবং প্রয়োজন নিশ্চিত করছি..."}
                      value={notes}
                      onChange={(e) => setNotes(e.target.value)}
                    />
                  </div>
                </CardContent>
                <CardFooter className="bg-slate-50 dark:bg-slate-800/50 p-4 border-t border-slate-100 dark:border-slate-800 flex gap-4 justify-end">
                  <Button variant="outline" className="text-slate-600 hover:text-slate-900">
                    <AlertTriangle className="mr-2 h-4 w-4" />
                    {t('ui.flag')}
                  </Button>
                  <Button onClick={() => handleApprove(v.id, req.id)} className="bg-blue-600 hover:bg-blue-700 text-white">
                    <CheckCircle className="mr-2 h-4 w-4" />
                    {t('ui.approve')}
                  </Button>
                </CardFooter>
              </Card>
            );
          })}
        </div>
      )}
    </div>
  );
}
