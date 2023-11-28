@php
    use App\Models\Survey;

    $currentUserId = auth()->id();
@endphp
@if ( !empty($data) && is_array($data) )
    @foreach ($data as $key => $assignment)
        @php
            $assignmentId = intval($assignment['id']);
            $surveyId = intval($assignment['survey_id']);
            $companyId = intval($assignment['company_id']);

            $surveyorId = isset($assignment['surveyor_id']) ? intval($assignment['surveyor_id']) : null;
            $auditorId = isset($assignment['auditor_id']) ? intval($assignment['auditor_id']) : null;

            $surveyorStatus = $assignment['surveyor_status'] ?? null;
            $auditorStatus = $assignment['auditor_status'] ?? null;

            if($designated == 'auditor'){
                $designatedUserId = $auditorId;
            }elseif($designated == 'surveyor'){
                $designatedUserId = $surveyorId;
            }

            $surveyorAvatar = getUserData($surveyorId)['avatar'];
            $surveyorName = getUserData($surveyorId)['name'];

            $auditorAvatar = getUserData($auditorId)['avatar'];
            $auditorName = getUserData($auditorId)['name'];

            $survey = Survey::findOrFail($surveyId);
            $templateName = getTemplateNameById($survey->template_id);

            // Count the number of steps that have been finished
            $countTopics = countSurveyTopics($surveyId);

            $countResponses = 0;

            if( in_array($statusKey, ['auditing']) && $designated == 'surveyor' ){
                $countResponses = countSurveyAuditorResponses($auditorId, $surveyId, $companyId);
            }else{
                if($designated == 'auditor'){
                    $countResponses = countSurveyAuditorResponses($auditorId, $surveyId, $companyId);
                }elseif($designated == 'surveyor'){
                    $countResponses = countSurveySurveyorResponses($surveyorId, $surveyId, $companyId);
                }
            }

            // Calculate the percentage
            $percentage = 0;
            if ($countTopics > 0) {
                $percentage = ($countResponses / $countTopics) * 100;
            }

            // Determine the progress bar class based on the percentage
            $progressBarClass = 'danger'; // default class
            if ($percentage > 25) {
                $progressBarClass = 'warning';
            }
            if ($percentage > 50) {
                $progressBarClass = 'primary';
            }
            if ($percentage > 75) {
                $progressBarClass = 'info';
            }
            if ($percentage > 95) {
                $progressBarClass = 'secondary';
            }
            if ($percentage >= 100) {
                $progressBarClass = 'success';
            }
        @endphp
        <div class="card tasks-box bg-body" data-assignment-id="{{$assignmentId}}">
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col text-theme fw-medium fs-15">
                        {{ getCompanyNameById($companyId) }}
                    </div>
                    <div class="col-auto">
                        @if ($designated == 'auditor')
                            <span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>
                        @elseif($designated == 'surveyor')
                            <span class="badge bg-dark-subtle text-body badge-border">Vistoria</span>
                        @endif
                    </div>
                </div>
                <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A data em que esta tarefa deverá ser desempenhada">
                    {{ $assignment['created_at'] ? date("d/m/Y", strtotime($assignment['created_at'])) : '-' }}
                </span>
                <h5 class="fs-13 text-truncate task-title mb-0 mt-2">
                    {{ $templateName }}
                </h5>
                @if ( $designated == 'auditor' && $surveyorStatus == 'losted' && $auditorStatus == 'losted' )
                    <div class="text-danger small mt-2">Esta <u>Auditoria</u> foi perdida pois a <u>Vistoria</u> não foi efetuada na data prevista</div>
                @endif

                @if ( $designated == 'surveyor' && $surveyorStatus == 'losted' )
                    <div class="text-danger small mt-2">Esta <u>Vistoria</u> foi perdida pois não foi efetuada na data prevista</div>
                @endif

                @if ( $surveyorStatus == 'completed' && $auditorStatus == 'completed' )
                    <div class="text-success small mt-2"><u>Vistoria</u> e <u>Auditoria</u> concluídas</div>
                @endif
            </div>
            <!--end card-body-->
            <div class="card-footer border-top-dashed bg-body">
                <div class="row">
                    <div class="col small">
                        <div class="avatar-group ps-0">
                            @if ($surveyorId === $auditorId)
                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="{{ route('profileShowURL', $surveyorId) }}" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas de Vistoria e Auditoria delegadas a <u>{{ $surveyorName }}</u>">
                                    <img
                                    @if( empty(trim($surveyorAvatar)) )
                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                    @else
                                        src="{{ URL::asset('storage/' .$surveyorAvatar ) }}"
                                    @endif
                                    alt="{{ $surveyorName }}" class="rounded-circle avatar-xxs">
                                </a>
                            @else
                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="{{ route('profileShowURL', $surveyorId) }}" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <u>{{ $surveyorName }}</u>">
                                    <img
                                    @if( empty(trim($surveyorAvatar)) )
                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                    @else
                                        src="{{ URL::asset('storage/' .$surveyorAvatar ) }}"
                                    @endif
                                    alt="{{ $surveyorName }}" class="rounded-circle avatar-xxs">
                                </a>

                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="{{ route('profileShowURL', $auditorId) }}" class="d-inline-block ms-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <u>{{ $auditorName }}</u>">
                                    <img
                                    @if( empty(trim($auditorAvatar)) )
                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                    @else
                                        src="{{ URL::asset('storage/' .$auditorAvatar ) }}"
                                    @endif
                                    alt="{{ $auditorName }}" class="rounded-circle avatar-xxs">
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        @if ($currentUserId == $designatedUserId && in_array($statusKey, ['new','pending','in_progress']) )
                            <button type="button"
                                data-bs-toggle="tooltip"
                                data-bs-trigger="hover"
                                data-bs-placement="top"
                                title="{{$status['reverse']}}"
                                class="btn btn-sm btn-label right waves-effect btn-soft-{{$status['color']}} {{ $designated == 'surveyor' ? 'btn-assignment-surveyor-action' : 'btn-assignment-auditor-action' }}"
                                data-survey-id="{{$surveyId}}"
                                data-assignment-id="{{$assignmentId}}"
                                data-current-status="{{$statusKey}}">
                                <i class="{{$status['icon']}} label-icon align-middle fs-16"></i> {{$status['reverse']}}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <!--end card-body-->
            @if ( in_array($statusKey, ['in_progress']) || ( in_array($statusKey, ['auditing']) && $designated == 'surveyor' ) )
                <div class="progress progress-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $percentage }}%">
                    <div class="progress-bar bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            @endif
        </div>
    @endforeach
@endif
