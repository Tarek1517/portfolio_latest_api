<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Library\SslCommerz\SslCommerzNotification;

class SslCommerzPaymentService
{
    public function initiatePayment(array $data, $orderId)
    {

        $post_data = [
            'total_amount' => $data['investment_amount'],
            'currency' => "BDT",
            'tran_id' => uniqid(),
            'product_name' => $data['package_name'],
            // Customer Information
            'cus_name' => $data['cus_name'] ?? 'N/A',
            'cus_email' => $data['cus_email'] ?? 'N/A',
            'cus_add1' => $data['cus_address'] ?? 'N/A',
            'cus_country' => "Bangladesh",
            'cus_phone' => $data['cus_phone'] ?? 'N/A',
            'sub_total' => $data['investment_amount'] ?? 'N/A',
            'grand_total' => $data['investment_amount'],
            // Shipment Information
            'shipping_method' => 'NO',
            'ship_name' => "Store Test",
            'ship_add1' => "Dhaka",
            'ship_city' => "Dhaka",
            'ship_postcode' => "1000",
            'ship_country' => "Bangladesh",
            'product_category' => "Goods",
            'product_profile' => "physical-goods",
            // Optional
            'value_a' => $orderId,
            'value_b' => "ref002",
        ];
        // Initiate SSLCommerz Payment
        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');
        return $payment_options;
    }

}
