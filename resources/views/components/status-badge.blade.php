@props(['status'])

@php
$normalized = strtolower(trim($status ?? ''));
$config = match($normalized) {
    'draft' => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        'dot' => 'bg-slate-400',
        'label' => 'Draft',
    ],
    'submitted' => [
        'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
        'dot' => 'bg-blue-500',
        'label' => 'Submitted',
    ],
    'under_review' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dot' => 'bg-amber-500',
        'label' => 'Under Review',
    ],
    'verified' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dot' => 'bg-emerald-500',
        'label' => 'Verified',
    ],
    'needs_information', 'needs_info' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dot' => 'bg-amber-500',
        'label' => 'Needs Info',
    ],
    'rejected' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
        'dot' => 'bg-rose-500',
        'label' => 'Rejected',
    ],
    'ai_assessed' => [
        'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
        'dot' => 'bg-purple-500',
        'label' => 'Assessed',
    ],
    'funding_open' => [
        'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'dot' => 'bg-indigo-500',
        'label' => 'Funding Open',
    ],
    'funded' => [
        'bg' => 'bg-teal-50 text-teal-700 border-teal-200',
        'dot' => 'bg-teal-500',
        'label' => '100% Funded',
    ],
    'procurement' => [
        'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'dot' => 'bg-cyan-500',
        'label' => 'Procurement',
    ],
    'procured' => [
        'bg' => 'bg-sky-50 text-sky-700 border-sky-200',
        'dot' => 'bg-sky-500',
        'label' => 'Procured',
    ],
    'delivered' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dot' => 'bg-emerald-500',
        'label' => 'Delivered',
    ],
    'delivery_disputed' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
        'dot' => 'bg-rose-500',
        'label' => 'Delivery Disputed',
    ],
    'monitoring' => [
        'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
        'dot' => 'bg-blue-500',
        'label' => 'In Monitoring',
    ],
    'completed' => [
        'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
        'dot' => 'bg-emerald-600',
        'label' => 'Completed',
    ],
    'failed', 'procurement_failed' => [
        'bg' => 'bg-red-50 text-red-700 border-red-200',
        'dot' => 'bg-red-500',
        'label' => 'Failed',
    ],
    'selected' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dot' => 'bg-emerald-500',
        'label' => 'Selected',
    ],
    'pending' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dot' => 'bg-amber-500',
        'label' => 'Pending',
    ],
    'active' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dot' => 'bg-emerald-500',
        'label' => 'Active',
    ],
    'suspended' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
        'dot' => 'bg-rose-500',
        'label' => 'Suspended',
    ],
    default => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        'dot' => 'bg-slate-400',
        'label' => ucfirst(str_replace('_', ' ', $normalized)),
    ],
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ' . $config['bg']]) }}>
  <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}" aria-hidden="true"></span>
  {{ $config['label'] }}
</span>
