@extends('layouts.master')
@section('title')
    Formulário de Vistoria
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysComposeIndexURL') }}
        @endslot
        @slot('li_1')
            Formulários
        @endslot
        @slot('title')
            @if($data)
                Edição <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$data->id}}</span> {{ limitChars($data->title ?? '', 20) }}</small>
            @else
                Compor Formulário
            @endif
        @endslot
    @endcomponent

        @include('components.alerts')

        <div class="row mb-3">
            <div class="col-md-12 col-lg-6 col-xxl-7">
                <div class="card h-100">
                    <form id="surveysComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate autocomplete="false">
                        @csrf

                        <input type="hidden" name="id" value="{{ $data->id ?? ''}}">
                        <input type="hidden" name="type" value="{{ $type ?? 'custom'}}">

                        <div class="card-header">
                            <div class="float-end">
                                <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-compose-store-or-update" tabindex="-1">
                                    @if ($data)
                                        Atualizar
                                    @else
                                        Salvar
                                    @endif
                                </button>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
                        </div>

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <div class="row mb-4">
                                <div class="col text-muted">
                                    Esta é a área de composição
                                </div>
                                <div class="col-auto">
                                    @if ($data)
                                        <span class="btn-group">
                                            @if ($data->status == 'active')
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-surveys-compose-toggle-status" data-status-to="disabled" data-compose-id="{{ $data->id }}" tabindex="-1">Desativar</button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-success" id="btn-surveys-compose-toggle-status" data-status-to="active" data-compose-id="{{ $data->id }}" tabindex="-1">Ativar</a>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-outline-info" id="btn-surveys-compose-clone" tabindex="-1">Clonar</button>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" value="{{ $data ? $data->title : '' }}" required autocomplete="off" maxlength="100">
                                <label for="floatingInput">Título do Formulário</label>
                            </div>
                            <div class="form-text">Título é necessário para que, quando na listagem, você facilmente identifique este modelo</div>

                            @if( !$topicsData && $type == 'default' )
                                @php
                                    $defaultTopics = file_get_contents(resource_path('views/surveys/demo/default-survey-topics.json'));
                                    $defaultTopics = json_decode($defaultTopics, true);

                                    $topicsData = [];
                                    foreach($getActiveDepartments as $index => $department){
                                        $topicsData[$index] = [
                                            'stepData' => [
                                                'step_name' => $department->department_alias,
                                                'step_id' => $department->id,
                                                'original_position' => $index,
                                                'new_position' => $index,
                                            ],
                                            'topicData' => $defaultTopics['topics']
                                        ];
                                    }
                                @endphp
                            @endif

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-2 p-0 mt-3">@if ($topicsData)
                                @foreach ($topicsData as $stepIndex => $step)
                                    @php
                                        $stepData = $step['stepData'];
                                        $stepName = $stepData['step_name'] ?? '';
                                        $originalPosition = $stepData['original_position'] ?? $stepIndex;
                                        $originalIndex = intval($originalPosition);
                                        $newPosition = $stepData['new_position'] ?? $stepIndex;
                                    @endphp
                                    <div id="{{ $originalIndex }}" class="accordion-item block-item mt-0 mb-3 border-dark p-0">
                                        <div class="input-group">
                                            @if( $type == 'custom' )
                                                <input type="text" class="form-control" name="[{{ $originalIndex }}]['stepData']['step_name']" value="{{ $stepName }}" autocomplete="off" maxlength="100" required>
                                            @else
                                                <input type="text" class="form-control disabled" autocomplete="off" maxlength="100" value="{{ $stepName }}"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Para Departamentos, este campo não é editável"
                                                readonly disabled>
                                                <input type="hidden" name="[{{ $originalIndex }}]['stepData']['step_name']" value="{{ $stepName }}">
                                            @endif
                                            <span class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

                                            <span class="btn btn-outline-light btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
                                        </div>

                                        <input type="hidden" name="[{{ $originalIndex }}]['stepData']['original_position']" value="{{ $originalPosition }}" tabindex="-1">
                                        <input type="hidden" name="[{{ $originalIndex }}]['stepData']['new_position']" value="{{ $newPosition }}" tabindex="-1">

                                        <div class="accordion-collapse collapse show">
                                            @include('surveys.includes.topics-input', [
                                                'type' => $type,
                                                'topicsData' =>  $step['topicData'],
                                                'originalIndex' => $originalIndex
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            @endif</div>

                            @if( $type == 'custom' )
                                <div class="clearfix">
                                    <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line"></i></button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 col-xxl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="float-end">
                            @if ($data)
                                <a href="{{ route('surveysComposeShowURL', ['id' => is_array($data) ? $data['id'] : $data->id]) }}" class="btn btn-sm btn-outline-theme position-absolute me-3 end-0" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line"></i></a>
                            @endif
                        </div>
                        <h4 class="card-title mb-0"><i class="ri-eye-2-fill fs-16 align-middle text-theme me-2"></i>Pré-visualização</h4>
                    </div>

                    <div id="load-preview" class="card-body">
                        <p class="text-center mt-3">Ao clicar em {{ $data ? 'Atualizar' : 'Salvar' }}, uma prévia do formulário será exibida aqui</p>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@php
    //appPrintR($getActiveDepartments);
    //appPrintR($topicsData);
@endphp
@section('script')
    <script src="{{ URL::asset('build/libs/sortablejs/Sortable.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/surveys-sortable.js') }}" type="module"></script>

    <script>
        var surveysComposeShowURL = "{{ route('surveysComposeShowURL') }}";
        var surveysComposeCreateOrUpdateURL = "{{ route('surveysComposeCreateOrUpdateURL') }}";
        var surveysComposeToggleStatusURL = "{{ route('surveysComposeToggleStatusURL') }}";
    </script>

    <script src="{{ URL::asset('build/js/surveys-compose.js') }}" type="module"></script>
@endsection
