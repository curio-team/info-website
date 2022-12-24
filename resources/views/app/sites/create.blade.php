@extends('layouts.app')

@section('content')
<div>
    <x-card>
        <x-form
            method="POST"
            action="{{ route('sites.store') }}"
            has-files>
            <x-stack-layout direction="column">
                @include('app.sites.form-inputs')

                <div class="mt-4">
                    <a href="{{ route('sites.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left text-primary"></i>
                        @lang('crud.common.back')
                    </a>

                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i>
                        @lang('crud.common.create')
                    </button>
                </div>
            </x-stack-layout>
        </x-form>
    </x-card>
</div>
@endsection
