@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signup')
@endsection
@section('content')
    @php
        $host = $_SERVER['HTTP_HOST'] ?? 'default';
        $logo2 = str_contains($host, 'testing') ? '-2' : '';
    @endphp
    <div class="auth-page-wrapper pt-5">
        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                    <img src="{{ URL::asset('build/images/logo-light' . $logo2 . '.png') }}" alt="{{appName()}}" height="31" loading="lazy">
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
                                <div class="text-center mt-2">
                                    <h5 class="text-theme">Registro</h5>
                                </div>
                                <div class="p-2 mt-4">
                                    <form class="needs-validation" novalidate method="POST"
                                        action="{{ route('register') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="useremail" class="form-label">E-mail <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" id="useremail"
                                                placeholder="Informe o e-mail corporativo" maxlength="100" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Razão Social <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name') }}" id="username"
                                                placeholder="Informe a Razão Social" maxlength="100" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="userpassword" class="form-label">Senha <span class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                id="userpassword" onpaste="return false" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Senha aqui" minlength="8" maxlength="20" required>
                                                <div id="passwordHelpBlock" class="form-text">
                                                    Sua senha deve conter entre 8 a 20 caracteres, letras e números e não deve ter espaços, caracteres especiais ou emojis.
                                                </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="input-password">Confirme a Senha <span
                                                class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                name="password_confirmation" id="input-password"
                                                placeholder="Redigite a senha" required>

                                            <div class="form-floating-icon">
                                                <i data-feather="lock"></i>
                                            </div>
                                        </div>
                                        <!--
                                        <div class="mb-3">
                                            <label for="input-avatar">Avatar <span
                                                class="text-danger">*</span></label>
                                            <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                                name="avatar" id="input-avatar" required>
                                            @error('avatar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="">
                                                <i data-feather="file"></i>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="subdomain" class="form-label">Subdomínio <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('subdomain') is-invalid @enderror"
                                                name="subdomain" value="{{ old('subdomain') }}" id="subdomain"
                                                placeholder="Informe o subdomínio" required>
                                            @error('subdomain')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        -->

                                        <div class="mb-4">
                                            <p class="mb-0 fs-12 text-muted fst-italic">Ao se cadastrar, você concorda com os <a href="#"
                                                    class="text-theme text-decoration-underline fst-normal fw-medium">Termos de Uso</a></p>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-theme w-100" type="submit">Cadastrar</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center">
                            <p class="mb-0">Já possui cadastro? <a href="{{ route('login') }}"
                                    class="fw-semibold text-theme text-decoration-underline"> Clique aqui </a> </p>
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
                            <p class="mb-0 text-muted">&copy; {{date('Y')}} {{appName()}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/form-validation.init.js') }}"></script>
@endsection
