import { Worker, AssetRequest, FundingPool, AIAssessment, Verification, LifecycleLog, Contribution } from "@/types";

export const DEMO_WORKERS: Worker[] = [
  {
    id: 'w-1', name: 'Rahim Uddin', occupation: 'Tailor', experience: '5 years', location: 'Dhaka', phone: '01700000001',
    story: 'I need an industrial sewing machine to take bulk orders and increase my daily income.', verified: false,
    profileImageUrl: 'https://images.unsplash.com/photo-1551817958-c5b51e52befa?w=400&q=80'
  },
  {
    id: 'w-2', name: 'Fatima Begum', occupation: 'Street Food Vendor', experience: '3 years', location: 'Chattogram', phone: '01700000002',
    story: 'My wooden cart broke during the monsoon. A steel cart will let me serve food safely again.', verified: true,
    profileImageUrl: 'https://images.unsplash.com/photo-1577902462377-101f3b3cc4bc?w=400&q=80'
  },
  {
    id: 'w-3', name: 'Karim Ali', occupation: 'Rickshaw Puller', experience: '10 years', location: 'Rajshahi', phone: '01700000003',
    story: 'Renting a rickshaw takes 50% of my income. Owning a battery-assisted rickshaw will change my family\'s life.', verified: true,
    profileImageUrl: 'https://images.unsplash.com/photo-1628185012586-724ec56eb3b6?w=400&q=80'
  },
  {
    id: 'w-4', name: 'Amina Khatun', occupation: 'Retail Stall Owner', experience: '2 years', location: 'Sylhet', phone: '01700000004',
    story: 'I want to expand my stall with a proper display freezer to sell perishable goods.', verified: true,
    profileImageUrl: 'https://images.unsplash.com/photo-1616231454508-32a15f070b40?w=400&q=80'
  },
  {
    id: 'w-5', name: 'Shafiqul Islam', occupation: 'Farmer', experience: '15 years', location: 'Bogura', phone: '01700000005',
    story: 'A shared power tiller will help me and 4 other farmers cultivate our land much faster.', verified: true,
    profileImageUrl: 'https://images.unsplash.com/photo-1595841696677-648934b11f32?w=400&q=80'
  },
  {
    id: 'w-6', name: 'Rina Akter', occupation: 'Embroidery Artisan', experience: '4 years', location: 'Cumilla', phone: '01700000006',
    story: 'An automatic embroidery machine helped me hire two assistants from my village.', verified: true
  },
  {
    id: 'w-7', name: 'Jamal Hossain', occupation: 'Carpenter', experience: '8 years', location: 'Khulna', phone: '01700000007',
    story: 'Modern power tools have doubled my production speed.', verified: true
  },
  {
    id: 'w-8', name: 'Hasina Banu', occupation: 'Poultry Farmer', experience: '1 year', location: 'Mymensingh', phone: '01700000008',
    story: 'Need an incubator to scale my small backyard poultry farm into a real business.', verified: true
  },
  {
    id: 'w-9', name: 'Tariq Rahman', occupation: 'Plumber', experience: '6 years', location: 'Barishal', phone: '01700000009',
    story: 'A professional plumbing toolkit will allow me to take commercial contracts.', verified: true
  },
  {
    id: 'w-10', name: 'Nusrat Jahan', occupation: 'Weaver', experience: '12 years', location: 'Tangail', phone: '01700000010',
    story: 'My traditional loom is failing. A semi-automatic loom is required to keep the craft alive.', verified: false
  }
];

