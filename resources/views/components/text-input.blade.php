@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-amber-200 focus:border-red-600 focus:ring-red-600 rounded-md shadow-sm']) }}>
