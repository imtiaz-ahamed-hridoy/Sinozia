@props(['status'])

@php
$config = match($status) {
    'new'          => ['label' => 'New',          'class' => 'badge-new'],
    'contacted'    => ['label' => 'Contacted',    'class' => 'badge-contacted'],
    'qualified'    => ['label' => 'Qualified',    'class' => 'badge-qualified'],
    'proposal'     => ['label' => 'Proposal',     'class' => 'badge-proposal'],
    'negotiation'  => ['label' => 'Negotiation',  'class' => 'badge-negotiation'],
    'closed_won'   => ['label' => 'Closed Won',   'class' => 'badge-won'],
    'closed_lost'  => ['label' => 'Closed Lost',  'class' => 'badge-lost'],
    default        => ['label' => ucfirst($status), 'class' => 'badge-default'],
};
@endphp

<span class="lead-badge {{ $config['class'] }}">{{ $config['label'] }}</span>

<style>
.lead-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
    letter-spacing: 0.3px;
}
.badge-new         { background: #E8F4FD; color: #1565C0; }
.badge-contacted   { background: #E8F5E9; color: #2E7D32; }
.badge-qualified   { background: #FFF8E1; color: #F57F17; }
.badge-proposal    { background: #F3E5F5; color: #6A1B9A; }
.badge-negotiation { background: #FBE9E7; color: #BF360C; }
.badge-won         { background: #E0F2F1; color: #004D40; }
.badge-lost        { background: #FFEBEE; color: #B71C1C; }
.badge-default     { background: #F5F5F5; color: #616161; }
</style>