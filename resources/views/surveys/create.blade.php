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
                Edição de Vistoria<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$survey->id}}</span> {{-- limitChars($survey->title ?? '', 20) --}}</small>
            @else
                Cadastrar Vistoria
            @endif
        @endslot
    @endcomponent

    @include('components.alerts')

    @php
        $surveyId = $survey->id ?? '';
        $userId = $survey->user_id ?? auth()->id();
        $assigned_to = $survey->assigned_to ?? '';
        $delegated_to = $survey->delegated_to ?? '';
        $audited_by = $survey->audited_by ?? '';
        $start_date = $survey->start_date ?? '';
        $start_date = !empty($start_date) && !is_null($start_date) ? date('d/m/Y', strtotime($start_date)) : date('d/m/Y', strtotime("+3 days"));
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

                    <a href="{{ route('surveysShowURL', ['id' => $survey->id]) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1">Visualizar</a>
                @else
                    <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-store-or-update" tabindex="-1">Salvar</button>
                @endif
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Atribuições</h4>
         </div>
        <div class="card-body">
            <form id="surveysForm" method="POST" autocomplete="off" class="needs-validation" novalidate autocomplete="false">
                @csrf
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-5 col-xxl-4">
                        <div class="p-3 border border-1 border-light rounded">
                            <input type="hidden" name="id" value="{{ $surveyId }}">

                            <input type="hidden" name="status" value="{{ $status }}">

                            <div class="mb-4">
                                <label for="title" class="form-label">Título:</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ $dateRange }}" maxlength="100" required>
                                <div class="form-text">Título é necessário para que, quando na listagem, você facilmente identifique este formulário</div>
                            </div>

                            <div class="mb-4">
                                <label for="date-range-field" class="form-label">Tipo de Recorrência:</label>
                                <select class="form-select" name="recurring" required>
                                    <option selected>- Selecione -</option>
                                    <option value="once">Uma vez</option>
                                    <option value="daily">Diária</option>
                                    {{--
                                    <option value="weekly">Semanal</option>
                                    <option value="biweekly">Quinzenal</option>
                                    <option value="monthly">Mensal</option>
                                    <option value="annual">Anual</option>
                                    --}}
                                </select>
                            </div>

                            {{--
                            <div class="mb-4">
                                <label for="date-range-field" class="form-label">Data Inicial e Limite:</label>
                                <input type="text" id="date-range" name="date_range" class="form-control flatpickr-range" value="{{ $dateRange }}" maxlength="25" required>
                            </div>
                            --}}

                            <div class="mb-4">
                                <label for="date-start-field" class="form-label">Data Inicial:</label>
                                <input type="text" id="date-start-field"" name="date_start" class="form-control flatpickr-default" value="{{ $dateRange }}" maxlength="10" required>
                            </div>

                            <!--end col-->
                            <div class="mb-4">
                                <label class="form-label mb-0">Vistoria Atribuída a:</label>
                                <div class="form-text mt-0 mb-2">Selecione para cada das Lojas o colaborador que irá proceder com a <strong>Vistoria</strong></div>
                                <div class="row">
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xxl-3">
                                        <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                                            @foreach ($getAuthorizedCompanies as $key => $companyId)
                                                <a class="nav-link text-uppercase {{ $key == 0 ? 'active' : '' }} text-uppercase" data-bs-target="#delegated_to-company-{{ $companyId }}" id="delegated_to-company-{{ $companyId }}-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true">{{ getCompanyAlias($companyId) }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xxl-9">
                                        <div class="tab-content p-0 bg-light h-100">
                                            @foreach ($getAuthorizedCompanies as $key => $companyId)
                                                <div class="tab-pane fade show {{ $key == 0 ? 'active' : '' }}" id="delegated_to-company-{{ $companyId }}" role="tabpanel" aria-labelledby="delegated_to-company-{{ $companyId }}-tab">
                                                    <div class="bg-light p-3 rounded-2">
                                                        <ul class="list-unstyled vstack gap-2 mb-0">
                                                            @foreach ($users as $user)
                                                                @php
                                                                    $userId = $user->id;
                                                                    $userName = $user->name;
                                                                    $userAvatar = $user->avatar;

                                                                    $userCompanies = getAuthorizedCompanies($userId) ?? null;
                                                                @endphp
                                                                @if ( is_array($userCompanies) && in_array($companyId, $userCompanies) )
                                                                    <li>
                                                                        <div class="form-check form-check-success d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="radio" name="delegated_to[{{ $companyId}}]"
                                                                                value="{{ $userId }}" id="delegated_to-user-{{ $companyId.$userId }}" @checked(old('delegated_to', $delegated_to) == $userId) required>
                                                                            <label class="form-check-label d-flex align-items-center"
                                                                                for="delegated_to-user-{{ $companyId.$userId }}">
                                                                                <span class="flex-shrink-0">
                                                                                    <img
                                                                                    @if(empty(trim($userAvatar)))
                                                                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                                                    @else
                                                                                        src="{{ URL::asset('storage/' . $userAvatar) }}"
                                                                                    @endif
                                                                                        alt="{{ $userName }}" class="avatar-xxs rounded-circle">
                                                                                </span>
                                                                                <span class="flex-grow-1 ms-2">{{ $userName }}</span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end col-->
                            <div class="mb-4">
                                <label class="form-label mb-0">Auditora Atribuída a:</label>
                                <div class="form-text mt-0 mb-2">Selecione para cada das Lojas o colaborador que irá <strong>Auditar</strong> a vistoria</div>
                                <div class="row">
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xxl-3">
                                        <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                                            @foreach ($getAuthorizedCompanies as $key => $companyId)
                                                <a class="nav-link text-uppercase {{ $key == 0 ? 'active' : '' }} text-uppercase" data-bs-target="#audited_by-company-{{ $companyId }}" id="audited_by-company-{{ $companyId }}-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true">{{ getCompanyAlias($companyId) }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xxl-9">
                                        <div class="tab-content p-0 bg-light h-100">
                                            @foreach ($getAuthorizedCompanies as $key => $companyId)
                                                <div class="tab-pane fade show {{ $key == 0 ? 'active' : '' }}" id="audited_by-company-{{ $companyId }}" role="tabpanel" aria-labelledby="audited_by-company-{{ $companyId }}-tab">
                                                    <div class="bg-light p-3 rounded-2">
                                                        <ul class="list-unstyled vstack gap-2 mb-0">
                                                            @foreach ($users as $user)
                                                                @php
                                                                    $userId = $user->id;
                                                                    $userName = $user->name;
                                                                    $userAvatar = $user->avatar;

                                                                    $userCompanies = getAuthorizedCompanies($userId) ?? null;
                                                                @endphp
                                                                @if ( is_array($userCompanies) && in_array($companyId, $userCompanies) )
                                                                    <li>
                                                                        <div class="form-check form-check-success d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="radio" name="audited_by[{{ $companyId}}]"
                                                                                value="{{ $userId }}" id="audited_by-user-{{ $companyId.$userId }}" @checked(old('audited_by', $delegated_to) == $userId) required>
                                                                            <label class="form-check-label d-flex align-items-center"
                                                                                for="audited_by-user-{{ $companyId.$userId }}">
                                                                                <span class="flex-shrink-0">
                                                                                    <img
                                                                                    @if(empty(trim($userAvatar)))
                                                                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                                                    @else
                                                                                        src="{{ URL::asset('storage/' . $userAvatar) }}"
                                                                                    @endif
                                                                                        alt="{{ $userName }}" class="avatar-xxs rounded-circle">
                                                                                </span>
                                                                                <span class="flex-grow-1 ms-2">{{ $userName }}</span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="form-label">Observações:</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="7" maxlength="500">{{ $description }}</textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-7 col-xxl-8">
                        <div class="p-3 border border-1 border-light rounded">

                            <p class="text-ody mb-0">Composição do formulário:</p>

                            <div id="nested-compose-area" style="min-height: 250px;">
                                <div class="accordion list-group nested-list nested-receiver">@if ($Default || $Custom)
                                    @if ($Default)
                                        @component('surveys.components.composer-form')
                                            @slot('type', 'default')
                                            @slot('data', $Default)
                                        @endcomponent
                                    @endif

                                    @if ($Custom)
                                        @component('surveys.components.composer-form')
                                            @slot('type', 'custom')
                                            @slot('data', $Custom)
                                        @endcomponent
                                    @endif
                                @endif</div>

                                <div class="clearfix mt-3">
                                    <button type="button" class="btn btn-ghost-dark btn-icon rounded-pill float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line text-theme fs-4"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysShowURL = "{{ route('surveysShowURL') }}";
        var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";

        var surveysTermsSearchURL = "{{ route('surveysTermsSearchURL') }}";
        var surveysTermsStoreOrUpdateURL = "{{ route('surveysTermsStoreOrUpdateURL') }}";

        var choicesSelectorClass = ".surveys-term-choice";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>

    <script src="{{ URL::asset('build/js/surveys-sortable.js') }}" type="module"></script>
@endsection
