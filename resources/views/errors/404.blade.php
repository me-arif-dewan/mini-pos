@php
    $path = request()->path();
    $back = match(true) {
        str_starts_with($path, 'orders')    => route('orders.index'),
        str_starts_with($path, 'customers') => route('customers.index'),
        str_starts_with($path, 'products')  => route('products.index'),
        str_starts_with($path, 'accounting')=> route('accounting.dashboard'),
        default                             => route('orders.index'),
    };
@endphp

@include('errors.layout', [
    'code'    => 404,
    'title'   => 'Page Not Found',
    'message' => 'The page you are looking for does not exist or may have been moved.',
    'back'    => $back,
])
