import { create } from 'zustand';
import { JibikaState, AssetRequest, Verification, AIAssessment, FundingPool, LifecycleLog, AssetRequestStatus, Role, Worker } from "@/types";
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

  registerWorker: (worker) => {
    const newWorker: Worker = {
      id: `w-new-${Date.now()}`,
      name: worker.name || 'New Worker',
      occupation: worker.occupation || 'Unspecified',
      experience: worker.experience || 'None',
      location: worker.location || 'Unknown',
      phone: worker.phone || '01700000000',
      story: worker.story || '',
      verified: false,
      communityStatus: 'NONE'
    };
    set((state) => ({
      workers: [...state.workers, newWorker],
      activeUserId: newWorker.id
    }));
  },

  requestJoinCommunity: (workerId, communityId) => {
    set((state) => ({
      workers: state.workers.map(w => 
        w.id === workerId ? { ...w, communityId, communityStatus: 'PENDING' } : w
      )
    }));
  },

  approveCommunityJoin: (workerId) => {
    set((state) => ({
      workers: state.workers.map(w => 
        w.id === workerId ? { ...w, communityStatus: 'APPROVED' } : w
      )
    }));
  },
  
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
    
    const log = createLog(
      newRequest.id,
      'Asset request created',
      'সম্পদের অনুরোধ তৈরি করা হয়েছে',
      null,
      'REQUESTED'
    );

    set((state) => ({
      assetRequests: [...state.assetRequests, newRequest],
      lifecycleLogs: [...state.lifecycleLogs, log],
    }));
  },
  
  // Backward compatibility alias if needed
  verifyRequest: (verificationId, notes) => {
    const state = get();
    const ver = state.verifications.find(v => v.id === verificationId);
    if (ver) {
      get().communityApproveRequest(ver.assetRequestId, notes);
    }
  },

  communityApproveRequest: (requestId, notes) => {
    set((state) => {
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'COMMUNITY_APPROVED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );
      const log = createLog(requestId, 'Community Leader approved request', 'কমিউনিটি লিডার অনুরোধ অনুমোদন করেছেন', 'REQUESTED', 'COMMUNITY_APPROVED');
      return { assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
    });
  },

  adminApproveRequest: (requestId) => {
    set((state) => {
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'ADMIN_APPROVED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );
      const log = createLog(requestId, 'Main Admin approved request', 'প্রধান অ্যাডমিন অনুরোধ অনুমোদন করেছেন', 'COMMUNITY_APPROVED', 'ADMIN_APPROVED');
      return { assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
    });
    // Immediately run AI assessment for demo flow
    get().runAIAssessment(requestId);
  },
  
  runAIAssessment: (requestId) => {
    set((state) => {
      const assessment: AIAssessment = {
        id: `ai-${Date.now()}`,
        assetRequestId: requestId,
        suitabilityScore: 'HIGH',
        recommendedAssetCategory: 'System Recommended Asset',
        explanation: [
          'Matches occupation',
          'Experience supports safe usage',
          'High demand in area'
        ],
        createdAt: new Date().toISOString()
      };
      
      const pool: FundingPool = {
        id: `pool-${Date.now()}`,
        assetRequestId: requestId,
        targetAmount: state.assetRequests.find(r => r.id === requestId)?.requestedAmount || 35000,
        currentAmount: 0,
        contributions: [],
        status: 'OPEN'
      };

      const log = createLog(requestId, 'AI assessment completed & Funding opened', 'এআই মূল্যায়ন সম্পন্ন ও ফান্ডিং শুরু হয়েছে', 'ADMIN_APPROVED', 'FUNDING');

      const requestsToFunding = state.assetRequests.map(r => 
        r.id === requestId ? { ...r, status: 'FUNDING' as AssetRequestStatus, aiAssessmentId: assessment.id } : r
      );

      return { 
        aiAssessments: [...state.aiAssessments, assessment],
        fundingPools: [...state.fundingPools, pool],
        assetRequests: requestsToFunding,
        lifecycleLogs: [...state.lifecycleLogs, log]
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
        const log = createLog(reqId, 'Funding target reached', 'তহবিলের লক্ষ্য অর্জিত হয়েছে', 'FUNDING', 'FUNDED');
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
      const log = createLog(requestId, 'Vendor assigned and procured', 'ভেন্ডর বরাদ্দ করা হয়েছে', 'FUNDED', 'PROCURED');
      return { assetRequests: requests, lifecycleLogs: [...state.lifecycleLogs, log] };
    });
  },
  
  markDelivered: (requestId) => {
    set((state) => {
      const requests = state.assetRequests.map(r =>
        r.id === requestId ? { ...r, status: 'DELIVERED' as AssetRequestStatus, updatedAt: new Date().toISOString() } : r
      );
      const requestsMonitored = requests.map(r =>
        r.id === requestId ? { ...r, status: 'MONITORING' as AssetRequestStatus } : r
      );
      const log = createLog(requestId, 'Asset delivered', 'সম্পদ ডেলিভারি করা হয়েছে', 'PROCURED', 'DELIVERED');
      const log2 = createLog(requestId, 'Monitoring started', 'পর্যবেক্ষণ শুরু হয়েছে', 'DELIVERED', 'MONITORING');
      return { assetRequests: requestsMonitored, lifecycleLogs: [...state.lifecycleLogs, log, log2] };
    });
  },
}));
