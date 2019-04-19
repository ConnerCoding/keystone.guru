<?php
$modal = isset($modal) ? $modal : false;
$modalClass = $modal ? 'modal-' : '';
$width = $modal ? '12' : '6';
$redirect = isset($redirect) ? $redirect : Request::get('redirect', Request::getPathInfo());
// May be set if the user failed his initial login and needs another passthrough of redirect
$redirect = old('redirect', $redirect);
?>

<form class="form-horizontal" method="POST"
      action="{{ route('login', ['redirect' => $redirect]) }}">
    {{ csrf_field() }}
    <h3>
        {{ __('Login') }}
    </h3>

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <label for="{{ $modalClass }}login_email" class="control-label">{{ __('E-mail address') }}</label>

        <div class="col-md-{{ $width }}">
            <input id="{{ $modalClass }}login_email" type="email" class="form-control" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username email">
        </div>
    </div>

    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <label for="{{ $modalClass }}login_password" class="control-label">{{ __('Password') }}</label>

        <div class="col-md-{{ $width }}">
            <input id="{{ $modalClass }}login_password" type="password" class="form-control" name="password"
                   autocomplete="current-password" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-{{ $width }} {{ $modal ? 'col-md-offset-4' : '' }}">
            <div class="checkbox">
                <label for="{{ $modalClass }}login_remember">
                    <input id="{{ $modalClass }}login_remember" type="checkbox"
                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                    {{ __('Remember me') }}
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">
                {{ __('Login') }}
            </button>

            <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        </div>
    </div>

    <hr>

    <a href="{{ route('login.google') }}">
        <img src="{{ url('/images/google/btn_google_signin_dark_normal_web.png') }}"/>
    </a>
</form>