@php
    use App\Models\Survey;
    use App\Models\SurveyAssignments;

    $currentUserId = auth()->id();
@endphp
@if ( !empty($data) && is_array($data) )
    @foreach ($data as $key => $assignment)
        @php
            $assignmentId = intval($assignment['id']);
            $surveyId = intval($assignment['survey_id']);

            $createdAt = $assignment['created_at'];

            $survey = Survey::findOrFail($surveyId);

            $title = $survey->title;

            $recurring = $survey->recurring;

            $deadline = \App\Models\SurveyAssignments::getSurveyAssignmentDeadline($recurring, $createdAt);

            $templateName = getSurveyTemplateNameById($survey->template_id);

            $companyId = intval($assignment['company_id']);
            $companyName = getCompanyNameById($companyId);

            $surveyorId = $assignment['surveyor_id'] ?? null;
            $surveyorStatus = $assignment['surveyor_status'] ?? null;
            $surveyorName = getUserData($surveyorId)['name'] ?? '';
            $surveyorAvatar = getUserData($surveyorId)['avatar'] ?? '';

            $auditorId = $assignment['auditor_id'] ?? null;
            $auditorStatus = $assignment['auditor_status'] ?? null;
            $auditorName = $auditorId ? getUserData($auditorId)['name'] : '';
            $auditorAvatar = $auditorId ? getUserData($auditorId)['avatar'] : '';

            $labelTitle = SurveyAssignments::getSurveyAssignmentLabelTitle($surveyorStatus, $auditorStatus, $statusKey);

            if($designated == 'auditor'){
                $designatedUserId = $auditorId;
            }elseif($designated == 'surveyor'){
                $designatedUserId = $surveyorId;
            }

            $percentage = SurveyAssignments::calculateSurveyPercentage($surveyId, $companyId, $assignmentId, $surveyorId, $auditorId, $designated);
            $progressBarClass = getProgressBarClass($percentage);
        @endphp

        <div class="card tasks-box bg-body" data-assignment-id="{{$assignmentId}}">
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col text-theme fw-medium fs-15">
                        {{ $companyName }}
                    </div>
                    <div class="col-auto">
                        @if( $surveyorStatus == 'completed' && $auditorStatus == 'completed')
                            <span class="badge bg-success-subtle text-success badge-border" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="{{ $labelTitle }}">
                                <span class="text-info">Vistoria</span> <span class="text-body">+</span> <span class="text-secondary">Auditoria</span>
                            </span>
                        @elseif($designated == 'surveyor')
                            <span class="badge bg-dark-subtle text-body badge-border" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="{{ $labelTitle }}">
                                Vistoria
                                @if ( in_array($statusKey, ['completed']) && $surveyorStatus == 'completed' && $auditorStatus == 'completed' )
                                    <i class="ri-check-double-fill ms-2 text-success"></i>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                <span class="fs-12" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A data limite para execução desta tarefa">
                    Prazo: {{ $deadline }}
                </span>

                <h5 class="fs-12 text-truncate task-title mb-0 mt-2">
                    {{ $title }}
                </h5>

                @if (in_array($statusKey, ['losted']))
                    @if ( $surveyorStatus == 'losted' && $auditorStatus == 'losted' )
                        <div class="text-danger small mt-2">
                            Esta <u>Auditoria</u> foi perdida pois a <u>Vistoria</u> não foi efetuada na data prevista.
                        </div>
                    @elseif ( $surveyorStatus == 'completed' && $auditorStatus == 'losted' )
                        <div class="text-warning small mt-2">
                            A <u>Vistoria</u> foi completada. Entretanto, a <u>Auditoria</u> não foi concluída em tempo.
                        </div>
                    @elseif ( $surveyorStatus != 'completed' && $surveyorStatus != 'losted' && $auditorStatus == 'losted' )
                        <div class="text-warning small mt-2">
                            Esta tarefa foi perdida pois não foi efetuada na data prevista.
                        </div>
                    @endif
                @endif
            </div>
            <!--end card-body-->
            <div class="card-footer border-top-dashed bg-body">
                <div class="row">
                    <div class="col small">
                        <div class="avatar-group ps-0">
                            @if ($surveyorId === $auditorId)
                                <a href="{{ route('profileShowURL', $surveyorId) }}" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas de Vistoria e Auditoria delegadas a <u>{{ $surveyorName }}</u>">
                                    <img src="{{ $surveyorAvatar }}"
                                    alt="{{ $surveyorName }}" class="rounded-circle avatar-xxs border border-1 border-white" loading="lazy">
                                </a>
                            @else
                                <a href="{{ route('profileShowURL', $surveyorId) }}" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <u>{{ $surveyorName }}</u>">
                                    <img src="{{ $surveyorAvatar }}"
                                    alt="{{ $surveyorName }}" class="rounded-circle avatar-xxs border border-1 border-white" loading="lazy">
                                </a>

                                @if($auditorId)
                                    <a href="{{ route('profileShowURL', $auditorId) }}" class="d-inline-block ms-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <u>{{ $auditorName }}</u>">
                                        <img src="{{ $auditorAvatar }}"
                                        alt="{{ $auditorName }}" class="rounded-circle avatar-xxs border border-1 border-secondary" loading="lazy">
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        {{--
                        @if ( in_array('audit', $currentUserCapabilities) && in_array($statusKey, ['new','pending','in_progress','completed']) )
                            <button type="button"
                            data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                            title="Requisitar esta tarefa para Auditoria"
                            class="btn btn-sm btn-label right waves-effect btn-soft-secondary btn-assignment-audit-enter"
                            data-assignment-id="{{$assignmentId}}">
                                <i class="ri-add-line label-icon align-middle fs-16"></i> Auditar
                            </button>
                            @if (in_array($statusKey, ['new','pending','in_progress','completed']))
                                <br>
                            @endif
                        @endif
                        --}}
                        @if ($currentUserId == $designatedUserId && in_array($statusKey, ['new','pending','in_progress']) )
                            <button type="button"
                                data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                title="{{$status['reverse']}}"
                                class="btn btn-sm btn-label right waves-effect btn-soft-{{$status['color']}} {{ $designated == 'surveyor' ? 'btn-assignment-surveyor-action' : 'btn-assignment-auditor-action' }}"
                                data-survey-id="{{$surveyId}}"
                                data-assignment-id="{{$assignmentId}}"
                                data-current-status="{{$statusKey}}">
                                    <i class="{{$status['icon']}} label-icon align-middle fs-16"></i> {{$status['reverse']}}
                            </button>

                            @if ( in_array('audit', $currentUserCapabilities) && in_array($statusKey, ['new','pending','in_progress','completed']) )
                                <a href="{{ route('assignmentShowURL', $assignmentId) }}"
                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                    title="Visualizar"
                                    class="btn btn-sm waves-effect btn-soft-dark ri-eye-line">
                                </a>
                            @endif
                        @elseif( ( ( $currentUserId === $surveyorId || $currentUserId === $auditorId ) && in_array($statusKey, ['completed']) ) || in_array('audit', $currentUserCapabilities) )
                            <a href="{{ route('assignmentShowURL', $assignmentId) }}"
                                data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                title="Visualizar"
                                class="btn btn-sm btn-label right waves-effect btn-soft-dark">
                                    <i class="ri-eye-line label-icon align-middle"></i> Visualizar
                            </a>
                        @endif

                        {{--
                        @if ( $surveyorStatus == 'completed' && $auditorStatus == 'losted' && in_array($statusKey, ['losted']) )
                            <a href="{{ route('assignmentShowURL', $assignmentId) }}"
                                data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                title="Visualizar"
                                class="btn btn-sm btn-label right waves-effect btn-soft-dark">
                                    <i class="ri-eye-line label-icon align-middle fs-16"></i> Visualizar
                            </a>
                        @endif
                        --}}

                        @if ( $currentUserId === $designatedUserId && $designated === 'surveyor' && $surveyorId === $auditorId && in_array($statusKey, ['auditing']) )
                            <i class="text-theme ri-questionnaire-fill" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Neste contexto a você foram delegadas tarefas de Vistoria e Auditoria.<br>Procure na coluna <b>Nova</b> o card correspondente a <b>{{ $companyName }}</b> de prazo: <b>{{ $deadline }}</b> e inicialize a tarefa "></i>
                        @endif
                    </div>
                </div>
            </div>
            <!--end card-body-->
            @if ( in_array($statusKey, ['in_progress']) )
                <div class="progress progress-sm animated-progress custom-progress p-0 rounded-bottom-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $percentage }}%">
                    <div class="progress-bar bg-{{ $progressBarClass }} rounded-0" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            @endif
        </div>
    @endforeach
@endif
