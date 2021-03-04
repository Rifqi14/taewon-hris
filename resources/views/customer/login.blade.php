@extends('customer.layouts.app')
@section('title', 'Login')

@section('content')
<!-- Log In -->
<section class="login">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="n-customer">
                    <h5>Pelanggan Baru</h5>
                    <p>Dengan membuat akun, Anda akan dapat berbelanja lebih cepat, mendapatkan informasi terbaru tentang status pesanan, dan melacak pesanan yang sudah Anda buat sebelumnya.</p>
                    <a href="{{route('register')}}">Daftar</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="r-customer">
                    <h5>Pelanggan Terdaftar</h5>
                    <p>Jika Anda memiliki akun di kami, silakan masuk.</p>
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul style="list-style: none;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" autocomplete="off">
                        @csrf
                        <div class="emal">
                            <label for="email">{{ __('E-Mail') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('E-Mail') }}">
                        </div>
                        <div class="pass">
                            <label>{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="{{ __('Password') }}" >
                        </div>
                        <div class="d-flex justify-content-between nam-btm">
                            <div>
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    {{ __('Ingat Saya') }}
                                </label>
                            </div>
                            <div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">
                                        {{ __('Lupa Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Log In -->
@endsection
