@php
    use App\Models\User;
    //appPrintR($data);
    //appPrintR($analyticTermsData);

    $capabilities = getUserData(auth()->id())['capabilities'] ?? null;

    $getSurveyRecurringTranslations = \App\Models\Survey::getSurveyRecurringTranslations();

    $title = $data->title;
    $surveyId = $data->id;
    $companies = $data->companies ? json_decode($data->companies, true) : [];

    $recurring = $data->recurring;
    $recurringLabel = $getSurveyRecurringTranslations[$recurring]['label'];

    // Format the output
    $createdAt = request('created_at') ?? null;
    $createdAt = request('created_at') ?? null;
    $createdAt = !$createdAt && $firstDate && $lastDate ? date("d/m/Y", strtotime($firstDate)) . ' até ' . date("d/m/Y", strtotime($lastDate)) : $createdAt;

    $startAt = $data->start_at ?? null;
    $startAt = $startAt ? date("d/m/Y", strtotime($startAt)) : '';

    $endIn = $data->end_in ?? null;
    $endIn = $endIn ? date("d/m/Y", strtotime($endIn)) : 'Data Indefinida';

    $filterCompanies = request('companies') ?? $companies;

    $templateData = \App\Models\SurveyTemplates::findOrFail($data->template_id);

    $authorId = $templateData->user_id;
    $getAuthorData = getUserData($authorId);
    $authorRoleName = \App\Models\User::getRoleName($getAuthorData['role']);
    $templateName = trim($templateData->title) ? nl2br($templateData->title) : '';
    $templateDescription = trim($templateData->description) ? nl2br($templateData->description) : '';

    $delegation = \App\Models\SurveyAssignments::getAssignmentDelegatedsBySurveyId($surveyId);

    //Reorganize the analyticTermsData to separate companies
    $companiesAnalyticTermsData = [];
    foreach ($analyticTermsData as $termId => $dates) {
        foreach ($dates as $date => $records) {
            foreach ($records as $record) {
                $companyId = $record['company_id'];
                if (!isset($companiesAnalyticTermsData[$companyId])) {
                    $companiesAnalyticTermsData[$companyId] = [];
                }
                $companiesAnalyticTermsData[$companyId][$termId][$date][] = $record;
            }
        }
    }
    asort($companiesAnalyticTermsData);
