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
                            <h5 class="job-title text-theme">{{ $stepName }}</h5>
                            <p class="delegated-name text-muted mb-0" title="Pessoa a qual foi delegada esta vistoria">Responsável: <span class="delegated_to-name"></span></p>
                        </div>
                        <div>
                            <div class="avatar-sm dropstart {{ $edition ? 'w-auto' : '' }}">
                                <div
                                @if ($edition)
                                    id="dropdownMenu-{{$originalPosition}}" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false"
                                @endif
                                class="avatar-title bg-light rounded {{ $edition ? 'dropdown-toggle p-3 pe-2' : '' }} ">
                                    <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                    @if (!$edition)
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa delegada ao (Nome do colaborador)"
                                    @endif
                                    class="avatar-xxs rounded-circle {{ $edition ? 'blink' : '' }}">

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu-{{$originalPosition}}" data-simplebar style="height: 130px;">
                                        <ul class="list-unstyled vstack gap-2 mb-0 p-2">
                                            @foreach ($users as $user)
                                                @if ($user->role == 4)
                                                    <li>
                                                        <div class="form-check form-check-success d-flex align-items-center">
                                                            <input class="form-check-input me-3"
                                                            type="radio"
                                                            data-step="{{$originalPosition}}"
                                                            name="delegated_to[][{{$data->id}}][{{$originalPosition}}]"
                                                            value="{{ $user->id }}"
                                                            id="user-{{$user->id}}{{$originalPosition}}{{$newPosition}}"
                                                            {{--
                                                            @checked(old('delegated_to', $delegated_to) == $user->id)
                                                            --}}
                                                            required>
                                                            <label class="form-check-label d-flex align-items-center"
                                                                for="user-{{$user->id}}{{$originalPosition}}{{$newPosition}}">
                                                                <span class="flex-shrink-0">
                                                                    <img
                                                                    @if(empty(trim($user->avatar)))
                                                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                                    @else
                                                                        src="{{ URL::asset('storage/' . $user->avatar) }}"
                                                                    @endif
                                                                        alt="{{ $user->name }}" class="avatar-xxs rounded-circle">
                                                                </span>
                                                                <span class="flex-grow-1 ms-2">{{ $user->name }}</span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>

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

                            $topicId = $topic['topic_id'] ?? '';
                            $originalPosition = $topic['original_position'] ?? 0;
                            $newPosition = $topic['new_position'] ?? 0;
                        @endphp
                        <div class="card-footer border-top-dashed bg-dark {{ $bg }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-uppercase pe-2">
                                    <span class="badge bg-light-subtle text-body badge-border text-theme">{{ $index }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0">{{ $topicId }}</h5>
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
