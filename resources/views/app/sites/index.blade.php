@extends('layouts.app')

@section('content')
<x-card>
    <x-stack-layout>
        <div style="display: flex; justify-content: space-between;">
            <h4 class="card-title">
                @lang('crud.studenten_info_sites.index_title')
            </h4>
        </div>

        <div>
            <x-stack-layout row>
                <div class="grow">
                    <form>
                        <x-stack-layout row>
                            <x-inputs.text name="search"
                                           placeholder="{{ __('crud.common.search') }}"
                                           value="{{ $search ?? '' }}"
                                           class="form-control"
                                           autocomplete="off" />
                            <x-buttons.primary submit>
                                <x-icons.search />
                            </x-buttons.primary>
                        </x-stack-layout>
                    </form>
                </div>
                <div class="grow">
                    @can('create', App\Models\Site::class)
                    <x-buttons.primary href="{{ route('sites.create') }}">
                        @lang('crud.common.create')
                    </x-buttons.primary>
                    @endcan
                </div>
            </x-stack-layout>
        </div>

        <div>
            <table class="w-full border-separate table-auto border-spacing-1">
                <thead>
                    <tr>
                        <th class="text-left">
                            @lang('crud.studenten_info_sites.inputs.name')
                        </th>
                        <th class="text-left">
                            @lang('crud.studenten_info_sites.inputs.year')
                        </th>
                        <th class="text-left">
                            @lang('crud.studenten_info_sites.inputs.path_nl')
                        </th>
                        <th class="text-left">
                            @lang('crud.studenten_info_sites.inputs.path_en')
                        </th>
                        <th class="text-center">
                            @lang('crud.common.actions')
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                    <tr>
                        <td>{{ $site->name ?? '-' }}</td>
                        <td>{{ $site->year ?? '-' }}</td>
                        <td>
                            @if($site->path_nl)
                            <x-buttons.link href="{{ \Storage::url($site->path_nl) }}"
                               target="blank">Download</x-buttons.link>
                            @else - @endif
                        </td>
                        <td>
                            @if($site->path_en)
                            <x-buttons.link href="{{ \Storage::url($site->path_en) }}"
                               target="blank">Download</x-buttons.link>
                            @else - @endif
                        </td>
                        <td class="text-center"
                            style="width: 134px;">
                            <div role="group"
                                 aria-label="Site Actions"
                                 class="flex flex-row gap-1">
                                @can('update', $site)
                                <a href="{{ route('sites.edit', $site) }}">
                                    <x-buttons.secondary>
                                        <x-icons.edit />
                                        <div class="hidden">
                                            @lang('crud.common.edit')
                                        </div>
                                    </x-buttons.secondary>
                                </a>
                                @endcan @can('view', $site)
                                <a href="{{ route('sites.show', $site) }}">
                                    <x-buttons.secondary>
                                        <x-icons.show />
                                        <div class="hidden">
                                            @lang('crud.common.show')
                                        </div>
                                    </x-buttons.secondary>
                                </a>
                                @endcan @can('delete', $site)
                                <form action="{{ route('sites.destroy', $site) }}"
                                      method="POST"
                                      onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')">
                                    @csrf @method('DELETE')
                                    <x-buttons.danger submit>
                                        <x-icons.delete />
                                        <div class="hidden">
                                            @lang('crud.common.delete')
                                        </div>
                                    </x-buttons.danger>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            @lang('crud.common.no_items_found')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">{!! $sites->render() !!}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-stack-layout>
</x-card>
@endsection