@endphp
@extends('layouts.master')
@section('title')
    Análise do Checklist
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
            Análise do Checklist
            <small>
                <i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i>
                {{limitChars($title ?? '', 30) }} #<span class="text-theme me-2">{{$surveyId}}</span>
            </small>
        @endslot
    @endcomponent

    @if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_CONTROLLERSHIP) || ( $capabilities && is_array($capabilities) && in_array('audit', $capabilities) ) )

        {{--
        <h6 class="text-uppercase mb-3">{{$title}}</h6>

        <h5>Modelo: '.$templateName.'</h5>
        --}}
        @if ($templateDescription)
            {!! !empty($templateDescription) ? '<div class="blockquote custom-blockquote blockquote-outline blockquote-dark rounded mt-2 mb-3"><h5 class="text-uppercase">'.$title.'</h5><p class="text-body mb-2">'.$templateDescription.'</p><footer class="blockquote-footer mt-0">'.$getAuthorData['name'].' <cite title="'.$authorRoleName.'">'.$authorRoleName.'</cite></footer></div>' : '' !!}
        @endif

        @if ( $analyticTermsData || isset($_REQUEST['filter']) )
            <div class="row">
                <div class="col-sm-12 col-md mb-4">
                    <div id="filter" class="p-3 bg-light-subtle rounded position-relative" style="z-index: 3; display: block;">
                        <form action="{{ route('surveysShowURL', $surveyId) }}" method="get" autocomplete="off" class="mb-0">
                            <div class="row g-2">

                                <div class="col-sm-12 col-md col-lg">
                                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="- Período -" data-min-date="{{ $firstDate ?? '' }}" data-max-date="{{ $lastDate ?? '' }}" value="{{$createdAt}}">
                                </div>

                                @if (!empty($companies) && is_array($companies) && count($companies) > 1)
                                    <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                                        <select class="form-control filter-companies" name="companies[]" multiple data-placeholder="- Loja -">
                                            @foreach ($companies as $companyId)
                                                <option {{ in_array($companyId, $filterCompanies) ? 'selected' : '' }} value="{{ $companyId }}">{{ getCompanyNameById($companyId) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">  {{-- d-none --}}
                                    <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader">
                                        <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-12 col-md-auto mb-4">
                    <div class="p-3 bg-light-subtle rounded position-relative">
                        <button type="button"
                            @if(count($filterCompanies) > 1)
                                id="btn-surveys-swap-toggle"
                            @else
                                onclick="alert('Esta ação requer dados de duas ou mais Unidades')"
                            @endif
                            class="btn btn-{{!$swapData ? 'soft-' : ''}}theme waves-effect w-100"
                            data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left" data-bs-title="Ativar/Desativar o Secionamento" data-bs-content="O <strong>Secionamento</strong> quando ativo permite visualizar os dados independentementes de cada das Unidades.
                            <br><br>{{$swapData ? '<span class="text-success">Secionamento Ativo</span>' : '<span class="text-danger">Secionamento Inativo</span>'}}">
                            <i class="ri-swap-box-line me-1 align-bottom"></i> Secionamento
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title text-uppercase mb-0 flex-grow-1">Aspectos</h4>
                    </div>
                    <div class="card-body h-100">
                        <div class="hstack gap-3 flex-wrap">
                            <div data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="O tipo de repetição">
                                Recorrência: {{$recurringLabel}}
                            </div>
                            <div class="vr"></div>

                            <div data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="A data da primeira interação">
                                Início: {{$startAt}}
                            </div>
                            <div class="vr"></div>

                            <div data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="A data da última interação">
                                Fim: {{$endIn}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title text-uppercase mb-0 flex-grow-1">Atribuições</h4>
                    </div>
                    <div class="card-body h-100 pb-0">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-3">
                                Vistoria:
                                @if (isset($delegation['surveyors']) && !empty($delegation['surveyors']))
                                    @foreach ($delegation['surveyors'] as $key => $value)
                                        @php
                                            $userId = $value['user_id'] ?? null;
                                            $getUserData = $userId ? getUserData($userId) : null;
                                            $companyId = $value['company_id'] ?? null;
                                            $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                        @endphp
                                        @if($userId)
                                            <a href="{{ route('profileShowURL', $userId) }}" class="avatar-group-item ms-2" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Vistoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                                <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                            <div class="col-sm-12 col-md-6 mb-3">
                                Auditoria:
                                @if (isset($delegation['auditors']) && !empty($delegation['auditors']))
                                    @foreach ($delegation['auditors'] as $key => $value)
                                        @php
                                            $userId = $value['user_id'] ?? null;
                                            $getUserData = $userId ? getUserData($userId) : null;
                                            $companyId = $value['company_id'] ?? null;
                                            $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                        @endphp
                                        @if($userId)
                                            <a href="{{ route('profileShowURL', $userId) }}" class="avatar-group-item ms-2" data-img="{{ $getUserData['avatar'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Auditoria: {{ $getUserData['name'] }} : {{ $companyName }}">
                                                <img src="{{ $getUserData['avatar'] }}" alt="" class="rounded-circle avatar-xxs">
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            @if ( $swapData && count($filterCompanies) > 1 )
                <div class="fs-6 text-uppercase text-center mb-3">
                    Dados <span class="text-theme">Secionados</span> :
                    @foreach ($filterCompanies as $companyId)
                    @php
                        $exists = $companiesAnalyticTermsData[$companyId] ?? null;
                    @endphp
                    <span class="badge bg-dark-subtle {{ !$exists ? 'text-danger' : 'text-body'}} badge-border ms-2" {!! !$exists ? 'data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não há dados"' : '' !!}>
                        {{getCompanyNameById($companyId)}}
                    </span>
                    @endforeach
                </div>

                <div class="p-3">
                    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-theme mb-0 sticky-top sticky-top-70 bg-body" role="tablist">
                        @php
                            $index = 0;
                        @endphp
                        @foreach ($filterCompanies as $companyId)
                            @php
                                $exists = $companiesAnalyticTermsData[$companyId] ?? null;
                            @endphp
                            <li class="nav-item" role="presentation" {!! !$exists ? 'data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não há dados"' : '' !!}>
                                <a class="nav-link {{ $index == 0 ? 'active' : ''}} {{ !$exists ? 'no-data' : '' }}" data-bs-toggle="tab" href="#nav-border-justified-{{$companyId}}" role="tab" {{ $index > 0 ?? 'aria-selected="true"' }}>
                                    {{getCompanyNameById($companyId)}}
                                    {!! !$exists ? '<i class="ri-close-circle-line text-danger align-top fs-14 ms-2"></i>' : '' !!}
                                </a>
                            </li>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                    </ul>
                    <div class="tab-content border border-1 border-light p-3">
                        @php
                            $index = 0;
                        @endphp
                        {{--
                        @foreach ($companiesAnalyticTermsData as $companyId => $analyticTermsData)
                            <div class="tab-pane {{ $index == 0 ? 'active' : ''}}" id="nav-border-justified-{{$companyId}}" role="tabpanel">
                                <div class="row">
                                    @include('surveys.layouts.chart-terms', ['companyId' => $companyId, 'tabMode' => true])

                                    @include('surveys.layouts.chart-calendar')
                                </div>
                            </div>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                        --}}
                        @foreach ($filterCompanies as $companyId)
                            <div class="tab-pane {{ $index == 0 ? 'active' : ''}}" id="nav-border-justified-{{$companyId}}" role="tabpanel">
                                @if (isset($companiesAnalyticTermsData[$companyId]))
                                    <div class="row">
                                        @include('surveys.layouts.chart-terms', ['analyticTermsData' => $companiesAnalyticTermsData[$companyId], 'companyId' => $companyId, 'tabMode' => true])

                                        @include('surveys.layouts.chart-calendar', ['analyticTermsData' => $companiesAnalyticTermsData[$companyId], 'companyId' => $companyId, 'tabMode' => true])
                                    </div>
                                @else
                                    @component('components.nothing')
                                        @slot('text', 'Não foram realizadas Vistorias no período selecionado')
                                    @endcomponent
                                @endif
                            </div>

                            @php
                                $index++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            @else
                @if ($analyticTermsData)
                    @if (count($filterCompanies) > 1)
                        <div class="fs-6 text-uppercase text-center mb-3">
                            Dados <span class="text-theme">Globais</span> :
                            @foreach ($filterCompanies as $companyId)
                                <span class="badge bg-dark-subtle text-body badge-border ms-2">{{getCompanyNameById($companyId)}}</span>
                            @endforeach
                        </div>
                    @endif

                    @include('surveys.layouts.chart-terms')

                    @include('surveys.layouts.chart-calendar')
                @else
                    @component('components.nothing')
                    @endcomponent
                @endif
            @endif

        </div>

    @else
        <div class="alert alert-danger">Acesso autorizado somente aos usuários de Nível Controladoria ou Auditoria</div>
    @endif
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/surveys.js') }}?v={{env('APP_VERSION')}}" type="module"></script>

    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/fullcalendar/index.global.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        const userAvatars = @json($userAvatars);
    </script>

    <script type="module">
        import {
            initFlatpickr,
        } from '{{ URL::asset('build/js/helpers.js') }}';

        initFlatpickr();
    </script>
@endsection
