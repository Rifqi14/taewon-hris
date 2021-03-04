@extends('admin.layouts.empty')
@section('title', 'Login')
@section('class', 'login-page')

@section('stylesheets')
<style type="text/css">
.login-page, .register-page {
    background: #d2d6de url("/adminlte/images/background.jpg") no-repeat fixed center !important;
}
</style>
@endsection
@section('content')
  <div class="login-box">
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo">
            <a href="#"><img src="{{asset(config('configs.app_logo'))}}" class="img-fluid"></a>
        </div>
        <form action="{{route('admin.login.post')}}" method="post" autocomplete="off">
          @csrf
          <div class="form-group">
            <div class="input-group">
              <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" autofocus placeholder="{{ __('E-Mail') }}">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
              {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
            </div>
            
          </div>
          <div class="form-group">
            <div class="input-group">
              <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
              {!! $errors->first('password', '<div class="invalid-feedback">:message</div>') !!}
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-{{config('configs.app_theme')}} btn-block">Login</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
