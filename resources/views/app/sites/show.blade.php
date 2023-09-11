@extends('layouts.frame')

@section('path_url', $site->getSiteUrl(isset($englishLanguage)))
@section('student_name', $site->name)
@section('title_text', $site->getYearText(isset($englishLanguage)))

@section('intro_text')
    @isset($englishLanguage)
        This version of our informational website was made by
    @else
        Deze versie van onze informatiesite is gemaakt door
    @endisset
@endsection
