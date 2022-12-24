@extends('layouts.app')

@section('content')
    <x-card>
        <x-form
            method="POST"
            action="{{ route('sites.store') }}"
            has-files>
            <x-stack-layout>
                @include('app.sites.form-inputs')

                <x-stack-layout>
                    <x-buttons.primary submit>
                        @lang('crud.common.create')
                    </x-buttons.primary>

                    <x-buttons.secondary href="{{ route('sites.index') }}">
                        @lang('crud.common.back')
                    </x-buttons.secondary>
                </x-stack-layout>
            </x-stack-layout>
        </x-form>
    </x-card>
@endsection
