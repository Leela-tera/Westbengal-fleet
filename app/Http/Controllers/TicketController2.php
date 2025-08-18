<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;

use App\User;
use App\Fleet;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\RequestFilter;
use App\FleetWallet;
use App\WalletRequests;
use App\MasterTicket;
use DB;

use Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\TicketController;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
	
	
	/**
     * Create a function to insert tickets data through api.
     *
     * @return void
     */
	
	
	public function insertData()
    {
        ini_set("allow_url_fopen", 1);
        ini_set('max_execution_time', 5000);
		ini_set('memory_limit', '500M');
        error_reporting(0);
         
        // $url = 'https://dash.apsfl.co.in:8443/Calll/rest/pop/mo';

        // $headers = array('accept: */*','Content-type: application/json', 'Connection: Keep-Alive');
        ob_start();
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, 'https://dash.apsfl.co.in:8443/Calll/rest/pop/mo');
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $jsonData = json_decode(curl_exec($curlSession), true);
        curl_close($curlSession);
        //echo "<pre>";
        //print_r($jsonData);
        
			foreach ($jsonData as $keyvalue)
			{
			  $data = array('district' => $keyvalue['district'], 
			  'mandal' => $keyvalue['mandal'], 
			  'lat' => $keyvalue['lat'],
			  'log' => $keyvalue['log'], 
			  'downtime' => $keyvalue['downtime'],
			  'downdate' => $keyvalue['downdate'], 
			  'up_date' => $keyvalue['update'],
			  'up_time' => $keyvalue['uptime'], 
			  'downreason' => $keyvalue['downreason'],
			  'downreasonindetailed' => $keyvalue['downreasonindetailed'], 
			  'subsategory' => $keyvalue['subsategory'],
			  'ticketid' => $keyvalue['ticketid']
			  
			  );
			  
			  DB::table('master_tickets')->insert($data);
			   
			}
			//DB::table('users')->insert($values);
			 //MasterTicket::insert($data);
		
        ob_flush();//Flush the data here

/*curl close*/

    }


     
	/**
     * Send the request to user 
     * Added By Ashok
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request) {
		
		$getticketdetails = DB::table('master_tickets')
		->where('ticketid','!=','')
		->orderBy('created_at','desc')
		->take(1)
		->first();
		
		//dd($getticketdetails);

		//$userdetails = DB::table('users')
		//->where('id',45)
		//->first();

		//dd($userdetails->id);

		
		$distance = Setting::get('provider_search_radius', '10');
       
        $latitude = $getticketdetails->lat;
        $longitude = $getticketdetails->log;
        $service_type = 2;
        //address find
         $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');

         $json = curl($destinationgeocodeFromLatLong);

         $desdetails = json_decode($json, TRUE);
		  $desstatus = $desdetails['status'];
		  //dd($status);
		  $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';
         

         //close address  
        $Providers = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id','latitude','longitude')
            ->where('status', 'approved')
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->get();

            //dd($Providers);

            //address find
         $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$Providers[0]->latitude.",".$Providers[0]->longitude."&key=".Setting::get('map_key');

         $json = curl($sourcegeocodeFromLatLong);

         $srcdetails = json_decode($json, TRUE);
		  $srcstatus = $srcdetails['status'];
		  //dd($status);
		  $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
         

         //close address
        
	        if(count($Providers) == 0) {
              echo "no providers found";
            }

            try{

            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$Providers[0]->latitude.",".$Providers[0]->longitude."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');



            $json = curl($details);

            $details = json_decode($json, TRUE);

            $route_key = $details['routes'][0]['overview_polyline']['points'];

            $UserRequest = new UserRequests;
            $UserRequest->booking_id = Helper::generate_booking_id();
         

            $UserRequest->user_id =45;
            
         
            $UserRequest->current_provider_id = $Providers[0]->id;
            $UserRequest->provider_id = $Providers[0]->id;

            $UserRequest->service_type_id = 2;
            $UserRequest->rental_hours = 10;
            $UserRequest->payment_mode = 'CASH';
            $UserRequest->promocode_id = 0;
            
            $UserRequest->status = 'SEARCHING';

            $UserRequest->s_address =$saddress;
            $UserRequest->d_address =$daddress;

            $UserRequest->s_latitude = $Providers[0]->latitude;
            $UserRequest->s_longitude = $Providers[0]->longitude;

            $UserRequest->d_latitude = $latitude;
            $UserRequest->d_longitude = $longitude;
            $UserRequest->distance = $Providers[0]->distance;
            $UserRequest->unit = Setting::get('distance', 'Kms');

           
            $UserRequest->use_wallet = 0;
         

            if(Setting::get('track_distance', 0) == 1){
                $UserRequest->is_track = "YES";
            }

            $UserRequest->otp = mt_rand(1000 , 9999);

            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->route_key = $route_key;

            if($Providers->count() <= Setting::get('surge_trigger') && $Providers->count() > 0){
                $UserRequest->surge = 1;
            }

         
             //$UserRequest->schedule_at = " ";
             //$UserRequest->is_scheduled = 'NO';
            

            //dd( $UserRequest);

            $UserRequest->save();
           

            
               //dd("hiiii");
            if(Setting::get('manual_request',0) == 0){
                //foreach ($Providers as $key => $Provider) {

                    if(Setting::get('broadcast_request',0) == 1){
                       (new SendPushNotification)->IncomingRequest($Providers[0]->id); 
                    }

                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    
                    $Filter->request_id = $UserRequest->id;
                    $Filter->provider_id = $Providers[0]->id; 
                    $Filter->save();
                //}
            }

             echo "Request sent successfully";

        } catch (Exception $e) {            
            echo "something went wrong";
        }
     
	 
	 		
	}


 
}
