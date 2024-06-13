<style>
    @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

    * {
        box-sizing: border-box;
    }

    table {
        font-family: 'Open Sans', sans-serif;
        width: 100%;
        border-collapse: collapse;
    }
    table, td, th {
        border: 1px solid;
    }
    @media print {
        body {-webkit-print-color-adjust: exact;}
        .helo{
            background: gray; line-height: 1px;
            print-color-adjust: exact;
        }
    }
    .helo{
        background: gray !important; line-height: 1px;
    }
</style>


    <table  cellspacing="0px" cellpadding="5px">
        <tr style="background: gray; line-height: 1px;" class="helo">
            <td colspan="2" style="color: white">

                <h3 style="padding-top:  24px;">{{ $order->branch_name }}</h3>

                <p>Order  {{ getSetting('order_code_prefix') }} {{ $order->orderGroup->order_code }}</p>
                <p>Order Date: {{ date('d M, Y', strtotime($order->created_at)) }}</p>
            </td>
            <td style="width: 50%;border-left: none;text-align: right">
                <p style="text-align: right;margin-top: -71px;float: right">
                    <img src="{{ uploadedAsset(getSetting('invoice_logo')) }}" alt="MRC Forms logo" height="100px" width="160px"></p>
            </td>
        </tr>
    </table>
<table cellspacing="0px" cellpadding="5px">
    <tr style="background: gainsboro;">
        <td style="text-align: center;width: 50%"><p><b>Sold to:</b></p></td>
        <td style="text-align: center;width: 50%"><p><b>Ship to:</b></p></td>
    </tr>
    <tr>
        <td style="font-size: 15px;">
            <p>{{ getSetting('system_title') }}</p>
            <p style="width: 300px;"> {{ getSetting('topbar_location') }}</p>
            <p>T: {{ getSetting('navbar_contact_number') }}</p>
        </td>
        <td style="font-size: 15px;width: 50%">
            @php
                $shippingAddress = $order->orderGroup->shippingAddress;
            @endphp
            <p>{{ optional($shippingAddress)->full_name }}</p>
            <p style="width: 300px;">

                    {{ optional($shippingAddress)->address }},
                    {{ optional(optional($shippingAddress)->city)->name }},
                    {{ optional(optional($shippingAddress)->country)->name }}

            </p>
            <p>T: {{ optional($shippingAddress)->phone }}</p>
        </td>
    </tr>
</table>

<table cellspacing="0px" cellpadding="5px" style="margin-top: 10px;">
    <tr style="background: gainsboro;">
        <td style="text-align: center;"><p><b>Payment Method:</b></p></td>
        <td style="text-align: center;"><p><b>Shipping Method:</b></p></td>
    </tr>
    <tr>
        <td style="font-size: 15px; width: 50%;">
            <p>{{ $order->orderGroup->payment_method == "cash_on_delivery" ? "Cash On Delivery" : ''  }}</p>
            <p> {{ $order->orderGroup->payment_method == "hbl_pay" ? "HBL(Habib Bank Limited)" : '' }}</p>
{{--            <p>Transaction ID:876246546546835</p>--}}
        </td>
        <td style="font-size: 15px; width: 50%;">
            <p> {{ localize('Logistic') }}: {{ $logistic->name }} - </p>
            <p>{{ $logistic->zones->standard_delivery_time  }} </p>
            <p>(Total Shipping Charges {{ formatPrice($order->orderGroup->total_shipping_cost) }})</p>
        </td>
    </tr>
</table>
<table cellspacing="0px" cellpadding="5px" style="margin-top: 10px;">
    <tr style="background: gainsboro;">
        <th>
            Sku
        </th>
        <th>
            Product Name
        </th>
        <th>
            Price
        </th>
        <th>
            Qty
        </th>
        <th>
            Discount
        </th>
        <th>
            Discount%
        </th>
{{--        <th>--}}
{{--            Discount Name--}}
{{--        </th>--}}
        <th>
            Subtotal
        </th>
    </tr>
<?php
    $item_count = 0;
    $discount_count = 0;
    $subtotal_count = 0;

//    $grand_total = $subtotal_count + $shipping;
?>
    @foreach ($order->orderItems as $key => $item)
        @php
            $product = $item->product_variation->product;
            $bar_code = \App\Models\ProductPrice::where('product_id',$product->id)->first()->bar_code;

        @endphp

    <tr>
        <td style="width:14%;">
            {{ $bar_code }}
        </td>
        <td style="width:36%;">
            {{ $product->collectLocalization('name') }}
        </td>
        <td>
            {{ formatPrice($item->unit_price) }}
        </td>
        <td>
            {{ $item->qty }}

            <?php
                $qty = intval($item->qty);
                $item_count += $qty   ?>
        </td>
        <td>
            @if($item->discount_value > 0)
                {{ $item->discount_value  }}
            @else
                0
            @endif
        </td>
        <td>
            @if($item->discount_value > 0)
                {{ $item->discount_value  }} {{ $item->discount_type == "percent" ? "%" : "fixed" }}
            @endif

            <?php
                    $discount_count += $item->discount_value;
                    ?>

        </td>
{{--        <td>--}}
{{--            Clearance Sale--}}
{{--        </td>--}}
        <td>
            {{ formatPrice($item->total_price) }}
            <?php
                $subtotal_count +=$item->total_price
                ?>
        </td>
    </tr>

    @endforeach
    <tr>
        <td colspan="3" style="text-align: center;"><b>Total</b></td>
        <td><b>{{ $item_count }}</b></td>
        <td><b>{{ $discount_count }}</b></td>
        <td><b></b></td>
{{--        <td><b></b></td>--}}
        <td><b>{{ formatPrice($subtotal_count) }}</b></td>
    </tr>
</table>

<table cellspacing="0px" cellpadding="5px" style="margin-top: 10px; width: 50%;margin-left: 70%;">
    <tr>
        <td style="background: gainsboro;width: 50%">Subtotal:</td>
        <td style="width: 50%">{{ formatPrice($subtotal_count) }}</td>
    </tr>

    <tr>
        <td style="background: gainsboro;">Shipping & Handling:</td>
        <td>  {{ formatPrice($order->orderGroup->total_shipping_cost) }}
{{--        <?--}}
{{--            $shipping = $order->orderGroup->total_shipping_cost;--}}
{{--        ?>--}}
        </td>
    </tr>

    <tr>
        <td style="background: gainsboro;">Grand Total:</td>
        <td>{{ formatPrice($order->orderGroup->grand_total_amount) }}</td>
    </tr>
</table>
<div style="width: 58%; margin-top: -100px;">
    <p>Hello <strong> {{ optional($order->user)->name }}</strong>,</p>
    <p>Thank you for shopping from our store and for your order. it is awesome to have you as one of our paid users. We hope that you will be happy with Clearly, if you ever have any questions, suggestions, ns or concerns please  do not hesitate to contact us.</p>
</div>

<div style="line-height: 1px;">
    <p>Best Regards,</p>
    <p>MCForms</p>
    <p>Whatsapp:{{ getSetting('invoice_whatsapp_number') }}</p>
    <p>Website: {{url('/')}}</p>
</div>
