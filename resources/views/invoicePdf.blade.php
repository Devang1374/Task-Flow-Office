<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            line-height: 1.4;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            padding: 20px;
        }

        .layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .layout-table td {
            padding: 0;
            vertical-align: top;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }
        
        .title h1 {
            font-size: 36px;
            color: #333;
            margin: 0;
            text-align: right;
            font-weight: 300;
        }

        .text-blue {
            color: #0076c0;
            font-weight: bold;
            text-decoration: none;
        }
        
        address {
            font-style: normal;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .order-meta-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        
        .order-meta-table td, .order-meta-table th {
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .order-meta-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        .product-table th {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-weight: bold;
            padding: 10px;
            font-size: 12px;
        }
        
        .product-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .total-table {
            width: 50%;
            margin-left: auto; 
            border-collapse: collapse;
            margin-top: 10px;
        }

        .total-table td, .total-table th {
            padding: 8px 10px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .bank-details {
            margin-top: 5px;
            font-size: 13px;
            line-height: 1.2;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            right: 20px;
            font-size: 11px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <table class="layout-table">
            <tr>
                <td class="logo">    
                    <img src="{{ public_path('images/taskFlow-logo.png') }}" alt="Logo">
                </td>
                <td class="title">    
                    <h1>Invoice</h1>
                </td>
            </tr>
        </table>

        <table class="layout-table" style="margin-top: 40px;">
            <tr>
                <td style="width: 50%;">
                    <strong>From:</strong><br>
                    <span class="text-blue">{{ $invoice['company_name'] }}</span><br>
                    <address>
                        {{ $invoice['company_address'] }}<br>
                        {{ $invoice['company_number'] }}
                    </address>
                    <a href="mailto:{{ $invoice['company_email'] }}" class="text-blue">{{ $invoice['company_email'] }}</a>
                </td>
                
                <td style="width: 50%;">
                    <table class="order-meta-table">
                        <tr>
                            <td>Invoice Number</td>
                            <td>{{ $invoice['invoice_number'] }}</td>
                        </tr>
                        <tr>
                            <td>Order Number</td>
                            <td>{{ $invoice['order_number'] }}</td>
                        </tr>
                        <tr>
                            <td>Invoice Date</td>
                            <td>{{ $invoice['created_at'] }}</td>
                        </tr>
                        <tr>
                            <td>Due Date</td>
                            <td>{{ $invoice['due_date'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Due (Tax Excluded)</th>
                            <th>{{ $totalDue }}</th>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="layout-table" style="margin-top: 20px;">
            <tr>
                <td>
                    <strong>To:</strong><br>
                    <span>{{ $invoice['customer_name'] }}</span><br>
                    <address>
                        {{ $invoice['customer_address'] }}<br>
                        {{ $invoice['customer_number'] }}
                    </address>
                    <a href="mailto:{{ $invoice['customer_email'] }}" class="text-blue">{{ $invoice['customer_email'] }}</a>
                </td>
            </tr>
        </table>

        <table class="product-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 10%;">Qty.</th>
                    <th style="text-align: left; width: 45%;">Product</th>
                    <th class="text-right" style="width: 15%;">Rate/Price</th>
                    <th class="text-right" style="width: 15%;">Tax</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $sub = [];
                    $priceTotal = 0;
                @endphp
                @foreach($products as $product)
                <tr>
                    <td class="text-center">{{ $product['quantity'] }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td class="text-right">{{ $product['price'] }}</td>
                    <td class="text-right">{{ $product['tax'] }}%</td>
                    <td class="text-right">
                        @php
                            $priceTotal = $product['quantity'] * $product['price'];
                            if($product['tax'] == 0){
                                $sub[] = $priceTotal;    
                            } else {
                                $priceTotal += $priceTotal * ($product['tax'] / 100);
                                $sub[] = $priceTotal; 
                            }
                        @endphp
                        {{ number_format($priceTotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="layout-table">
            <tr>
                <td style="width: 50%; padding-top: 20px;">
                    <div class="bank-details">
                        <p style="margin: 0; font-weight: bold;">ANZ Bank</p>
                        <p style="margin: 0;">ACC # 1234 1234</p>
                        <p style="margin: 0;">BSB # 4321 432</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <table class="total-table">
                        @foreach($sub as $s)
                        <tr>
                            @php $total += $s; @endphp
                            <td class="text-right" style="color: #777;">Sub Total</td>
                            <td class="text-right">{{ number_format($s, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th class="text-right" style="border-top: 1px solid #333; padding-top: 10px;">Total</th>
                            <th class="text-right" style="border-top: 1px solid #333; padding-top: 10px;">{{ number_format($total, 2) }}</th>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>

    <div class="footer">
        <div class="terms">
            {{ $invoice['terms'] }}
        </div>
    </div>
</body>
</html>
