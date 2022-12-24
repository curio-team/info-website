@props([
    'direction' => 'row',
])
<div {{
    $attributes->class([
        'flex justify-between items-center gap-4',
        'flex-row' => $direction === 'row',
        'flex-col' => $direction === 'column',
    ])
}}>
    {{ $slot }}
</div>
