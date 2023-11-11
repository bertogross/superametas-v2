@foreach ($data as $stepIndex => $step)
    @php
        $stepData = $step['stepData'];
        $stepName = $stepData['step_name'] ?? '';
        $originalPosition = $stepData['original_position'] ?? $stepIndex;
        $originalIndex = intval($originalPosition);
        $newPosition = $stepData['new_position'] ?? $stepIndex;

        $topicsData = $step['topicsData'] ?? null;
    @endphp
    <div id="{{ $originalIndex }}" class="accordion-item block-item mt-3 mb-0 border-dark border-1 rounded rounded-2 p-0">
        <div class="input-group">
            @if( $type == 'custom' )
                <input type="text" class="form-control text-theme" name="['stepData']['step_name']" value="{{ $stepName }}" placeholder="Informe o Título/Setor/Etapa" autocomplete="off" maxlength="100" required>
            @else
                <input type="text" class="form-control disabled text-theme" autocomplete="off" maxlength="100" value="{{ $stepName }}"
                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Para Departamentos, este campo não é editável"
                readonly disabled>
                <input type="hidden" name="['stepData']['step_name']" value="{{ $stepName }}">
            @endif

            <span class="tn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

            <span class="tn btn-ghost-dark btn-icon rounded-pill btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
        </div>

        <input type="hidden" name="['stepData']['type']" value="{{ $type }}" tabindex="-1">
        <input type="hidden" name="['stepData']['original_position']" value="{{ $originalPosition }}" tabindex="-1">
        <input type="hidden" name="['stepData']['new_position']" value="{{ $newPosition }}" tabindex="-1">

        <div class="accordion-collapse collapse show">
            <div class="nested-receiver-block mt-0 p-1">@if (isset($topicsData) && is_array($topicsData))
                @foreach ($topicsData as $topicIndex => $topic)
                    @php
                        $topicId = $topic['topic_id'] ?? '';
                        $originalPosition = $topic['original_position'] ?? 0;
                        $originalTopicIndex = $originalPosition ? intval($originalPosition) : 0;
                        $newPosition = $topic['new_position'] ?? 0;
                    @endphp
                    <div id="{{ $originalIndex . $originalTopicIndex }}" class="step-item mt-1 mb-1">
                        <div class="row">
                            <div class="col-auto">
                                <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-topic" data-target="{{ $originalIndex . $originalTopicIndex }}" title="Remover Tópico"><i class="ri-delete-bin-3-line"></i></span>
                            </div>
                            <div class="col">
                                <select select-one data-choices-removeItem class="form-control surveys-term-choice w-100" title="Exemplo: Organização do setor?... Abastecimento de produtos/insumos está em dia?" data-placeholder="Tópico..." name="['topicsData'][{{ $originalTopicIndex }}]['topic_id']" required>
                                    <option value="{{$topicId}}" selected>{{getTermNameById($topicId)}}</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <span class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line"></i></span>
                            </div>
                        </div>
                        <input type="hidden" name="['topicsData'][{{ $originalTopicIndex }}]['original_position']" value="{{ $originalPosition }}" tabindex="-1">
                        <input type="hidden" name="['topicsData'][{{ $originalTopicIndex }}]['new_position']" value="{{ $newPosition }}" tabindex="-1">
                    </div>
                @endforeach
            @endif</div>

            <div class="clearfix">
                <span class="btn btn-ghost-dark btn-icon btn-add-topic rounded-pill float-end cursor-copy text-theme" data-block-index="{{ $originalIndex }}" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>

                @if ( $type == 'custom' )
                    <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-block float-start" data-target="{{ $originalIndex }}" title="Remover Bloco"><i class="ri-delete-bin-7-fill"></i></span>
                @endif
            </div>

        </div>
    </div>
@endforeach
