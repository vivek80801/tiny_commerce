@extends("components.layout")

@section("title", "Order Detail")

@section("content")
    <div class="overflow-x-auto">
        <h2 class="m-3">Order Detail</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($orderDetails as $orderDetail)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{$orderDetail->product->name}}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><img
                            src="/storage/uploads/{{$orderDetail->product->image->filename}}"
                            width="100px"
                            height="100px"
                            class="rounded"
                            alt="{{$orderDetail->product->name}}"/></td>

                        <td class="px-6 py-4 whitespace-nowrap">&#x20B9;{{$orderDetail->price / 100}}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{$orderDetail->quantity}}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">&#x20B9;{{$orderDetail->amount / 100}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-center items-center">
            <a href="{{route('dashboard')}}">
                <button
                    class="
                    p-3 bg-blue-600 m-2 rounded
                    text-white hover:bg-blue-800
                    cursor-pointer
                    "
                    >
                    Back
                </button>
            </a>
        </div>
    </div>
@endsection
