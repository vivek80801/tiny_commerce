@extends("components.layout")

@section("title", "Dashboard")

@section("content")
    <div class="flex justify-around items-center flex-col">
        <h1 class="m-3">Profile</h1>
        <span>Name: <b>{{auth()->user()->name}}</b></span>
        <span>Email: {{auth()->user()->email}}</span>
        <div class="overflow-x-auto">
            <h2 class="m-3">Orders</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Id</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Detail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
            @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">ORD-#{{$order->order_id}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{$order->address->address}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a class="underline text-blue-500" href="{{route('orderdetail', $order->id)}}">View Detail</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">&#x20B9;{{$order->total / 100}}</td>
                    @if(
                        file_exists(
                            \App\Helpers\pathForInvoice(
                                \App\Helpers\orderInvoicePath(
                                    $order
                                )
                            )
                        )
                    )
                        <td>
                            <a
                                class="underline text-blue-500"
                                href="{{
                                    \App\Helpers\orderInvoiceUrl(
                                        $order
                                    )
                                }}">View Receipt</a>
                        </td>
                    @else
                        <td>
                            <a
                            class="underline text-blue-500"
                            href="{{
                                route(
                                    'generate_invoice',
                                    $order->id
                                )
                        }}">
                                Generate Receipt
                            </a>
                        </td>
                    @endif
                </tr>
            @endforeach
                </tbody>
            </table>
            {{$orders->links()}}
        </div>

        {{--<div class="flex mt-3 w-250 h-5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
            <div
                class="flex flex-col justify-center rounded-full overflow-hidden bg-indigo-600 text-xs text-white text-center whitespace-nowrap dark:bg-indigo-500 transition duration-500"
                style="width: 25%"
                >
                25%
            </div>
        </div> --}}

        <div class="m-3 flex justify-around items-center flex-col">
            <h1 class="m-3">Addresses</h1>
            @forelse($addresses as $address)
                <div class="m-3 flex justify-around items-center flex-col">
                    <h3>Address {{$loop->iteration}}</h3>
                    <span>Name: {{$address->name}}</span>
                    <span>Phone Number: {{$address->phone}}</span>
                    <span>House Number: {{$address->house_number}}</span>
                    <span>City: {{$address->city}}</span>
                    <span>Pin Code: {{$address->pin_code}}</span>
                    <span>Address: {{$address->address}}</span>
                    <span>Country: {{$address->country->name}}</span>
                    <span>State: {{$address->state->name}}</span>
                    <span>District: {{$address->district->name}}</span>
                </div>
            @empty
                <h3>No Address Found</h3>
            @endforelse
        </div>
    </div>
@endSection
