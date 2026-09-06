"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ActivitySquare, Box, Truck } from 'lucide-react';

export default function AdminDashboard() {
  const { assetRequests, workers, fundingPools, markProcured, markDelivered } = useJibikaStore();
  const { t, language } = useI18n();

  const fullyFunded = assetRequests.filter(r => r.status === 'FUNDED');
  const procured = assetRequests.filter(r => r.status === 'PROCURED');
  const delivered = assetRequests.filter(r => r.status === 'DELIVERED' || r.status === 'MONITORING');

  return (
    <div className="container mx-auto px-4 py-8 max-w-6xl space-y-8">
      <div className="flex items-center gap-3 mb-8">
        <div className="h-12 w-12 bg-slate-800 text-white rounded-xl flex items-center justify-center">
          <ActivitySquare className="h-6 w-6" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50">
            {language === 'en' ? 'Admin / Platform Operations' : 'অ্যাডমিন / প্ল্যাটফর্ম অপারেশন'}
          </h1>
          <p className="text-slate-500">
            {language === 'en' ? 'Manage procurement and delivery of fully funded assets.' : 'সম্পূর্ণ ফান্ডেড সম্পদের ক্রয় এবং বিতরণ পরিচালনা করুন।'}
          </p>
        </div>
      </div>

      <div className="grid md:grid-cols-3 gap-6 mb-8">
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Fully Funded' : 'সম্পূর্ণ ফান্ডেড'}</h3>
            <p className="text-4xl font-bold text-slate-900 dark:text-slate-50">{fullyFunded.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Procured' : 'ক্রয়কৃত'}</h3>
            <p className="text-4xl font-bold text-primary-600">{procured.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Delivered & Monitoring' : 'বিতরণকৃত এবং পর্যবেক্ষণে'}</h3>
            <p className="text-4xl font-bold text-green-600">{delivered.length}</p>
          </CardContent>
        </Card>
      </div>

      <div className="grid md:grid-cols-2 gap-8">
        {/* Procurement Queue */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Box className="h-5 w-5 text-slate-500" />
              {language === 'en' ? 'Procurement Queue (Funded)' : 'ক্রয় অপেক্ষমাণ (ফান্ডেড)'}
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {fullyFunded.length === 0 ? (
              <p className="text-sm text-slate-500 italic">No assets waiting for procurement.</p>
            ) : (
              fullyFunded.map(req => {
                const worker = workers.find(w => w.id === req.workerId);
                return (
                  <div key={req.id} className="p-4 border border-slate-200 dark:border-slate-800 rounded-lg flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                    <div>
                      <p className="font-semibold">{req.requestedAssetCategory}</p>
                      <p className="text-sm text-slate-500">For: {worker?.name}</p>
                    </div>
                    <Button size="sm" onClick={() => markProcured(req.id)}>
                      {language === 'en' ? 'Assign Vendor' : 'ভেন্ডর বরাদ্দ করুন'}
                    </Button>
                  </div>
                )
              })
            )}
          </CardContent>
        </Card>

        {/* Delivery Queue */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Truck className="h-5 w-5 text-slate-500" />
              {language === 'en' ? 'Delivery Queue (Procured)' : 'বিতরণ অপেক্ষমাণ (ক্রয়কৃত)'}
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {procured.length === 0 ? (
              <p className="text-sm text-slate-500 italic">No assets waiting for delivery.</p>
            ) : (
              procured.map(req => {
                const worker = workers.find(w => w.id === req.workerId);
                return (
                  <div key={req.id} className="p-4 border border-slate-200 dark:border-slate-800 rounded-lg flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                    <div>
                      <p className="font-semibold">{req.requestedAssetCategory}</p>
                      <p className="text-sm text-slate-500">For: {worker?.name}</p>
                    </div>
                    <Button size="sm" variant="accent" onClick={() => markDelivered(req.id)}>
                      {language === 'en' ? 'Mark Delivered' : 'বিতরণ সম্পন্ন'}
                    </Button>
                  </div>
                )
              })
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