export const DEMO_ASSET_REQUESTS: AssetRequest[] = [
  { id: 'ar-1', workerId: 'w-1', requestedAssetCategory: 'Industrial Sewing Machine', reason: 'Bulk orders', requestedAmount: 35000, status: 'REQUESTED', createdAt: '2026-09-01T10:00:00Z', updatedAt: '2026-09-01T10:00:00Z' },
  { id: 'ar-2', workerId: 'w-2', requestedAssetCategory: 'Steel Food Cart', reason: 'Safe food service', requestedAmount: 18000, status: 'VERIFIED', verificationId: 'v-2', createdAt: '2026-09-02T10:00:00Z', updatedAt: '2026-09-02T12:00:00Z' },
  { id: 'ar-3', workerId: 'w-3', requestedAssetCategory: 'Battery-Assisted Rickshaw', reason: 'Own instead of rent', requestedAmount: 45000, status: 'FUNDING', verificationId: 'v-3', aiAssessmentId: 'ai-3', fundingPoolId: 'fp-3', createdAt: '2026-08-25T10:00:00Z', updatedAt: '2026-08-27T10:00:00Z' },
  { id: 'ar-4', workerId: 'w-4', requestedAssetCategory: 'Display Freezer', reason: 'Sell perishables', requestedAmount: 28000, status: 'FUNDED', verificationId: 'v-4', aiAssessmentId: 'ai-4', fundingPoolId: 'fp-4', createdAt: '2026-08-20T10:00:00Z', updatedAt: '2026-09-05T10:00:00Z' },
  { id: 'ar-5', workerId: 'w-5', requestedAssetCategory: 'Power Tiller', reason: 'Faster cultivation', requestedAmount: 85000, status: 'PROCURED', verificationId: 'v-5', aiAssessmentId: 'ai-5', fundingPoolId: 'fp-5', procurementId: 'pr-5', createdAt: '2026-08-10T10:00:00Z', updatedAt: '2026-09-04T10:00:00Z' },
  { id: 'ar-6', workerId: 'w-6', requestedAssetCategory: 'Embroidery Machine', reason: 'Hire assistants', requestedAmount: 40000, status: 'MONITORING', verificationId: 'v-6', aiAssessmentId: 'ai-6', fundingPoolId: 'fp-6', procurementId: 'pr-6', createdAt: '2026-07-01T10:00:00Z', updatedAt: '2026-08-01T10:00:00Z' },
  { id: 'ar-7', workerId: 'w-7', requestedAssetCategory: 'Power Tools Kit', reason: 'Double speed', requestedAmount: 12000, status: 'MONITORING', verificationId: 'v-7', aiAssessmentId: 'ai-7', fundingPoolId: 'fp-7', procurementId: 'pr-7', createdAt: '2026-06-15T10:00:00Z', updatedAt: '2026-07-10T10:00:00Z' },
  { id: 'ar-8', workerId: 'w-8', requestedAssetCategory: 'Poultry Incubator', reason: 'Scale farm', requestedAmount: 25000, status: 'FUNDING', verificationId: 'v-8', aiAssessmentId: 'ai-8', fundingPoolId: 'fp-8', createdAt: '2026-09-03T10:00:00Z', updatedAt: '2026-09-04T10:00:00Z' },
  { id: 'ar-9', workerId: 'w-9', requestedAssetCategory: 'Commercial Plumbing Kit', reason: 'Commercial contracts', requestedAmount: 15000, status: 'FUNDING', verificationId: 'v-9', aiAssessmentId: 'ai-9', fundingPoolId: 'fp-9', createdAt: '2026-09-01T10:00:00Z', updatedAt: '2026-09-02T10:00:00Z' },
  { id: 'ar-10', workerId: 'w-10', requestedAssetCategory: 'Semi-Auto Loom', reason: 'Keep craft alive', requestedAmount: 50000, status: 'REQUESTED', createdAt: '2026-09-06T08:00:00Z', updatedAt: '2026-09-06T08:00:00Z' }
];

