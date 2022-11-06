
@include('admin.includes.header')
<body class="vertical-layout vertical-menu-modern semi-dark-layout 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="semi-dark-layout">

<div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-8 col-11 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                
                                <div class="col-lg-12 col-12 px-2" style="padding:20px 0;">
									<div align="center" class="px-2"><img src="{{asset('app-assets/images/logo/logo.png')}}" alt="Login"></div>
									
                                    <div class="card rounded-0 mb-0 px-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title t-center">
                                                <h4 class="mb-0">Login</h4>
                                            </div>
                                        </div>
                                      
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                            <form method="POST" action="{{ url('/admin/verifylogin') }}">
											
                                                @csrf
                                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="username" placeholder="Username" name="username" value="{{ old('username') }}" required autocomplete="email" autofocus>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                        <label for="user-name">{{ __('Username') }}</label>
                                                    </fieldset>
                                                    
                                                    @error('username')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input  id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                                                        <div class="form-control-position">
                                                            <i class="feather icon-lock"></i>
                                                        </div>
                                                        <label for="user-password">{{ __('Password') }}</label>
                                                    </fieldset>
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <!--div class="form-group d-flex justify-content-between align-items-center">
                                                        <div class="text-left">
                                                            <fieldset class="checkbox">
                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <span class=""> {{ __('Remember Me') }}</span>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="text-right">
                                                        @if (Route::has('password.request'))
                                                            <a class="card-link" href="{{ route('password.request') }}">
                                                                {{ __('Forgot Your Password?') }}
                                                            </a>
                                                        @endif                                                            
                                                    </div-->                                                  
                                                    <button type="submit" class="btn btn-primary float-right btn-inline"> {{ __('Login') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                      </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

    </body>
</html>