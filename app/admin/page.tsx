"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ActivitySquare, Box, Truck, ShieldCheck } from 'lucide-react';

export default function AdminDashboard() {
  const { assetRequests, workers, adminApproveRequest, markProcured, markDelivered } = useJibikaStore();
  const { t, language } = useI18n();

  const communityApproved = assetRequests.filter(r => r.status === 'COMMUNITY_APPROVED');
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
            {language === 'en' ? 'Manage approvals, procurement and delivery.' : 'অনুমোদন, সংগ্রহ এবং ডেলিভারি পরিচালনা করুন।'}
          </p>
        </div>
      </div>

      <div className="grid md:grid-cols-4 gap-6 mb-8">
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Pending Approval' : 'অপেক্ষমাণ অনুমোদন'}</h3>
            <p className="text-4xl font-bold text-blue-600">{communityApproved.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Fully Funded' : 'সম্পূর্ণ অর্থায়িত'}</h3>
            <p className="text-4xl font-bold text-slate-900 dark:text-slate-50">{fullyFunded.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Procured' : 'সংগৃহীত'}</h3>
            <p className="text-4xl font-bold text-primary-600">{procured.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <h3 className="text-slate-500 font-medium">{language === 'en' ? 'Delivered' : 'সরবরাহকৃত'}</h3>
            <p className="text-4xl font-bold text-green-600">{delivered.length}</p>
          </CardContent>
        </Card>
      </div>

      {/* Admin Approval Queue */}
      <Card className="mb-8 border-blue-200">
        <CardHeader className="bg-blue-50/50">
          <CardTitle className="flex items-center gap-2 text-blue-800">
            <ShieldCheck className="h-5 w-5" />
            {language === 'en' ? 'Final Approval Queue (From Community)' : 'চূড়ান্ত অনুমোদনের সারি'}
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4 p-6">
          {communityApproved.length === 0 ? (
            <p className="text-sm text-slate-500 italic">No asset requests pending final approval.</p>
          ) : (
            communityApproved.map(req => {
              const worker = workers.find(w => w.id === req.workerId);
              return (
                <div key={req.id} className="p-4 border border-blue-100 rounded-lg flex justify-between items-center bg-white dark:bg-slate-950 shadow-sm">
                  <div>
                    <p className="font-semibold text-lg">{req.requestedAssetCategory}</p>
                    <p className="text-sm text-slate-600 dark:text-slate-400">For: <span className="font-medium">{worker?.name}</span> • {worker?.location}</p>
                    <p className="text-xs text-slate-500 mt-1 line-clamp-1">"{req.reason || worker?.story}"</p>
                  </div>
                  <Button size="sm" onClick={() => adminApproveRequest(req.id)} className="bg-blue-600 hover:bg-blue-700">
                    {language === 'en' ? 'Approve & Open Funding' : 'অনুমোদন ও ফান্ডিং শুরু'}
                  </Button>
                </div>
              )
            })
          )}
        </CardContent>
      </Card>

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
