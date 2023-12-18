<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">
                <i class="ri-fingerprint-2-line fs-16 align-bottom text-theme me-2"></i>Listagem
            </h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">

                </div>
            </div>
        </div>
    </div>

    <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0" style="flex: inherit !important;">
        <form action="{{ route('surveysIndexURL') }}" method="get" autocomplete="off">
            <div class="row g-3">

                <div class="col-sm-12 col-md col-lg">
                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="- Período -" data-min-date="{{ $firstDate ?? '' }}" data-max-date="{{ $lastDate ?? '' }}" value="{{ request('created_at') ?? '' }}">
                </div>

                <div class="col-sm-12 col-md col-lg">
                    <select class="form-control form-select" name="status">
                        <option value="">- Status -</option>
                        @foreach ( ['pending', 'in_progress', 'completed', 'losted'] as $key)
                            <option {{ $getSurveyAssignmentStatusTranslations[$key] == request('status') ? 'selected' : '' }} value="{{ $key }}" title="{{ $getSurveyAssignmentStatusTranslations[$key]['description'] }}">
                                {{ $getSurveyAssignmentStatusTranslations[$key]['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                    <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader">
                        <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="card-body">
        @if (!$data || $data->isEmpty())
            @component('components.nothing')
                {{--
                @slot('url', route('surveysCreateURL'))
                --}}
            @endcomponent
        @else
            <div class="table-responsive table-card mb-4">
                <table class="table table-sm align-middle table-nowrap mb-0 table-striped" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">
                                Tarefa
                            </th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Unidade Auditada">
                                Unidade
                            </th>
                            <th class="text-left" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usário ao qual foi designada a tarefa">
                                Vistoriado por
                            </th>
                            {{--
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data da Vistoria">
                                Vistoriado em
                            </th>
                            --}}
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usuário Auditor(a)">Auditado por</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data da Auditoria">
                                Auditado em
                            </th>
                            <th class="text-center">
                                Status
                            </th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $assignment)
                            @php
                                $assignmentId = $assignment->id;
                                $title = $assignment->title;
                                $surveyId = $assignment->survey_id;

                                $surveyorId = $assignment->surveyor_id;
                                $auditorId = $assignment->auditor_id;

                                $surveyorStatus = $assignment->surveyor_status;
                                $auditorStatus = $assignment->auditor_status;

                                $companyId = $assignment->company_id;
                                $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                $assignmentStatus = $assignment->auditor_status;

                                $getSurveyTemplateNameById = getSurveyTemplateNameById($assignment->template_id);

                                $countSurveyAssignmentBySurveyId = \App\Models\SurveyAssignments::countSurveyAssignmentBySurveyId($surveyId);

                                $delegation = \App\Models\SurveyAssignments::getAssignmentDelegatedsBySurveyId($surveyId);
                            @endphp
                            <tr class="main-row" data-id="{{ $surveyId }}">
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ucfirst($title)}}">
                                        {{ limitChars(ucfirst($title), 30) }}
                                    </span>

                                    <div class="text-muted small" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ limitChars(ucfirst($getSurveyTemplateNameById), 200) }}">
                                        <strong>Modelo:</strong> <span class="text-body"></span>{{ limitChars(ucfirst($getSurveyTemplateNameById), 100) }}
                                    </div>
                                </td>
                                <td>
                                    {{ $companyName }}
                                </td>
                                <td>
                                    <div class="avatar-group flex-nowrap d-inline-block align-middle">
                                        @php
                                            $getUserData = getUserData($surveyorId);
                                        @endphp
                                        <a href="{{ route('profileShowURL', $surveyorId) }}" class="avatar-group-item" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Vistoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                            <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                        </a> {{ $getUserData['name'] }}
                                    </div>
                                </td>
                                {{--
                                <td>
                                    {{ $assignment->created_at ? date('d/m/Y', strtotime($assignment->created_at)) : '-' }}
                                </td>
                                --}}
                                <td>
                                    <div class="avatar-group">
                                        @php
                                            $getUserData = getUserData($auditorId);
                                        @endphp
                                        <div class="avatar-group flex-nowrap d-inline-block align-middle">
                                            <a href="{{ route('profileShowURL', $auditorId) }}" class="avatar-group-item" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Vistoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                                <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                            </a> {{ $getUserData['name'] }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $assignment->updated_at ? date('d/m/Y H:i', strtotime($assignment->updated_at)) : '-' }}
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $getSurveyAssignmentStatusTranslations[$assignmentStatus]['color'] }}-subtle text-{{ $getSurveyAssignmentStatusTranslations[$assignmentStatus]['color'] }} text-uppercase"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                        title="{{ $getSurveyAssignmentStatusTranslations[$assignmentStatus]['description'] }}">
                                        {{ $getSurveyAssignmentStatusTranslations[$assignmentStatus]['label'] }}
                                        @if ($assignmentStatus == 'started')
                                            <span class="spinner-border align-top ms-1"></span>
                                        @endif
                                    </span>
                                </td>
                                <td scope="row" class="text-end">
                                    @if (in_array($auditorStatus, ['new', 'pending', 'in_progress']))
                                        <a
                                        @if ($surveyorStatus == 'completed')
                                            href="{{route('formAuditorAssignmentURL', $assignmentId)}}"
                                        @else
                                            onclick="alert('Necessário aguardar finalização da Vistoria')"
                                        @endif
                                         class="btn btn-sm btn-label right waves-effect btn-soft-secondary" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Abrir formulário">
                                            <i class="ri-fingerprint-2-line label-icon align-middle fs-16"></i> Auditar
                                        </a>
                                    @elseif (in_array($auditorStatus, ['completed', 'losted']))
                                        <a href="{{ route('assignmentShowURL', $assignmentId) }}"
                                        data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                        title="Visualizar"
                                        class="btn btn-sm btn-label right waves-effect btn-soft-dark">
                                            <i class="ri-eye-line label-icon align-middle"></i> Visualizar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {!! $data->links('layouts.custom-pagination') !!}
            </div>
        @endif
    </div>
</div>
