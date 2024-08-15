@php
$bgColor = $row->is_approved == \App\Models\Withdrawal::APPROVED ? 'bg-success' : ($row->is_approved == \App\Models\Withdrawal::INPROCESS ? 'bg-warning' :'bg-danger');
@endphp
<span class="badge {{ $bgColor }} me-2">
{{ \App\Models\Withdrawal::APPROVAL_STATUS[$row->is_approved] }}
</span>
