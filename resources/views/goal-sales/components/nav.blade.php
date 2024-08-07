@php
    use App\Models\User;

    $currentUserId = auth()->id();

    $explodeMeantime = $getCustomMeantime ? explode(' até ', $getCustomMeantime) : '';

    $lastUpdate = getLastSalesUpdate('wlsm_sales', 'd/m/Y H:i');
@endphp
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                {{ $title }}
                <span class="small">
                    <i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i>

                    @php
                    if (is_array($explodeMeantime) && count($explodeMeantime) > 1) {
                        echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($explodeMeantime[0]))));

                        echo '<i class="ri-arrow-left-right-line text-theme me-1 ms-1 align-middle small"></i>';

                        echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($explodeMeantime[1]))));

                    }elseif($getMeantime == 'custom' && !empty($getCustomMeantime)){
                        echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($getCustomMeantime))));
                    }elseif( $getMeantime == 'today' ){
                        echo 'Hoje, <span class="small text-body">'.date("d/m/Y", strtotime($getMeantime)).'</span>';
                    }else{
                        echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($getMeantime))));
                    }
                    @endphp
                </span>
            </h4>
            <div class="page-title-right">
                <div class="dropstart float-end">
                    <button type="button" class="btn btn-sm btn-ghost-theme fs-4 pe-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Opções"><i class="ri-more-2-line"></i></button>

                    <ul class="dropdown-menu">
                        @if ($lastUpdate)
                            <div class="dropdown-header noti-title font-size-13 text-muted text-truncate mn-0" id="check-current-browser-update" title="Data e hora da última atualização do banco de dados" data-update="22-10-2023 05:00">
                                Atualizado em: {{ $lastUpdate }}
                            </div>

                            <div class="dropdown-divider"></div>
                        @endif

                        @if(canManageGoalSales())
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" id="btn-goal-sales-settings">
                                    <i class="ri-edit-line text-muted fs-16 align-middle me-1 text-theme"></i>
                                    <span class="align-middle">Gerenciar {{ $title }}</span>
                                </a>
                            </li>

                            <div class="dropdown-divider"></div>
                        @endif

                        <h6 class="dropdown-header mb-2">
                            <a class="init-loader float-end" id="restore-session" href="#" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Restaurar consultas ao formato padrão" title="Restaurar consultas ao formato padrão"><i class="ri-arrow-go-back-line fs-14 text-theme"></i></a>
                            Disposições
                        </h6>

                        <li class="dropdown-item pt-0 pb-0 bg-transparent">
                            <div class="form-check form-switch form-switch-theme mb-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Exibir/Ocultar Filtro">
                                <input class="form-check-input filter-toggle" type="checkbox" role="switch" checked id="filter-toggle">
                                <label class="form-check-label" for="filter-toggle"><span>Filtro</span></label>
                            </div>

                            <div class="form-check form-switch form-switch-theme mb-2" data-bs-toggle="tooltip" data-bs-placement="left" title="O Modo Slide requer 2 ou mais Lojas">
                                <input class="form-check-input slide-mode" type="radio" role="switch" name="mode" @if (getUserMeta($currentUserId, 'slide-mode') == 'on') checked @endif id="slide-mode">
                                <label class="form-check-label" for="slide-mode">Modo Slide</label>
                            </div>

                            @if (auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR))
                                <div class="form-check form-switch form-switch-theme mb-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Ativar/Desativar Modo Analítico">
                                    <input class="form-check-input analytic-mode" type="radio" role="switch" name="mode" @if (getUserMeta($currentUserId, 'analytic-mode') == 'on') checked @endif id="analytic-mode">
                                    <label class="form-check-label" for="analytic-mode">Modo Analítico</label>
                                </div>
                            @endif
                        </li>

                        <div class="dropdown-divider mt-3"></div>

                        <li class="dropdown-item pt-0 pb-0 bg-transparent">
                            <div class="input-step full-width light p-0">
                                <button type="button" class="minus" id="zoom_out" data-bs-toggle="tooltip" data-bs-placement="top" title="Reduzir Zoom">-</button>
                                <input type="text" value="100%" readonly="" id="zoom_reset" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="Restaurar Zoom" title="Restaurar Zoom">
                                <button type="button" class="plus" id="zoom_in" data-bs-toggle="tooltip" data-bs-placement="top" title="Ampliar Zoom">+</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
