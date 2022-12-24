@extends('layouts.app')

@section('content')
    <x-card>
        <x-stack-layout>
            <x-headings.section>
                @lang('crud.studenten_info_sites.edit_title')
            </x-headings.section>

            <x-form
                method="PUT"
                action="{{ route('sites.update', $site) }}"
                has-files
                class="mt-4"
            >
                @include('app.sites.form-inputs')

                <x-stack-layout>
                    <x-buttons.primary submit>
                        @lang('crud.common.update')
                    </x-buttons.primary>

                    <x-buttons.secondary href="{{ route('sites.index') }}">
                        @lang('crud.common.back')
                    </x-buttons.secondary>
                </x-stack-layout>
            </x-form>
        </x-stack-layout>
    </x-card>
</div>
@endsection
