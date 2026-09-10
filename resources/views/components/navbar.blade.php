<nav class="desktop-nav">
    <h2>Logo</h2>
    <ul class="flex justify-center items-center">
        <li><a href="{{route('home')}}">Home</a></li>
        @guest
            <li><a href="{{route('register')}}">Register</a></li>
            <li><a href="{{route('login')}}">Login</a></li>
        @endguest
        @auth
            <li><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li>
                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        @endauth
    </ul>
</nav>

<nav class="mobile-nav">
    <h2>Logo</h2>
    <div class="lines">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <ul>
        <li><a href="{{route('home')}}">Home</a></li>
        @guest
            <li><a href="{{route('register')}}">Register</a></li>
            <li><a href="{{route('login')}}">Login</a></li>
        @endguest
        @auth
            <li><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li>
                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        @endauth
    </ul>
</nav>

@push('js')
    <script>
        const lines = document.querySelector(".lines");

        lines && lines.addEventListener("click", function(){
            const mobileNavUl = document.querySelector(".mobile-nav > ul");
            mobileNavUl.classList.toggle("!flex");
        });
    </script>
@endpush
