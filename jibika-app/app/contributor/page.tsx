"use client";

import { useJibikaStore } from "@/store/jibikaStore";
import { useI18n } from "@/lib/i18n";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Sparkles, MapPin, Briefcase } from 'lucide-react';
import { useRouter } from 'next/navigation';

export default function ContributorDashboard() {
  const { fundingPools, assetRequests, workers, aiAssessments } = useJibikaStore();
  const { t, language } = useI18n();
  const router = useRouter();

  // Show only open pools
  const openPools = fundingPools.filter(p => p.status === 'OPEN');

  return (
    <div className="container mx-auto px-4 py-8 max-w-5xl space-y-8">
      <div>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-50 mb-2">
          {t('contrib.discover')}
        </h1>
        <p className="text-slate-500">
          {language === 'en' ? 'Support verified workers to acquire productive assets.' : 'উৎপাদনশীল সম্পদ অর্জনে যাচাইকৃত কর্মীদের সহায়তা করুন।'}
        </p>
      </div>

      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {openPools.length === 0 ? (
          <div className="col-span-full text-center p-12 text-slate-500 border-2 border-dashed rounded-xl">
            {language === 'en' ? 'No open opportunities right now.' : 'বর্তমানে কোন সুযোগ খোলা নেই।'}
          </div>
        ) : (
          openPools.map(pool => {
            const req = assetRequests.find(r => r.id === pool.assetRequestId);
            const worker = workers.find(w => w.id === req?.workerId);
            const assessment = aiAssessments.find(a => a.assetRequestId === req?.id);
            
            if (!req || !worker) return null;

            const progress = (pool.currentAmount / pool.targetAmount) * 100;

            return (
              <Card key={pool.id} className="overflow-hidden flex flex-col group cursor-pointer hover:border-accent-500 transition-colors" onClick={() => router.push(`/contributor/opportunity/${pool.id}`)}>
                {/* Image Placeholder */}
                <div className="h-48 bg-slate-200 dark:bg-slate-800 relative overflow-hidden">
                  {worker.profileImageUrl ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img 
                      src={worker.profileImageUrl} 
                      alt={worker.name} 
                      className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  ) : (
                    <div className="absolute inset-0 flex items-center justify-center text-4xl">
                      📦
                    </div>
                  )}
                  {assessment?.suitabilityScore === 'HIGH' && (
                    <div className="absolute top-3 left-3 bg-primary-600 text-white text-xs font-bold px-2 py-1 rounded-md flex items-center gap-1 shadow-md z-10">
                      <Sparkles className="h-3 w-3" />
                      {language === 'en' ? 'Smart Match' : 'স্মার্ট ম্যাচ'}
                    </div>
                  )}
                </div>
                
                <CardContent className="p-5 flex-1 flex flex-col">
                  <div className="flex justify-between items-start mb-2">
                    <h3 className="font-bold text-lg">{worker.name}</h3>
                    <Badge variant="success" className="bg-green-100 text-green-800">
                      <ShieldCheck className="h-3 w-3 mr-1 inline" />
                      {language === 'en' ? 'Verified' : 'যাচাইকৃত'}
                    </Badge>
                  </div>
                  
                  <div className="flex items-center gap-4 text-xs text-slate-500 mb-4">
                    <span className="flex items-center gap-1"><Briefcase className="h-3 w-3"/> {worker.occupation}</span>
                    <span className="flex items-center gap-1"><MapPin className="h-3 w-3"/> {worker.location}</span>
                  </div>

                  <p className="font-semibold text-slate-700 dark:text-slate-300 mb-4">
                    {req.requestedAssetCategory}
                  </p>

                  <div className="mt-auto space-y-2">
                    <div className="flex justify-between text-sm font-medium">
                      <span>৳ {pool.currentAmount.toLocaleString()}</span>
                      <span className="text-slate-500">৳ {pool.targetAmount.toLocaleString()}</span>
                    </div>
                    <div className="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                      <div 
                        className="h-full bg-accent-500 rounded-full transition-all duration-1000"
                        style={{ width: `${progress}%` }}
                      />
                    </div>
                    <div className="text-right text-xs text-slate-500 font-semibold">{Math.round(progress)}% Funded</div>
                  </div>
                </CardContent>
              </Card>
            );
          })
        )}
      </div>
    </div>
  );
}

// Temporary component mock since I didn't import ShieldCheck correctly above
function ShieldCheck(props: any) {
  return <svg {...props} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>;
}
