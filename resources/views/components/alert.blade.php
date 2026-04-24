@props(['type' => 'success', 'message'])

<div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
    <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'exclamation-triangle' : 'info-circle') }} me-2"></i>
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
