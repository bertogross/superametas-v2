@extends('layouts.master')
@section('title')
    Análise do Checklist
@endsection
@section('css')
@endsection
@section('content')
    @php
        use App\Models\User;
        //appPrintR($data);
        //appPrintR($analyticCompaniesData);
        //appPrintR($analyticTermsData);

        $title = $data->title;
        $surveyId = $data->id;
        $companies = $data->companies ? json_decode($data->companies, true) : null;

        // Format the output
        $createdAt = request('created_at') ?? null;
        $createdAt = request('created_at') ?? null;
        $createdAt = !$createdAt && $firstDate && $lastDate ? date("d/m/Y", strtotime($firstDate)) . ' até ' . date("d/m/Y", strtotime($lastDate)) : $createdAt;

        /*
        $uniqueCompanyIds = [];
        foreach ($analyticTermsData as $termId => $dates) {
            foreach ($dates as $date => $items) {
                foreach ($items as $item) {
                    $companyId = $item['company_id'];
                    // Use company_id as key to ensure uniqueness
                    $uniqueCompanyIds[$companyId] = true;
                }
            }
        }
        $extractedCompanies = array_keys($uniqueCompanyIds);
        */

        $filterCompanies = request('companies') ?? $companies;

        $templateData = \App\Models\SurveyTemplates::findOrFail($data->template_id);

        $authorId = $templateData->user_id;
        $getAuthorData = getUserData($authorId);
        $authorRoleName = \App\Models\User::getRoleName($getAuthorData['role']);
        $templateName = trim($templateData->title) ? nl2br($templateData->title) : '';
        $templateDescription = trim($templateData->description) ? nl2br($templateData->description) : '';


    @endphp
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

    @if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_CONTROLLERSHIP) )

        <h6 class="text-uppercase mb-3">{{$title}}</h6>

        @if ($templateDescription)

            {!! !empty($templateDescription) ? '<div class="blockquote custom-blockquote blockquote-outline blockquote-dark rounded mt-2 mb-3"><h5>Modelo: '.$templateName.'</h5><p class="text-body mb-2">'.$templateDescription.'</p><footer class="blockquote-footer mt-0">'.$getAuthorData['name'].' <cite title="'.$authorRoleName.'">'.$authorRoleName.'</cite></footer></div>' : '' !!}

        @endif

        @if ( $analyticTermsData || isset($_REQUEST['filter']) )
            <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3; display: block;">
                <form action="{{ route('surveysShowURL', $surveyId) }}" method="get" autocomplete="off">
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

                        <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                            <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                        </div>

                    </div>
                </form>
            </div>
        @endif

        {{--
        <div class="card" style="z-index: 2;">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Lojas</h5>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body pb-0">
                @if ($analyticCompaniesData)
                    @include('surveys.layouts.chart-companies')
                @else
                    @component('components.nothing')
                    @endcomponent
                @endif
            </div>
        </div>

        <div class="card" style="z-index: 2;">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Setores</h5>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body pb-0">
            </div>
        </div>
        --}}

        @if ($analyticTermsData)
            @include('surveys.layouts.chart-terms')
        @else
            @component('components.nothing')
            @endcomponent
        @endif

    @else
        <div class="alert alert-danger">Acesso não autorizado</div>
    @endif
@endsection

@section('script')
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
