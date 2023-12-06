@php
    use App\Models\SurveyResponse;
    //appPrintR(json_decode($data, true));

    $assignmentId = $assignmentData->id;
    $assignmentDate = $assignmentData->created_at;
    $surveyId = $assignmentData->survey_id;
    $companyId = $assignmentData->company_id;
    $surveyorId = $assignmentData->surveyor_id;
    $auditorId = $assignmentData->auditor_id;
    $surveyorStatus = $assignmentData->surveyor_status;
    $auditorStatus = $assignmentData->auditor_status;

    $templateName = $surveyData ? getTemplateNameById($surveyData->template_id) : '';
    $templateDescription = $surveyData ? getTemplateDescriptionById($surveyData->template_id) : '';

    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $surveyorName = getUserData($surveyorId)['name'];
    $auditorName = getUserData($auditorId)['name'];

    $responsesData = SurveyResponse::where('assignment_id', $assignmentId)
        ->get()
        ->toArray();

    $complianceSurveyorYesCount = $complianceSurveyorNoCount = $complianceAuditorYesCount = $complianceAuditorNoCount = 0;

    foreach ($responsesData as $item) {
        if (isset($item['compliance_survey'])) {
            if ($item['compliance_survey'] === 'yes') {
                $complianceSurveyorYesCount++;
            } elseif ($item['compliance_survey'] === 'no') {
                $complianceSurveyorNoCount++;
            }
        }
        if (isset($item['compliance_audit'])) {
            if ($item['compliance_audit'] === 'yes') {
                $complianceAuditorYesCount++;
            } elseif ($item['compliance_audit'] === 'no') {
                $complianceAuditorNoCount++;
            }
        }
    }
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

        <div class="card mt-n4 mx-n3">
            <div class="bg-warning-subtle">
                <div class="card-body pb-4">
                    <h4 class="fw-semibold">
                        <span class="text-theme">{{ $companyName }}</span> <i class="ri-arrow-right-s-fill align-bottom"></i> {{ limitChars($templateName ?? '', 100) }}
                    </h4>
                    <div class="hstack gap-3 flex-wrap">

                        <div class="text-muted">
                            {{ $assignmentDate ? \Carbon\Carbon::parse($assignmentDate)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-' }}
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

        @if ($templateDescription)
            <h6 class="text-uppercase mb-3">Descrição</h6>
            <p class="text-muted">
                {{ nl2br($templateDescription) }}
            </p>
        @endif

        <div class="row mb-2">
            <div class="col-sm-6 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div id="barTermsChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div id="mixedTermsChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="row">
                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Vistoria</h6>
                                <span class="text-success">Conforme</span>: {{$complianceSurveyorYesCount}}
                                <br>
                                <span class="text-danger">Não Conforme</span>: {{$complianceSurveyorNoCount}}
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                @if( !$complianceAuditorYesCount && ! $complianceAuditorNoCount )
                                    <span class="fs-5 ri-alert-fill text-warning float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left"  title="Auditoria não foi realizada"></span>
                                @endif
                                <h6 class="text-muted text-uppercase">Auditoria</h6>
                                <span class="text-success">De Acordo</span>: {{$complianceAuditorYesCount}}
                                <br>
                                <span class="text-danger">Indeferido</span>: {{$complianceAuditorNoCount}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div id="polarTermsAreaChart"></div>
                    </div>
                </div>
            </div>
        </div>

        @if ( $surveyorStatus == 'completed' && $auditorStatus == 'losted')
            <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> A Vistoria foi completada. Entretanto, o prazo da Auditoria expirou.
            </div>
        @elseif ( $surveyorStatus == 'losted' && $auditorStatus == 'losted')
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> A Vistoria e a Auditoria não foram realizadas no prazo.
            </div>
        {{--
        @elseif ($surveyorStatus == 'losted')
            <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> O prazo expirou e esta Vistoria foi perdida
            </div>
        @elseif ($auditorStatus == 'losted')
            <div class="alert alert-secondary alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> O prazo expirou e esta Auditoria foi perdida
            </div>
        --}}
        @endif

        @foreach ($stepsWithTopics as $stepIndex => $step)
            @php
                $topicBadgeIndex = 0;

                $stepId = isset($step['step_id']) ? intval($step['step_id']) : '';
                $termId = isset($step['term_id']) ? intval($step['term_id']) : '';
                // use the term_id to get term name. If term_id is less than 9000, find the getDepartmentNameById(term_id)
                $stepName = $termId < 9000 ? getDepartmentNameById($termId) : getTermNameById($termId);
                //$type =
                $originalPosition = isset($step['step_order']) ? intval($step['step_order']) : 0;
                $newPosition = $originalPosition;
                $topics = $step['topics'];
            @endphp

            @if( $topics )
                <div class="card joblist-card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="job-title text-theme text-uppercase">{{ $stepName }}</h5>
                    </div>
                    @if ( $topics && is_array($topics))
                        @php
                            $bg = 'bg-opacity-75';
                        @endphp
                        @foreach ($topics as $topicIndex => $topic)
                            @php
                                $topicBadgeIndex++;

                                $topicId = isset($topic['topic_id']) ? intval($topic['topic_id']) : '';
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
                                $bgSurveyor = $complianceSurvey ? $bgSurveyor : 'bg-opacity-10 bg-warning';

                                $bgAuditor = $complianceAudit == 'yes' ? 'bg-opacity-10 bg-success' : 'bg-opacity-10 bg-danger';
                                $bgAuditor = $complianceAudit ? $bgAuditor : 'bg-opacity-10 bg-warning';

                                $topicBadgeColor = $complianceSurvey == 'no' || $complianceAudit == 'no' ? 'danger' : 'theme';

                                if($complianceSurvey && $complianceAudit){
                                    $topicLabelColor = $complianceSurvey == 'no' || $complianceAudit == 'no' ? '<span class="spinner-grow spinner-grow-sm text-danger float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não Conforme"></span>' : '<span class="fs-5 ri-check-double-fill text-theme float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Em conformidade"></span>';
                                }else{
                                    $topicLabelColor = '<span class="fs-5 ri-alert-fill text-warning float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não Comparável"></span>';
                                }

                            @endphp
                            <div class="card-body pb-0">
                                {!! $topicLabelColor !!}
                                <h5 class="mb-0">
                                    <span class="badge bg-light-subtle badge-border text-{{$topicBadgeColor}} align-bottom me-1">{{ $topicBadgeIndex }}</span>
                                    {{ $question ? ucfirst($question) : 'NI' }}
                                </h5>
                                <div class="row mt-3">
                                    <div class="col-md-6 pb-3">
                                        <div class="card border-0 h-100">
                                            <div class="card-header border-1 border-bottom-dashed {{ $bgSurveyor }}">
                                                <h6 class="card-title mb-0">
                                                    Vistoria:
                                                    {!! $complianceSurvey && $complianceSurvey == 'yes' ? '<span class="text-theme">Conforme</span>' : '' !!}
                                                    {!! $complianceSurvey && $complianceSurvey == 'no' ? '<span class="text-danger">Não Conforme</span>' : '' !!}
                                                    {!! !$complianceSurvey ? '<span class="text-warning">Não Informado</span>' : '' !!}
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

                                                                        $dateAttachment = App\Models\Attachments::getAttachmentDateById($attachmentId);
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
                                                    Auditoria:
                                                    {!! $complianceAudit && $complianceAudit == 'yes' ? '<span class="text-theme">Aprovada</span>' : '' !!}
                                                    {!! $complianceAudit && $complianceAudit == 'no' ? '<span class="text-danger">Indeferida</span>' : '' !!}
                                                    {!! !$complianceAudit ? '<span class="text-warning">Não Informado</span>' : '' !!}
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

                                                                    $dateAttachment = App\Models\Attachments::getAttachmentDateById($attachmentId);
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

    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        const rawTermsData = @json($analyticTermsData);
        const terms = @json($terms);
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // #barTermsChart
        var seriesData = [];
        var categories = [];

        for (var date in rawTermsData) {
            for (var termId in rawTermsData[date]) {
                var termData = rawTermsData[date][termId];
                var totalComplianceYes = termData.filter(item => item.compliance_survey === 'yes').length;
                var totalComplianceNo = termData.filter(item => item.compliance_survey === 'no').length;

                seriesData.push({
                    x: terms[termId]['name'],
                    y: totalComplianceYes - totalComplianceNo
                });

                categories.push(terms[termId]['name']);
            }
        }

        var optionsTermsChart = {
            series: [{
                name: 'Score',
                data: seriesData
            }],
            title: {
                //text: 'Compliance Bars'
            },
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: false,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    colors: {
                        ranges: [{
                            from: -1000,
                            to: 0,
                            color: '#DF5253'
                        }, {
                            from: 1,
                            to: 1000,
                            color: '#1FDC01'
                        }],
                    },
                    dataLabels: {
                        position: 'top',
                    },
                },
            },
            xaxis: {
                categories: categories
            },
            fill: {
                opacity: 1
            },
        };

        var barTermsChart = new ApexCharts(document.querySelector("#barTermsChart"), optionsTermsChart);
        barTermsChart.render();

        // #mixedTermsChart
        var columnSeriesData = [];
        var lineSeriesData = [];
        var categories = [];

        for (var date in rawTermsData) {
            for (var termId in rawTermsData[date]) {
                var termData = rawTermsData[date][termId];
                var totalComplianceYes = termData.filter(item => item.compliance_survey === 'yes').length;
                var totalComplianceNo = termData.filter(item => item.compliance_survey === 'no').length;

                columnSeriesData.push(totalComplianceYes);
                lineSeriesData.push(totalComplianceNo);
                categories.push(terms[termId]['name'] + ' (' + date + ')');
            }
        }

        var optionsMixedTermsChart = {
            series: [{
                name: 'Conforme',
                type: 'column',
                data: columnSeriesData
            }, {
                name: 'Não Conforme',
                type: 'line',
                data: lineSeriesData
            }],
            chart: {
                height: 400,
                type: 'line',
                toolbar: {
                    show: false,
                }
            },
            stroke: {
                width: [0, 4]
            },
            title: {
                //text: 'Compliance Trends'
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: categories,
            xaxis: {
                type: 'category'
            },
            yaxis: [{
                title: {
                    text: 'Conforme'
                }
            }, {
                opposite: true,
                title: {
                    text: 'Não Conforme'
                }
            }],
            colors: ['#1FDC01', '#DF5253']  // Assign custom colors to Compliance Yes and No
        };

        var mixedTermsChart = new ApexCharts(document.querySelector("#mixedTermsChart"), optionsMixedTermsChart);
        mixedTermsChart.render();

        // #polarTermsAreaChart
        var seriesData = [];
        var labels = [];

        var termMetrics = {};

        // Aggregate data for each term
        for (var date in rawTermsData) {
            for (var termId in rawTermsData[date]) {
                var termData = rawTermsData[date][termId];
                var totalCompliance = termData.filter(item => item.compliance_survey === 'yes').length;

                if (!termMetrics[termId]) {
                    termMetrics[termId] = 0;
                }
                termMetrics[termId] += totalCompliance;
            }
        }

        // Prepare data for the chart
        for (var termId in termMetrics) {
            seriesData.push(termMetrics[termId]);
            labels.push(terms[termId]['name']);
        }

        var optionsTermsAreaChart = {
            series: seriesData,
            chart: {
                height: 327,
                type: 'polarArea',
                toolbar: {
                    show: false,
                }
            },
            labels: labels,
            stroke: {
                colors: ['#fff']
            },
            fill: {
                opacity: 0.8
            },
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    }
                }
            }]
        };

        var polarTermsAreaChart = new ApexCharts(document.querySelector("#polarTermsAreaChart"), optionsTermsAreaChart);
        polarTermsAreaChart.render();
    });
    </script>


@endsection
