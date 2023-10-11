@extends('layouts.master')
@section('title')
    Meu {{ env('APP_NAME') }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ url('settings-account') }}
        @endslot
        @slot('li_1')
            @lang('translation.settings')
        @endslot
        @slot('title')
            Meu {{ env('APP_NAME') }}
        @endslot
    @endcomponent

    Content HERE

@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
