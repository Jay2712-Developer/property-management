@props([
    'title' => null,
    'metaDescription' => null,
])

@component('layouts.customer', [
    'title' => $title,
    'metaDescription' => $metaDescription,
])
    {{ $slot }}
@endcomponent
