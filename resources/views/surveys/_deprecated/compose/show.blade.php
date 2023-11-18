@extends('layouts.master')
@section('title')
    Visualização do Formulário de Vistoria
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysComposeIndexURL') }}
        @endslot
        @slot('li_1')
            Formulários
        @endslot
        @slot('title')
            Visualização<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$data->id}}</span> {{ limitChars($data->title ?? '', 20) }}</small>
        @endslot
    @endcomponent

    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-warning-subtle position-relative">
            @if (!$edition)
                <span class="float-start m-3 position-absolute">{!! statusBadge($data->status) !!}</span>
            @endif

            @if(!$preview && !$edition)
                <a href="{{ route('surveysComposeEditURL', ['id' => $data->id]) }}" class="btn btn-sm btn-light btn-icon waves-effect ms-2 float-end m-3" title="Editar registro: {{ limitChars($data->title ?? '', 20) }}"><i class="ri-edit-line"></i></a>
            @endif

            <div class="card-body p-5 text-center">
                <h3>{{ $data ? $data->title : '' }}</h3>
                <div class="mb-0 text-muted">
                    Atualizado em:
                    {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                </div>
            </div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="1440" height="60" preserveAspectRatio="none" viewBox="0 0 1440 60">
                    <g mask="url(&quot;#SvgjsMask1001&quot;)" fill="none">
                        <path d="M 0,4 C 144,13 432,48 720,49 C 1008,50 1296,17 1440,9L1440 60L0 60z" style="fill: var(--vz-secondary-bg);"></path>
                    </g>
                    <defs>
                        <mask id="SvgjsMask1001">
                            <rect width="1440" height="60" fill="#ffffff"></rect>
                        </mask>
                    </defs>
                </svg>
            </div>
        </div>

        <div>
            {{--
                @include('surveys.components.steps-card', ['data' => $data, 'topicsData' => $topicsData, 'edition' => $edition, 'users' => $users])
            --}}
            @component('surveys.components.steps-card')
                @slot('data', $data)
                @slot('topicsData', $topicsData)
                @slot('edition', $edition)
                @slot('users', $users)
            @endcomponent
        </div>
        @php
        //appPrintR($data->jsondata);
        //appPrintR($topicsData);
        @endphp
    </div>

@endsection
@section('script')

<script>
    var surveysComposeShowURL = "{{ route('surveysComposeShowURL') }}";
    var surveysComposeStoreOrUpdateURL = "{{ route('surveysComposeStoreOrUpdateURL') }}";
    var surveysComposeToggleStatusURL = "{{ route('surveysComposeToggleStatusURL') }}";

    var surveysTermsSearchURL = "{{ route('surveysTermsSearchURL') }}";
    var surveysTermsStoreOrUpdateURL = "{{ route('surveysTermsStoreOrUpdateURL') }}";
    var choicesSelectorClass = ".surveys-term-choice";
</script>
<script src="{{ URL::asset('build/js/surveys-compose.js') }}" type="module"></script>
@endsection