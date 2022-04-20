@extends('layouts.frame')

@section('path_url', $site->getSiteUrl())
@section('intro_text', 'Deze versie van onze informatiesite is gemaakt door')
@section('student_name', $site->name)
@section('title_text', $site->getYearText())
