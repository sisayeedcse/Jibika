<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\CommunityVerification;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $unreadCount = Notification::where('user_id', $user->id)->whereNull('read_at')->count();

        if ($user->isWorker()) {
            $requests = AssetRequest::where('worker_id', $user->id)
                ->with(['category', 'fundingPool', 'procurementOrder.delivery'])
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'total'      => AssetRequest::where('worker_id', $user->id)->count(),
                'drafts'     => AssetRequest::where('worker_id', $user->id)->where('status', 'draft')->count(),
                'in_progress'=> AssetRequest::where('worker_id', $user->id)->whereIn('status', ['submitted', 'verified', 'ai_assessed', 'funding_open'])->count(),
                'funded'     => AssetRequest::where('worker_id', $user->id)->whereIn('status', ['funded', 'procurement', 'procured', 'delivered', 'monitoring', 'completed'])->count(),
            ];

            return view('pages.dashboards.worker', compact('user', 'unreadCount', 'requests', 'stats'));
        }

        if ($user->isCommunity()) {
            $pendingVerifications = CommunityVerification::where('status', 'pending')
                ->with(['request.worker.workerProfile', 'request.category'])
                ->latest()
                ->take(8)
                ->get();

            $myDecisions = CommunityVerification::where('community_member_id', $user->id)
                ->where('status', '!=', 'pending')
                ->with('request.worker')
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'pending_queue'  => CommunityVerification::where('status', 'pending')->count(),
                'verified_by_me' => CommunityVerification::where('community_member_id', $user->id)->where('status', 'verified')->count(),
                'flagged_by_me'  => CommunityVerification::where('community_member_id', $user->id)->whereIn('status', ['flagged', 'rejected', 'needs_info'])->count(),
            ];

            return view('pages.dashboards.community', compact('user', 'unreadCount', 'pendingVerifications', 'myDecisions', 'stats'));
        }

        if ($user->isContributor()) {
            $openPools = FundingPool::where('status', 'open')
                ->with(['request.worker.workerProfile', 'request.category'])
                ->latest()
                ->take(6)
                ->get();

            $myPledges = ContributionOrPledge::where('contributor_id', $user->id)
                ->with('pool.request')
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'total_pledged' => ContributionOrPledge::where('contributor_id', $user->id)->sum('amount_bdt'),
                'projects_backed' => ContributionOrPledge::where('contributor_id', $user->id)->distinct('pool_id')->count(),
                'open_opportunities' => FundingPool::where('status', 'open')->count(),
            ];

            return view('pages.dashboards.contributor', compact('user', 'unreadCount', 'openPools', 'myPledges', 'stats'));
        }

        if ($user->isVendor()) {
            $opportunities = AssetRequest::where('status', 'funded')
                ->whereDoesntHave('procurementOrder')
                ->with(['category', 'worker'])
                ->latest()
                ->take(6)
                ->get();

            $myQuotes = VendorQuotation::where('vendor_id', $user->id)
                ->with(['procurementOrder.request'])
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'open_quotes'  => AssetRequest::where('status', 'funded')->whereDoesntHave('procurementOrder')->count(),
                'my_quotes'    => VendorQuotation::where('vendor_id', $user->id)->count(),
                'orders_won'   => ProcurementOrder::where('vendor_id', $user->id)->count(),
            ];

            return view('pages.dashboards.vendor', compact('user', 'unreadCount', 'opportunities', 'myQuotes', 'stats'));
        }

        // Admin
        $stats = [
            'users'                => User::count(),
            'requests'             => AssetRequest::count(),
            'pending_verification' => AssetRequest::where('status', 'submitted')->count(),
            'funded'               => AssetRequest::where('status', 'funded')->count(),
            'delivered'            => AssetRequest::whereHas('procurementOrder.delivery', fn($q) => $q->where('status', 'delivered'))->count(),
        ];

        $recentRequests = AssetRequest::with(['worker', 'category'])->latest()->take(6)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(8)->get();

        return view('pages.dashboards.admin', compact('user', 'unreadCount', 'stats', 'recentRequests', 'recentAuditLogs'));
    }
}
