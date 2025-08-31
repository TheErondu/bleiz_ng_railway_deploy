@props([
    'direction' => 'vertical', // or 'horizontal'
    'size' => '4' // Tailwind spacing scale: 1 = 0.25rem, 4 = 1rem, etc.
])

<div @class([
    'w-full' => $direction === 'vertical',
    'h-full' => $direction === 'horizontal',
    "h-{$size}" => $direction === 'vertical',
    "w-{$size}" => $direction === 'horizontal',
])></div>
