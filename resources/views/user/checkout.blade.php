@extends("components.layout")

@section("title", "Checkout")

@section("content")
    <div class="bg-gray-300 flex justify-center items-center flex-col p-3">
        <form action="{{route('checkout')}}" method="POST">
            @csrf

            @if(count($addresses) <= 0)
                @include("user.address.partial.inputs",
                [
                    "url" => route("checkout")
                ])
            @else
                <div class="form-group">
                    <fieldset>
                        <legend>Address</legend>
                        @foreach($addresses as $address)
                            <label for="{{$address->address}}">
                                <input
                                    class="p-3"
                                    type="radio"
                                    name="address"
                                    value="{{$address->id}}" />
                                    {{$address->address}}
                            </label></br>
                        @endforeach
                    </fieldset>
                </div>
                @error("address")
                    <span class="err-msg">{{$message}}</span>
                @enderror
                <a
                    href="{{route('address.create')}}"
                    class="
                        text-blue-600
                        cursor-pointer
                        underline
                    "
                    >Create New Address</a>
                </br>
                </br>
            @endif

            <button type="submit">Submit</button>
        </form>
    </div>
@endSection
