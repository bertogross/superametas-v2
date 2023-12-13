<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

@yield('script')
@yield('script-bottom')

<script>
window.App = {!! json_encode([
    'url' => URL::asset('/'),
]) !!};
</script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>

<script>
    var profileChangeLayoutModeURL = "{{ route('profileChangeLayoutModeURL') }}";
</script>
<script src="{{ URL::asset('build/js/app-custom.js') }}" type="module"></script>

@php
    $HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $SUBDOMAIN = $HTTP_HOST ? strtok($HTTP_HOST, '.') : '';
@endphp
@if ( $SUBDOMAIN && $SUBDOMAIN != 'app' )
    @php
        $replacements = [
            'localhost:8000' => 'local',
            'localhost' => 'local',
            'development' => 'dev',
            'testing' => 'test'
        ];

        foreach ($replacements as $search => $replace) {
            $SUBDOMAIN = str_replace($search, $replace, $SUBDOMAIN);
        }
    @endphp
    <div class="ribbon-box border-0 ribbon-fill position-fixed top-0 start-0 d-none d-lg-block d-xl-block" data-bs-toggle="tooltip" data-bs-placement="right" title="{{$SUBDOMAIN}} Environment" style="z-index:5000; width: 60px; height:60px;">
        <div class="ribbon ribbon-{{$SUBDOMAIN == 'development' ? 'danger' : 'warning'}} text-uppercase">{{ $SUBDOMAIN }}</div>
    </div>
@endif
