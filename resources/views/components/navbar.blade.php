<nav class="desktop-nav">
    <h2>Logo</h2>
    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact Us</a></li>
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
        <li><a href="#">Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact Us</a></li>
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
