@if ( $topicsData )
    @foreach ($topicsData as $step)
        @php
            $stepData = $step['stepData'] ?? null;
            $stepName = $stepData['step_name'] ?? 0;
            $originalPosition = $stepData['original_position'] ?? 0;
            $newPosition = $stepData['new_position'] ?? 0;
        @endphp

        @if($stepData)
            <div class="card joblist-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="job-title">{{ e($stepName) }}</h5>
                            <p class="company-name text-muted mb-0" title="Pessoa a qual foi delegada esta vistoria">Responsável: </p>
                        </div>
                        <div>
                            <div class="avatar-sm">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa delegada ao (Nome do colaborador)" class="avatar-xxs rounded-circle">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<p class="text-muted job-description"></p>-->
                </div>
                @if (isset($step['topicData']) && is_array($step['topicData']))
                    @php
                        $index = 0;
                        $bg = 'bg-opacity-75';
                    @endphp
                    @foreach ($step['topicData'] as $topicIndex => $topic)
                        @php
                            $index++;

                            $bg = $bg == 'bg-opacity-75' ? 'bg-opacity-50' : 'bg-opacity-75';

                            $topicName = $topic['topic_name'] ?? '';
                            $originalPosition = $topic['original_position'] ?? 0;
                            $newPosition = $topic['new_position'] ?? 0;
                        @endphp
                        <div class="card-footer border-top-dashed bg-dark {{ $bg }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-uppercase pe-2">
                                    <span class="badge bg-light-subtle text-body badge-border text-theme">{{ $index }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0">{{ e($topicName) }}</h5>
                                    <div class="row mt-3">
                                        <div class="col-auto">
                                            <div class="form-check form-switch form-switch-lg form-switch-theme mb-3">
                                                <input tabindex="-1" class="form-check-input" type="radio" name="compliance" role="switch" id="SwitchCheck{{ $topicIndex }}">
                                                <label class="form-check-label" for="SwitchCheck{{ $topicIndex }}">Conforme</label>
                                            </div>
                                            <div class="form-check form-switch form-switch-lg form-switch-danger">
                                                <input tabindex="-1" class="form-check-input" type="radio" name="compliance" role="switch" id="SwitchCheck2{{ $topicIndex }}">
                                                <label class="form-check-label" for="SwitchCheck2{{ $topicIndex }}">Não Conforme</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light ps-1 pe-1 dropdown" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Bater foto"><i  class="ri-image-add-fill fs-5 m-2"></i></button>

                                                <textarea tabindex="-1" class="form-control" maxlength="1000" rows="3" placeholder="Observações..."></textarea>

                                                <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light"><i  class="ri-save-3-line fs-3 m-2 text-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Salvar"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    @endforeach
@endif
