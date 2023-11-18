<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-survey-line fs-16 align-bottom text-theme me-2"></i>Vistorias</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end" id="btn-surveys-create" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Vistoria
                    </button>
                </div>
            </div>
        </div>
    </div>
    @if (!$data->isEmpty())
        <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0">
            <form action="{{ route('surveysIndexURL') }}" method="get" autocomplete="off">
                <div class="row g-3">
                    {{--
                    <div class="col-sm-12 col-md-2 col-lg">
                        <div class="search-box">
                            <input type="text" class="form-control search bg-light border-light" placeholder="Search for tasks or something...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" data-choices data-choices-removeItem name="delegated_to[]" multiple data-placeholder="Atribuíção">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, $delegated_to) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    --}}

                    <div class="col-sm-12 col-md col-lg">
                        <input type="text" class="form-control bg-light border-light flatpickr-range" name="created_at" placeholder="Período" data-min-date="{{ $firstDate }}" data-max-date="{{ $lastDate }}" value="{{ request('created_at') }}">
                    </div>

                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" name="status">
                                <option value="" {{  !request('status') ? 'selected' : '' }}>Status</option>
                                @foreach ($getSurveyStatusTranslations as $key => $value)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                        <button type="submit" class="btn btn-theme w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                    </div>

                </div>
            </form>
        </div>
    @endif

    <div class="card-body pt-1">
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
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Modelo</th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro não é necessáriamente a data de início das vistorias">Registro</th>
                            {{--
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Vistoria">Vistoria</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Auditoria">Auditoria</th>
                            --}}
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaboradores que receberam a tarefa de Vistoria">Vistoriadores</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaboradores que receberam a tarefa de Auditoria">Auditores</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="{{ implode('<br>', array_column($getSurveyRecurringTranslations, 'label')) }}">Recorrência</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Status Possíveis" data-bs-content="{{ implode('<br>', array_column($getSurveyStatusTranslations, 'label')) }}">Status</th>
                            {{--
                            <th data-sort="priority">Priority</th>
                            --}}
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $survey)
                            @php
                                $surveyId = $survey->id;
                                $distributedData = $survey->distributed_data;
                                $decodedData = json_decode($distributedData, true);

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

                                $getTemplateNameById = getTemplateNameById($survey->template_id);
                            @endphp
                            <tr class="main-row" data-id="{{ $surveyId }}">
                                <td title="{{ $getTemplateNameById }}">
                                    {{ limitChars($getTemplateNameById, 30) }}
                                </td>
                                <td class="text-center">
                                    {{ $survey->created_at ? date("d/m/Y", strtotime($survey->created_at)) : '-' }}
                                </td>
                                <td>
                                    @if ($delegatedToIds)
                                        <div class="avatar-group">
                                            @foreach ($delegatedToIds as $userId)
                                                @php
                                                    $avatar = getUserData($userId)['avatar'];
                                                    $name = getUserData($userId)['name'];
                                                @endphp
                                                <div class="avatar-group-item">
                                                    <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="{{ $name }}" title="{{ $name }}">
                                                        <img
                                                        @if( empty(trim($avatar)) )
                                                            src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                        @else
                                                            src="{{ URL::asset('storage/' .$avatar ) }}"
                                                        @endif
                                                        alt="{{ $name }}" class="rounded-circle avatar-xxs">
                                                    </a> {{-- $name --}}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($auditedByIds)
                                        <div class="avatar-group">
                                            @foreach ($auditedByIds as $userId)
                                                @php
                                                    $avatar = getUserData($userId)['avatar'];
                                                    $name = getUserData($userId)['name'];
                                                @endphp
                                                <div class="avatar-group-item">
                                                    <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="{{ $name }}" title="{{ $name }}">
                                                        <img
                                                        @if( empty(trim($avatar)) )
                                                            src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                        @else
                                                            src="{{ URL::asset('storage/' .$avatar ) }}"
                                                        @endif
                                                        alt="{{ $name }}" class="rounded-circle avatar-xxs">
                                                    </a> {{-- $name --}}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $getSurveyRecurringTranslations[$recurring]['color'] }}-subtle text-{{ $getSurveyRecurringTranslations[$recurring]['color'] }} text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $getSurveyRecurringTranslations[$recurring]['description'] }}">
                                     {{ $recurringLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $getSurveyStatusTranslations[$survey->status]['color'] }}-subtle text-{{ $getSurveyStatusTranslations[$survey->status]['color'] }} text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $getSurveyStatusTranslations[$survey->status]['description'] }}">
                                        {{ $getSurveyStatusTranslations[$survey->status]['label'] }}
                                    </span>
                                </td>
                                {{--
                                <td class="priority">
                                    <span class="badge bg-danger text-uppercase">
                                        {{ $survey->priority }}
                                    </span>
                                </td>
                                --}}
                                <td scope="row" class="text-end">
                                    @if ( $survey->status == 'new' )
                                        <button type="button" class="btn btn-sm btn-soft-theme btn-surveys-edit" data-survey-id="{{$surveyId}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    @else
                                        <button type="button" disabled class="btn btn-sm btn-soft-primary cursor-not-allowed" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left" data-bs-title="Edição Bloqueada" data-bs-content="Status <b class='text-{{ $getSurveyStatusTranslations[$survey->status]['color'] }}'>{{ $getSurveyStatusTranslations[$survey->status]['label'] }}</b><br><br>A edição será possível somente se você <b>Interromper</b> esta Tarefa">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('surveysShowURL', $surveyId) }}" class="btn btn-sm btn-soft-theme" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Fromulário de Vistoria em nova Janela">
                                        <i class="ri-eye-line"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-soft-theme btn-toggle-row-detail" data-id="{{ $surveyId }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Expand/Collapse this row">
                                        <i class="ri-folder-line"></i>
                                        <i class="ri-folder-open-line d-none"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="details-row d-none bg-body-tertiary" data-details-for="{{ $surveyId }}">
                                <td colspan="9">
                                    <div class="load-row-content" data-survey-id="{{ $surveyId }}">
                                        @component('surveys.components.distributeds-card')
                                            @slot('survey', $survey)
                                            @slot('distributedData', $decodedData)
                                            @slot('recurringLabel', $recurringLabel)
                                            @slot('getSurveyStatusTranslations', $getSurveyStatusTranslations)
                                        @endcomponent
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
