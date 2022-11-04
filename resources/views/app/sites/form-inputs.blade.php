@php $editing = isset($site) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Voornaam Student"
            value="{{ old('name', ($editing ? $site->name : '')) }}"
            maxlength="255"
            placeholder="Janiek"
            required
        ></x-inputs.text>
        @error('name') 
            @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="year"
            label="Leerjaar"
            value="{{ old('year', ($editing ? $site->year : '')) }}"
            minlength="1"
            maxlength="4"
            placeholder="2"
            required
        ></x-inputs.text>
        @error('year') 
            @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.partials.label
            name="path_nl"
            label="Website als .zip - Nederlandstalig"
        ></x-inputs.partials.label
        ><br />

        <input
            type="file"
            name="path_nl"
            id="path_nl"
            class="form-control-file"
        />

        @if($editing && $site->path_nl)
        <div class="mt-2">
            <a href="{{ \Storage::url($site->path_nl) }}" target="_blank"
                ><i class="icon ion-md-download"></i>&nbsp;Download</a
            >
        </div>
        @endif
        @error('path_nl') 
            @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.partials.label
            name="path_en"
            label="Website als .zip - Engelstalig"
        ></x-inputs.partials.label
        ><br />

        <input
            type="file"
            name="path_en"
            id="path_en"
            class="form-control-file"
        />

        @if($editing && $site->path_en)
        <div class="mt-2">
            <a href="{{ \Storage::url($site->path_en) }}" target="_blank"
                ><i class="icon ion-md-download"></i>&nbsp;Download</a
            >
        </div>
        @endif
        @error('path_en') 
            @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="allow_unsafe"
            label="Sta onveilige bestandsextensies toe"
            checked="{{ old('allow_unsafe', ($editing ? $site->allow_unsafe : '')) }}"
        ></x-inputs.text>
        <em>(Zonder vinkje zullen alleen deze extensies uitgepakt worden uit de zip: {{ implode(', ', \App\Http\Controllers\SiteController::EXTENSION_ALLOWLIST) }})</em>
    </x-inputs.group>
</div>
