@extends('layouts.layout')

@section('container')
    @if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
    @endif
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <script src="{{ asset('js/show-pass.js')}}"></script>

        <div class="form">
            <form action="{{ url('/login') }}" method="post">
                @csrf
                <div class="conten">
                    <h1 class="d-flex">Login</h1><br>
                    <ul>
                        <li>
                            <label for="username">Username:</label>
                        </li>
                        <li>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username Anda" required value="{{ old('username') ?? (Cookie::get('remember_username') ?? '') }}">
                        </li>
                        <li>
                            <label for="password">Password:</label>
                        </li>
                        <li>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password Anda" required value="{{ old('password') ?? (Cookie::get('remember_password') ?? '') }}">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" onclick="showPassword()">
                                    <i class="fa fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                     <!-- Checkbox Remember Me -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input"  {{ old('remember') || Cookie::get('remember_username') ? 'checked' : '' }}>
                        <label for="remember" class="form-check-label">Remember Me</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection


