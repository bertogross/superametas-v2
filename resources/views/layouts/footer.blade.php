@php
    $getCompanyLogo = getCompanyLogo();
@endphp
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                @if ($getCompanyLogo)
                    <a href="{{ url('/') }}" title="Ir para inicial do {{env('APP_NAME')}}">
                        <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="{{env('APP_NAME')}}" class="me-1" height="27">
                    </a>
                    {{ env('APP_NAME') }} - <script>document.write(new Date().getFullYear())</script> ©
                @else
                    <script>document.write(new Date().getFullYear())</script> © {{ env('APP_NAME') }}
                @endif
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <span class="badge bg-warning">Badge</span>
                </div>
            </div>
        </div>
    </div>
</footer>
