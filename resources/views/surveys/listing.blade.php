{{--
<div class="row">
    @foreach ($getSurveyStatusTranslations as $key => $value)
    <div class="col">
        <div class="card card-animate" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="{{ $value['description'] }}">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">{{ $value['label'] }}</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{ $datatatusCount[$key] ?? 0 }}"></span></h2>
                        <!--
                        <p class="mb-0 text-muted"><span class="badge bg-light text-{{ $value['color'] }} mb-0">
                                <i class="ri-arrow-up-line align-middle"></i> 0.63 %
                            </span> vs. previous month
                        </p>
                        -->
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-{{ $value['color'] }}-subtle text-{{ $value['color'] }} rounded-circle fs-4">
                                <i class="{{ !empty($value['icon']) ? $value['icon'] : 'ri-ticket-2-line' }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>
    <!--end col-->
    @endforeach
</div>
--}}

<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">Vistorias</h5>
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
        @if ($data->isEmpty())
            @component('components.nothing')
                {{--
                @slot('url', route('surveysCreateURL'))
                --}}
            @endcomponent
        @else
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Modelo</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro">Início</th>
                            {{--
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Vistoria">Vistoria</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Auditoria">Auditoria</th>
                            --}}
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaborador ao qual foi atribuída a tarefa de Vistoria">Atribuído a</th>
                            <th data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Status Possíveis" data-bs-content="{{ implode('<br>', array_column($getSurveyStatusTranslations, 'label')) }}">Status</th>
                            {{--
                            <th data-sort="priority">Priority</th>
                            --}}
                            <th scope="col" width="25"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $survey)
                            <tr>
                                <td>
                                    {{ limitChars(getTemplateNameById($survey->template_id), 30) }}
                                </td>
                                <td>
                                    {{ $survey->start_date ? date("d/m/Y", strtotime($survey->start_date)) : '-' }}
                                </td>
                                {{--
                                <td>
                                    {{ $survey->completed_at ? date("d/m/Y", strtotime($survey->completed_at)) : '-' }}
                                </td>
                                <td>
                                    {{ $survey->audited_at ? date("d/m/Y", strtotime($survey->audited_at)) : '-' }}
                                </td>
                                --}}
                                <td class="align-middle">
                                    <!--
                                    @if ($survey->delegated_to)
                                        @php
                                            $avatar = getUserData($survey->delegated_to)['avatar'];
                                            $name = getUserData($survey->delegated_to)['name'];
                                        @endphp
                                        <a href="javascript: void(0);" class="avatar-group-item me-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="{{ $name }}" title="{{ $name }}">
                                            <img
                                            @if( empty(trim($avatar)) )
                                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                            @else
                                                src="{{ URL::asset('storage/' .$avatar ) }}"
                                            @endif
                                            alt="{{ $name }}" class="rounded-circle avatar-xxs">
                                        </a> {{-- $name --}}
                                    @endif
                                    -->
                                    -
                                </td>
                                <td>
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
                                    <div class="dropdown dropstart">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-theme fs-18"><i class="ri-more-2-line"></i></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            @if ( $survey->status == 'new' )
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item btn-surveys-edit" data-survey-id="{{$survey->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar">Editar</a>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="javascript:void(0);" disabled class="cursor-not-allowed" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left" data-bs-title="Edição Bloqueada" data-bs-content="Status <b class='text-{{ $getSurveyStatusTranslations[$survey->status]['color'] }}'>{{ $getSurveyStatusTranslations[$survey->status]['label'] }}</b><br><br>A edição será possível somente se você <b>Interromper</b> esta Tarefa">Editar</a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ route('surveysShowURL', $survey->id) }}" class="dropdown-item" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Vistoria em nova Janela">Visualizar</a>
                                            </li>
                                        </div>
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
