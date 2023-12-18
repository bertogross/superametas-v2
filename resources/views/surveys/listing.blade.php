<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i
                    class="ri-checkbox-line fs-16 align-bottom text-theme me-2"></i>Listagem</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect"
                        @if (is_object($templates) && count($templates) > 0) id="btn-surveys-create"
                    @else
                        onclick="alert('Você deverá primeiramente registrar um Modelo');" @endif
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                        title="Adicionar Tarefa de Checklist">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Checklist
                    </button>
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
                        @foreach ($getSurveyStatusTranslations as $key => $value)
                            <option {{ $key == request('status') ? 'selected' : '' }} value="{{ $key }}" title="{{ $value['description'] }}">
                                {{ $value['label'] }}
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
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usuário autor deste registro" width="50"></th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">
                                Título
                            </th>
                            <th class="text-center d-none" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data de Registro não é necessáriamente a data de início das tarefas">
                                Registrado em
                            </th>
                            <th class="text-left" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usários que foram designados para tarefas de Vistoria e Auditoria">
                                Designados
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data de início da rotina">
                                Inicial
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data final da rotina">
                                Final
                            </th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="{{ implode('<br>', array_column($getSurveyRecurringTranslations, 'label')) }}">
                                Recorrência
                            </th>
                            <th class="text-center">
                                Status
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Quantidade de Tarefas Concluídas"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $survey)
                            @php

                                $authorId = $survey->user_id;

                                $surveyId = $survey->id;

                                $title = $survey->title;

                                $distributedData = $survey->distributed_data;
                                $decodedData = json_decode($distributedData, true);
                                $companies = $decodedData ? array_column($decodedData['surveyor_id'], 'company_id') : null;
                                $companies = $companies ? array_unique($companies) : null;
                                $companyNames = [];
                                if($companies){
                                    foreach ($companies as $company => $companyId){
                                        $companyNames[] = getCompanyNameById($companyId);
                                    }
                                }

                                $surveyStatus = $survey->status;


                                $recurring = $survey->recurring;
                                $recurringLabel = $getSurveyRecurringTranslations[$recurring]['label'];

                                $getSurveyTemplateNameById = getSurveyTemplateNameById($survey->template_id);

                                $countSurveyAssignmentBySurveyId = \App\Models\SurveyAssignments::countSurveyAssignmentBySurveyId($surveyId);

                                $delegation = \App\Models\SurveyAssignments::getAssignmentDelegatedsBySurveyId($surveyId);
                            @endphp
                            <tr class="main-row" data-id="{{ $surveyId }}">
                                <td>
                                    <div class="avatar-group">
                                        @php
                                            $avatar = getUserData($authorId)['avatar'];
                                            $name = getUserData($authorId)['name'];
                                        @endphp
                                        <div class="avatar-group-item">
                                            <a href="{{ route('profileShowURL', $authorId) }}" class="d-inline-block"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                                title="{{ $name }} foi o autor deste registro">
                                                <img src="{{ $avatar }}" alt="{{ $name }}" class="rounded-circle avatar-xxs" loading="lazy">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ucfirst($title)}}">
                                        {{ limitChars(ucfirst($title), 30) }}
                                    </span>

                                    <div class="text-muted small" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ limitChars(ucfirst($getSurveyTemplateNameById), 200) }}">
                                        <strong>Modelo:</strong> <span class="text-body"></span>{{ limitChars(ucfirst($getSurveyTemplateNameById), 30) }}
                                    </div>

                                    <div class="text-muted small" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Unidades relacionadas a este registro">
                                        <strong>Unidades:</strong> <span class="text-body"> {{ $companyNames ? implode(', ', $companyNames) : 'Não Informado'}}</span>
                                    </div>
                                </td>
                                <td class="text-center d-none">
                                    {{ $survey->created_at ? date('d/m/Y H:i', strtotime($survey->created_at)) : '-' }}
                                </td>
                                <td>
                                    @if ($delegation)
                                        <div class="avatar-group flex-nowrap">
                                            @if (isset($delegation['surveyors']) && !empty($delegation['surveyors']))
                                                @foreach ($delegation['surveyors'] as $key => $value)
                                                    @php
                                                        $userId = $value['user_id'] ?? null;
                                                        $getUserData = $userId ? getUserData($userId) : null;
                                                        $companyId = $value['company_id'] ?? null;
                                                        $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                                    @endphp
                                                    @if($userId)
                                                        <a href="{{ route('profileShowURL', $userId) }}" class="avatar-group-item" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Vistoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                                            <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if (isset($delegation['auditors']) && !empty($delegation['auditors']))
                                                @foreach ($delegation['auditors'] as $key => $value)
                                                    @php
                                                        $userId = $value['user_id'] ?? null;
                                                        $getUserData = $userId ? getUserData($userId) : null;
                                                        $companyId = $value['company_id'] ?? null;
                                                        $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                                    @endphp
                                                    @if($userId)
                                                        <a href="{{ route('profileShowURL', $userId) }}" class="avatar-group-item" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Auditoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                                            <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $survey->start_at ? date('d/m/Y', strtotime($survey->start_at)) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $survey->end_in ? date('d/m/Y', strtotime($survey->end_in)) : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-border bg-dark-subtle text-body text-uppercase"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                        title="{{ $getSurveyRecurringTranslations[$recurring]['description'] }}">
                                        {{ $recurringLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }}-subtle text-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }} text-uppercase"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                        title="{{ $getSurveyStatusTranslations[$surveyStatus]['description'] }}">
                                        {{ $getSurveyStatusTranslations[$surveyStatus]['label'] }}
                                        @if ($surveyStatus == 'started')
                                            <span class="spinner-border align-top ms-1"></span>
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-dark-subtle text-body" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Quantidade de Tarefas Concluídas">{{ $countSurveyAssignmentBySurveyId }}</span>
                                </td>
                                <td scope="row" class="text-end">
                                    @if (in_array($surveyStatus, ['new', 'started', 'stopped']))
                                        <button type="button" data-survey-id="{{ $survey->id }}"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                            class="btn btn-sm btn-label right waves-effect btn-soft-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }} btn-surveys-change-status"
                                            data-current-status="{{ $surveyStatus }}"
                                            title="{{ $getSurveyStatusTranslations[$surveyStatus]['reverse'] }}">
                                            <i
                                                class="{{ $getSurveyStatusTranslations[$surveyStatus]['icon'] }} label-icon align-middle fs-16"></i>{{ $getSurveyStatusTranslations[$surveyStatus]['reverse'] }}
                                        </button>
                                    @endif

                                    @if (!in_array($surveyStatus, ['completed', 'filed']))
                                        <button type="button"
                                            @if ($authorId != auth()->id()) class="btn btn-sm btn-soft-dark waves-effect ri-edit-line"
                                            onclick="alert('Você não possui autorização para editar um registro gerado por outra pessoa');"
                                        @else
                                            class="btn btn-sm btn-soft-dark waves-effect btn-surveys-edit ri-edit-line"
                                            data-survey-id="{{ $surveyId }}" @endif
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                                            title="Editar"></button>
                                    @endif

                                    @if ( !in_array($surveyStatus, ['scheduled', 'new']) )
                                        <a
                                            @if ($countSurveyAssignmentBySurveyId > 0)
                                                href="{{ route('surveysShowURL', $surveyId) }}"
                                            @else
                                                onclick="alert('Não há dados para relatório')"
                                            @endif
                                            class="btn btn-sm btn-soft-dark waves-effect ri-line-chart-fill {{ $countSurveyAssignmentBySurveyId == 0 ? 'cursor-not-allowed' : '' }}"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                                            title="Visualização Analítica"></a>
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