export const DEMO_VERIFICATIONS: Verification[] = [
  { id: 'v-1', assetRequestId: 'ar-1', communityMemberId: 'cm-1', status: 'PENDING', notes: '' },
  { id: 'v-2', assetRequestId: 'ar-2', communityMemberId: 'cm-1', status: 'VERIFIED', notes: 'Known vendor in the area.', verifiedAt: '2026-09-02T12:00:00Z' },
  { id: 'v-3', assetRequestId: 'ar-3', communityMemberId: 'cm-2', status: 'VERIFIED', notes: 'Hardworking driver.', verifiedAt: '2026-08-26T10:00:00Z' },
  { id: 'v-4', assetRequestId: 'ar-4', communityMemberId: 'cm-3', status: 'VERIFIED', notes: 'Valid business location.', verifiedAt: '2026-08-21T10:00:00Z' },
  { id: 'v-5', assetRequestId: 'ar-5', communityMemberId: 'cm-4', status: 'VERIFIED', notes: 'Cooperative member.', verifiedAt: '2026-08-11T10:00:00Z' },
  { id: 'v-6', assetRequestId: 'ar-6', communityMemberId: 'cm-5', status: 'VERIFIED', notes: 'Excellent craftswoman.', verifiedAt: '2026-07-02T10:00:00Z' },
  { id: 'v-7', assetRequestId: 'ar-7', communityMemberId: 'cm-6', status: 'VERIFIED', notes: 'Verified workshop.', verifiedAt: '2026-06-16T10:00:00Z' },
  { id: 'v-8', assetRequestId: 'ar-8', communityMemberId: 'cm-7', status: 'VERIFIED', notes: 'Has space for incubator.', verifiedAt: '2026-09-04T10:00:00Z' },
  { id: 'v-9', assetRequestId: 'ar-9', communityMemberId: 'cm-8', status: 'VERIFIED', notes: 'Certified plumber.', verifiedAt: '2026-09-02T10:00:00Z' },
  { id: 'v-10', assetRequestId: 'ar-10', communityMemberId: 'cm-9', status: 'FLAGGED', notes: 'Needs more income proof before approval.', verifiedAt: '2026-09-06T09:00:00Z' }
];

export const DEMO_AI_ASSESSMENTS: AIAssessment[] = [
  { id: 'ai-3', assetRequestId: 'ar-3', suitabilityScore: 'HIGH', recommendedAssetCategory: 'Battery Rickshaw', explanation: ['10 yrs experience', 'High local demand'], createdAt: '2026-08-26T10:05:00Z' },
  { id: 'ai-4', assetRequestId: 'ar-4', suitabilityScore: 'MEDIUM', recommendedAssetCategory: 'Display Freezer', explanation: ['Moderate experience', 'Location supports foot traffic'], createdAt: '2026-08-21T10:05:00Z' },
  { id: 'ai-5', assetRequestId: 'ar-5', suitabilityScore: 'HIGH', recommendedAssetCategory: 'Power Tiller', explanation: ['High community impact', '15 yrs experience'], createdAt: '2026-08-11T10:05:00Z' },
  { id: 'ai-6', assetRequestId: 'ar-6', suitabilityScore: 'HIGH', recommendedAssetCategory: 'Embroidery Machine', explanation: ['Scalable skill', 'Generates employment'], createdAt: '2026-07-02T10:05:00Z' },
  { id: 'ai-7', assetRequestId: 'ar-7', suitabilityScore: 'HIGH', recommendedAssetCategory: 'Power Tools', explanation: ['Immediate productivity boost'], createdAt: '2026-06-16T10:05:00Z' },
  { id: 'ai-8', assetRequestId: 'ar-8', suitabilityScore: 'HIGH', recommendedAssetCategory: 'Incubator', explanation: ['Good ROI potential', 'Space verified'], createdAt: '2026-09-04T10:05:00Z' },
  { id: 'ai-9', assetRequestId: 'ar-9', suitabilityScore: 'MEDIUM', recommendedAssetCategory: 'Plumbing Kit', explanation: ['Steady demand in urban area'], createdAt: '2026-09-02T10:05:00Z' },
];

