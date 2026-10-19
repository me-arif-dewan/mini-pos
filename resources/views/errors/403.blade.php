@php
    $referer = request()->headers->get('referer', '');
    $back    = $referer ?: route('orders.index');
@endphp

@include('errors.layout', [
    'code'    => 403,
    'title'   => 'Access Denied',
    'message' => $exception->getMessage() ?: 'You do not have permission to perform this action.',
    'back'    => $back,
])
