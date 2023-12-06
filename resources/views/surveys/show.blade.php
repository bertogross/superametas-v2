@extends('layouts.master')
@section('title')
    Análise das Vistorias
@endsection
@section('css')
@endsection
@section('content')
    @php
        use App\Models\User;
        //appPrintR($data);
        //appPrintR($analyticTermsData);

        $filterCompanies = request('companies', []);

        $templateName = getTemplateNameById($data->template_id);
    @endphp
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot
        @slot('li_1')
            @lang('translation.surveys')
        @endslot
        @slot('title')
            Análise das Vistorias
            <small>
                <i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i>
                #<span class="text-theme me-2">{{$data->id}}</span> {{limitChars($templateName ?? '', 30) }}
            </small>
        @endslot
    @endcomponent

    @if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_CONTROLLERSHIP) )

        @if ($analyticCompaniesData)
            <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3; display: block;">
                <form action="{{ route('surveysShowURL', $data->id) }}" method="get" autocomplete="off">
                    <div class="row g-2">

                        <div class="col-sm-12 col-md col-lg">
                            <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="Período" data-min-date="{{ $firstDate ?? '' }}" data-max-date="{{ $lastDate ?? '' }}" value="{{ request('created_at') ?? '' }}">
                        </div>

                        @if (!empty($companies) && is_array($companies) && count($companies) > 1)
                            <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                                <select class="form-control" data-choices data-choices-removeItem name="companies[]" id="filter-companies" multiple data-placeholder="Loja">
                                    @foreach ($companies as $companyId => $company)
                                        <option {{ in_array($companyId, $filterCompanies) ? 'selected' : '' }} value="{{ $companyId }}">{{ $company['name'] }}</option>
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
                        {{--
                        @slot('url', route('surveysCreateURL'))
                        --}}
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
                @if ($analyticTermsData)
                    @include('surveys.layouts.chart-terms')
                @else
                    @component('components.nothing')
                        {{--
                        @slot('url', route('surveysCreateURL'))
                        --}}
                    @endcomponent
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-danger">Acesso não autorizado</div>
    @endif
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

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
            initFlatpickrRange
        } from '{{ URL::asset('build/js/helpers.js') }}';

        initFlatpickrRange();
        initFlatpickr();
    </script>
@endsection
