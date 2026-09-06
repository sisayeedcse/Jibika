export type Role = 'WORKER' | 'COMMUNITY' | 'CONTRIBUTOR' | 'VENDOR' | 'ADMIN';

export type AssetRequestStatus = 
  | 'REQUESTED' 
  | 'COMMUNITY_APPROVED' 
  | 'ADMIN_APPROVED' 
  | 'FUNDING' 
  | 'FUNDED' 
  | 'PROCURED' 
  | 'DELIVERED' 
  | 'MONITORING';

export interface Community {
  id: string;
  name: string;
  location: string;
  leaderId: string;
}

export interface Worker {
  id: string;
  name: string;
  occupation: string;
  experience: string;
  location: string;
  phone: string;
  profileImageUrl?: string;
  story: string;
  verified: boolean;
  communityId?: string;
  communityStatus?: 'NONE' | 'PENDING' | 'APPROVED';
}

export interface AssetCategory {
  id: string;
  name: string;
  nameBn: string;
  estimatedCost: number;
  imageUrl?: string;
}

export interface AssetRequest {
  id: string;
  workerId: string;
  requestedAssetCategory: string;
  reason: string;
  requestedAmount: number;
  status: AssetRequestStatus;
  
  // Relations
  verificationId?: string;
  aiAssessmentId?: string;
  fundingPoolId?: string;
  procurementId?: string;
  
  createdAt: string;
  updatedAt: string;
}

export interface Verification {
  id: string;
  assetRequestId: string;
  communityMemberId: string;
  status: 'PENDING' | 'VERIFIED' | 'FLAGGED';
  notes: string;
  verifiedAt?: string;
}

export interface AIAssessment {
  id: string;
  assetRequestId: string;
  suitabilityScore: 'HIGH' | 'MEDIUM' | 'LOW';
  recommendedAssetCategory: string;
  explanation: string[];
  createdAt: string;
}

export interface FundingPool {
  id: string;
  assetRequestId: string;
  targetAmount: number;
  currentAmount: number;
  contributions: Contribution[];
  status: 'OPEN' | 'COMPLETED';
}

export interface Contribution {
  id: string;
  contributorId: string;
  amount: number;
  timestamp: string;
}

export interface LifecycleLog {
  id: string;
  assetRequestId: string;
  event: string;
  eventBn: string; // localized message
  previousStatus: AssetRequestStatus | null;
  newStatus: AssetRequestStatus;
  timestamp: string;
}

// Global Demo State
export interface JibikaState {
  communities: Community[];
  workers: Worker[];
  assetRequests: AssetRequest[];
  verifications: Verification[];
  aiAssessments: AIAssessment[];
  fundingPools: FundingPool[];
  lifecycleLogs: LifecycleLog[];
  
  // Active User Context (For Demo)
  activeRole: Role;
  activeUserId: string;
  
  // Actions
  setActiveRole: (role: Role, userId: string) => void;
  
  // Worker & Community Actions
  registerWorker: (worker: Partial<Worker>) => void;
  requestJoinCommunity: (workerId: string, communityId: string) => void;
  approveCommunityJoin: (workerId: string) => void;
  createAssetRequest: (request: Partial<AssetRequest>) => void;
  
  // Old verification replaced by community/admin approval
  verifyRequest: (verificationId: string, notes: string) => void; // Keep for backward compat or transition
  communityApproveRequest: (requestId: string, notes: string) => void;
  adminApproveRequest: (requestId: string) => void;
  
  runAIAssessment: (requestId: string) => void;
  addContribution: (poolId: string, amount: number) => void;
  markProcured: (requestId: string) => void;
  markDelivered: (requestId: string) => void;
  resetStore: () => void;
}
