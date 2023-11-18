@php
appPrintR($distributedData);
@endphp
{{--
<!--
based on apps-crm-deals.blade.php
-->
<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="search-box">
                    <input type="text" class="form-control search" placeholder="Search for deals...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <!--end col-->
            <div class="col-md-auto ms-auto">
                <div class="d-flex hastck gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Sort by: </span>
                        <select class="form-control mb-0" data-choices data-choices-search-false
                            id="choices-single-default">
                            <option value="Owner">Owner</option>
                            <option value="Company">Company</option>
                            <option value="Date">Date</option>
                        </select>
                    </div>
                    <button data-bs-toggle="modal" data-bs-target="#adddeals" class="btn btn-success"><i
                            class="ri-add-fill align-bottom me-1"></i> Add
                        Deals</button>
                    <div class="dropdown">
                        <button class="btn btn-soft-info btn-icon fs-14" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-settings-4-line"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#">Copy</a></li>
                            <li><a class="dropdown-item" href="#">Move to pipline</a></li>
                            <li><a class="dropdown-item" href="#">Add to exceptions</a></li>
                            <li><a class="dropdown-item" href="#">Switch to common form view</a>
                            </li>
                            <li><a class="dropdown-item" href="#">Reset form view to default</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
--}}

<div class="row">

    <div class="col-sm-12 col-md-4">
        <div class="card">
            @if ($survey->status == 'new')
                <button type="button" class="btn btn-lg btn-soft-theme btn-icon w-100 h-100 p-4" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Iniciar Rotina {{ $recurringLabel }}"><i class="ri-play-fill"></i></button>
            @else
                <button type="button" class="btn btn-lg btn-soft-danger btn-icon w-100 h-100 p-4" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Parar Rotina {{ $recurringLabel }}"><i class="ri-stop-fill"></i></button>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Informações</h5>
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="ps-0" scope="row">Data de Início :</th>
                                <td class="text-muted">dd/mm/YYYY</td>
                            </tr>
                            {{--
                            <tr>
                                <th class="ps-0" scope="row">Envolvidos :</th>
                                <td class="text-muted">
                                    @php
                                        $merged = array_merge($distributedData['audited_by'], $distributedData['delegated_to']);
                                        $columns = array_column($merged, 'user_id');
                                        $uniqued = count($columns) > 1 ? array_unique($columns) : $columns;
                                        echo is_array($uniqued) ? count($uniqued) : 0;
                                    @endphp
                                </td>
                            </tr>
                            --}}
                            <tr>
                                <th class="ps-0" scope="row">Falhas :</th>
                                <td class="text-muted">??</td>
                            </tr>
                            <tr>
                                <th class="ps-0" scope="row">Em Progresso :</th>
                                <td class="text-muted">??</td>
                            </tr>
                            <tr>
                                <th class="ps-0" scope="row">Concluídas :</th>
                                <td class="text-muted">??</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- end card body -->
        </div>

        {{--
        <div class="row">
            @foreach ($getSurveyStatusTranslations as $key => $value)
                <div class="col-12">
                    <div class="card mb-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $value['description'] }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="fw-medium text-muted mb-0">{{ $value['label'] }}</p>
                                    <h2 class="mt-0 mb-0 ff-secondary fw-semibold"><span class="counter-value" data-target="{{ $distributedDatatatusCount[$key] ?? 0 }}"></span></h2>
                                </div>
                                <div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-{{ $value['color'] }}-subtle text-{{ $value['color'] }} rounded-circle fs-6 p-3">
                                            <i class="{{ !empty($value['icon']) ? $value['icon'] : 'ri-ticket-2-line' }}"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div>
                </div>
                <!--end col-->
            @endforeach
        </div>
        --}}
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="card">
            <a class="card-body bg-info-subtle"><!-- data-bs-toggle="collapse" href="#leadDiscovered" role="button"
                aria-expanded="false" aria-controls="leadDiscovered" -->
                <h5 class="card-title text-uppercase mb-1 fs-14">
                    @php
                        $columns = array_column($distributedData['delegated_to'], 'user_id');
                        $uniqued = count($columns) > 1 ? array_unique($columns) : $columns;
                        echo is_array($uniqued) ? count($uniqued) : 0;
                    @endphp
                     Vistoriador{{ count($uniqued) > 1 ? 'es' : '' }}
                </h5>
                <p class="text-muted mb-0"><span class="fw-medium">{{ is_array($distributedData['delegated_to']) ? count($distributedData['delegated_to']) : ''}} lojas</span></p>
            </a>
        </div>
        <!--end card-->
        <div class="collapse show" id="leadDiscovered">
            @foreach ($distributedData['delegated_to'] as $index => $value)
                @php
                    $userId = $value['user_id'];
                    $companyName = getCompanyAlias($value['company_id']);
                    $avatar = getUserData($userId)['avatar'];
                    $name = getUserData($userId)['name'];
                @endphp
                <div class="card mb-1 ribbon-box ribbon-fill ribbon-sm right">
                    <div class="ribbon ribbon-primary"><i class="ri-flashlight-fill"></i></div>
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#leadDiscovered{{$index}}" role="button"
                            aria-expanded="false" aria-controls="leadDiscovered{{$index}}">
                            <div class="flex-shrink-0">
                                <img
                                @if( empty(trim($avatar)) )
                                    src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                @else
                                    src="{{ URL::asset('storage/' .$avatar ) }}"
                                @endif
                                alt="{{ $name }}"
                                class="avatar-xs rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-13 mb-1">{{ $name }}</h6>
                                <p class="text-muted mb-0">{{ $companyName }}</p>
                            </div>
                        </a>
                    </div>
                    <div class="collapse border-top border-top-dashed" id="leadDiscovered{{$index}}">
                        <div class="card-body">
                            <h6 class="fs-14 mb-1">Nesta Technologies <small class="badge bg-danger-subtle text-danger">4 Days</small></h6>
                            <p class="text-muted text-break">As a company grows however, you find it's not as easy to shout across</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-question-answer-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Meeting with Thomas</h6>
                                            <small class="text-muted">Yesterday at 9:12AM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-mac-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Product Demo</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-earth-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Marketing Team Meeting</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        {{--
                        <div class="card-footer hstack gap-2">
                            <button class="btn btn-warning btn-sm w-100"><i class="ri-phone-line align-bottom me-1"></i>  Call</button>
                            <button class="btn btn-info btn-sm w-100"><i class="ri-question-answer-line align-bottom me-1"></i> Message</button>
                        </div>
                        --}}
                    </div>
                </div>
                <!--end card-->
            @endforeach
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="card">
            <a class="card-body bg-primary-subtle"><!-- data-bs-toggle="collapse" href="#contactInitiated" role="button"
                aria-expanded="false" aria-controls="contactInitiated" -->
                <h5 class="card-title text-uppercase mb-1 fs-14">
                    @php
                        $columns = array_column($distributedData['audited_by'], 'user_id');
                        $uniqued = count($columns) > 1 ? array_unique($columns) : $columns;
                        echo is_array($uniqued) ? count($uniqued) : 0;
                    @endphp
                     Auditores
                </h5>
                <p class="text-muted mb-0"><span class="fw-medium">{{ is_array($distributedData['audited_by']) ? count($distributedData['audited_by']) : ''}} lojas</span></p>
            </a>
        </div>
        <!--end card-->
        <div class="collapse show" id="contactInitiated">
            @foreach ($distributedData['audited_by'] as $index => $value)
                @php
                    $userId = $value['user_id'];
                    $companyName = getCompanyAlias($value['company_id']);
                    $avatar = getUserData($userId)['avatar'];
                    $name = getUserData($userId)['name'];
                @endphp
                <div class="card mb-1 ribbon-box ribbon-fill ribbon-sm right">
                    <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#contactInitiated{{$index}}"
                            role="button" aria-expanded="false" aria-controls="contactInitiated{{$index}}">
                            <div class="flex-shrink-0">
                                <img
                                @if( empty(trim($avatar)) )
                                    src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                @else
                                    src="{{ URL::asset('storage/' .$avatar ) }}"
                                @endif
                                alt="{{ $name }}"
                                class="avatar-xs rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-13 mb-1">{{ $name }}</h6>
                                <p class="text-muted mb-0">{{ $companyName }}</p>
                            </div>
                        </a>
                    </div>
                    <div class="collapse border-top border-top-dashed" id="contactInitiated{{$index}}">
                        <div class="card-body">
                            <h6 class="fs-14 mb-1">Nesta Technologies <small class="badge bg-danger-subtle text-danger">4
                                    Days</small></h6>
                            <p class="text-muted text-break">As a company grows however, you find it's not as easy
                                to shout across</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-question-answer-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Meeting with Thomas</h6>
                                            <small class="text-muted">Yesterday at 9:12AM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-mac-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Product Demo</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-earth-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Marketing Team Meeting</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        {{--
                        <div class="card-footer hstack gap-2">
                            <button class="btn btn-warning btn-sm w-100"><i class="ri-phone-line align-bottom me-1"></i> Call</button>
                            <button class="btn btn-info btn-sm w-100"><i class="ri-question-answer-line align-bottom me-1"></i>  Message</button>
                        </div>
                        --}}
                    </div>
                </div>
                <!--end card-->
            @endforeach
        </div>
    </div>
</div>
