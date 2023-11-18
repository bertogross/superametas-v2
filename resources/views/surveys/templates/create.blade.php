@extends('layouts.master')
@section('title')
    @lang('translation.surveys')
@endsection
@section('css')
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
                Compor Modelo de Formulário
            @endif
        @endslot
    @endcomponent

    @include('components.alerts')

    @php
        //appPrintR($data);
        $templateId = $data->id ?? '';
        $title = $data->title ?? '';
        $description = $data->description ?? '';
    @endphp

    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @if ($data)
                    <button type="button" class="btn btn-sm btn-label right btn-outline-theme" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Atualizar</button>

                    {{--
                    <button type="button" class="btn btn-sm btn-label right btn-outline-info" id="btn-surveys-clone" tabindex="-1"><i class="ri-file-copy-line label-icon align-middle fs-16 ms-2"></i>Clonar</button>
                    --}}

                    <a href="{{ route('surveyTemplateShowURL', ['id' => $templateId]) }}" class="btn btn-sm btn-label right btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line label-icon align-middle fs-16 ms-2"></i>Visualizar</a>
                @else
                    <button type="button" class="btn btn-sm btn-label right btn-outline-theme" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Salvar</button>
                @endif
            </div>
            <h4 class="card-title mb-0"><i class="ri-survey-line fs-16 align-middle text-theme me-2"></i>{{ $data->title ?? 'Formulário' }}</h4>
         </div>
        <div class="card-body">
            <form id="surveyTemplateForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                @csrf
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-6">
                        <div class="p-3">
                            <p class="text-body mb-4">Composição do Modelo:</p>

                            <input type="hidden" name="id" value="{{ $templateId }}">

                            <div class="mb-4">
                                <label for="title" class="form-label">Título:</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ $title }}" maxlength="100" required>
                                <div class="form-text">Exemplo: Checklist Abertura de Loja</div>
                            </div>

                            <div>
                                <label for="description" class="form-label">Descrição:</label>
                                <textarea name="description" class="form-control maxlength" maxlength="1000" id="description" rows="7" maxlength="500" placeholder="Descreva, por exemplo, a funcionalidade ou destino deste modelo...">{{ $description }}</textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                            <hr class="w-50 start-50 position-relative translate-middle-x clearfix mt-4 mb-4">

                            <div id="nested-compose-area" style="min-height: 250px;">
                                <div class="accordion list-group nested-list nested-sortable-block">@if ($result)
                                    @include('surveys.templates.form', ['data' => $result] )
                                @endif</div>

                                <div class="clearfix mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-theme btn-label right float-end" data-bs-toggle="modal" data-bs-target="#addStepModal" tabindex="-1" title="Adicionar Etapa/Setor/Departamento"><i class="ri-terminal-window-line label-icon align-middle fs-16 ms-2"></i>Adicionar</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-6">
                        <p class="text-body mb-1 p-3">Pré-visualização:</p>
                        <div id="load-preview" class="p-3 border border-1 border-light rounded"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="addStepModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-right">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalgridLabel">Termos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="load-terms-form">
                        @include('surveys.terms.form', ['terms' => $terms] )
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')

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

        var surveysTermsStoreOrUpdateURL = "{{ route('surveysTermsStoreOrUpdateURL') }}";
        var surveysTermsFormURL = "{{ route('surveysTermsFormURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys-templates.js') }}" type="module"></script>

    <script src="{{ URL::asset('build/js/surveys-sortable.js') }}" type="module"></script>
@endsection