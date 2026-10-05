<!DOCTYPE html>
<html>
    <head>
        <title>
            Invoice
        </title>
        <style>
            * {
                box-sizing: border-box;
                padding: 0;
                margin:0;
            }
            body {
                font-family: "DejaVu Sans", system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif,;
            }

            .container {
                margin: 20pt 40pt;
            }

            .container > h1 {
                text-align: center;
                color: blue;
            }

            h1 {
                color: blue;
            }

            .img {
                width: 70pt;
                height: 70pt;
                border-radius: 50%;
                background-color: black;
            }

            .head {
                width: 100%;
            }

            .head::after {
                content: "";
                clear:both;
                display: block;
            }

            .logo {
                float: left;
            }

            .company {
                float: right;
                text-align: right;
            }

            .table {
                display: table;
                margin: 30pt auto;
                border: 1px solid #000;
            }

            .table-row {
                display: table-row;
            }

            .table-cell {
                display: table-cell;
                padding: 1.5rem;
                border: 1px solid #000;
            }

            .colspan-4 {
                width: 70%;
            }

            .table-header-group {
                display: table-header-group;
            }

            .table-row-group {
                display: table-row-group;
            }

            .table-footer-group {
                display: table-header-group;
            }

            .table-column {
                display: table-column;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Invoice</h1>
            <div class="head">
                <div class="logo">
                    <div class="img"></div>
                </div>
                <div class="company">
                    <h2>Company Name</h2>
                    <h5>Company Tag</h5>
                </div>
            </div>
            @if($user)
                <h3 style="text-align: center; color: green;">{{$user->name}}</h3>
                <h3 style="text-align: center; color: green;">{{$user->email}}</h3>
            @endif
            @if(count($orderItems) > 0)
                <div class="table">
                    <div class="table-header-group">
                        <div class="table-row">
                            <div class="table-cell">
                                <h4>Name</h4>
                            </div>
                            <div class="table-cell">
                                <h4>Price</h4>
                            </div>
                            <div class="table-cell">
                                <h4>Quantity</h4>
                            </div>
                            <div class="table-cell">
                                <h4>Amount</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-row-group">
                        @foreach($orderItems as $orderItem)
                            <div class="table-row">
                                <div class="table-cell">
                                    <span>{{$orderItem->product->name}}</span>
                                </div>
                                <div class="table-cell">
                                    <span>&#x20B9;{{$orderItem->product->getPrice()}}</span>
                                </div>
                                <div class="table-cell">
                                    <span>{{ $orderItem->quantity }}</span>
                                </div>

                                <div class="table-cell">
                                    <span>&#x20B9;{{$orderItem->amount / 100}}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="table-footer-group">
                        <div class="table-row">
                            <div class="table-cell colspan-4">
                                Total
                            </div>
                            <div class="table-cell">
                                &#x20B9;{{$order->total / 100}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>

