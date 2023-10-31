@php
    $userId = getUserData()['id'];

    $totalPercentAccrued = $ndxChartId = 0;

    $getCompaniesAuthorized = getCompaniesAuthorized();
    //APP_print_r($getCompaniesAuthorized);
    $getActiveCompanies = getActiveCompanies();
    //APP_print_r($getActiveCompanies);
    $getDepartmentsActive = getDepartmentsActive();
    //APP_print_r($getDepartmentsActive);

    //$getMeantime = request('meantime', date('Y-m'));
    //$getCustomMeantime = request('getCustomMeantime');

    $getMeantime = !empty($_REQUEST['meantime']) ? $_REQUEST['meantime'] : date('Y-m');
    $getCustomMeantime = !empty($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : '';
    $getMeantime = $getMeantime == 'custom' && empty($getCustomMeantime) ? date('Y-m') : $getMeantime;

    $explode = !empty($getCustomMeantime) ? explode(' até ', $getCustomMeantime) : '';
    $explodeMeantime = !empty($explode) && is_array($explode) && count($explode) > 1 ? $explode : $getCustomMeantime;

    //$filterCompanies = request('companies', array());
    $filterCompanies = isset($_REQUEST['companies']) ? $_REQUEST['companies'] : array();
    //$filterDepartments = request('departments', array());
    $filterDepartments = isset($_REQUEST['departments']) ? $_REQUEST['departments'] : array();

    $metric = metricGoalSales($getMeantime);
    $metricNumber = convertToNumeric($metric);

    $dateRange = getSaleDateRange();
    $firstDate = $dateRange['first_date'];
    $lastDate = $dateRange['last_date'];
    $currentMonth = now()->format('Y-m');
    $previousMonth = now()->subMonth()->format('Y-m');
@endphp

@extends('layouts.master')
@section('title')
    @lang('translation.goal-sales')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/gridjs/theme/mermaid.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/style.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.goal-sales-nav')
        @slot('url')
            {{ route('goalSalesIndexURL') }}
        @endslot
        @slot('title')
            @lang('translation.goal-sales')
        @endslot
        @slot('getMeantime')
            {{ $getMeantime }}
        @endslot
        @slot('getCustomMeantime')
            {{ $getCustomMeantime }}
        @endslot
    @endcomponent

    <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3;">
        <form action="{{ route('goalSalesIndexURL') }}" class="row g-2" autocomplete="off">

            <div class="col-sm-12 col-md-2 col-lg-auto">
                <select class="form-control form-select" name="meantime" title="Selecione o período">
                    <option {{ $getMeantime == 'today' ? 'selected' : '' }} value="today">HOJE</option>

                    <option {{ $getMeantime == $currentMonth || $getMeantime == date('Y-m') || ( $getMeantime == 'custom' && empty($getCustomMeantime) )  ? 'selected' : '' }} value="{{ $currentMonth }}">MÊS ATUAL</option>

                    @if ($firstDate <= $previousMonth)
                        <option {{ $getMeantime == $previousMonth ? 'selected' : '' }} value="{{ $previousMonth }}">MÊS ANTERIOR</option>
                    @endif

                    <option @if($getMeantime == 'custom' && !empty($getCustomMeantime)) selected @endif value="custom">CUSTOMIZADO</option>
                </select>
            </div>

            <div class="col-sm-12 col-md-auto col-lg-auto custom_meantime_is_selected" style="min-width:270px; @if(empty($getCustomMeantime)) display:none; @endif ">
                <input type="text" class="form-control flatpickr-range-month" name="custom_meantime" data-min-date="{{ $firstDate }}"
                data-max-date="{{ $lastDate }}" value="@if($getMeantime == 'custom'){{ $getCustomMeantime}}@endif" placeholder="Selecione o Período">
            </div>

            @if (!empty($getCompaniesAuthorized) && is_array($getCompaniesAuthorized) && count($getCompaniesAuthorized) > 1)
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                    <select class="form-control" data-choices data-choices-removeItem name="companies[]" id="filter-companies" multiple data-placeholder="Loja">
                        @foreach ($getCompaniesAuthorized as $company)
                            <option {{ in_array($company, $filterCompanies) ? 'selected' : '' }} value="{{ $company }}">{{ getCompanyAlias(intval($company)) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (!empty($getDepartmentsActive) && is_object($getDepartmentsActive) && count($getDepartmentsActive) > 1)
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Departamentos selecionados">
                    <select class="form-control" data-choices data-choices-removeItem name="departments[]" multiple data-placeholder="Departamento">
                        @foreach ($getDepartmentsActive as $department)
                            <option {{ in_array($department->department_id, $filterDepartments) ? 'selected' : '' }} value="{{ $department->department_id }}">{{ $department->department_alias }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                <button type="submit" class="btn btn-theme w-100 init-loader" title="Filtrar"><i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
            </div>
        </form>
    </div>

    @if(!$data)
        <div class="alert alert-warning alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-fill label-icon"></i>Não há dados
        </div>
    @endif

    @if (getUserMeta($userId, 'analytic-mode') == 'on')
        @include('goal-sales/analytic')
    @elseif (getUserMeta($userId, 'slide-mode') == 'on')
        @if (count($filterCompanies) == 1 || count($getCompaniesAuthorized) == 1)
            @include('goal-sales/single')
        @else
            @include('goal-sales/slide')
        @endif
    @else
        @if (count($filterCompanies) == 1 || count($getCompaniesAuthorized) == 1)
            @include('goal-sales/single')
        @else
            @include('goal-sales/table')
        @endif
    @endif

@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>


    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>

    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/goal-sales.js') }}" type="module"></script>
@endsection
