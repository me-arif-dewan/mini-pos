@php
    $back = request()->headers->get('referer') ?: route('orders.index');
@endphp

@include('errors.layout', [
    'code'    => 500,
    'title'   => 'Server Error',
    'message' => 'Something went wrong on our end. Please try again or contact support if the problem persists.',
    'back'    => $back,
])
