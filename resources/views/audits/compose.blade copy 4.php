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

        @include('components.alert-errors')

        @include('components.alert-success')

        <div class="row">
            <div class="col-xxl-9">
                <div class="card">
                    <form id="auditsComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                        @csrf

                        <div class="card-header">
                            <div class="btn-group float-end">
                                <button type="button" class="btn btn-sm btn-outline-theme" id="btn-audits-compose-update">Salvar e Visualizar</button>

                                <a class="btn btn-sm btn-outline-danger btn-delete-audit-model">Deletar</a>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Área do Formulário</h4>
                        </div>

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
                        </div>
                    </form>
                </div>
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
                                                    <input type="text" class="form-control border-0" name="audit_compose[step][]" placeholder="Informe o Título / Setor / Departamento / Etapa" autocomplete="off">
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
                                            <input type="text" class="form-control" name="audit_compose['step'][]['topic'][]" placeholder="Preencha este campo informando o tópico" maxlenght="100">
                                            <div class="form-text text-warning text-opacity-50 small">Exemplo: "Este setor/departamento está organizado?"... "O abastecimento de produtos/insumos está em dia?"</div>
                                        </div>
                                        {!! $buttonDragAndDrop !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script>
        var auditsComposeUpdateURL = "{{ route('auditsComposeUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/audits-compose.js') }}" type="module"></script>
@endsection
