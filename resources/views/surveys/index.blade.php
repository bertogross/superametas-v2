@extends('layouts.master')
@section('title')
    @lang('translation.surveys')
@endsection
@section('css')
@endsection
@section('content')
    {{--
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot

        @slot('title')
            @lang('translation.surveys')
        @endslot
    @endcomponent
    --}}
    <div class="row mb-3">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">@lang('translation.surveys')</h4>
                            <p class="text-muted mb-0">Aqui estão os componentes necessários para suas tarefas de vistoria</p>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <form action="javascript:void(0);">
                                <div class="row g-3 mb-0 align-items-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-soft-theme btn-icon waves-effect waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{--
                <div class="row">
                    @foreach ($getSurveyStatusTranslations as $key => $value)
                    <div class="col">
                        <div class="card card-animate" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="{{ $value['description'] }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">{{ $value['label'] }}</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{ $surveyStatusCount[$key] ?? 0 }}"></span></h2>
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
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-3">
                    @include('surveys.templates.listing')
                </div>

                <div class="col-sm-12 col-md-12 col-lg-8 col-xxl-9">
                    @include('surveys.listing')
                </div>
            </div>
        </div>

        <div class="col-auto layout-rightside-col d-block">
            <div class="overlay"></div>

            <div class="layout-rightside h-100 pb-1">
                <div class="card h-100 rounded-0">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <h6 class="text-muted mb-0 text-uppercase fw-semibold">Atividades Recentes</h6>
                        </div>
                        <div class="p-3">
                            TODO
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        var totalCompanies = {{ !empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) ? count($getAuthorizedCompanies) : 1 }};

        var surveysIndexURL = "{{ route('surveysIndexURL') }}";
        var surveysCreateURL = "{{ route('surveysCreateURL') }}";
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysChangeStatusURL = "{{ route('surveysChangeStatusURL') }}";
        var surveysShowURL = "{{ route('surveysShowURL') }}";
        var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";
        var formSurveyorAssignmentURL = "{{ route('formSurveyorAssignmentURL') }}";
        var formAuditorAssignmentURL = "{{ route('formAuditorAssignmentURL') }}";
        var changeAssignmentSurveyorStatusURL = "{{ route('changeAssignmentSurveyorStatusURL') }}";
        var changeAssignmentAuditorStatusURL = "{{ route('changeAssignmentAuditorStatusURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>
@endsection
