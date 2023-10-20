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
        @slot('title')
            @lang('translation.goal-sales')
        @endslot
    @endcomponent

    @php
        use App\Models\User;

        $getAuthorizedCompanies = getAuthorizedCompanies();
        //APP_print_r($getAuthorizedCompanies);
        $getActiveCompanies = getActiveCompanies();
        //APP_print_r($getActiveCompanies);
        $getActiveDepartments = getActiveDepartments();
        //APP_print_r($getActiveDepartments);

        $getMeantime = isset($_REQUEST['meantime']) ? $_REQUEST['meantime'] : '';

        $getCustomMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : '';

        $filterCompanies = isset($_REQUEST['companies']) ? $_REQUEST['companies'] : array();
        $filterDepartments = isset($_REQUEST['departments']) ? $_REQUEST['departments'] : array();
        $customMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : date('Y-m');

    @endphp

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

                    <option {{ $getMeantime == $currentMonth || empty($getMeantime) || ( $getMeantime == 'custom' && empty($getCustomMeantime) )  ? 'selected' : '' }} value="{{ $currentMonth }}">MÊS ATUAL</option>

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


    <div class=" mb-4 rounded position-relative wrap-filter-result toogle_zoomInOut ribbon-box border ribbon-fill shadow-none" id="load-listing">
        <div class="ribbon ribbon-info bg-theme text-black fs-12 @if(empty($result) ) d-none @endif" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
            @php
                echo metricGoalSales();
            @endphp
        </div>
        <div class="table-responsive mb-0">
            <table id="goals_sales-dataTable" class="table table-striped-columns table-nowrap listing-chart mb-0">
                <thead class="text-uppercase table-light">
                    <tr>
                        <th scope="col" class="bg-transparent fs-20 text-center invisible"></th>
                        @foreach ($companies as $company)
                            <th scope="col" class="text-center" data-emp-id="{{ $company }}">
                                {{ getCompanyAlias(intval($company)) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result as $row)
                        <tr tr-department="{{ $row['department_id'] }}" class="">
                            <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                                {{ getDepartmentAlias(intval($row['department_id'])) }}
                            </th>
                            @foreach ($companies as $company)
                                <td class="text-center align-middle" data-emp-id="{{ $company }}" data-chart-id="0">
                                    {{ $row[$company] }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-uppercase table-light">
                    <tr>
                        <th scope="col" class="bg-transparent invisible"></th>
                        @foreach ($companies as $company)
                            <th scope="col" class="text-center" data-emp-id="{{ $company }}">
                                {{ getCompanyAlias(intval($company)) }}
                            </th>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!--end row-->
@endsection
@section('script')

<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
<script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>

<script src="{{ URL::asset('build/js/goal-sales.js') }}" type="module"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
