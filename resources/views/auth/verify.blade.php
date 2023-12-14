@extends('layouts.master-without-navbtn-success')
@section('title')
    @lang('translation.reset-mail')
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
                                <div class="mb-4">
                                    <div class="avatar-lg mx-auto">
                                        <div class="avatar-title bg-light text-theme display-5 rounded-circle">
                                            <i class="ri-mail-line"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2 mt-4">
                                    <div class="text-muted text-center mb-4 mx-lg-3">
                                        <h4 class="">Verify Your Email</h4>
                                        <p>Please enter the 4 digit code sent to <span class="fw-semibold">example@abc.com</span></p>
                                    </div>

                                    <form>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="digit1-input" class="visually-hidden">Dight 1</label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-light text-center"
                                                        onkeyup="moveToNext(this, 2)" maxLength="1"
                                                        id="digit1-input">
                                                </div>
                                            </div><!-- end col -->

                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="digit2-input" class="visually-hidden">Dight 2</label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-light text-center"
                                                        onkeyup="moveToNext(this, 3)" maxLength="1"
                                                        id="digit2-input">
                                                </div>
                                            </div><!-- end col -->

                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="digit3-input" class="visually-hidden">Dight 3</label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-light text-center"
                                                        onkeyup="moveToNext(this, 4)" maxLength="1"
                                                        id="digit3-input">
                                                </div>
                                            </div><!-- end col -->

                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="digit4-input" class="visually-hidden">Dight 4</label>
                                                    <input type="text" class="form-control form-control-lg bg-light border-light text-center"
                                                        onkeyup="moveToNext(this, 4)" maxLength="1"
                                                        id="digit4-input">
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                    </form><!-- end form -->

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-theme w-100">Confirm</button>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center">
                            <p class="mb-0">Didn't receive a code ? <a href="auth-pass-reset-basic" class="fw-semibold text-theme text-decoration-underline">Resend</a> </p>
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
    <!-- end auth-page-wrapper -->
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/particles.js/particles.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/particles.app.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/two-step-verification.init.js') }}"></script>
@endsection
