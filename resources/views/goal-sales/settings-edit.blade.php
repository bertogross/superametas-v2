@php
use App\Models\User;

$type = 'sales';

$getAuthorizedCompanies = getAuthorizedCompanies();
$getActiveCompanies = getActiveCompanies();
$getActiveDepartments = getActiveDepartments();

$currentMonth = now()->format('Y-m');
$previousMonth = now()->subMonth()->format('Y-m');

$startYear = date('Y', strtotime($firstDate));
$endYear = intval($currentMonth) >= (intval(date('Y'))+11) ? date('Y', strtotime($currentMonth." +1 year")) : date('Y');
@endphp

<div class="modal flip" id="goalSalesSettingsModal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">Gerenciar Metas</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if( !auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR) )
                    <div class="alert alert-danger">Acesso não autorizado</div>
                    @php exit; @endphp
                @endunless

                @if (!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies))
                    <ul class="nav nav-tabs nav-border-top nav-justified" role="tablist">
                        @foreach ($getAuthorizedCompanies as $key => $companyId)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $key == 0 ? 'active' : '' }} text-uppercase" id="company-{{ $companyId }}-tab" data-bs-toggle="tab" data-bs-target="#company-{{ $companyId }}" type="button" role="tab" aria-controls="company-{{ $companyId }}" aria-selected="true">{{ getCompanyNameById($companyId) }}</button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content p-3 bg-light">
                        @foreach ($getAuthorizedCompanies as $key => $companyId)
                            <div class="tab-pane fade show {{ $key == 0 ? 'active' : '' }}" id="company-{{ $companyId }}" role="tabpanel" aria-labelledby="company-{{ $companyId }}-tab">
                                <div id="load-emp-{{ $companyId }}">
                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box mt-1" id="accordion-{{ $companyId }}">
                                        @foreach (range($endYear, $startYear) as $aYear)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="accordion-{{ $companyId.$aYear }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ getCompanyNameById($companyId) }} ano {{ $aYear }}">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accor-{{ $companyId.$aYear }}" aria-expanded="false" aria-controls="accor-{{ $companyId.$aYear }}">
                                                        <i class="ri-calendar-line me-1"></i> {{ $aYear }} <span class="badge badge-theme badge-border ms-2" id="count-year-posts-{{ $companyId }}-{{ $aYear }}"></span>
                                                    </button>
                                                </h2>
                                                <div id="accor-{{ $companyId.$aYear }}" class="accordion-collapse collapse" aria-labelledby="accordion-{{ $companyId.$aYear }}" data-bs-parent="#accordion-{{ $companyId }}">
                                                    <div class="accordion-body">
                                                        <table class="table table-sm table-bordered table-striped mb-0">
                                                            <tbody>
                                                                @php
                                                                    $periods = [];
                                                                    foreach (range(12, 1) as $aMonth) {
                                                                        $periods[] = date('Y-m', strtotime($aYear.'-'.$aMonth));
                                                                    }

                                                                @endphp

                                                                @foreach ($periods as $period)
                                                                    @php
                                                                        $explode = explode('-', $period);
                                                                        $year = $explode[0];
                                                                        $month = $explode[1];

                                                                        $Id = getGoalsId($companyId, $period, $type);
                                                                    @endphp
                                                                    <tr data-meantime="{{$period}}" data-company="{{$companyId}}">
                                                                        <td class="align-middle ps-3">
                                                                            <span class="meantime @if (!empty($Id)) text-theme @endif">
                                                                                {{ $year }}, {{ ucfirst(strftime("%B", strtotime($period))) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="align-middle pe-3 ps-3" width="80">
                                                                            @if (empty($Id))
                                                                                <button type="button" class="btn btn-sm btn-outline-theme btn-goal-sales-edit waves-effect waves-light float-end w-100" data-meantime="{{ $period }}" data-company-id="{{ $companyId }}" data-company-name="{{ getCompanyNameById($companyId) }}" data-purpose="store" title="Adicionar Meta de Vendas {{ $period }} {{ count($getActiveCompanies) > 1 ? ':: '.getCompanyNameById($companyId) : '' }}" modal-title="Adicionar Meta de Vendas :: <span class='text-theme'>{{ getCompanyNameById($companyId) }}</span>">Adicionar</button>
                                                                            @else
                                                                                <button type="button" class="btn btn-sm btn-theme btn-goal-sales-edit waves-effect waves-light float-end w-100" data-id="{{ $Id }}" data-meantime="{{ $period }}" data-company-id="{{ $companyId }}" data-company-name="{{ getCompanyNameById($companyId) }}" data-purpose="update" title="Editar Meta de Vendas {{ $period }} {{ getCompanyNameById($companyId) }}" modal-title="Editar Meta de Vendas :: <span class='text-theme'>{{ getCompanyNameById($companyId) }}</span>">Editar</button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="alert alert-warning">Empresas ainda não foram cadastradas/ativadas</div>
                @endif
            </div>
        </div>
    </div>
</div>
