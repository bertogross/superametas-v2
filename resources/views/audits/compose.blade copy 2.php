@extends('layouts.master')
@section('title')
    @lang('translation.audits')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('auditsIndexURL') }}
        @endslot
        @slot('li_1')
            @lang('translation.audits')
        @endslot
        @slot('title')
            Composição
        @endslot
    @endcomponent
        @php
            $buttonRemove = '<button type="button" class="input-group-text btn-remove-element bg-danger-subtle text-danger" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Deletar este Elemento" data-target=".nested-list"><i class="ri-delete-bin-2-line"></i></button>';

            $buttonDragAndDrop = '<span class="input-group-text bg-light-subtle text-body cursor-n-resize" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Mover este Elemento"><i class="ri-arrow-up-down-fill"></i></span>';

            $buttonEdit = '<button type="button" class="input-group-text btn-edit-element bg-info-subtle text-info" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Configurar este Elemento"><i class="ri-settings-4-fill"></i></button>';
        @endphp
        <div class="row">
            <div class="col-xxl-9">
                <div class="card">
                    <form name="auditsComposeForm" id="auditsComposeForm" action="{{ route('auditsComposeSingleURL') }}" method="POST" autocomplete="off" class="needs-validation" novalidate>
                        @csrf
                        <div class="card-header">
                            <div class="btn-group float-end">
                                <button type="input" class="btn btn-sm btn-outline-theme">Salvar e Visualizar</button>

                                <a class="btn btn-sm btn-outline-danger btn-delete-audit-model">Deletar</a>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Área do Formulário</h4>
                        </div><!-- end card header -->

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <p class="text-muted">
                                Esta é a área de composição
                            </p>
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" value="sdfs df" required autocomplete="off">
                                <label for="floatingInput">Título Deste Formulário</label>
                            </div>
                            <div class="form-text">Necessário para posterior identificação do modelo</div>

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-1 p-0 mt-3"></div>

                            {{--
                            @foreach ($getDepartmentsActive as $department)
                                <div class="bg-dark-subtle" data-dep="{{$department->department_id}}" draggable="false">
                                    {{ $department->department_alias }}
                                    <div class="list-group nested-list">
                                        <div class="nested-2">Analytics</div>
                                        <div class="nested-2">CRM</div>
                                    </div>
                                </div>
                            @endforeach
                            --}}
                        </div><!-- end card-body -->
                    </form>
                </div><!-- end card -->
            </div>

            <div class="col-xxl-3">
                <div class="card sticky-top sticky-top-85">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-bring-to-front fs-16 align-middle text-theme me-2"></i>Elementos</h4>
                    </div><!-- end card header -->

                    <div id="nested-elements-area" class="card-body">
                        <p class="text-muted">
                            <i class="float-end text-theme ri-question-line" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left" data-bs-title="Bloco do Formuláro" data-bs-content="Arraste o Bloco do Formulário para depois inserir Conteúdo do Bloco"></i>
                            Aqui estão os elementos de composição
                        </p>

                        <div class="list-group nested-list nested-model">
                            <div class="nested-element">
                                <div class="accordion-item mt-0 mb-3 rounded rounded-1 border-dark p-0">

                                    <div class="nested-receiver-block-fake border border-1 rounded rounded-1 border-dark p-3 bg-light"></div>

                                    <div class="always-this-one-as-first-and-dont-sort mt-0" draggable="false">
                                        <div class="list-group nested-list mt-0">
                                            <div class="accordion-header">
                                                <div class="input-group">
                                                    <input type="text" class="form-control border-0" name="audit_compose[]['title']" placeholder="Informe o Título / Setor / Departamento / Etapa" autocomplete="off">
                                                    <div class="btn-group float-end">
                                                        <button type="button" class="btn btn-sm btn-remove-element bg-dark-subtle text-danger" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Deletar este Bloco?"><i class="ri-delete-bin-2-line" data-target=".nested-model"></i></button>

                                                        <button type="button" class="btn btn-sm bg-dark-subtle text-body cursor-n-resize" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Mover este Bloco"><i class="ri-arrow-up-down-fill"></i></button>

                                                        <button type="button" class="btn btn-sm bg-dark-subtle text-body btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-collapse collapse show nested-receiver-block border-1 rounded-1 border-dashed border-dark mt-0" style="min-height: 100px;"></div>
                                </div>

                            </div>

                            <div class="nested-element-to-block text-center">
                                <div class="list-group nested-list m-1">
                                    <div class="label-element border border-1 rounded rounded-1 border-dark p-3" draggable="false" style="">
                                        <div class=" fs-6">Conteúdo do Bloco</div>
                                        <i class="ri-input-method-line fs-1"></i>
                                        <i class="ri-radio-button-line fs-1"></i>
                                    </div>

                                    <div class="input-group">
                                        {!! $buttonRemove !!}
                                        <div class="form-control">
                                            <input type="text" class="form-control" name="audit_compose[]['title']['topic']"
                                                placeholder="Preencha este campo informando o tópico" maxlenght="100" wfd-id="id21">
                                            <div class="form-text text-warning text-opacity-50 small">Exemplo: "Este setor/departamento está organizado?"... "O abastecimento de produtos/insumos está em dia?"</div>

                                            {{--
                                            <div class="form-check form-witch form-switch-theme form-check-inline">
                                                <input id="form-check-radio-1" type="radio" class="form-check-input" value="1" role="switch">
                                                <label class="form-check-label" for="form-check-radio-1">Conforme</label>
                                            </div>
                                            <div class="form-check form-witch form-switch-danger form-check-inline">
                                                <input id="form-check-radio-0" type="radio" class="form-check-input" value="0" role="switch">
                                                <label class="form-check-label" for="form-check-radio-0">Não Conforme</label>
                                            </div>
                                            }}
                                        </div>

                                        {{--
                                        <div class="form-control" style="max-width: 200px;">
                                            <h6 class="fs-6 text-info text-opacity-75 mb-3">Campos Adicionais</h6>
                                            <!-- Custom Checkboxes Color -->
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="formCheck1">
                                                <label class="form-check-label" for="formCheck1">
                                                    Observações
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="formCheck2">
                                                <label class="form-check-label" for="formCheck2">
                                                    Upload de Fotos
                                                </label>
                                            </div>
                                        </div>
                                        --}}

                                        {!! $buttonDragAndDrop !!}
                                    </div>

                                </div>
                            </div>

                            {{--
                            <div class="row">
                                @php
                                    // Extract the first
                                    $topicElement = $auditElements[0];

                                    // array_shift to Remove the first element because the O is used on the top
                                    array_shift($auditElements);
                                @endphp
                                @foreach ($auditElements as $index => $element)
                                    <div class="col-xxl-6 col-md-12 mb-3 nested-element-to-block text-center">
                                        <div class="{{ $element['type'] == 'radio' ? 'nested-this-can-repeat' : '' }}">
                                                <span class="label-element fs-6">{{ $element['label'] }}</span>

                                            <div class="list-group nested-list">
                                                <i class="label-element {{ $element['icon'] }} fs-1"></i>

                                                @switch($element['type'])
                                                    @case('text')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <span class="input-group-text">
                                                                {{ $element['label'] }}
                                                            </span>
                                                            <input type="text" class="form-control" name="audit_compose[]['{{ $element['name'] }}']" disabled placeholder="{{ $element['placeholder'] }}" maxlenght="{{ $element['maxlenght'] }}">
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break
                                                    @case('textarea')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <span class="input-group-text">
                                                                {{ $element['label'] }}
                                                            </span>
                                                            <textarea class="form-control" name="audit_compose[]['{{ $element['name'] }}']" maxlenght="{{ $element['maxlenght'] }}" placeholder="{{ $element['placeholder'] }}" disabled rows="3"></textarea>
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break

                                                    @case('checkbox')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <span class="input-group-text">
                                                                {{ $element['label'] }}
                                                            </span>
                                                            <div class="form-control">

                                                                <div class="form-check form-check-inline">
                                                                    <input type="checkbox" class="form-check-input" name="audit_compose[]['{{ $element['name'] }}']" disabled>
                                                                    <label class="form-check-label">Marque A</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input type="checkbox" class="form-check-input" name="audit_compose[]['{{ $element['name'] }}']" disabled>
                                                                    <label class="form-check-label">Marque B</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input type="checkbox" class="form-check-input" name="audit_compose[]['{{ $element['name'] }}']" disabled>
                                                                    <label class="form-check-label">Marque C</label>
                                                                </div>
                                                            </div>
                                                            {!! $buttonEdit !!}
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break

                                                    @case('radio')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <span class="input-group-text">
                                                                {{ $element['label'] }}
                                                            </span>
                                                            <div class="form-control">
                                                                <input type="text" class="form-control" name="audit_compose[]['{{ $topicElement['name'] }}']" placeholder="Preencha este campo informando o tópico" maxlenght="{{ $topicElement['maxlenght'] }}">
                                                                <div class="form-text text-warning text-opacity-50 small mb-3">{{ $topicElement['placeholder'] }}</div>

                                                                <div class="form-check form-witch form-switch-theme form-check-inline">
                                                                    <input id="form-check-radio-1" type="radio" class="form-check-input" name="audit_compose[]['{{ $element['name'] }}']" value="1" role="switch" disabled>
                                                                    <label class="form-check-label" for="form-check-radio-1">Conforme</label>
                                                                </div>
                                                                <div class="form-check form-witch form-switch-danger form-check-inline">
                                                                    <input id="form-check-radio-0" type="radio" class="form-check-input" name="audit_compose[]['{{ $element['name'] }}']" value="0" role="switch" disabled>
                                                                    <label class="form-check-label" for="form-check-radio-0">Não Conforme</label>
                                                                </div>
                                                            </div>
                                                            {!! $buttonEdit !!}
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break

                                                    @case('select')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <span class="input-group-text">
                                                                {{ $element['label'] }}
                                                            </span>
                                                            <select class="form-select" name="audit_compose[]['{{ $element['name'] }}']" placeholder="{{ $element['placeholder'] }}">
                                                                <option value="" selected>{{ $element['placeholder'] }}</option>
                                                                <option value="1">Item 1</option>
                                                                <option value="2">Item 2</option>
                                                                <option value="3">Item 3</option>
                                                            </select>
                                                            {!! $buttonEdit !!}
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break

                                                    @case('file')
                                                        <div class="input-group">
                                                            {!! $buttonRemove !!}
                                                            <input type="file" class="form-control" name="audit_compose[]['{{ $element['name'] }}']" placeholder="{{ $element['placeholder'] }}" disabled>
                                                            <span class="input-group-text">{{ $element['label'] }}</span>
                                                            {!! $buttonDragAndDrop !!}
                                                        </div>
                                                        @break
                                                @endswitch

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
        </div>

    @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/audits.js') }}" type="module"></script>
@endsection
