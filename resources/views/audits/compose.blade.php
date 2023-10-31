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

        <div class="row">
            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Área do Formulário</h4>
                    </div><!-- end card header -->

                    <div id="nested-compose-area" class="card-body">
                        <p class="text-muted">
                            Componha o formulário arrastando os elementos para esta área.
                        </p>
                        <div class="list-group nested-list nested-receiver">

                            @foreach ($getDepartmentsActive as $department)
                                <div class="list-group-item nested-1 bg-dark-subtle" data-dep="{{$department->department_id}}" draggable="false">
                                    {{ $department->department_alias }}
                                    <div class="list-group nested-list">
                                        <div class="list-group-item nested-2">Analytics</div>
                                        <div class="list-group-item nested-2">CRM</div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>

            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-bring-to-front fs-16 align-middle text-theme me-2"></i>Elementos</h4>
                    </div><!-- end card header -->

                    <div id="nested-elements-area" class="card-body">
                        <p class="text-muted">
                            Aqui estão os elementos de composição.<br>
                            Você poderá complementar o formulário arrastando para a posição desejada.
                        </p>

                        <div class="list-group nested-list">
                            <div class="nested-element">
                                <div class="list-group-item bg-light-subtle nested-1">
                                    <div class="clearfix">
                                        <button type="button" class="btn btn-ghost-danger float-end btn-remove-element pt-0 pb-0"><i class="ri-delete-bin-2-line"></i></button>
                                        <span class="label-element">
                                            Bloco dos Elementos
                                        </span>
                                        <input type="text" class="form-control" name="audit_compose[]['title']" placeholder="Informe o Título / Setor / Departamento">
                                    </div>

                                    <div class="list-group-item nested-1 nested-receiver-block-fake" style="min-height: 50px;"></div>

                                    <div class="list-group-item nested-1 nested-receiver-block" style="min-height: 50px;"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                @foreach ($auditElements as $index => $element)
                                    <div class="col-xxl-6 col-md-12 nested-element-to-block">
                                        <div class="list-group-item bg-light-subtle nested-1">
                                            <div class="clearfix">
                                                <button type="button" class="btn btn-ghost-danger float-end btn-remove-element pt-0 pb-0"><i class="ri-delete-bin-2-line"></i></button>

                                                {{ $element['label'] }}
                                            </div>

                                            <div class="list-group nested-list">
                                                <div class="list-group-item nested-2">
                                                    {{ $element['type'] }}
                                                </div>
                                                <div class="list-group-item nested-2">
                                                    {{ $element['name'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
