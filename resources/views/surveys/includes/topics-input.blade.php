<div class="nested-receiver-block border-1 border-dashed border-dark mt-0 p-1 rounded-0">@if (isset($topicsData) && is_array($topicsData))
    @foreach ($topicsData as $topicIndex => $topic)
        @php
            $topicName = $topic['topic_name'] ?? '';
            $originalPosition = $topic['original_position'] ?? 0;
            $originalTopicIndex = $originalPosition ? intval($originalPosition) : 0;
            $newPosition = $topic['new_position'] ?? 0;
        @endphp
        <div class="input-group mt-1 mb-1" id="{{ $originalIndex . $originalTopicIndex }}">
            <span class="btn btn-outline-light btn-remove-topic" data-target="{{ $originalIndex . $originalTopicIndex }}" title="Remover Tópico"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

            <input type="text" class="form-control" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['topic_name']" value="{{ $topicName }}" maxlength="100" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?" required>

            <input type="hidden" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['original_position']" value="{{ $originalPosition }}" tabindex="-1">
            <input type="hidden" name="[{{ $originalIndex }}]['topicData'][{{ $originalTopicIndex }}]['new_position']" value="{{ $newPosition }}" tabindex="-1">

            <span class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>
        </div>
    @endforeach
@endif</div>

<div class="clearfix">
    @if ( $type == 'custom' )
        <span class="btn btn-outline-light btn-remove-block float-start" data-target="{{ $originalIndex }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Remover Bloco"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>
    @endif

    <span class="btn btn-outline-light btn-add-topic float-end cursor-copy text-theme" data-block-index="{{ $originalIndex }}" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>
</div>
