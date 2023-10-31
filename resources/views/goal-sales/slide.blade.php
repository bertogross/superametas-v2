@section('css')
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none @if (!$data) d-none @endif">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 @if(empty($data)) d-none @endif" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        {{ $metric . '%' }}
    </div>

    <div class="swiper pagination-scrollbar-swiper">
        <div class="swiper-wrapper">
            @foreach ($data as $companyId => $departments)
                <div class="swiper-slide">
                    <h2 class="text-center m-2 text-theme">{{ getCompanyAlias($companyId) }}</h2>
                    <hr class="mt-0">
                    <div class="row listing-chart">
                        @foreach ($departments as $departmentId => $values)
                            @php
                                $ndxChartId++;

                                $sales = floatval($values['sales'] ?? 0);
                                $goal = floatval($values['goal'] ?? 0);

                                $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                                $percentAccrued = $percent > 0 && $metric > 0 ? ($percent/$metric) * 100 : 0;

                                // Calculate the sum of sales and goals for each company
                                $totalSales[$companyId] = floatval($totalSales[$companyId] ?? 0) + $sales;
                                $totalGoals[$companyId] = floatval($totalGoals[$companyId] ?? 0) + $goal;

                                $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                            @endphp
                            <div class="col m-4 text-center text-uppercase">
                                {{--
                                <div>Sales: {{ numberFormat($sales, 2) }}</div>
                                <div>Goals: {{ numberFormat($goal, 2) }}</div>
                                <div>Percent: {{ numberFormat($percent, 2) }}</div>
                                --}}
                                @php
                                    echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $percent, $percentAccrued);
                                @endphp
                                <div class="chart-label fw-bold">{{ getDepartmentAlias($departmentId) }}</div>
                            </div>
                        @endforeach

                        @if (count($getDepartmentsActive) > 1)
                            <div class="col-12 m-4 text-center text-uppercase @if (!empty($filterDepartments) && count($filterDepartments) == 1)  d-none @endif">
                                {{--
                                <div>Sales: {{ numberFormat($totalSales[$companyId], 2) }}</div>
                                <div>Goals: {{ numberFormat($totalGoals[$companyId], 2) }}</div>
                                <div>Percent: {{ numberFormat($totalPercentValue, 2) }}</div>
                                --}}
                                @php
                                $ndxChartId++;

                                //APP_print_r($totalPercent);
                                $totalPercentValue = numberFormat($totalPercent[$companyId], 2);
                                $totalPercentAccrued = ($totalPercentValue / $metric) * 100;

                                echo goalsEmojiChart($ndxChartId, numberFormat($totalGoals[$companyId], 2), numberFormat($totalSales[$companyId], 2), 'general', 'Geral', getCompanyAlias($companyId), $totalPercentValue, $totalPercentAccrued, 'general');
                                @endphp
                                <div class="chart-label fw-bold fs-4">Geral</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-scrollbar"></div>
    </div>
</div>

@section('script-bottom')
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script>
    // Scrollbar Swiper
    var swiper = new Swiper(".pagination-scrollbar-swiper", {
        loop: true,
        autoplay: {
            delay: 10000,
            disableOnInteraction: false,
        },
        scrollbar: {
            el: ".swiper-scrollbar",
            hide: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        }
    });
    </script>
@endsection
