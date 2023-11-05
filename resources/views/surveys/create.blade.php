@extends('layouts.master')
@section('title')
    @lang('translation.surveys')
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot
        @slot('li_1')
            @lang('translation.surveys')
        @endslot
        @slot('title')
            @if($survey)
                Edição de Audotoria<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$survey->id}}</span> {{-- limitChars($survey->title ?? '', 20) --}}</small>
            @else
                Cadastrar Vistoria
            @endif
        @endslot
    @endcomponent

    @include('components.alerts')

    @php
        $surveyId = $survey->id ?? '';
        $created_by = $survey->created_by ?? auth()->id();
        $assigned_to = $survey->assigned_to ?? '';
        $delegated_to = $survey->delegated_to ?? '';
        $audited_by = $survey->audited_by ?? '';
        $due_date = $survey->due_date ?? '';
        $due_date = !empty($due_date) && !is_null($due_date) ? date('d/m/Y', strtotime($due_date)) : date('d/m/Y', strtotime("+3 days"));
        $dateRange = '';
        $status = $survey->status ?? 'pending';
        $description = $survey->description ?? '';
        //appPrintR($survey);
    @endphp

    @if (!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 0)
    @else
        <div class="alert alert-warning">Lojas ainda não foram ativadas para seu perfil</div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @if ($survey)
                    <button type="button" class="btn btn-sm btn-outline-theme" id="btn-surveys-store-or-update" tabindex="-1">Atualizar</button>

                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-surveys-clone" tabindex="-1">Clonar</button>
                @else
                    <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-store-or-update" tabindex="-1">Salvar</button>
                @endif

                <a href="{{-- route('surveysComposeShowURL', ['id' => $compose->id]) --}}" class="btn btn-sm btn-outline-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1">Visualizar</a>
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 col-md-5 col-lg-4 col-xxl-3">
                    <div class="p-3 border border-1 border-light rounded">
                        <form id="surveysForm" method="POST" autocomplete="off" class="needs-validation" novalidate autocomplete="false">
                            @csrf
                            <input type="hidden" name="id" value="{{ $surveyId }}" />

                            <input type="hidden" name="status" value="{{ $status }}" />
                            <input type="hidden" name="created_by" value="{{ $created_by }}" />
                            <input type="hidden" name="current_user_editor" value="{{ auth()->id() }}" />


                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Loja</label>
                                <select class="form-select" name="assigned_to" id="assigned_to" required>
                                    <option {{ empty($assigned_to) ? 'selected' : '' }} value="">- Selecione -</option>
                                    @foreach ($getAuthorizedCompanies as $companyId)
                                        <option value="{{ $companyId }}" @selected(old('assigned_to', $assigned_to) == $companyId)>{{ getCompanyAlias(intval($companyId)) }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Selecione a loja que será vistoriada e auditada</div>
                            </div>

                            <div class="mb-3">
                                <label for="date-range-field" class="form-label">Data Inicial e Limite</label>
                                <input type="text" id="date-range" name="date_range" class="form-control flatpickr-range" value="{{ $dateRange }}" required>
                                <div class="form-text d-none">Opcional</div>
                            </div>

                            {{--
                            <!--end col-->
                            <div class="mb-3">
                                <label class="form-label">Atribuído a</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        @foreach ($users as $user)
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="delegated_to"
                                                        value="{{ $user->id }}" id="user-{{ $user->id }}" @checked(old('delegated_to', $delegated_to) == $user->id) required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="user-{{ $user->id }}">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            @if(empty(trim($user->avatar)))
                                                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                            @else
                                                                src="{{ URL::asset('storage/' . $user->avatar) }}"
                                                            @endif
                                                                alt="{{ $user->name }}" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2">{{ $user->name }}</span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá efetuar a vistoria</div>
                            </div>

                            <!--end col-->
                            <div class="mb-3">
                                <label class="form-label">Auditor(a)</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        @foreach ($usersByRole as $auditor)
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="audited_by"
                                                        value="{{ $auditor->id }}" id="auditor-{{ $auditor->id }}" @checked(old('audited_by', $audited_by) == $auditor->id) required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="auditor-{{ $auditor->id }}">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            @if(empty(trim($auditor->avatar)))
                                                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                            @else
                                                                src="{{ URL::asset('storage/' . $auditor->avatar) }}"
                                                            @endif
                                                                alt="{{ $auditor->name }}" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2">{{ $auditor->name }}</span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá auditar a vistoria</div>
                            </div>
                            --}}

                            <div>
                                <label for="description" class="form-label">Observações</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="8">{{ $description }}</textarea>
                                <div class="form-text">Opcional</div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-6 col-md-7 col-lg-8 col-xxl-9">
                    <div class="p-3 border border-1 border-light rounded">
                        <p class="text-muted mb-2">Use <code>arrow-navtabs </code>class to create arrow nav tabs.</p>
                        <ul class="nav nav-tabs nav-border-top nav-border-top-theme mb-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#arrow-departments" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                    <span class="d-none d-sm-block">Departamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#arrow-custom" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                    <span class="d-none d-sm-block">Formulário Customizado</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content text-muted p-3 border border-1 border-light rounded rounded-top-0">
                            <div class="tab-pane active" id="arrow-departments" role="tabpanel">
                                <h6>Departamentos</h6>
                                <div class="mb-3">
                                    <select class="form-select" name="survey_compose_default_id" id="survey_compose_default_id">
                                        <option value="">- Selecionar Formulário -</option>
                                        @foreach ($getSurveyComposeDefault as $default)
                                            <option value="{{ $default->id }}" @selected(old('survey_compose_default_id', $surveyComposeDefaultId ?? '') == $default->id)>
                                                {{ $default->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="load-default-form" class="border border-1 rounded border-light p-3"></div>
                            </div>
                            <div class="tab-pane" id="arrow-custom" role="tabpanel">
                                <h6>Formulário Customizado</h6>
                                <div class="mb-3">
                                    <select class="form-select" name="survey_compose_custom_id" id="survey_compose_custom_id">
                                        <option value="">- Selecionar Formulário -</option>
                                        @foreach ($getSurveyComposeCustom as $custom)
                                            <option value="{{ $custom->id }}" @selected(old('survey_compose_custom_id', $surveyComposeCustomId ?? '') == $custom->id)>
                                                {{ $custom->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="load-custom-form" class="border border-1 rounded border-light p-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysCreateOrUpdateURL = "{{ route('surveysCreateOrUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>
@endsection
