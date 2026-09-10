@extends("components.layout")

@section("title", "Home")

@section("content")
    <div class="products-container">
        <div class="products">
            @forelse($products as $product)
                <div class="product">
                    <img src="/storage/uploads/{{$product->image->filename}}" alt="Product Image">
                    <h4>{{$product->name}}</h4>
                    <b>&#x20B9;{{$product->price / 100}}</b>
                    @if($product->quantity <= 0)
                        <span class="bg-yellow-300">Sold</span>
                    @elseif($product->quantity <= 20)
                        <span class="bg-yellow-300 p-2 rounded">Only {{$product->quantity}} items Left</span>
                    @endif
                    <p>{{$product->description}}</p>
                    <div class="flex justify-between items-center w-full">
                        <button class="bg-pink-600 hover:bg-pink-800 text-white p-2 mt-2 rounded cursor-pointer">Buy Now</button>
                        <button class="bg-blue-600 hover:bg-blue-800 text-white p-2 mt-2 rounded cursor-pointer">Add To Cart</button>
                    </div>
                </div>
            @empty
                <h2>No Products Found</h2>
            @endforelse
        </div>
        {{$products->links()}}
    </div>
@endsection
