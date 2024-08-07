@php
    $getCompanyLogo = getCompanyLogo();
@endphp
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                @if ($getCompanyLogo)
                    <a href="{{ url('/') }}" title="Ir para inicial do {{appName()}}">
                        <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="{{appName()}}" class="me-1" height="27" loading="lazy">
                    </a>
                    {{ appName() }} - {{date('Y')}} ©
                @else
                    {{date('Y')}} © {{ appName() }}
                @endif
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    {{ subscriptionLabel() }}
                </div>
            </div>
        </div>
    </div>
</footer>
