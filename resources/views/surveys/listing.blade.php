<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-survey-line fs-16 align-bottom text-theme me-2"></i>Vistorias</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect"
                    @if( is_object($templates) && count($templates) > 0 )
                        id="btn-surveys-create"
                    @else
                        onclick="alert('Você deverá primeiramente registrar um Modelo');"
                    @endif
                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Vistoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0">
        <form action="{{ route('surveysIndexURL') }}" method="get" autocomplete="off">
            <div class="row g-3">

                <div class="col-sm-12 col-md col-lg">
                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="Período" data-min-date="{{ $firstDate ?? '' }}" data-max-date="{{ $lastDate ?? '' }}" value="{{ request('created_at') ?? '' }}">
                </div>

                <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                    <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                </div>

            </div>
        </form>
    </div>

    <div class="card-body">
        @if ( !$data || $data->isEmpty() )
            @component('components.nothing')
                {{--
                @slot('url', route('surveysCreateURL'))
                --}}
            @endcomponent
        @else
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usuário autor deste registro" width="50"></th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Título</th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data de Registro não é necessáriamente a data de início das tarefas">Registro</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="{{ implode('<br>', array_column($getSurveyRecurringTranslations, 'label')) }}">Recorrência</th>
                            <th class="text-center">Status</th>
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

                                $surveyStatus = $survey->status;

                                $delegatedToIds = [];
                                $delegatedTo = array_map(function($item) {
                                    return $item['user_id'];
                                }, $decodedData['delegated_to']);
                                $delegatedToIds = count($delegatedTo) > 1 ? array_unique($delegatedTo) : $delegatedTo;

                                $auditedByIds = [];
                                $auditedBy = array_map(function($item) {
                                    return $item['user_id'];
                                }, $decodedData['audited_by']);
                                $auditedByIds = count($auditedBy) > 1 ? array_unique($auditedBy) : $auditedBy;

                                $recurring = $survey->recurring;
                                $recurringLabel = $getSurveyRecurringTranslations[$recurring]['label'];

                                $getSurveyTemplateNameById = getSurveyTemplateNameById($survey->template_id);
                            @endphp
                            <tr class="main-row" data-id="{{ $surveyId }}">
                                <td>
                                    <div class="avatar-group">
                                        @php
                                            $avatar = getUserData($authorId)['avatar'];
                                            $name = getUserData($authorId)['name'];
                                        @endphp
                                        <div class="avatar-group-item">
                                            <a href="{{ route('profileShowURL', $authorId) }}" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $name }} é o autor deste registro">
                                                <img src="{{ $avatar }}"
                                                alt="{{ $name }}" class="rounded-circle avatar-xxs">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td title="{{ $getSurveyTemplateNameById }}">
                                    {{ limitChars($title, 30) }}<br>
                                    Modelo: {{ limitChars($getSurveyTemplateNameById, 30) }}
                                </td>
                                <td class="text-center">
                                    {{ $survey->created_at ? date("d/m/Y", strtotime($survey->created_at)) : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $getSurveyRecurringTranslations[$recurring]['color'] }}-subtle text-{{ $getSurveyRecurringTranslations[$recurring]['color'] }} text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $getSurveyRecurringTranslations[$recurring]['description'] }}">
                                        {{ $recurringLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }}-subtle text-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }} text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $getSurveyStatusTranslations[$surveyStatus]['description'] }}">
                                        {{ $getSurveyStatusTranslations[$surveyStatus]['label'] }}
                                        @if ($surveyStatus == 'started')
                                            <span class="spinner-border align-top ms-1"></span>
                                        @endif
                                    </span>
                                </td>
                                <td scope="row" class="text-end">
                                    @if ( in_array($surveyStatus, ['new', 'started', 'stopped']) )
                                        <button type="button" data-survey-id="{{ $survey->id }}"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                            class="btn btn-sm btn-label right waves-effect btn-soft-{{ $getSurveyStatusTranslations[$surveyStatus]['color'] }} btn-surveys-change-status"
                                            data-current-status="{{ $surveyStatus }}"
                                            title="{{ $getSurveyStatusTranslations[$surveyStatus]['reverse'] }}">
                                                <i class="{{ $getSurveyStatusTranslations[$surveyStatus]['icon'] }} label-icon align-middle fs-16 ms-2"></i> {{ $getSurveyStatusTranslations[$surveyStatus]['reverse'] }}
                                        </button>
                                    @endif

                                    <div class="btn-group">
                                        <button type="button"
                                        @if ($authorId != auth()->id())
                                            class="btn btn-sm btn-soft-dark waves-effect ri-edit-line"
                                            onclick="alert('Você não possui autorização para editar um registro gerado por outra pessoa');"
                                        @else
                                            class="btn btn-sm btn-soft-dark waves-effect btn-surveys-edit ri-edit-line"
                                            data-survey-id="{{$surveyId}}"
                                        @endif
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar"></button>

                                        <a href="{{ route('surveysShowURL', $surveyId) }}" class="btn btn-sm btn-soft-dark waves-effect ri-line-chart-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualização Analítica"></a>
                                    </div>
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
    <!--end card-body-->
</div>
