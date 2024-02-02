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

        $delivery->tracking_number = $this->carrierService->SendDelivery(
            $recipient->name, 
            $recipient->phone_number, 
            $recipient->email, 
            $recipient->address,
            $parcels
        );   
            
        $delivery->save(); 

        return response()->json([
            'message' => 'Delivery created successfully',
            'delivery' => $delivery            
        ]);
    }
   
}
