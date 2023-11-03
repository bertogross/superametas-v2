@extends('layouts.master')
@section('title')
    Composição
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('auditsComposeIndexURL') }}
        @endslot
        @slot('li_1')
            Composições
        @endslot
        @slot('title')
            @if($compose)
                Edição de Formulário
            @else
                Compor Formulário
            @endif
        @endslot
    @endcomponent

        @include('components.alert-errors')

        @include('components.alert-success')

        <div class="row mb-3">
            <div class="col-md-12 col-lg-6 col-xxl-7">
                <div class="card h-100">
                    <form id="auditsComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                        @csrf

                        <input type="hidden" name="id" value="{{ $compose->id ?? ''}}">

                        <div class="card-header">
                            <div class="float-end">
                                @if ($compose)
                                    <button type="button" class="btn btn-sm btn-outline-theme" id="btn-audits-compose-store-or-update" tabindex="-1">Atualizar</button>

                                    @if ($compose->status == 'active')
                                        <button type="button" class="btn btn-sm btn-outline-warning" id="btn-audits-compose-toggle-status" data-status-to="disabled" data-compose-id="{{ $compose->id }}" tabindex="-1">Desativar</button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-audits-compose-toggle-status" data-status-to="active" data-compose-id="{{ $compose->id }} tabindex="-1">Ativar</a>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-audits-compose-clone" tabindex="-1">Clonar</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-theme" id="btn-audits-compose-store-or-update" tabindex="-1">Salvar</button>
                                @endif
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
                        </div>

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <p class="text-muted">
                                Esta é a área de composição
                            </p>
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" value="{{ $compose ? $compose->title : '' }}" required autocomplete="off" maxlength="100">
                                <label for="floatingInput">Título do Formulário</label>
                            </div>
                            <div class="form-text">Título é necessário para que, quando na listagem, você facilmente identifique este modelo</div>

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-2 p-0 mt-3">@if ($jsondata)
                                @foreach ($jsondata as $stepIndex => $step)
                                    @php
                                        $data = $step['stepData'];
                                        $stepName = $data['step_name'] ?? '';
                                        $originalPosition = $data['original_position'] ?? '';
                                        $originalIndex = intval($originalPosition);
                                        $newPosition = $data['new_position'] ?? '';
                                    @endphp
                                    <div id="{{ $originalIndex }}" class="accordion-item block-item mt-0 mb-3 border-dark p-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="[{{ $originalIndex }}]['stepData']['step_name']" value="{{ $stepName }}" autocomplete="off" maxlength="100" required>

                                            <input type="hidden" name="[{{ $originalIndex }}]['stepData']['original_position']" value="{{ $originalPosition }}" tabindex="-1">
                                            <input type="hidden" name="[{{ $originalIndex }}]['stepData']['new_position']" value="{{ $newPosition }}" tabindex="-1">

                                            <span class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

                                            <span class="btn btn-outline-light btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
                                        </div>
                                        <div class="accordion-collapse collapse show">
                                            <div class="nested-receiver-block border-1 border-dashed border-dark mt-0 p-1 rounded-0">@if (isset($step['topicData']) && is_array($step['topicData']))
                                                @foreach ($step['topicData'] as $topicIndex => $topic)
                                                    @php
                                                        $topicName = $topic['topic_name'] ?? '';
                                                        $originalPosition = $topic['original_position'] ?? '';
                                                        $originalTopicIndex = intval($originalPosition);
                                                        $newPosition = $topic['new_position'] ?? '';
                                                    @endphp
                                                    <div class="input-group mt-1 mb-1" id="{{ $originalIndex . $originalTopicIndex }}">
                                                        <span class="btn btn-outline-light btn-remove-topic" data-target="{{ $originalIndex . $originalTopicIndex }}" title="Remover Tópico"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                                                        <input type="text" class="form-control" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['topic_name']" value="{{ $topicName }}" maxlength="100" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?">

                                                        <input type="hidden" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['original_position']" value="{{ $originalPosition }}" tabindex="-1">
                                                        <input type="hidden" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['new_position']" value="{{ $newPosition }}" tabindex="-1">

                                                        <span class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>
                                                    </div>
                                                @endforeach
                                            @endif</div>

                                            <div class="clearfix">
                                                <span class="btn btn-outline-light btn-remove-block float-start" data-target="{{ $originalIndex }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Remover Bloco"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                                                <span class="btn btn-outline-light btn-add-topic float-end cursor-copy text-theme" data-block-index="{{ $originalIndex }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif</div>

                            <div class="clearfix">
                                <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 col-xxl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="float-end">
                            <a href="{{ route('auditsComposeShowURL', ['id' => $compose->id]) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line"></i></a>
                        </div>
                        <h4 class="card-title mb-0"><i class="ri-eye-2-fill fs-16 align-middle text-theme me-2"></i>Pré-visualização</h4>
                    </div>

                    <div id="load-preview" class="card-body">
                        <p class="text-center mt-3">Ao clicar em {{ $compose ? 'Atualizar' : 'Salvar' }} uma prévia do formulário será exibida aqui</p>
                    </div>
                </div>
            </div>
        </div>

    @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script>
        var auditsComposeShowURL = "{{ route('auditsComposeShowURL') }}";
        var auditsComposeStoreURL = "{{ route('auditsComposeStoreURL') }}";
        var auditsComposeUpdateURL = "{{ route('auditsComposeUpdateURL') }}";
        var auditsComposeToggleStatusURL = "{{ route('auditsComposeToggleStatusURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/audits-compose.js') }}" type="module"></script>
@endsection
