@extends("components.layout")

@section("title", "Register")

@section("content")
    <div class="bg-gray-300 flex justify-center items-center flex-col p-3">
        <h1>Regiser</h1>

        <form action="{{route('register')}}" method="POST">
            @csrf

            @error("auth")
                <span class="err-msg">{{$message}}</span>
            @enderror
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Johan Doe" required />
            </div>
            @error("name")
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
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="{{env('APP_DEBUG') ? 'text' : 'password'}}" name="password_confirmation"  required />
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
@endsection
