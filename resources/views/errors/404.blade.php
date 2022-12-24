{{-- 404 page --}}
@extends('layouts.app')

@section('content')
<x-card>
    <x-stack-layout>
        <x-headings.section>
            @lang('404')
        </x-headings.section>

        <div>
            @lang($exception->getMessage())
        </div>
    </x-stack-layout>
</x-card>
@endsection
