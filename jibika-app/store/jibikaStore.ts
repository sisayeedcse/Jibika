import { create } from 'zustand';
import { JibikaState, AssetRequest, Verification, AIAssessment, FundingPool, LifecycleLog, AssetRequestStatus, Role } from "@/types";
import { DEMO_INITIAL_STATE } from "@/data/demoData";

const createLog = (
  requestId: string,
  event: string,
  eventBn: string,
  prev: AssetRequestStatus | null,
  next: AssetRequestStatus
): LifecycleLog => ({
  id: `log-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
  assetRequestId: requestId,
  event,
  eventBn,
  previousStatus: prev,
  newStatus: next,
  timestamp: new Date().toISOString(),
});

export const useJibikaStore = create<JibikaState>((set, get) => ({
  ...DEMO_INITIAL_STATE,
  
  activeRole: 'WORKER' as Role,
  activeUserId: 'w-1',
  
  setActiveRole: (role, userId) => set({ activeRole: role, activeUserId: userId }),
  resetStore: () => set({ ...DEMO_INITIAL_STATE, activeRole: 'WORKER', activeUserId: 'w-1' }),
  
  createAssetRequest: (request) => {
    const newRequest: AssetRequest = {
      id: `req-${Date.now()}`,
      workerId: get().activeUserId,
      requestedAssetCategory: request.requestedAssetCategory || 'Sewing Machine',
      reason: request.reason || '',
      requestedAmount: request.requestedAmount || 35000,
      status: 'REQUESTED',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };
    
    const newVerification: Verification = {
      id: `ver-${Date.now()}`,
      assetRequestId: newRequest.id,
      communityMemberId: 'community-001',
      status: 'PENDING',
      notes: ''
    };

    const log = createLog(
      newRequest.id,
      'Asset request created',
      'সম্পদের অনুরোধ তৈরি করা হয়েছে',
      null,
      'REQUESTED'
    );

    set((state) => ({
      assetRequests: [...state.assetRequests, newRequest],
      verifications: [...state.verifications, newVerification],
      lifecycleLogs: [...state.lifecycleLogs, log],
    }));
  },
  
  verifyRequest: (verificationId, notes) => {
    set((state) => {
      const verifications = state.verifications.map(v => 
        v.id === verificationId ? { ...v, status: 'VERIFIED' as const, notes, verifiedAt: new Date().toISOString() } : v
      );
      
      const verification = state.verifications.find(v => v.id === verificationId);
      if (!verification) return state;
      
      const reqId = verification.assetRequestId;
      
      const requests = state.assetRequests.map(r =>
        r.id === reqId ? { ...r, status: 'VERIFIED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );

      const log = createLog(reqId, 'Community verification completed', 'কমিউনিটি যাচাইকরণ সম্পন্ন হয়েছে', 'REQUESTED', 'VERIFIED');

      return { verifications, assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
    });
  },
  
  runAIAssessment: (requestId) => {
    set((state) => {
      const assessment: AIAssessment = {
        id: `ai-${Date.now()}`,
        assetRequestId: requestId,
        suitabilityScore: 'HIGH',
        recommendedAssetCategory: 'Industrial Sewing Machine',
        explanation: [
          'Matches occupation (Tailor)',
          '5 years experience supports safe usage',
          'High demand in Chattogram'
        ],
        createdAt: new Date().toISOString()
      };
      
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'AI_ASSESSED' as AssetRequestStatus, aiAssessmentId: assessment.id, updatedAt: new Date().toISOString() } : r
      );
      
      const pool: FundingPool = {
        id: `pool-${Date.now()}`,
        assetRequestId: requestId,
        targetAmount: state.assetRequests.find(r => r.id === requestId)?.requestedAmount || 35000,
        currentAmount: 0,
        contributions: [],
        status: 'OPEN'
      };

      const log = createLog(requestId, 'AI assessment completed & Funding opened', 'এআই মূল্যায়ন সম্পন্ন এবং ফান্ডিং শুরু হয়েছে', 'VERIFIED', 'AI_ASSESSED');
      const log2 = createLog(requestId, 'Opportunity matched to contributors', 'কন্ট্রিবিউটরদের জন্য সুযোগ তৈরি হয়েছে', 'AI_ASSESSED', 'FUNDING');

      // To simplify flow, automatically transition from AI_ASSESSED to FUNDING
      const requestsToFunding = requests.map(r => 
        r.id === requestId ? { ...r, status: 'FUNDING' as AssetRequestStatus } : r
      );

      return { 
        aiAssessments: [...state.aiAssessments, assessment],
        fundingPools: [...state.fundingPools, pool],
        assetRequests: requestsToFunding,
        lifecycleLogs: [...state.lifecycleLogs, log, log2]
      };
    });
  },
  
  addContribution: (poolId, amount) => {
    set((state) => {
      let isFullyFunded = false;
      let reqId = '';
      
      const pools = state.fundingPools.map(p => {
        if (p.id === poolId) {
          reqId = p.assetRequestId;
          const newTotal = p.currentAmount + amount;
          isFullyFunded = newTotal >= p.targetAmount;
          return {
            ...p,
            currentAmount: newTotal,
            status: (isFullyFunded ? 'COMPLETED' : 'OPEN') as 'OPEN' | 'COMPLETED',
            contributions: [...p.contributions, {
              id: `cont-${Date.now()}`,
              contributorId: state.activeUserId,
              amount,
              timestamp: new Date().toISOString()
            }]
          };
        }
        return p;
      });
      
      if (!reqId) return state;
      
      if (isFullyFunded) {
        const requests = state.assetRequests.map(r =>
          r.id === reqId ? { ...r, status: 'FUNDED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
        );
        const log = createLog(reqId, 'Funding target reached', 'ফান্ডিং লক্ষ্য অর্জিত হয়েছে', 'FUNDING', 'FUNDED');
        return { fundingPools: pools, assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
      }
      
      return { fundingPools: pools };
    });
  },
  
  markProcured: (requestId) => {
    set((state) => {
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'PROCURED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );
      const log = createLog(requestId, 'Vendor assigned and procured', 'ভেন্ডর নিয়োগ এবং সংগ্রহ সম্পন্ন', 'FUNDED', 'PROCURED');
      return { assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
    });
  },
  
  markDelivered: (requestId) => {
    set((state) => {
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'DELIVERED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );
      // Let's immediately transition to MONITORING since it's an MVP demo
      const requestsMonitored = requests.map(r =>
        r.id === requestId ? { ...r, status: 'MONITORING' as AssetRequestStatus } : r
      );
      const log = createLog(requestId, 'Asset delivered', 'সম্পদ হস্তান্তর করা হয়েছে', 'PROCURED', 'DELIVERED');
      const log2 = createLog(requestId, 'Monitoring started', 'পর্যবেক্ষণ শুরু হয়েছে', 'DELIVERED', 'MONITORING');
      return { assetRequests: requestsMonitored, lifecycleLogs: [...state.lifecycleLogs, log, log2] };
    });
  },
}));
