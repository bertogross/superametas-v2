<div class="text-center m-4">
    <h5 class="text-uppercase">Ainda não há dados 😭</h5>
    <p class="text-muted mb-4">Você deverá registrar informações!</p>
    @if (isset($url))
        <a class="btn btn-outline-theme" href="{{ $url }}"><i class="ri-add-line"></i></a>
    @endif
    @if (isset($warning))
        <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-line label-icon"></i> {!! $warning !!}
        </div>
    @endif
</div>