export const DEMO_CONTRIBUTIONS: Contribution[] = [
  { id: 'c-1', contributorId: 'usr-1', amount: 15000, timestamp: '2026-08-27T10:00:00Z' },
  { id: 'c-2', contributorId: 'usr-2', amount: 14250, timestamp: '2026-08-28T10:00:00Z' },
  { id: 'c-3', contributorId: 'usr-3', amount: 28000, timestamp: '2026-09-05T10:00:00Z' }, // ar-4 funded
  { id: 'c-4', contributorId: 'usr-4', amount: 85000, timestamp: '2026-09-01T10:00:00Z' }, // ar-5 funded
  { id: 'c-5', contributorId: 'usr-5', amount: 40000, timestamp: '2026-07-15T10:00:00Z' }, // ar-6 funded
  { id: 'c-6', contributorId: 'usr-6', amount: 12000, timestamp: '2026-06-20T10:00:00Z' }, // ar-7 funded
  { id: 'c-7', contributorId: 'usr-1', amount: 5000, timestamp: '2026-09-05T10:00:00Z' },
  { id: 'c-8', contributorId: 'usr-2', amount: 13500, timestamp: '2026-09-06T10:00:00Z' },
];

export const DEMO_FUNDING_POOLS: FundingPool[] = [
  { id: 'fp-3', assetRequestId: 'ar-3', targetAmount: 45000, currentAmount: 29250, status: 'OPEN', contributions: [DEMO_CONTRIBUTIONS[0], DEMO_CONTRIBUTIONS[1]] },
  { id: 'fp-4', assetRequestId: 'ar-4', targetAmount: 28000, currentAmount: 28000, status: 'COMPLETED', contributions: [DEMO_CONTRIBUTIONS[2]] },
  { id: 'fp-5', assetRequestId: 'ar-5', targetAmount: 85000, currentAmount: 85000, status: 'COMPLETED', contributions: [DEMO_CONTRIBUTIONS[3]] },
  { id: 'fp-6', assetRequestId: 'ar-6', targetAmount: 40000, currentAmount: 40000, status: 'COMPLETED', contributions: [DEMO_CONTRIBUTIONS[4]] },
  { id: 'fp-7', assetRequestId: 'ar-7', targetAmount: 12000, currentAmount: 12000, status: 'COMPLETED', contributions: [DEMO_CONTRIBUTIONS[5]] },
  { id: 'fp-8', assetRequestId: 'ar-8', targetAmount: 25000, currentAmount: 5000, status: 'OPEN', contributions: [DEMO_CONTRIBUTIONS[6]] },
  { id: 'fp-9', assetRequestId: 'ar-9', targetAmount: 15000, currentAmount: 13500, status: 'OPEN', contributions: [DEMO_CONTRIBUTIONS[7]] },
];

export const DEMO_LIFECYCLE_LOGS: LifecycleLog[] = [
  { id: 'll-1', assetRequestId: 'ar-6', event: 'Income increased by 40%', eventBn: 'আয় ৪০% বৃদ্ধি পেয়েছে', previousStatus: 'DELIVERED', newStatus: 'MONITORING', timestamp: '2026-08-15T10:00:00Z' },
  { id: 'll-2', assetRequestId: 'ar-7', event: 'Workshop output doubled', eventBn: 'ওয়ার্কশপের উৎপাদন দ্বিগুণ হয়েছে', previousStatus: 'DELIVERED', newStatus: 'MONITORING', timestamp: '2026-08-01T10:00:00Z' }
];

export const DEMO_INITIAL_STATE = {
  workers: DEMO_WORKERS,
  assetRequests: DEMO_ASSET_REQUESTS,
  verifications: DEMO_VERIFICATIONS,
  aiAssessments: DEMO_AI_ASSESSMENTS,
  fundingPools: DEMO_FUNDING_POOLS,
  lifecycleLogs: DEMO_LIFECYCLE_LOGS,
};
