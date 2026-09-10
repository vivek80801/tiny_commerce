@extends("components.layout")

@section("title", "Login")

@section("content")
    <div class="bg-gray-300 flex justify-center items-center flex-col p-3">
        <h1>Login</h1>

        <form action="{{route('login')}}" method="POST">
            @csrf

            @error("auth")
                <span class="err-msg">{{$message}}</span>
            @enderror
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="johan@gmail.com " required />
            </div>
            @error("email")
                <span class="err-msg">{{$message}}</span>
            @enderror
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="{{env('APP_DEBUG') ? 'text' : 'password'}}" name="password"  required />
            </div>
            @error("password")
                <span class="err-msg">{{$message}}</span>
            @enderror
            <button type="submit">Login</button>
        </form>
    </div>
@endsection
