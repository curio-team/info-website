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
        <div class="border border-amber-400 bg-amber-50 rounded p-4 text-sm flex flex-col gap-2">
            <strong>Controleer de zip voordat je hem uploadt</strong>
            <p>
                Studentensites worden op dit domein gepubliceerd. In de normale weergave draait de site in een afgeschermd
                frame, maar de bestanden zijn ook rechtstreeks te openen via <code>/storage/…</code>. Dan draait de
                JavaScript van de student alsof het onze eigen site is en kan die, als je ingelogd bent, handelingen
                namens jou uitvoeren (zoals sites verwijderen). Kijk daarom in de <code>.html</code>- en
                <code>.js</code>-bestanden of je het volgende tegenkomt:
            </p>
            <ul class="list-disc pl-5 flex flex-col gap-1">
                <li>Verzoeken naar dit beheerpaneel of andere pagina's van deze site, bijvoorbeeld <code>fetch</code>,
                    <code>XMLHttpRequest</code> of formulieren met <code>/beheer</code> of <code>/test</code> erin.</li>
                <li>Code die cookies of opgeslagen gegevens leest: <code>document.cookie</code>,
                    <code>localStorage</code> of <code>sessionStorage</code>.</li>
                <li>Scripts die van een onbekende externe website geladen worden:
                    <code>&lt;script src="https://…"&gt;</code>.</li>
                <li>Onleesbare of verstopte code: <code>eval(</code>, <code>new Function(</code>, <code>atob(</code> of
                    lange reeksen willekeurige tekens.</li>
                <li>Doorsturen naar andere sites: <code>window.location</code>, <code>top.location</code> of
                    <code>&lt;meta http-equiv="refresh"&gt;</code>.</li>
                <li>Formulieren die om een wachtwoord of inloggegevens vragen.</li>
            </ul>
            <p>
                Twijfel je? Vraag de student wat de code doet of laat het weghalen. Bekijk studentensites via de normale
                weergave en niet rechtstreeks via de <code>/storage/</code>-link terwijl je ingelogd bent.
            </p>
        </div>
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
                </x-inputs.checkbox>
                <em>Zonder vinkje zullen alleen deze type bestanden uitgepakt worden uit de zip: {{ implode(', ',
                    \App\Http\Controllers\SiteController::EXT_ALLOWLIST) }}</em>
                <div class="mt-4 text-sm text-red-700 flex flex-col gap-2">
                    <p>
                        <strong>Let op:</strong> met dit vinkje worden <strong>alle</strong> bestanden uit de zip
                        uitgepakt in de openbare map van de webserver. Bestanden zoals <code>.php</code>,
                        <code>.phtml</code>, <code>.phar</code> of <code>.cgi</code> worden dan op de server uitgevoerd
                        zodra iemand ze opent. Zo'n bestand kan alles op de server lezen en aanpassen, ook wachtwoorden en
                        de sites van andere studenten. Een <code>.htaccess</code>- of <code>.user.ini</code>-bestand kan
                        de instellingen van de webserver veranderen, bijvoorbeeld zodat een gewoon <code>.html</code>-bestand
                        als PHP wordt uitgevoerd.
                    </p>
                    <p>
                        Gebruik dit alleen als de site een bestandstype nodig heeft dat niet in de lijst hierboven staat
                        (bijvoorbeeld <code>.mp4</code>, <code>.mp3</code> of <code>.ico</code>). Pak de zip eerst zelf
                        uit en controleer dat er, ook in submappen, geen <code>.php</code>, <code>.phtml</code>,
                        <code>.phar</code>, <code>.cgi</code>, <code>.htaccess</code> of <code>.user.ini</code> in zit.
                        Bestanden die met een punt beginnen zijn op een Mac standaard verborgen. Zitten ze erin? Laat de
                        student ze verwijderen in plaats van dit vinkje te gebruiken.
                    </p>
                </div>
        </div>
    </x-inputs.group>
</x-stack-layout>
