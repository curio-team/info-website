@extends('layouts.app')

@section('content')
    <x-card>
        <x-headings.section>
            @lang('crud.studenten_info_sites.test')
        </x-headings.section>
        <p>
            @lang('crud.studenten_info_sites.test_description', [
                'lifetime' => config('app.site_testing.remove_after')
            ])
        </p>
        <x-form
            method="POST"
            action="{{ route('sites.test.submit') }}"
            has-files>
            <x-stack-layout>
                <x-inputs.group class="col-sm-12">
                    <x-inputs.partials.label name="path"
                                            label="Website als .zip"></x-inputs.partials.label><br />

                    <input type="file"
                        name="path"
                        id="path"
                        class="form-control-file" />
                </x-inputs.group>

                <x-stack-layout>
                    <x-buttons.primary submit>
                        @lang('crud.studenten_info_sites.test_submit')
                    </x-buttons.primary>
                </x-stack-layout>
            </x-stack-layout>
        </x-form>
    </x-card>
@endsection
