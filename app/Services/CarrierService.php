<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CarrierService
{
    private $sender_address;

    public function __construct()
    {       
        $this->sender_address = env('SENDER_ADDRESS');
    }

    public function SendDeliveryFedex($customer_name, $phone_number, $email, $recipient_address, $parcels)
    {
        $url = 'https://fedex.test/api/delivery';
        $data = [
            'customer_name' => $customer_name,
            'phone_number' => $phone_number,
            'email' => $email,
            'sender_address' => $this->sender_address,
            'recipient_address' => $recipient_address,
            'parcels' => $parcels
        ];
        
        try {       
            $response = Http::post($url, $data);
            if ($response->successful()) {
                $tracking_number = $response->json()['tracking_number'];
                return $tracking_number;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
        
    }

    public function SendDeliveryDHL($customer_name, $phone_number, $email, $recipient_address, $parcels)
    {
        /*Here would be the structure to use another courier service */        
    }
}
