@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signin')
@endsection
@section('css')
@endsection
@section('content')
    @php
        $host = $_SERVER['HTTP_HOST'] ?? 'default';
        $logo2 = str_contains($host, 'testing') ? '-2' : '';
    @endphp
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                    <img src="{{ URL::asset('build/images/logo-light' . $logo2 . '.png')}}" alt="{{appName()}}" height="49" loading="lazy">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="p-2">
                                    <form id="loginForm" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="database" id="database" autocomplete="off">

                                        <div class="mb-3">
                                            <label for="username" class="form-label">E-mail <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="username" name="email" placeholder="Informe o e-mail" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="{{ route('password.update') }}" class="text-muted small">Esqueceu a senha?</a>
                                            </div>
                                            <label class="form-label" for="password-input">Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" name="password" placeholder="Senha aqui" id="password-input" required maxlength="20">
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle text-body"></i></button>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="auth-remember-check">
                                            <label class="form-check-label" for="auth-remember-check">Manter conexão</label>
                                        </div>

                                        <div class="mt-4">
                                            <button id="btn-login" class="btn btn-theme w-100" type="submit">Entrar</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center d-none">
                            <p class="mb-0">Don't have an account ? <a href="{{ route('register') }}" class="fw-semibold text-theme text-decoration-underline"> Signup </a> </p>
                        </div>

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> {{appName()}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
@endsection
@section('script')
<script>
    var checkDatabasesURL = "{{ route('checkDatabasesURL') }}";
</script>

<script src="{{ URL::asset('build/js/login.js') }}" type="module"></script>

<script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
@endsection
