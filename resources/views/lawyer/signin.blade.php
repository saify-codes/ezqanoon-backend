<x-lawyer.guest>
    {{-- <div class="relative p-6 z-1 sm:p-0">
        <div class="relative flex flex-col justify-center w-full h-screen sm:p-0 lg:flex-row">
            <!-- Form -->
            <div class="flex flex-col flex-1 w-full lg:w-1/2">
                <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                    <div>
                        <div class="mb-5 sm:mb-8">
                            <h1 class="mb-2 font-semibold text-gray-800 text-title-sm sm:text-title-md"> Sign In</h1>
                            <p class="text-sm text-gray-500">
                                Enter your email and password to sign in!
                            </p>
                        </div>
                        <div>

                            @session('error')
                                <div class="rounded-xl border border-red-500 bg-red-50 p-4  mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="-mt-0.5 text-red-500">
                                            <box-icon class="fill-current" type='solid' name='error-alt'></box-icon>
                                        </div>

                                        <div>
                                            <h4 class="mb-1 text-sm font-semibold text-gray-800">
                                                Oops!
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{!! session('error') !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endsession

                            @session('success')
                                <div class="rounded-xl border border-green-500 bg-green-50 p-4  mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="-mt-0.5 text-green-500">
                                            <box-icon class="fill-current" type='solid' name='party'></box-icon>
                                        </div>

                                        <div>
                                            <h4 class="mb-1 text-sm font-semibold text-gray-800">
                                                Hurray!
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{!! session('success') !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endsession

                            <form action="{{ route('lawyer.signin') }}" method="POST">
                                @csrf
                                <div class="space-y-5">
                                    <!-- Email -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" id="email" name="email"
                                            placeholder="Enter your email"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary focus:outline-hidden focus:ring-3"
                                            required />
                                        @error('email')
                                            <small class="text-red-500 inline-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- Password -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Password <span class="text-red-500">*</span>
                                        </label>
                                        <div x-data="{ showPassword: false }" class="relative">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                placeholder="Enter your password" name="password"
                                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary focus:outline-hidden focus:ring-3 focus:ring-secondary"
                                                required />
                                            <box-icon @click="showPassword=true" x-show="!showPassword"
                                                class="fill-current absolute z-10 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer right-4"
                                                name='show'></box-icon>
                                            <box-icon @click="showPassword=false" x-show="showPassword"
                                                class="fill-current absolute z-10 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer right-4"
                                                name='hide'></box-icon>
                                        </div>
                                        @error('password')
                                            <small class="text-red-500 inline-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- Checkbox -->
                                    <div class="flex items-center justify-between">
                                        <div x-data="{ checkboxToggle: false }">
                                            <label for="remember"
                                                class="flex items-center text-sm font-normal text-gray-700 cursor-pointer select-none">
                                                <div class="relative">
                                                    <input type="checkbox" id="remember"
                                                        class="mr-1 accent-secondary" />
                                                </div>
                                                Keep me logged in
                                            </label>
                                        </div>
                                        <a href="/reset-password.html"
                                            class="text-sm text-secondary hover:text-brand-600 dark:text-brand-400">Forgot
                                            password?</a>
                                    </div>
                                    <!-- Button -->
                                    <div>
                                        <button
                                            class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-secondary shadow-theme-xs hover:bg-secondary-600">
                                            Sign In
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-5">
                                <p class="text-sm font-normal text-center text-gray-700 sm:text-start">
                                    Don't have an account?
                                    <a href="{{ route('lawyer.signup') }}"
                                        class="text-brand-500 underline hover:text-primary dark:text-brand-400">Sign
                                        Up</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative bg-primary items-center hidden w-full h-full bg-brand-950 lg:grid lg:w-1/2">
                <div class="flex items-center justify-center z-1">
                    <!-- ===== Common Grid Shape Start ===== -->
                    <div class="absolute right-0 top-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]">
                        <img src="https://demo.tailadmin.com/src/images/shape/grid-01.svg" alt="grid" />
                    </div>
                    <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]">
                        <img src="https://demo.tailadmin.com/src/images/shape/grid-01.svg" alt="grid" />
                    </div>

                    <div class="flex flex-col items-center max-w-xs">
                        <a href="index.html" class="block mb-4">
                            <img src="{{ asset('logo.png') }}" alt="Logo" />
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div> --}}
    <div class="page-content d-flex align-items-center justify-content-center">

        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-6 mx-auto">
                <div class="card">
                    <div class="row">
                        <div class="col-md-4 pe-md-0">
                            <div class="auth-side-wrapper"
                                style="background-image: url({{ url('https://via.placeholder.com/219x452') }})">

                            </div>
                        </div>
                        <div class="col-md-8 ps-md-0">
                            <div class="auth-form-wrapper px-4 py-5">
                                <a href="#" class="noble-ui-logo d-block mb-2">Noble<span>UI</span></a>
                                <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>
                                <form class="forms-sample">
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="userEmail" placeholder="Email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="userPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="userPassword"
                                            autocomplete="current-password" placeholder="Password">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="authCheck">
                                        <label class="form-check-label" for="authCheck">
                                            Remember me
                                        </label>
                                    </div>
                                    <div>
                                        <a href="{{ url('/') }}"
                                            class="btn btn-primary me-2 mb-2 mb-md-0">Login</a>
                                        <button type="button"
                                            class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                            <i class="btn-icon-prepend" data-feather="twitter"></i>
                                            Login with twitter
                                        </button>
                                    </div>
                                    <a href="{{ url('/auth/register') }}" class="d-block mt-3 text-muted">Not a user?
                                        Sign up</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </x-lawyer.app>
