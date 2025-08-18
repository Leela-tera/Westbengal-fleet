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

use App\FleetWallet;
use App\WalletRequests;
use App\MasterTicket;
use GuzzleHttp\Client;

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
	
	
	public function insertData()
    {
		
		ini_set('max_execution_time', 300);
		$client = new Client();
        $res = $client->request('GET', 'https://dash.apsfl.co.in:8443/Calll/rest/pop/mo', [
            'form_params' => [
                'client_id' => 'test_id',
                'secret' => 'test_secret',
            ]
        ]);

        $result= $res->getBody();
        //dd($result);
		
        
         
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
         MasterTicket::chunk(200, function($jsonData)
		{
			foreach ($jsonData as $keyvalue)
			{
			  $data = array('district' => $keyvalue['district'], 
			  'mandal' => $keyvalue['mandal'], 
			  'lat' => $keyvalue['lat'],
			  'log' => $keyvalue['log'], 
			  'downtime' => $keyvalue['downtime'],
			  'downdate' => $keyvalue['downdate'], 
			  'update' => $keyvalue['update'],
			  'uptime' => $keyvalue['uptime'], 
			  'downreason' => $keyvalue['downreason'],
			  'downreasonindetailed' => $keyvalue['downreasonindetailed'], 
			  'subsategory' => $keyvalue['subsategory'],
			  'ticketid' => $keyvalue['ticketid']
			  
			  );
			  dd($data);
			  MasterTicket::insert($data);  
			}
		});
     
       // ob_flush();//Flush the data here

/*curl close*/

    }
	
	
	
	public function sinsertData()
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
         MasterTicket::chunk(200, function($jsonData)
		{
			foreach ($jsonData as $keyvalue)
			{
			  $data = array('district' => $keyvalue['district'], 
			  'mandal' => $keyvalue['mandal'], 
			  'lat' => $keyvalue['lat'],
			  'log' => $keyvalue['log'], 
			  'downtime' => $keyvalue['downtime'],
			  'downdate' => $keyvalue['downdate'], 
			  'update' => $keyvalue['update'],
			  'uptime' => $keyvalue['uptime'], 
			  'downreason' => $keyvalue['downreason'],
			  'downreasonindetailed' => $keyvalue['downreasonindetailed'], 
			  'subsategory' => $keyvalue['subsategory'],
			  'ticketid' => $keyvalue['ticketid']
			  
			  );
			  dd($data);
			  MasterTicket::insert($data);  
			}
		});
     
       // ob_flush();//Flush the data here

/*curl close*/

    }


 
}
