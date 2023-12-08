@php
    use Carbon\Carbon;
    use App\Models\SurveyResponse;
    use App\Models\SurveyTemplates;
    use App\Models\User;

    $templateData = SurveyTemplates::findOrFail($surveyData->template_id);

    $authorId = $templateData->user_id;
    $getAuthorData = getUserData($authorId);
    $authorRoleName = (new User)->getRoleName($getAuthorData['role']);
    $description = trim($templateData->description) ? nl2br($templateData->description) : '';

    $currentUserId = auth()->id();

    $surveyId = $surveyData->id ?? '';

    $title = $surveyData->title ?? '';

    $templateName = $surveyData ? getSurveyTemplateNameById($surveyData->template_id) : '';

    $assignmentId = $assignmentData->id ?? null;
    $assignmentCreatedAt = $assignmentData->created_at ?? null;

    $auditorStatus = $assignmentData->auditor_status ?? null;
    $auditorId = $assignmentData->auditor_id ?? null;

    $companyId = $assignmentData->company_id ?? '';
    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $surveyorId = $assignmentData->surveyor_id ?? null;
    $surveyorName = getUserData($surveyorId)['name'];

    $today = Carbon::today();

    $responsesData = SurveyResponse::where('survey_id', $surveyId)
        ->where('assignment_id', $assignmentId)
        //->where('surveyor_id', $surveyorId)
        //->orWhere('auditor_id', $auditorId)
        //->where('company_id', $companyId)
        //->whereDate('created_at', '=', $today)
        ->get()
        ->toArray();
    //appPrintR($responsesData);

@endphp
@extends('layouts.master')
@section('title')
    Formulário de Auditoria
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
            Auditoria <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$surveyId}}</span> {{ limitChars($templateName ?? '', 20) }}</small>
        @endslot
    @endcomponent
    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-secondary-subtle position-relative">
            <div class="card-body p-5 text-center">
                @if ($companyName )
                    <h2 class="text-theme text-uppercase">{{ $companyName }}</h2>
                @endif
                <h2 class="text-secondary">Auditoria</h2>
                <p>A Vistoria foi realizada por <u>{{$surveyorName}}</u></p>
                <h3>{{ $title ? ucfirst($title) : 'NI' }}</h3>
                <div class="mb-0 text-muted">
                    Executar em:
                    {{-- $surveyData->updated_at ? \Carbon\Carbon::parse($surveyData->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' --}}
                    {{ $assignmentCreatedAt ? \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-' }}
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
        @if ($currentUserId != $auditorId)
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa
            </div>
        @else
            @if ($auditorStatus == 'completed')
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> Esta Auditoria já foi finalizada e não poderá ser editada
                </div>
            @endif
            @if ($auditorStatus == 'losted')
                <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> Esta Auditoria foi perdida pois o prazo expirou e por isso não poderá mais ser editada
                </div>
            @endif

            {!! !empty($description) ? '<div class="blockquote custom-blockquote blockquote-outline blockquote-dark rounded mt-2 mb-2"><p class="text-body mb-2">'.$description.'</p><footer class="blockquote-footer mt-0">'.$getAuthorData['name'].' <cite title="'.$authorRoleName.'">'.$authorRoleName.'</cite></footer></div>' : '' !!}

            <div id="assignment-container">
                @csrf
                <input type="hidden" name="survey_id" value="{{$surveyId}}">
                <input type="hidden" name="company_id" value="{{$companyId}}">
                @if ($surveyData && $responsesData)
                    @component('surveys.layouts.form-auditor-step-cards')
                        @slot('data', $stepsWithTopics)
                        @slot('responsesData', $responsesData)
                        @slot('auditorStatus', $auditorStatus)
                        @slot('assignmentId', $assignmentId)
                    @endcomponent
                @else
                    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                        <i class="ri-alert-line label-icon"></i> Não há dados para gerar os campos deste formulário de Auditoria
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div id="survey-progress-bar" class="fixed-bottom mb-0 ms-auto me-auto w-100">
        <div class="flex-grow-1">
            <div class="progress animated-progress custom-progress progress-label">
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"><div class="label"></div></div>
            </div>
        </div>
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
        var profileShowURL = "{{ route('profileShowURL') }}";
        var changeAssignmentAuditorStatusURL = "{{ route('changeAssignmentAuditorStatusURL') }}";
        var responsesAuditorStoreOrUpdateURL = "{{ route('responsesAuditorStoreOrUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-auditor.js') }}" type="module"></script>

    <script>
        var uploadPhotoURL = "{{ route('uploadPhotoURL') }}";
        var deletePhotoURL = "{{ route('deletePhotoURL') }}";
        var assetUrl = "{{ URL::asset('/') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-attachments.js') }}" type="module"></script>
@endsection
