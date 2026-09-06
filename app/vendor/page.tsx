"use client";

import { useJibikaStore } from '@/store/jibikaStore';
import { useI18n } from '@/lib/i18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Store, Truck, CheckCircle2 } from 'lucide-react';

export default function VendorDashboard() {
  const { assetRequests, workers, markDelivered } = useJibikaStore();
  const { t, language } = useI18n();

  // Vendors only care about PROCURED status
  const procuredRequests = assetRequests.filter(r => r.status === 'PROCURED');
  const deliveredRequests = assetRequests.filter(r => r.status === 'DELIVERED' || r.status === 'MONITORING');

  return (
    <div className="container mx-auto px-4 py-8 max-w-5xl space-y-8 animate-in fade-in duration-500">
      <div className="flex items-center gap-3 mb-8">
        <div className="h-12 w-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600">
          <Store className="h-6 w-6" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50">
            {language === 'en' ? 'Vendor Fulfillment' : 'ভেন্ডর ফুলফিলমেন্ট'}
          </h1>
          <p className="text-slate-500">
            {language === 'en' ? 'Manage and ship assigned procurement orders.' : 'বরাদ্দকৃত ক্রয়ের অর্ডার পরিচালনা করুন এবং শিপ করুন।'}
          </p>
        </div>
      </div>

      <div className="grid md:grid-cols-2 gap-8">
        <Card className="border-t-4 border-t-orange-500 shadow-md">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Truck className="h-5 w-5 text-orange-600" />
              {language === 'en' ? 'Pending Shipments' : 'অপেক্ষমাণ শিপমেন্ট'}
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {procuredRequests.length === 0 ? (
              <div className="p-8 text-center text-slate-500 border-2 border-dashed rounded-lg">
                {language === 'en' ? 'No pending orders.' : 'কোন অপেক্ষমাণ অর্ডার নেই।'}
              </div>
            ) : (
              procuredRequests.map(req => {
                const worker = workers.find(w => w.id === req.workerId);
                return (
                  <div key={req.id} className="p-4 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-900/50 hover:shadow-md transition-shadow">
                    <div className="flex justify-between items-start mb-3">
                      <div>
                        <p className="font-bold text-lg text-slate-900 dark:text-slate-100">{req.requestedAssetCategory}</p>
                        <p className="text-sm text-slate-500 flex items-center gap-1">
                          {language === 'en' ? 'Deliver to:' : 'ডেলিভারি গন্তব্য:'} <span className="font-medium text-slate-700 dark:text-slate-300">{worker?.name}, {worker?.location}</span>
                        </p>
                      </div>
                      <Badge variant="warning">{language === 'en' ? 'Ready to Ship' : 'শিপ করার জন্য প্রস্তুত'}</Badge>
                    </div>
                    <Button 
                      className="w-full bg-orange-600 hover:bg-orange-700 text-white shadow-sm"
                      onClick={() => markDelivered(req.id)}
                    >
                      <CheckCircle2 className="mr-2 h-4 w-4" />
                      {language === 'en' ? 'Mark Fulfilled & Shipped' : 'সম্পন্ন এবং শিপ করা হয়েছে'}
                    </Button>
                  </div>
                )
              })
            )}
          </CardContent>
        </Card>

        <Card className="shadow-md">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CheckCircle2 className="h-5 w-5 text-green-600" />
              {language === 'en' ? 'Recently Fulfilled' : 'সম্প্রতি সম্পন্ন'}
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {deliveredRequests.slice(0, 5).map(req => {
              const worker = workers.find(w => w.id === req.workerId);
              return (
                <div key={req.id} className="p-3 border border-slate-100 dark:border-slate-800 rounded-lg flex justify-between items-center bg-white dark:bg-slate-900">
                  <div>
                    <p className="font-semibold text-sm">{req.requestedAssetCategory}</p>
                    <p className="text-xs text-slate-500">{worker?.name}</p>
                  </div>
                  <Badge variant="success" className="text-[10px]">Delivered</Badge>
                </div>
              )
            })}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
