@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-red-900']) }}>
    {{ $value ?? $slot }}
</label>
