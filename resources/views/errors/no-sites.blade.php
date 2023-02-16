{{-- No sites error page page --}}
@extends('layouts.app')

@section('content') 
<x-card>
    <x-stack-layout>
        <x-headings.section class="text-center">
            Geen sites beschikbaar <?php if(isset($english)) : ?> in het Engels <?php endif; ?>
        </x-headings.section>

        <div>
            <div class="error-wrapper text-center">
                <h1>Er zijn op dit moment geen sites beschikbaar.</h1>
                <p>Bezoek deze website op een later moment weer.</p>
                <p>Mocht je al een student zijn, lever dan jouw info-site in bij de docent zodat die jouw site hier kan uploaden!</p>
            </div>
        </div>
    </x-stack-layout>
</x-card>
@endsection