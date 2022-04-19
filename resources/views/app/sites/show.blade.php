@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('sites.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.studenten_info_sites.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.studenten_info_sites.inputs.name')</h5>
                    <span>{{ $site->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.studenten_info_sites.inputs.year')</h5>
                    <span>{{ $site->year ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.studenten_info_sites.inputs.path_nl')</h5>
                    @if($site->path_nl)
                    <a href="{{ \Storage::url($site->path_nl) }}" target="blank"
                        ><i class="icon ion-md-download"></i>&nbsp;Download</a
                    >
                    @else - @endif
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.studenten_info_sites.inputs.path_en')</h5>
                    @if($site->path_en)
                    <a href="{{ \Storage::url($site->path_en) }}" target="blank"
                        ><i class="icon ion-md-download"></i>&nbsp;Download</a
                    >
                    @else - @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('sites.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Site::class)
                <a href="{{ route('sites.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
