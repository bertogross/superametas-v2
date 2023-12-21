@extends('layouts.master')
@section('title')
    Auditorias
@endsection
@section('css')
@endsection
@section('content')
    @php
        use App\Models\User;
    @endphp
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysAuditIndexURL') }}
        @endslot

        @slot('title')
            Auditorias
            @if (request('userId'))
                de
                <span class="text-theme ms-1">{{getUserData(request('userId'))['name']}}</span>
            @endif

        @endslot
    @endcomponent
    <div class="row mb-3">
        <div class="col">
            @if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_CONTROLLERSHIP) || in_array('audit', $currentUserCapabilities))
                @include('surveys.audits.listing')
            @else
                <div class="alert alert-danger">Acesso não autorizado</div>
            @endif
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        var surveysIndexURL = "{{ route('surveysIndexURL') }}";
        var surveysCreateURL = "{{ route('surveysCreateURL') }}";
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysChangeStatusURL = "{{ route('surveysChangeStatusURL') }}";
        var surveysShowURL = "{{ route('surveysShowURL') }}";
        var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";
        var getRecentActivitiesURL = "{{ route('getRecentActivitiesURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>
@endsection