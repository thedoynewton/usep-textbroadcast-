@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium']) }}>
    {{ $value ?? $slot }}
</label>
