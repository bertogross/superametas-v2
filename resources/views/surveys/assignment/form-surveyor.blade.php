@php
    $currentUserId = auth()->id();

    $surveyId = $surveyData->id ?? '';
    $templateName = $surveyData ? getTemplateNameById($surveyData->template_id) : '';

    $assignmentId = $assignmentData->id ?? null;
    $surveyorStatus = $assignmentData->surveyor_status ?? null;

    $companyId = $assignmentData->company_id ?? '';
    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $auditorId = $assignmentData->auditor_id ?? null;
    $auditorName = getUserData($auditorId)['name'];

    use Carbon\Carbon;
    use App\Models\SurveyResponse;

    $today = Carbon::today();
    $responsesData = SurveyResponse::where('survey_id', $surveyId)
        ->where('surveyor_id', $currentUserId)
        ->where('company_id', $companyId)
        ->whereDate('created_at', '=', $today)
        ->get()
        ->toArray();
@endphp
@extends('layouts.master')
@section('title')
    Formulário de Vistoria
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/glightbox/css/glightbox.min.css') }}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot
        @slot('li_1')
            Vistorias
        @endslot
        @slot('title')
            Tarefa <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$surveyId}}</span> {{ limitChars($templateName ?? '', 20) }}</small>
        @endslot
    @endcomponent
    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-info-subtle position-relative">
            <div class="card-body p-5 text-center">
                @if ($companyName )
                    <h2 class="text-theme text-uppercase">{{ $companyName }}</h2>
                @endif
                <h2>Vistoria</h2>
                <p>Auditoria será realizada por <u>{{$auditorName}}</u></p>
                <h3>{{ $templateName }}</h3>
                <div class="mb-0 text-muted">
                    Executar em:
                    {{-- $surveyData->updated_at ? \Carbon\Carbon::parse($surveyData->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' --}}
                    {{ $surveyData->created_at ? \Carbon\Carbon::parse($surveyData->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-' }}
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

        @if ($currentUserId != $assignmentData->surveyor_id)
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa
            </div>
        @else
            @if ($surveyorStatus == 'auditing')
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> Esta Vistoria já foi enviada para Auditoria e não poderá ser editada
                </div>
            @endif
            @if ($surveyorStatus == 'losted')
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> O prazo expirou e esta Vistoria foi perdida. Por isso não poderá mais ser editada
                </div>
            @endif
            <div id="assignment-container">
                @csrf
                <input type="hidden" name="survey_id" value="{{$surveyId}}">
                <input type="hidden" name="company_id" value="{{$companyId}}">

                @if ($surveyData)
                    @component('surveys.layouts.form-surveyor-step-cards')
                        @slot('data', $stepsWithTopics)
                        @slot('responsesData', $responsesData)
                        @slot('purpose', 'validForm')
                        @slot('surveyorStatus', $surveyorStatus)
                        @slot('assignmentId', $assignmentId)
                    @endcomponent
                @else
                    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                        <i class="ri-alert-line label-icon"></i> Não há dados para gerar os campos deste formulário de Vistoria
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/glightbox/js/glightbox.min.js') }}"></script>

    <script>
        var surveysIndexURL = "{{ route('surveysIndexURL') }}";
        var surveysCreateURL = "{{ route('surveysCreateURL') }}";
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysChangeStatusURL = "{{ route('surveysChangeStatusURL') }}";
        var surveysShowURL = "{{ route('surveysShowURL') }}";
        var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>

    <script>
        var formSurveyorAssignmentURL = "{{ route('formSurveyorAssignmentURL') }}";
        var changeAssignmentSurveyorStatusURL = "{{ route('changeAssignmentSurveyorStatusURL') }}";
        var responsesSurveyorStoreOrUpdateURL = "{{ route('responsesSurveyorStoreOrUpdateURL') }}";
        var profileShowURL = "{{ route('profileShowURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-surveyor.js') }}" type="module"></script>

    <script>
        var uploadPhotoURL = "{{ route('uploadPhotoURL') }}";
        var deletePhotoURL = "{{ route('deletePhotoURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-attachments.js') }}" type="module"></script>
@endsection
