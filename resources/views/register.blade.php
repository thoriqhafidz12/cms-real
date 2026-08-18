<x-template-top />

<body class="bg-gradient-primary"
    style="background-image: url('{{ url('/') }}/assets/img/bg-login.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-10 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">{{ $titlePage }}</h1>
                            </div>

                            {{-- Notifikasi Gagal / Berhasil --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="user" action="{{ url($route) }}" method="POST" id="register-form">
                                @csrf
                                <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                                
                                @foreach ($form as $field)
                                    <div class="form-group">
                                        <label for="{{ $field['name'] }}">{{ $field['label'] }}</label><span
                                            class="text-danger"> *</span>
                                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                            id="{{ $field['name'] }}"
                                            class="form-control form-control-user @error($field['name']) is-invalid @enderror"
                                            value="{{ old($field['name']) }}"
                                            placeholder="{{ $field['placeholder'] }}"
                                            {{ !empty($field['required']) ? 'required' : '' }}>
                                        @error($field['name'])
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Daftar
                                </button>
                                <hr>
                            </form>
                            {{-- <hr> --}}
                            <div class="text-center">
                                <a class="btn btn-sm btn-success btn-user" href="{{ route('login') }}">Login !</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

@if (config('services.recaptcha.enabled'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    <script>
        document.getElementById('register-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = this;

            grecaptcha.ready(function() {
                grecaptcha.execute(
                    '{{ config('services.recaptcha.site_key') }}', {
                        action: 'register'
                    }
                ).then(function(token) {

                    document.getElementById('recaptcha_token').value = token;

                    form.submit();
                });
            });
        });
    </script>
@endif

<x-template-bottom />
