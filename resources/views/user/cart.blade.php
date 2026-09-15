@extends("components.layout")

@section("title", "Cart")

@section("content")
    @if(count($carts) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
            @foreach($carts as $cart)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">{{$cart->product->name}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img
                        src="/storage/uploads/{{$cart->product->image->filename}}"
                        width="100px"
                        height="100px"
                        class="rounded"
                        alt="{{$cart->product->name}}"/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{$cart->quantity}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">&#x20B9;{{$cart->product->getPrice()}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">&#x20B9;{{
                        ($cart->quantity * $cart->product->price) / 100
                    }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a class="{{
                                $cart->product->quantity === $cart->quantity
                                ? 'pointer-events-none' : ''
                            }}"
                           href="
                           {{
                               route('cart.inc', $cart->product->id)
                           }}">
                            <button
                                class="
                                    p-3 bg-blue-600 text-white hover:bg-blue-800 cursor-pointer rounded
                                    disabled:bg-blue-300
                                "
                                {{
                                    $cart->product->quantity === $cart->quantity
                                    ? 'disabled' : ''
                                }}
                                >
                                +
                            </button>
                        </a>
                        <a href="{{route('cart.dec', $cart->product->id)}}">
                            <button class="p-3 bg-red-600 text-white hover:bg-red-800 cursor-pointer rounded">
                                @if($cart->quantity <= 1)
                                    Delete
                                @else
                                    -
                                @endif
                            </button>
                        </a>
                    </td>
                </tr>
            @endforeach
            <tr >
                <td
                    class="px-6 py-4 whitespace-nowrap"
                    colspan="3">Total</td>
                <td
                    class="px-6 py-4 whitespace-nowrap"
                    >&#x20B9;{{$cartsSum / 100}}</td>
            </tr>
                </tbody>
            </table>
            {{$carts->links()}}
        </div>
        <div class="flex justify-center items-center">
            <a href="{{route('checkout')}}">
                <button
                    class="
                    p-3 bg-blue-600 m-2 rounded
                    text-white hover:bg-blue-800
                    cursor-pointer
                    "
                    >
                    Checkout
                </button>
            </a>
        </div>
    @else
        <h3 class="p-5 text-center">No Items found in your cart</h3>
    @endif
@endsection
