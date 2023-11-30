@php
    use App\Models\SurveyResponse;
    //appPrintR(json_decode($data, true));

    $assignmentId = $assignmentData->id;
    $surveyId = $assignmentData->survey_id;
    $companyId = $assignmentData->company_id;
    $surveyorId = $assignmentData->surveyor_id;
    $auditorId = $assignmentData->auditor_id;
    $surveyorStatus = $assignmentData->surveyor_status;
    $auditorStatus = $assignmentData->auditor_status;

    $templateName = $surveyData ? getTemplateNameById($surveyData->template_id) : '';

    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $surveyorName = getUserData($surveyorId)['name'];
    $auditorName = getUserData($auditorId)['name'];

    $responsesData = SurveyResponse::where('assignment_id', $assignmentId)
        ->get()
        ->toArray();

    //appPrintR($responsesData);
@endphp
@extends('layouts.master')
@section('title')
    Resultado da Vistoria
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/glightbox/css/glightbox.min.css') }}">
@endsection
@section('content')

    <div id="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-n4 mx-n3">
                    <div class="bg-warning-subtle">
                        <div class="card-body pb-4">
                            <h4 class="fw-semibold">
                                {{ limitChars($templateName ?? '', 100) }}
                            </h4>
                            <div class="hstack gap-3 flex-wrap">
                                <div class="text-muted">
                                    {{ $companyName }}
                                </div>

                                <div class="vr"></div>

                                <div class="text-muted">
                                    {{ $surveyData->created_at ? \Carbon\Carbon::parse($surveyData->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-' }}
                                </div>

                                <div class="vr"></div>

                                <div class="text-muted">
                                    Vistoria: {{$surveyorName}}
                                </div>

                                <div class="vr"></div>

                                <div class="text-muted">
                                    Auditoria: {{$auditorName}}
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div>
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->


        <h6 class="text-uppercase mb-3">Descrição da Tarefa</h6>
        <p class="text-muted">It would also help to know what the errors are - it could be something simple like a message saying delivery is not available which could be a problem with your shipping templates.

        @if ( $surveyorStatus == 'losted' && $auditorStatus == 'losted')
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> O prazo expirou. A Vistoria e a Auditoria foram perdidas.
            </div>
        @elseif ($surveyorStatus == 'losted')
            <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> O prazo expirou e esta Vistoria foi perdida
            </div>
        @elseif ($auditorStatus == 'losted')
            <div class="alert alert-secondary alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> O prazo expirou e esta Auditoria foi perdida
            </div>
        @endif

        @foreach ($stepsWithTopics as $stepIndex => $step)
            @php
                $topicBadgeIndex = 0;

                $stepId = intval($step['step_id']);
                $termId = intval($step['term_id']);
                // use the term_id to get term name. If term_id is less than 9000, find the getDepartmentNameById(term_id)
                $stepName = $termId < 9000 ? getDepartmentNameById($termId) : getTermNameById($termId);
                //$type =
                $originalPosition = intval($step['step_order']);
                $newPosition = $originalPosition;
                $topics = $step['topics'];
            @endphp

            @if( $topics )
                <div class="card joblist-card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="job-title text-theme">{{ $stepName }}</h5>
                    </div>
                    @if ( $topics && is_array($topics))
                        @php
                            $bg = 'bg-opacity-75';
                        @endphp
                        @foreach ($topics as $topicIndex => $topic)
                            @php
                                $topicBadgeIndex++;

                                $topicId = intval($topic['topic_id']);
                                $question = $topic['question'] ?? '';

                                $originalPosition = 0;
                                $newPosition = 0;

                                $stepIdToFind = $stepId;
                                $topicIdToFind = $topicId;

                                $filteredItems = array_filter($responsesData, function ($item) use ($stepIdToFind, $topicIdToFind) {
                                    return $item['step_id'] == $stepIdToFind && $item['topic_id'] == $topicIdToFind;
                                });

                                // Reset array keys
                                $filteredItems = array_values($filteredItems);

                                $responseId = $filteredItems[0]['id'] ?? '';

                                $surveyAttachmentIds =  $filteredItems[0]['attachments_survey'] ?? '';
                                $surveyAttachmentIds = $surveyAttachmentIds ? json_decode($surveyAttachmentIds, true) : '';

                                $auditAttachmentIds =  $filteredItems[0]['attachments_audit'] ?? '';
                                $auditAttachmentIds = $auditAttachmentIds ? json_decode($auditAttachmentIds, true) : '';

                                $commentSurvey = $filteredItems[0]['comment_survey'] ?? '';
                                $complianceSurvey = $filteredItems[0]['compliance_survey'] ?? '';

                                $commentAudit = $filteredItems[0]['comment_audit'] ?? '';
                                $complianceAudit = $filteredItems[0]['compliance_audit'] ?? '';

                                $bgSurveyor = $complianceSurvey == 'yes' ? 'bg-opacity-10 bg-success' : 'bg-opacity-10 bg-danger';

                                $bgAuditor = $complianceAudit == 'yes' ? 'bg-opacity-10 bg-success' : 'bg-opacity-10 bg-danger';

                                $topicBadgeColor = $complianceSurvey == 'no' || $complianceAudit == 'no' ? 'danger' : 'theme';

                                $topicLabelColor = $complianceSurvey == 'no' || $complianceAudit == 'no' ? '<span class="spinner-grow spinner-grow-sm text-danger float-end" title="Não Conforme"></span>' : '<span class="fs-5 ri-check-double-fill text-theme float-end" title="Em conformidade"></span>';

                            @endphp
                            <div class="card-body pb-0">
                                {!! $topicLabelColor !!}
                                <h5 class="mb-0">
                                    <span class="badge bg-light-subtle badge-border text-{{$topicBadgeColor}} align-bottom me-1">{{ $topicBadgeIndex }}</span>
                                    {{ $question }}
                                </h5>
                                <div class="row mt-3">
                                    <div class="col-md-6 pb-3">
                                        <div class="card border-0 h-100">
                                            <div class="card-header border-1 border-bottom-dashed {{ $bgSurveyor }}">
                                                <h6 class="card-title mb-0">
                                                    Vistoria: {!! $complianceSurvey && $complianceSurvey == 'yes' ? '<span class="text-theme">Conforme</span>' : '<span class="text-danger">Não Conforme</span>' !!}
                                                </h6>
                                            </div>
                                            <div class="card-body {{ $bgSurveyor }}">
                                                {!! $commentSurvey ? nl2br($commentSurvey) : '' !!}
                                            </div>
                                            @if ( !empty($surveyAttachmentIds) && is_array($surveyAttachmentIds) )
                                                <div class="card-footer border-0 {{ $bgSurveyor }}">
                                                    <div class="row">
                                                            @foreach ($surveyAttachmentIds as $attachmentId)
                                                                @php
                                                                    $attachmentUrl = $dateAttachment = '';
                                                                    if (!empty($attachmentId)) {
                                                                        $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                                        $dateAttachment = App\Models\Attachments::getAttachmentDateAttachmentById($attachmentId);
                                                                    }
                                                                @endphp
                                                                @if ($attachmentUrl)
                                                                    <div class="element-item col-auto">
                                                                        <div class="gallery-box card p-0 m-1">
                                                                            <div class="gallery-container">
                                                                                <a href="{{ $attachmentUrl }}" class="image-popup" title="Imagem capturada em {{$dateAttachment}}hs" data-gallery="gallery-{{$responseId}}">
                                                                                    <img class="rounded gallery-img" alt="image" height="70" src="{{ $attachmentUrl }}">

                                                                                    <div class="gallery-overlay">
                                                                                        <h5 class="overlay-caption fs-10">{{$dateAttachment}}</h5>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <div class="card border-0 h-100">
                                            <div class="card-header border-1 border-bottom-dashed {{ $bgAuditor }}">
                                                <h6 class="card-title mb-0">
                                                    Auditoria: {!! $complianceAudit && $complianceAudit == 'yes' ? '<span class="text-theme">Aprovada</span>' : '<span class="text-danger">Indeferida</span>' !!}
                                                </h6>
                                            </div>
                                            <div class="card-body {{ $bgAuditor }}">
                                                {!! $commentAudit ? nl2br($commentAudit) : '' !!}
                                            </div>
                                            @if ( !empty($auditAttachmentIds) && is_array($auditAttachmentIds) )
                                                <div class="card-footer border-0 {{ $bgAuditor }}">
                                                    <div class="row">
                                                        @foreach ($auditAttachmentIds as $attachmentId)
                                                            @php
                                                                $attachmentUrl = $dateAttachment = '';
                                                                if (!empty($attachmentId)) {
                                                                    $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                                    $dateAttachment = App\Models\Attachments::getAttachmentDateAttachmentById($attachmentId);
                                                                }
                                                            @endphp
                                                            @if ($attachmentUrl)
                                                                <div class="element-item col-auto">
                                                                    <div class="gallery-box card p-0 m-1">
                                                                        <div class="gallery-container">
                                                                            <a href="{{ $attachmentUrl }}" class="image-popup" title="Imagem capturada em {{$dateAttachment}}hs" data-gallery="gallery-{{$responseId}}">
                                                                                <img class="rounded gallery-img" alt="image" height="70" src="{{ $attachmentUrl }}">

                                                                                <div class="gallery-overlay">
                                                                                    <h5 class="overlay-caption fs-10">{{$dateAttachment}}</h5>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        @endforeach

    </div>

    @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/glightbox/js/glightbox.min.js') }}"></script>

@endsection
