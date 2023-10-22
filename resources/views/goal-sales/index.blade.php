@php
use App\Models\User;

$getAuthorizedCompanies = getAuthorizedCompanies();
//APP_print_r($getAuthorizedCompanies);
$getActiveCompanies = getActiveCompanies();
//APP_print_r($getActiveCompanies);
$getActiveDepartments = getActiveDepartments();
//APP_print_r($getActiveDepartments);

$getMeantime = isset($_REQUEST['meantime']) ? $_REQUEST['meantime'] : date('Y-m');

$getCustomMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : '';
$explodeCustomMeantime = $getCustomMeantime ? explode(' até ', $getCustomMeantime) : '';

$filterCompanies = isset($_REQUEST['companies']) ? $_REQUEST['companies'] : array();
$filterDepartments = isset($_REQUEST['departments']) ? $_REQUEST['departments'] : array();
$customMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : date('Y-m');

@endphp

@extends('layouts.master')
@section('title')
    @lang('translation.goal-sales')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/gridjs/theme/mermaid.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/style.css') }}" rel="stylesheet">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ url('/') }}
        @endslot
        @slot('li_1')
            @lang('translation.dashboards')
        @endslot
        @slot('title')
            @lang('translation.goal-sales')
        @endslot
    @endcomponent

    {{--
        @if ($getMeantime == 'today')
            Hoje, {{ date("d/m/Y") }}
        @elseif ($getCustomMeantime && is_array($explodeCustomMeantime) && count($explodeCustomMeantime) == 2)
            {{ ucfirst(strftime("%B/%Y", strtotime($explodeCustomMeantime[0]))) }}
            <i class="ri-arrow-left-right-line text-theme me-1 ms-1 align-middle small"></i>
            {{ ucfirst(strftime("%B/%Y", strtotime($explodeCustomMeantime[1]))) }}
        @elseif ($getCustomMeantime && !is_array($explodeCustomMeantime))
            {{ ucfirst(strftime("%B/%Y", strtotime($getCustomMeantime))) }}
        @else
            {{ ucfirst(strftime("%B/%Y", strtotime($getMeantime))) }}
        @endif
    --}}

    <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3;">
        <form id="filterForm" action="{{ route('goal-sales.index') }}" class="row g-2 text-uppercase" autocomplete="off">

            <div class="col-sm-12 col-md-2 col-lg-auto">
                <select class="form-control form-select" name="meantime" title="Selecione o período">
                    <option {{ $getMeantime == 'today' ? 'selected' : '' }} value="today">HOJE</option>

                    @php
                        $dateRange = getSaleDateRange();
                        //APP_print_r($dateRange);
                        $firstDate = $dateRange['first_date'];
                        $lastDate = $dateRange['last_date'];
                        $currentMonth = now()->format('Y-m');
                        $previousMonth = now()->subMonth()->format('Y-m');

                    @endphp

                    <option {{ $getMeantime == $currentMonth || $getMeantime == date('Y-m') || ( $getMeantime == 'custom' && empty($getCustomMeantime) )  ? 'selected' : '' }} value="{{ $currentMonth }}">MÊS ATUAL</option>

                    @if ($firstDate <= $previousMonth)
                        <option {{ $getMeantime == $previousMonth ? 'selected' : '' }} value="{{ $previousMonth }}">MÊS ANTERIOR</option>
                    @endif

                    <option @if($getMeantime == 'custom' && !empty($getCustomMeantime)) selected @endif value="custom">CUSTOMIZADO</option>
                </select>
            </div>

            <div class="col-sm-12 col-md-auto col-lg-auto custom_meantime_is_selected" style="min-width:270px; @if(empty($getCustomMeantime)) display:none; @endif ">
                <input type="text" class="form-control flatpickr-range-month" name="custom_meantime" data-min-date="{{ $firstDate }}"
                data-max-date="{{ $lastDate }}" value="@if($getMeantime == 'custom'){{ $customMeantime}}@endif" placeholder="Selecione o Período">
            </div>

            @if (!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 1)
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                    <select class="form-control" data-choices data-choices-removeItem name="companies[]" multiple data-placeholder="Loja">
                        @foreach ($getAuthorizedCompanies as $company)
                            <option {{ in_array($company, $filterCompanies) ? 'selected' : '' }} value="{{ $company }}">{{ getCompanyAlias(intval($company)) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (!empty($getActiveDepartments) && is_object($getActiveDepartments) && count($getActiveDepartments) > 1)
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Departamentos selecionados">
                    <select class="form-control" data-choices data-choices-removeItem name="departments[]" multiple data-placeholder="Departamento">
                        @foreach ($getActiveDepartments as $department)
                            <option {{ in_array($department->department_id, $filterDepartments) ? 'selected' : '' }} value="{{ $department->department_id }}">{{ $department->department_alias }}</option>
                        @endforeach
                    </select>
            </div>
            @endif

            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btne">
                <button type="submit" class="btn btn-theme w-100 init-loader" title="Filtrar">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- resources/views/goal-sales_table.blade.php -->
    @include('goal-sales/table')

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>

    <script src="{{ URL::asset('build/js/goal-sales.js') }}" type="module"></script>
@endsection
