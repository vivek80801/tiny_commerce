@extends("components.layout")

@section("title", "Address Create")

@section("content")
    <div class="bg-gray-300 flex justify-center items-center flex-col p-3">
        <form action="{{route('address.store')}}" method="POST">
            @csrf

            @include("user.address.partial.inputs", [
                "url" => route("address.create")
            ])
            <button type="submit">Submit</button>
        </form>
    </div>
@endsection
