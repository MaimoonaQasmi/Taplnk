<div>
    <span class="badge {{ $row->type == 1 ? 'bg-success' : 'bg-info' }}  me-2">
        {{ \App\Models\CouponCode::TYPE[$row->type] }}
    </span>
</div>
