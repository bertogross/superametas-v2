@php
    use App\Models\User;

    $currentUserId = auth()->id();

    $totalPercentAccrued = $ndxChartId = 0;

    $getAuthorizedCompanies = getAuthorizedCompanies();
    $getActiveCompanies = getActiveCompanies();
    $getActiveDepartments = getActiveDepartments();

    $getMeantime = request('meantime', date('Y-m'));
    $getCustomMeantime = request('custom_meantime', '');

    // Ensure 'meantime' is a valid value
    $validMeantimes = ['today', 'custom', date('Y-m'), now()->subMonth()->format('Y-m')];
    if (!in_array($getMeantime, $validMeantimes)) {
        $getMeantime = date('Y-m');
    }

    $getMeantime = $getMeantime === 'custom' && empty($getCustomMeantime) ? date('Y-m') : $getMeantime;

    $explode = !empty($getCustomMeantime) ? explode(' até ', $getCustomMeantime) : [];
    $explodeMeantime = count($explode) === 2 ? $explode : $getCustomMeantime;

    $filterCompanies = request('companies', []);
    $filterDepartments = request('departments', []);

    $metric = metricGoalSales($getMeantime);
    $metricNumber = convertToNumeric($metric);

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
@endsection

@section('content')
    @component('goal-sales.components.nav')
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

            @if (!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 1)
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                    <select class="form-control" data-choices data-choices-removeItem name="companies[]" id="filter-companies" multiple data-placeholder="Loja">
                        @foreach ($getAuthorizedCompanies as $company)
                            <option {{ in_array($company, $filterCompanies) ? 'selected' : '' }} value="{{ $company }}">{{ getCompanyNameById($company) }}</option>
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

            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader" title="Filtrar"><i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
            </div>
        </form>
    </div>

    @if( !$data )
        @component('components.nothing')
            {{--
            @slot('url', route('surveysCreateURL'))
            --}}
        @endcomponent
    @else
        @if (getUserMeta($currentUserId, 'analytic-mode') == 'on')
            @include('goal-sales/analytic')
        @elseif (getUserMeta($currentUserId, 'slide-mode') == 'on')
            @if (count($filterCompanies) == 1 || count($getAuthorizedCompanies) == 1)
                @include('goal-sales/single')
            @else
                @include('goal-sales/slide')
            @endif
        @else
            @if (count($filterCompanies) == 1 || count($getAuthorizedCompanies) == 1)
                @include('goal-sales/single')
            @else
                @include('goal-sales/table')
            @endif
        @endif
    @endif

@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>

    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        var goalSalesStoreOrUpdateURL = "{{ route('goalSalesStoreOrUpdateURL') }}";
        var goalSalesEditURL = "{{ route('goalSalesEditURL') }}";
        var goalSalesSettingsEditURL = "{{ route('goalSalesSettingsEditURL') }}";
        var goalSalesAnalyticModeURL = "{{ route('goalSalesAnalyticModeURL') }}";
        var goalSalesSlideModeURL = "{{ route('goalSalesSlideModeURL') }}";
        var goalSalesDefaultModeURL = "{{ route('goalSalesDefaultModeURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/goal-sales.js') }}" type="module"></script>

    @if (auth()->user()->hasAnyRole(User::ROLE_OPERATIONAL))
        <script>
            // Auto refresh page
            setInterval(function() {
                window.location.reload();// true to cleaning cache
            }, 600000); // 600000 milliseconds = 10 minutes
        </script>
    @endif

@endsection
