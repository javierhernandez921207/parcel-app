<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Parcel;
use App\Models\Recipient;
use Illuminate\Http\Request;
use App\Services\CarrierService;

class DeliveryController extends Controller
{
    private $carrierService;

    public function __construct(CarrierService $carrierService) {
        $this->carrierService = $carrierService;
    }
   
    /**
     * We save the data of a shipment, the package data and call the carrier service 
     * to obtain a trackin_number to know its status in the future.
     *  
     * I assume that the recipient is already registered in the application
     * and I use a request like the following.
     * 
     * {
     * "recipient_id":1,
     * "parcels":[
     *      {"width":23,"height":20,"length":23,"weight":6},
     *      {"width":12,"height":12,"length":2,"weight":3}],
     * "status":"Send",
     * "carrier":"Fedex"
     * }
     * 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $delivery = new Delivery();
        $recipient = Recipient::find($request->input('recipient_id'));
        
        if (!$recipient) {  
            return response()->json([
                'message' => 'Recipient not found'
            ], 404);            
        }
                
        $parcels = $request->input('parcels');

        if (empty($parcels)) {
            return response()->json([
                'message' => 'Parcels not found'
            ], 404);
        }
        $delivery->recipient_id = $recipient->id;
        $delivery->status = $request->input('status');
        $delivery->carrier = $request->input('carrier');    
        $delivery->tracking_number = 0;     
        $delivery->save();
        
        foreach ($parcels as $p) {
            $parcel = new Parcel();
            $parcel->width = $p['width'];
            $parcel->height = $p['height'];
            $parcel->length = $p['length'];
            $parcel->weight = $p['weight'];
            $parcel->delivery_id = $delivery->id;
            $parcel->save();
        }

        /**
         * If the request defines the FedEx service, I used this service or another.
         * Through the tracking number, the recipient can know the status of their shipment.
         */
        if($delivery->carrier == 'Fedex')
        {
            $delivery->tracking_number = $this->carrierService->SendDeliveryFedex(
                $recipient->name, 
                $recipient->phone_number, 
                $recipient->email, 
                $recipient->address,
                $parcels
            );
        }
            
        $delivery->save(); 

        return response()->json([
            'message' => 'Delivery created successfully',
            'delivery' => $delivery            
        ]);
    }
   
}
