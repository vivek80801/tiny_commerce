@extends("components.layout")

@section("title", "Home")

@section("content")
    <div class="products-container">
        <div class="flex justify-around items-center">
            <div></div>
            <div>
                <form class="flex justify-between items-center m-2" action="{{route('home')}}" method="GET">
                    <input
                        class="border-3 m-2 rounded p-4 border-gray-400 outline-blue-600"
                        type="text"
                        name="search"
                        placeholder="Serach Product"
                        value="{{old('search')}}"
                    />
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <div class="flex justify-around items-center flex-col">
            <h3>Price Filters</h3>
            <form class="flex justify-around items-center flex-col" action="{{route('home')}}" method="GET">
                <input
                    class="border-3 m-2 rounded p-4 border-gray-400 outline-blue-600"
                    type="number"
                    name="min_price"
                    placeholder="min Price"
                    value="{{old('min_price')}}"
                />

                <input
                    class="border-3 m-2 rounded p-4 border-gray-400 outline-blue-600"
                    type="number"
                    name="max_price"
                    placeholder="max Price"
                    value="{{old('max_price')}}"
                />
                <button type="submit">Filter</button>
            </form>
        </div>
        <div class="products">
            @forelse($products as $product)
                <div class="product">
                    <img src="/storage/uploads/{{$product->image->filename}}" alt="Product Image">
                    <h4>{{$product->name}}</h4>
                    <b>&#x20B9;{{ $product->getPrice() }}</b>
                    <span>Category: {{$product->category->name}}</span>
                    @if($product->quantity <= 0)
                        <span class="bg-yellow-300 p-2 rounded text-red-600">Sold</span>
                    @elseif($product->quantity <= 20)
                        <span class="bg-yellow-300 p-2 rounded">Only {{$product->quantity}} items Left</span>
                    @endif
                    <p>{{$product->description}}</p>
                    @if($product->quantity > 0)
                        <div class="flex @guest justify-center @endguest @auth justify-between @endauth items-center w-full">
                            @auth
                                <a href="{{route('buynow', $product->id)}}">
                                    <button class="bg-pink-600 hover:bg-pink-800 text-white p-2 mt-2 rounded cursor-pointer">
                                        Buy Now
                                    </button>
                                </a>
                            @endauth
                            <a href="{{route('cart.add', $product->id)}}">
                                <button class="bg-blue-600 hover:bg-blue-800 text-white p-2 mt-2 rounded cursor-pointer">
                                    Add To Cart
                                </button>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <h2>No Products Found</h2>
            @endforelse
        </div>
        {{$products->links()}}
    </div>
@endsection
