"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { useRouter } from 'next/navigation';
import { PlusCircle, ActivitySquare, CheckCircle2, Clock } from 'lucide-react';

export default function WorkerDashboard() {
  const { workers, assetRequests, activeUserId, lifecycleLogs } = useJibikaStore();
  const { t, language } = useI18n();
  const router = useRouter();

  const worker = workers.find(w => w.id === activeUserId);
  const activeRequest = assetRequests.find(r => r.workerId === activeUserId);

  if (!worker) return <div>Worker not found</div>;

  const getStatusBadge = (status: string) => {
    switch(status) {
      case 'DELIVERED':
      case 'MONITORING':
        return <Badge variant="success">{t(`status.${status.toLowerCase()}`)}</Badge>;
      case 'FUNDING':
      case 'FUNDED':
      case 'PROCURED':
        return <Badge variant="warning">{t(`status.${status.toLowerCase()}`)}</Badge>;
      default:
        return <Badge variant="default">{t(`status.${status.toLowerCase()}`)}</Badge>;
    }
  };

  const getStatusIcon = (status: string) => {
    if (status === 'DELIVERED' || status === 'MONITORING') return <CheckCircle2 className="h-6 w-6 text-green-500" />;
    if (status === 'REQUESTED' || status === 'VERIFIED') return <Clock className="h-6 w-6 text-yellow-500" />;
    return <ActivitySquare className="h-6 w-6 text-primary-600" />;
  };

  const myLogs = lifecycleLogs
    .filter(log => log.assetRequestId === activeRequest?.id)
    .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime());

  return (
    <div className="container mx-auto px-4 py-8 max-w-3xl space-y-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50">
            {language === 'en' ? `Welcome, ${worker.name}` : `স্বাগতম, ${worker.name}`}
          </h1>
          <p className="text-slate-500">{worker.occupation} • {worker.location}</p>
        </div>
      </div>

      {!activeRequest ? (
        <Card className="border-dashed border-2 bg-slate-50/50 dark:bg-slate-900/50">
          <CardContent className="flex flex-col items-center justify-center p-12 text-center space-y-4">
            <div className="h-16 w-16 bg-primary-100 dark:bg-primary-900/20 rounded-full flex items-center justify-center">
              <PlusCircle className="h-8 w-8 text-primary-600" />
            </div>
            <h3 className="text-xl font-semibold">
              {language === 'en' ? 'No Active Requests' : 'কোন সক্রিয় অনুরোধ নেই'}
            </h3>
            <p className="text-slate-500 max-w-sm">
              {language === 'en' 
                ? 'Request a productive asset to grow your livelihood.'
                : 'আপনার জীবনযাত্রা উন্নত করতে একটি সম্পদের জন্য অনুরোধ করুন।'}
            </p>
            <Button size="lg" className="mt-4" onClick={() => router.push('/worker/request')}>
              {t('worker.req.title')}
            </Button>
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-6">
          <Card>
            <CardHeader className="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
              <div className="flex justify-between items-center">
                <CardTitle className="flex items-center gap-2 text-lg">
                  {getStatusIcon(activeRequest.status)}
                  {activeRequest.requestedAssetCategory}
                </CardTitle>
                {getStatusBadge(activeRequest.status)}
              </div>
            </CardHeader>
            <CardContent className="p-6">
              <div className="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span className="text-slate-500 block mb-1">
                    {language === 'en' ? 'Requested Amount' : 'অনুরোধকৃত পরিমাণ'}
                  </span>
                  <span className="font-semibold text-lg">৳ {activeRequest.requestedAmount.toLocaleString()}</span>
                </div>
                <div>
                  <span className="text-slate-500 block mb-1">
                    {language === 'en' ? 'Reason' : 'কারণ'}
                  </span>
                  <span className="font-medium text-slate-700 dark:text-slate-300 line-clamp-2">
                    {activeRequest.reason}
                  </span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-lg">
                {language === 'en' ? 'Lifecycle Tracking' : 'লাইফসাইকেল ট্র্যাকিং'}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                {myLogs.map((log, i) => (
                  <div key={log.id} className="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div className="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-300 group-[.is-active]:bg-primary-600 text-slate-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                      <CheckCircle2 className="w-5 h-5" />
                    </div>
                    <div className="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700 shadow-sm">
                      <div className="flex items-center justify-between space-x-2 mb-1">
                        <div className="font-bold text-slate-900 dark:text-slate-100">
                          {language === 'en' ? log.event : log.eventBn}
                        </div>
                      </div>
                      <div className="text-xs text-slate-500">
                        {new Date(log.timestamp).toLocaleDateString()} {new Date(log.timestamp).toLocaleTimeString()}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}
