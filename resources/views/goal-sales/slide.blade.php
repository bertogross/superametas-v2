@section('css')
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none bg-light bg-opacity-25 @if (!$data) d-none @endif">
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

                                // Ensure sales and goal are numbers
                                $sales = floatval($values['sales'] ?? 0);
                                $goal = floatval($values['goal'] ?? 0);

                                // Calculate percentage, ensuring not to divide by zero
                                $percent = $goal > 0 ? ($sales / $goal) * 100 : 0;

                                // Calculate accrued percent, ensuring not to divide by zero and that $metric is numeric
                                $percentAccrued = ($percent > 0 && is_numeric($metric) && $metric > 0) ? ($percent / $metric) * 100 : 0;

                                // Initialize total sales and goals for each company if not already set
                                $totalSales[$companyId] = $totalSales[$companyId] ?? 0;
                                $totalGoals[$companyId] = $totalGoals[$companyId] ?? 0;

                                // Add current sales and goals to the total
                                $totalSales[$companyId] += $sales;
                                $totalGoals[$companyId] += $goal;

                                // Calculate total percent, ensuring not to divide by zero
                                $totalPercent[$companyId] = $totalGoals[$companyId] > 0 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                            @endphp
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2 m-4 text-center text-uppercase">
                                @php
                                    // Use number_format for display, not for calculations
                                    echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), number_format($percent, 2), number_format($percentAccrued, 2));
                                @endphp
                                <div class="chart-label fw-bold">{{ getDepartmentAlias($departmentId) }}</div>
                            </div>
                        @endforeach

                        @if (count($getActiveDepartments) > 1)
                            <div class="col-12 m-4 text-center text-uppercase @if (!empty($filterDepartments) && count($filterDepartments) == 1) d-none @endif">
                                @php
                                $ndxChartId++;

                                // Calculate total percent value and accrued percent, ensuring not to divide by zero and that $metric is numeric
                                $totalPercentValue = $totalGoals[$companyId] > 0 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                                $totalPercentAccrued = (is_numeric($metric) && $metric > 0) ? ($totalPercentValue / $metric) * 100 : 0;

                                // Use number_format for display, not for calculations
                                echo goalsEmojiChart($ndxChartId, number_format($totalGoals[$companyId], 2), number_format($totalSales[$companyId], 2), 'general', 'Geral', getCompanyAlias($companyId), number_format($totalPercentValue, 2), number_format($totalPercentAccrued, 2), 'general');
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
    // Initialize Swiper
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
