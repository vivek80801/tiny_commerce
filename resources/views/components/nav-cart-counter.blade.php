@php
    $cart_count = 0;
    if(auth()->user())
    {
        $cart_count = \App\Models\Cart::where("user_id", auth()->user()->id)->count();
    } else {
        if(\Illuminate\Support\Facades\Cookie::get("guest_token"))
        {
            $token = \Illuminate\Support\Facades\Cookie::get("guest_token");
            $cart_count = \App\Models\Cart::where("guest_token", $token)->count();
        }
    }
@endphp
<li><a href="{{route('cart.index')}}">Cart <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{$cart_count}}</a></li>
