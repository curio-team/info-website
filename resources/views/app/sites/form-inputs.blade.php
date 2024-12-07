@php $editing = isset($site) @endphp

<x-stack-layout>
    <x-inputs.group class="col-sm-12">
        <x-inputs.text name="name"
                       label="Voornaam Student"
                       value="{{ old('name', ($editing ? $site->name : '')) }}"
                       maxlength="255"
                       placeholder="Janiek"
                       required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text name="year"
                       label="Leerjaar"
                       value="{{ old('year', ($editing ? $site->year : '')) }}"
                       minlength="1"
                       maxlength="4"
                       placeholder="2"
                       required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.partials.label name="path_nl"
                                 label="Website als .zip - Nederlandstalig"></x-inputs.partials.label><br />

        <input type="file"
               name="path_nl"
               id="path_nl"
               class="form-control-file" />

        @if($editing && $site->path_nl)
        <div class="mt-2">
            <x-buttons.link href="{{ \Storage::url($site->path_nl) }}"
                            target="_blank">Download</x-buttons.link>
        </div>
        @endif
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.partials.label name="path_en"
                                 label="Website als .zip - Engelstalig"></x-inputs.partials.label><br />

        <input type="file"
               name="path_en"
               id="path_en"
               class="form-control-file" />

        @if($editing && $site->path_en)
        <div class="mt-2">
            <x-buttons.link href="{{ \Storage::url($site->path_en) }}"
               target="_blank">Download</x-buttons.link>
        </div>
        @endif
    </x-inputs.group>


    <x-inputs.group class="col-sm-12 d-flex align-items-start"
                    style="gap: 1em;"
                    x-data="{ showUnsafe: false }">
        <x-buttons.danger @click="showUnsafe = !showUnsafe">
            Maak gevaarlijke actie mogelijk
        </x-buttons.danger>
        <div class="collapsed border p-4"
             :class="{ 'show': showUnsafe }">
            <x-inputs.checkbox name="allow_unsafe"
                               label="Sta onveilige bestandsextensies toe"
                               checked="{{ old('allow_unsafe', ($editing ? $site->allow_unsafe : '')) }}">
                </x-inputs.text>
                <em>Zonder vinkje zullen alleen deze type bestanden uitgepakt worden uit de zip: {{ implode(', ',
                    \App\Http\Controllers\SiteController::EXT_ALLOWLIST) }}</em>
        </div>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 d-flex align-items-start"
                    style="gap: 1em;"
                    x-data="{ showDeactivate: false }">
        <x-buttons.danger @click="showDeactivate = !showDeactivate">
            Deactiveer Site
        </x-buttons.danger>
        <div class="collapsed border p-4"
             :class="{ 'show': showDeactivate }">
            <x-inputs.checkbox name="deactivate"
                               label="Deactiveer"
                               checked="{{ old('deactivate', ($editing ? $site->deactivate : '')) }}">
                </x-inputs.text>
                <em>Zonder vinkje zal de site nog steeds zichtbaar zijn</em>
        </div>
    </x-inputs.group>

</x-stack-layout>
