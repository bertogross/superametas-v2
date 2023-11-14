@extends('layouts.master')
@section('title')
    @lang('translation.surveys')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
            @if($data)
                Edição de Modelo <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> <span class="text-theme">{{$data->id}}</span> {{-- limitChars($data->title ?? '', 20) --}}</small>
            @else
                Compor Modelo
            @endif
        @endslot
    @endcomponent

    @include('components.alerts')

    @php
        $templateId = $data->id ?? '';
        $title = $data->title ?? '';
        $description = $data->description ?? '';
        $recurring = $data->recurring ?? '';

        //appPrintR($data);
        //appPrintR($default);
        //appPrintR($custom);
    @endphp

    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @if ($data)
                    <button type="button" class="btn btn-sm btn-label right btn-outline-theme" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Atualizar</button>

                    <button type="button" class="btn btn-sm btn-label right btn-outline-info" id="btn-surveys-clone" tabindex="-1"><i class="ri-file-copy-line label-icon align-middle fs-16 ms-2"></i>Clonar</button>

                    <a href="{{ route('surveyTemplateShowURL', ['id' => $templateId]) }}" class="btn btn-sm btn-label right btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line label-icon align-middle fs-16 ms-2"></i>Visualizar</a>
                @else
                    <button type="button" class="btn btn-sm btn-label right btn-outline-theme" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Salvar</button>
                @endif
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>{{ $data->title ?? 'Formulário' }}</h4>
         </div>
        <div class="card-body">
            <form id="surveyTemplateForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                @csrf
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-3">
                        <div class="p-3 border border-1 border-light rounded">
                            <input type="hidden" name="id" value="{{ $templateId }}">

                            <div class="mb-4">
                                <label for="title" class="form-label">Título:</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ $title }}" maxlength="100" required>
                                <div class="form-text">
                                    Exemplo: Checklist Abertura de Loja
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="date-range-field" class="form-label">Tipo de Recorrência:</label>
                                <select class="form-select" name="recurring" required>
                                    <option disabled {{ !$recurring ?? 'selected' }}>- Selecione -</option>
                                    @foreach ($getSurveyRecurringTranslations as $index => $value)
                                        <option value="{{$index}}" {{ $recurring == $index ? 'selected' : ''}}>{{ $value['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div>
                                <label for="description" class="form-label">Observações:</label>
                                <textarea name="description" class="form-control maxlength" maxlength="1000" id="description" rows="7" maxlength="500">{{ $description }}</textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-5">
                        <div class="p-3 border border-1 border-light rounded">

                            <p class="text-ody mb-0">Composição do Modelo:</p>

                            <div id="nested-compose-area" style="min-height: 250px;">
                                <div class="accordion list-group nested-list nested-receiver">@if ($default || $custom)
                                    @if ($default)
                                        @component('surveys.components.template-form')
                                            @slot('type', 'default')
                                            @slot('data', $default)
                                        @endcomponent
                                    @endif

                                    @if ($custom)
                                        @component('surveys.components.template-form')
                                            @slot('type', 'custom')
                                            @slot('data', $custom)
                                        @endcomponent
                                    @endif
                                @endif</div>

                                <div class="clearfix mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" title="Adicionar Bloco"><i class="ri-folder-add-line align-middle me-1"></i>Adicionar Bloco</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-4">
                        <div id="load-preview" class="p-3 border border-1 border-light rounded"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script>
        var surveysIndexURL = "{{ route('surveysIndexURL') }}";
        var surveyTemplateEditURL = "{{ route('surveyTemplateEditURL') }}";
        var surveyTemplateShowURL = "{{ route('surveyTemplateShowURL') }}";
        var surveysTemplateStoreOrUpdateURL = "{{ route('surveysTemplateStoreOrUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-template.js') }}" type="module"></script>

    <script src="{{ URL::asset('build/js/surveys-sortable.js') }}" type="module"></script>
@endsection
