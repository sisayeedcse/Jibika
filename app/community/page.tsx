"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ShieldCheck, CheckCircle, AlertTriangle, Users } from 'lucide-react';
import { useState } from 'react';

export default function CommunityDashboard() {
  const { workers, assetRequests, communityApproveRequest, approveCommunityJoin, activeUserId, communities } = useJibikaStore();
  const { t, language } = useI18n();
  const [notes, setNotes] = useState('');

  // Find the community this leader manages
  // For demo purposes, we will default to com-1 if activeUserId is not a leader
  const myCommunity = communities.find(c => c.leaderId === activeUserId) || communities[0];

  const pendingWorkers = workers.filter(w => w.communityId === myCommunity.id && w.communityStatus === 'PENDING');
  
  // Asset requests from workers in this community that are in REQUESTED state
  const pendingRequests = assetRequests.filter(req => 
    req.status === 'REQUESTED' && 
    workers.some(w => w.id === req.workerId && w.communityId === myCommunity.id && w.communityStatus === 'APPROVED')
  );

  const handleApproveRequest = (requestId: string) => {
    communityApproveRequest(requestId, notes || 'Verified by community leader.');
    setNotes('');
  };

  const handleApproveWorker = (workerId: string) => {
    approveCommunityJoin(workerId);
  };

  return (
    <div className="container mx-auto px-4 py-8 max-w-4xl space-y-8">
      <div className="flex items-center gap-3 mb-8">
        <div className="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
          <ShieldCheck className="h-6 w-6" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-slate-900">
            {myCommunity.name} Leader Dashboard
          </h1>
          <p className="text-slate-500">
            {language === 'en' ? 'Manage members and verify asset requests.' : 'সদস্য পরিচালনা এবং অনুরোধ যাচাই করুন।'}
          </p>
        </div>
      </div>

      <div className="space-y-8">
        {/* Pending Worker Joins */}
        <section>
          <h2 className="text-xl font-bold mb-4 flex items-center gap-2">
            <Users className="h-5 w-5 text-blue-600" />
            Pending Member Approvals
          </h2>
          {pendingWorkers.length === 0 ? (
            <Card className="border-dashed bg-slate-50">
              <CardContent className="p-8 text-center text-slate-500">
                No pending members.
              </CardContent>
            </Card>
          ) : (
            <div className="grid md:grid-cols-2 gap-4">
              {pendingWorkers.map(w => (
                <Card key={w.id}>
                  <CardContent className="p-4 flex justify-between items-center">
                    <div>
                      <p className="font-bold">{w.name}</p>
                      <p className="text-xs text-slate-500">{w.occupation} • {w.location}</p>
                    </div>
                    <Button size="sm" onClick={() => handleApproveWorker(w.id)} className="bg-blue-600 hover:bg-blue-700">Approve</Button>
                  </CardContent>
                </Card>
              ))}
            </div>
          )}
        </section>

        {/* Pending Asset Requests */}
        <section>
          <h2 className="text-xl font-bold mb-4 flex items-center gap-2">
            <ShieldCheck className="h-5 w-5 text-blue-600" />
            Pending Asset Requests
          </h2>
          {pendingRequests.length === 0 ? (
            <Card className="border-dashed bg-slate-50">
              <CardContent className="p-8 text-center text-slate-500">
                {language === 'en' ? 'No pending requests.' : 'কোন অপেক্ষমাণ অনুরোধ নেই।'}
              </CardContent>
            </Card>
          ) : (
            <div className="grid gap-6">
              {pendingRequests.map(req => {
                const worker = workers.find(w => w.id === req.workerId);
                if (!worker) return null;

                return (
                  <Card key={req.id} className="overflow-hidden">
                    <CardHeader className="bg-slate-50 border-b border-slate-100">
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
                          <p className="text-slate-700 italic">"{req.reason || worker.story}"</p>
                        </div>
                      </div>

                      <div className="pt-4 border-t border-slate-100">
                        <label className="text-sm font-medium block mb-2">
                          {language === 'en' ? 'Verification Notes (Optional)' : 'যাচাইকরণ নোট (ঐচ্ছিক)'}
                        </label>
                        <textarea 
                          className="w-full p-3 rounded-md border border-slate-300 outline-none focus:border-blue-500"
                          rows={2}
                          placeholder={language === 'en' ? "I confirm this person's identity and need..." : "আমি এই ব্যক্তির পরিচয় এবং প্রয়োজন নিশ্চিত করছি..."}
                          value={notes}
                          onChange={(e) => setNotes(e.target.value)}
                        />
                      </div>
                    </CardContent>
                    <CardFooter className="bg-slate-50 p-4 border-t border-slate-100 flex gap-4 justify-end">
                      <Button variant="outline" className="text-slate-600 hover:text-slate-900">
                        <AlertTriangle className="mr-2 h-4 w-4" />
                        {t('ui.flag')}
                      </Button>
                      <Button onClick={() => handleApproveRequest(req.id)} className="bg-blue-600 hover:bg-blue-700 text-white">
                        <CheckCircle className="mr-2 h-4 w-4" />
                        {language === 'en' ? 'Approve & Send to Admin' : 'অনুমোদন করুন এবং অ্যাডমিনের কাছে পাঠান'}
                      </Button>
                    </CardFooter>
                  </Card>
                );
              })}
            </div>
          )}
        </section>
      </div>
    </div>
  );
}
