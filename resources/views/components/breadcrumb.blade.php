<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @if(isset($li_1))
                        <li class="breadcrumb-item">
                            <a href="@if(isset($url)){{ $url }}@else javascript: void(0);@endif">
                                {{ $li_1 }}
                            </a>
                        </li>
                    @endif
                    @if(isset($title) && isset($li_1))
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
</div>
