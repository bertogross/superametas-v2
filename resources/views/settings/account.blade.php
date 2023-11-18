@extends('layouts.master')
@section('title')
    Meu {{ env('APP_NAME') }}
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('build/libs/filepond/filepond.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ URL::asset('build/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ url('settings') }}
        @endslot
        @slot('li_1')
            @lang('translation.settings')
        @endslot
        @slot('title')
            Meu {{ env('APP_NAME') }}
        @endslot
    @endcomponent

    @include('components.alerts')

    <!-- resources/views/settings/database.blade.php -->

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2">
                    <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                        <a class="nav-link text-uppercase {{ session('active_tab') == 'account' || session('active_tab') == '' ? 'active show' : '' }}" href="#v-pills-account" id="v-pills-account-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true">Dados da Conta</a>
                        <a class="nav-link text-uppercase {{ session('active_tab') == 'stripe' ? 'active show' : '' }}" id="v-pills-stripe-tab" data-bs-toggle="pill" href="#v-pills-stripe" role="tab" aria-controls="v-pills-stripe" aria-selected="false">Faturamento da Assinatura</a>
                    </div>
                </div> <!-- end col-->
                <div class="col-lg-10">
                    <div class="tab-content text-muted mt-3 mt-lg-2">
                        <div class="tab-pane fade {{ session('active_tab') == 'account' || session('active_tab') == '' ? 'active show' : '' }}" id="v-pills-account" role="tabpanel" aria-labelledby="v-pills-account-tab">
                            @include('settings.account-form')
                        </div><!--end tab-pane-->

                        <div class="tab-pane fade {{ session('active_tab') == 'stripe' ? 'active show' : '' }}" id="v-pills-stripe" role="tabpanel" aria-labelledby="v-pills-stripe-tab">
                            @include('settings.account-stripe')
                        </div><!--end tab-pane-->
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div><!-- end card-body -->
    </div><!--end card-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>

    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>

    <script>
        var uploadLogoURL = "{{ route('uploadLogoURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/settings-account.js') }}" type="module"></script>
@endsection
