<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;
use \Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderResources\TripController;

use App\User;
use App\Fleet;
use App\Admin;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\CustomPush;
use App\AdminWallet;
use App\ProviderWallet;
use App\FleetWallet;
use App\WalletRequests;
use App\ProviderDocument;
use App\MasterTicket;
use ZipArchive;
use DB;
use Session;
use App\Block;
use App\Document;
use App\District;


class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('demo', ['only' => [
                'settings_store', 
                'settings_payment_store',
                'profile_update',
                'password_update',
                'send_push',
            ]]);
        $this->perpage = Setting::get('per_page', '10');
    }


    /**
     * Dashboard.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    { 
        try{
           
            Session::put('user', Auth::User());
           
            /*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

            echo "<pre>";
            print_r($UserRequest->toArray());exit;

            return view('emails.invoice',['Email' => $UserRequest]);*/

            
            //$rides = UserRequests::has('user')->orderBy('id','desc')->get();
            $rides = DB::table('user_requests')->join('users','user_requests.user_id','=','users.id')->where('user_requests.status','ONGOING')->orderBy('user_requests.id','desc')->get();
            $cancel_rides = UserRequests::with('masterticket')->where('status','CANCELLED')->get();

            $master_tickets = MasterTicket::with('jointicket')->where('ticketid','!=','')->count();
            $ongoing_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->orWhere('status','INCOMING')->count();
            $onhold_tickets = UserRequests::with('masterticket')->where('status','ONHOLD')->count();
            $scheduled_rides = UserRequests::with('masterticket')->where('status','SCHEDULED')->count();
            $completed_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->count();
            $ups = MasterTicket::with('jointicket')->where('ticketid','!=','')->where('downreason', 'like', '%POWER SHUTDOWN%')->orWhere('downreason', 'like', '%Power%')->orWhere('downreasonindetailed', 'like', '%Power')->count();
            $electronics = MasterTicket::with('jointicket')->where('ticketid','!=','')->where('downreason', 'like', '%ELECTRONICS%')->orWhere('downreason', 'like', '%Electronics%')->orWhere('downreasonindetailed', 'like', '%Electronics')->count();
            $fiber = MasterTicket::with('jointicket')->where('ticketid','!=','')->where('downreason', 'like', '%FIBER CUT%')->orWhere('downreason', 'like', '%Fiber%')->orWhere('downreasonindetailed', 'like', '%Fiber%')->count();
            $poles = MasterTicket::with('jointicket')->where('ticketid','!=','')->where('downreason', 'like', '%POLE CHANGE%')->orWhere('downreason', 'like', '%Pole Change%')->count();
            //dd($ups);
            $pending_tickets = UserRequests::with('masterticket')->where('status','SEARCHING')->count();
            $user_cancelled = UserRequests::with('masterticket')->where('status','CANCELLED')->where('cancelled_by','USER')->count();
            $provider_cancelled = UserRequests::with('masterticket')->where('status','CANCELLED')->where('cancelled_by','PROVIDER')->count();
            $cancel_rides = $cancel_rides->count();
            $service = ServiceType::count();
            $fleet = Fleet::count();
            
            $provider = Provider::count();
            $revenue = UserRequestPayment::sum('total');
            $wallet['tips'] = UserRequestPayment::sum('tips');
            $providers = Provider::take(10)->orderBy('rating','desc')->get();
            $wallet['admin'] = AdminWallet::sum('amount');
            $wallet['provider_debit'] = Provider::select(DB::raw('SUM(CASE WHEN wallet_balance<0 THEN wallet_balance ELSE 0 END) as total_debit'))->get()->toArray();
            $wallet['provider_credit'] = Provider::select(DB::raw('SUM(CASE WHEN wallet_balance>=0 THEN wallet_balance ELSE 0 END) as total_credit'))->get()->toArray();
            $wallet['fleet_debit'] = Fleet::select(DB::raw('SUM(CASE WHEN wallet_balance<0 THEN wallet_balance ELSE 0 END) as total_debit'))->get()->toArray();
            $wallet['fleet_credit'] = Fleet::select(DB::raw('SUM(CASE WHEN wallet_balance>=0 THEN wallet_balance ELSE 0 END) as total_credit'))->get()->toArray();

            $wallet['admin_tax'] = AdminWallet::where('transaction_type',9)->sum('amount');
            $wallet['admin_commission'] = AdminWallet::where('transaction_type',1)->sum('amount');
            $wallet['admin_discount'] = AdminWallet::where('transaction_type',10)->sum('amount');

            //dd("asdasdsad");
            return view('admin.dashboard',compact('providers','fleet','provider','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue', 'wallet','master_tickets','completed_tickets','pending_tickets','ongoing_tickets','onhold_tickets','ups','electronics','fiber','poles'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }


    /**
     * Heat Map.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function heatmap()
    {
        try{
           $rides = DB::table('user_requests')->join('users','user_requests.user_id','=','users.id')->orderBy('user_requests.id','desc')->get();
            $providers = Provider::take(100)->orderBy('rating','desc')->get();
            return view('admin.heatmap',compact('providers','rides'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }


    	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
		
        try{
             if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
            $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('providers.district_id', $request->district_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->orderBy('attendance.created_at','desc')->get();
              } else if(!empty($request->from_date) && !empty($request->to_date)){
            $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->orderBy('attendance.created_at','desc')->get();
              }
              else{
              $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('attendance.created_at','>=',Carbon::today())->orderBy('attendance.created_at','desc')->get();
              }

			$districts= DB::table('districts')->get();
			
            return view('admin.attendance',compact('providers','districts'));
        }
        catch(Exception $e){
            return redirect()->route('admin.attendance')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }
	
	
	
	
	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function attendancereport(Request $request)
    {
		
		
        try{

            //dd($request->from_date);
			
             if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
             $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
			->join('districts','providers.district_id','=','districts.id')
			->where('providers.district_id', $request->district_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
            //dd($providers);
              } else if(!empty($request->district_id)){
                $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
            ->join('districts','providers.district_id','=','districts.id')
            ->where('providers.district_id', $request->district_id )
            ->groupBy('providers.id')
            ->orderBy('attendance.created_at','desc')->get();
            // echo $providers;
              } else if(!empty($request->from_date) && !empty($request->to_date)){
                $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
            ->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
              }
              else{
				  
				  
              $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
			->join('districts','providers.district_id','=','districts.id')
			->whereDate('attendance.created_at','>=', DB::raw('DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, "%Y/%m/26")'))
			->whereDate('attendance.created_at','<=', DB::raw('DATE_FORMAT(CURRENT_DATE, "%Y/%m/25")'))
            ->groupBy('providers.id')
			->orderBy('attendance.created_at','desc')->get();
              }
			  

			 //dd($providers);

			$districts= DB::table('districts')->get();
			
            return view('admin.attendancereport',compact('providers','districts'));
        }
        catch(Exception $e){
            return redirect()->route('admin.reportattendance')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }




     /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function occ(Request $request)
    {   

                     
        if(!empty($request->page) && $request->page=='all'){
            $users =  DB::table('users')
            ->select('users.id','users.first_name','users.last_name','users.email','users.mobile','users.rating','users.district_id','users.created_at','users.type','districts.name' )
            ->join('districts', 'users.district_id', '=', 'districts.id')->where('type',2)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$users));
        }
        else{

             $users =  DB::table('users')
            ->select('users.id','users.first_name','users.last_name','users.email','users.mobile','users.rating','users.district_id','users.created_at','users.type','districts.name' )
            ->join('districts', 'users.district_id', '=', 'districts.id')->where('type',2)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($users);
            return view('admin.users.index', compact('users','pagination'));
        }

        
    }


    /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function frt(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',2)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',2)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }

    /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function zonalincharge(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',3)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',3)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }


   /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function districtincharge(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',4)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',4)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }


    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function map_index(Request $request)
    {
        $districts= DB::table('districts')->get();
        $district_id = $request->district_id;
		
        //dd($district_id);
        return view('admin.map.index',compact('districts','district_id'));
    }
	
	 /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function trackattendance(Request $request)
    {
        $districts= DB::table('districts')->get();
		$providers= DB::table('providers')->get();
        $district_id = $request->district_id;
		$provider_id = $request->provider_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
        //dd($district_id);
        return view('admin.attendancemap.index',compact('districts','district_id','providers','provider_id','from_date','to_date'));
    }


    /**
     * Map of all  gps.
     *
     * @return \Illuminate\Http\Response
     */
    public function tracklocations(Request $request)
    {
        $districts= DB::table('districts')->get();
		$providers= DB::table('providers')->get();
        $district_id = $request->district_id;
		$provider_id = $request->provider_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
        //dd($district_id);
        return view('admin.allmaps.index',compact('districts','district_id','providers','provider_id','from_date','to_date'));
    }


    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentlocation($id)
    {

          $Providers = Provider::where('id', '=', $id)
                    ->with('service')
                    ->first();

        return view('admin.map.current',compact('Providers'));
    }

    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function map_ajax(Request $request)
    {
        try {
            //dd($request->all());
           if(!empty($request->district_id)){
            $Providers = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->where('district_id', '=', $request->district_id)
                    ->with('service')
                    ->get();
             }else{
              $Providers = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->with('service')
                    ->get();
             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'user';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }
	
	
	
    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function trackmap_ajax(Request $request)
    {
        try {
            //dd($request->all());
           if(!empty($request->district_id)){
					$Providers =DB::table('providers')
					->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.status as astatus','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
					->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')
                    ->where('providers.district_id', '=', $request->district_id)
					->where('providers.id', '=', $request->provider_id)
					->where('attendance.created_at','>=',$request->from_date)
					->where('attendance.created_at','<=',$request->to_date)
                    ->get();
             }else{
               $Providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.status as astatus','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->leftjoin('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','=',Carbon::today())->orderBy('attendance.created_at','desc')->get();

             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'user';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }



     /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function alltrackmap_ajax(Request $request)
    {
        try {
            //dd($request->all());
           if(!empty($request->district_id)){
					$Providers =DB::table('providers')
					->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.status as astatus','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
					->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')
                    ->where('providers.district_id', '=', $request->district_id)
					->where('providers.id', '=', $request->provider_id)
					->where('attendance.created_at','>=',$request->from_date)
					->where('attendance.created_at','<=',$request->to_date)
                    ->get();
             }else{
               $Providers =DB::table('gp_list')
            ->select('gp_list.gp_name','gp_list.provider','gp_list.latitude','gp_list.longitude','gp_list.district_id','gp_list.lgd_code','districts.name as district_name','blocks.name as block_name','gp_list.status','gp_list.contact_no')
            ->join('districts','gp_list.district_id','=','districts.id')->join('blocks','blocks.id','=','gp_list.block_id')->get();

             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'location';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }

	
	
	
	

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        return view('admin.settings.application');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_store(Request $request)
    {
        $this->validate($request,[
                'site_title' => 'required',
                'site_icon' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
                'site_logo' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            ]);

        if($request->hasFile('site_icon')) {
            $site_icon = Helper::upload_picture($request->file('site_icon'));
            Setting::set('site_icon', $site_icon);
        }

        if($request->hasFile('site_logo')) {
            $site_logo = Helper::upload_picture($request->file('site_logo'));
            Setting::set('site_logo', $site_logo);
        }

        if($request->hasFile('site_email_logo')) {
            $site_email_logo = Helper::upload_picture($request->file('site_email_logo'));
            Setting::set('site_email_logo', $site_email_logo);
        }

        Setting::set('site_title', $request->site_title);
        Setting::set('store_link_android_user', $request->store_link_android_user);
        Setting::set('store_link_android_provider', $request->store_link_android_provider);
        Setting::set('store_link_ios_user', $request->store_link_ios_user);
        Setting::set('store_link_ios_provider', $request->store_link_ios_provider);
        Setting::set('store_facebook_link', $request->store_facebook_link);
        Setting::set('store_twitter_link', $request->store_twitter_link);
        Setting::set('provider_select_timeout', $request->provider_select_timeout);
        Setting::set('provider_search_radius', $request->provider_search_radius);
        Setting::set('sos_number', $request->sos_number);
        Setting::set('contact_number', $request->contact_number);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('site_copyright', $request->site_copyright);        
        Setting::set('social_login', $request->social_login);
        Setting::set('map_key', $request->map_key);
        Setting::set('fb_app_version', $request->fb_app_version);
        Setting::set('fb_app_id', $request->fb_app_id);
        Setting::set('fb_app_secret', $request->fb_app_secret);
        Setting::set('manual_request', $request->manual_request == 'on' ? 1 : 0 );
        Setting::set('broadcast_request', $request->broadcast_request == 'on' ? 1 : 0 );
        Setting::set('track_distance', $request->track_distance == 'on' ? 1 : 0 );
        Setting::set('distance', $request->distance);
        Setting::save();
        
        return back()->with('flash_success','Settings Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_payment()
    {
        return view('admin.payment.settings');
    }

    /**
     * Save payment related settings.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_payment_store(Request $request)
    {

        $this->validate($request, [
                'CARD' => 'in:on',
                'CASH' => 'in:on',
                'stripe_secret_key' => 'required_if:CARD,on|max:255',
                'stripe_publishable_key' => 'required_if:CARD,on|max:255',                
                'daily_target' => 'required|integer|min:0',
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'surge_percentage' => 'required|numeric|min:0|max:100',
                'commission_percentage' => 'required|numeric|min:0|max:100',
                'fleet_commission_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
                'surge_trigger' => 'required|integer|min:0',
                'currency' => 'required'
            ]);

        if($request->has('CARD')==0 && $request->has('CASH')==0){
            return back()->with('flash_error','Atleast one payment mode must be enable.');
        }

        Setting::set('CARD', $request->has('CARD') ? 1 : 0 );
        Setting::set('CASH', $request->has('CASH') ? 1 : 0 );
        Setting::set('stripe_secret_key', $request->stripe_secret_key);
        Setting::set('stripe_publishable_key', $request->stripe_publishable_key);
        //Setting::set('stripe_oauth_url', $request->stripe_oauth_url);
        Setting::set('daily_target', $request->daily_target);
        Setting::set('tax_percentage', $request->tax_percentage);
        Setting::set('surge_percentage', $request->surge_percentage);
        Setting::set('commission_percentage', $request->commission_percentage);
        Setting::set('provider_commission_percentage', 0);
        Setting::set('fleet_commission_percentage', $request->has('fleet_commission_percentage')?$request->fleet_commission_percentage : 0);
        Setting::set('surge_trigger', $request->surge_trigger);
        Setting::set('currency', $request->currency);
        Setting::set('booking_prefix', $request->booking_prefix);
        Setting::save();

        return back()->with('flash_success','Settings Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('admin.account.profile');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile_update(Request $request)
    {
        //print_r($request->all()); exit;
        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:admins,email,'.Auth::guard('admin')->user()->id.',id',
            'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try{
            $admin = Auth::guard('admin')->user();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->language = $request->language;
            
            if($request->hasFile('picture')){
                $admin->picture = $request->picture->store('admin/profile');             }
            $admin->save();

            Session::put('user', Auth::User());

            return redirect()->back()->with('flash_success','Profile Updated');
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password()
    {
        return view('admin.account.change-password');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password_update(Request $request)
    {

        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {

           $Admin = Admin::find(Auth::guard('admin')->user()->id);

            if(password_verify($request->old_password, $Admin->password))
            {
                $Admin->password = bcrypt($request->password);
                $Admin->save();

                return redirect()->back()->with('flash_success','Password Updated');
            }
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        try {
             $payments = UserRequests::where('paid', 1)
                    ->has('user')
                    ->has('provider')
                    ->has('payment')
                    ->orderBy('user_requests.created_at','desc')
                    ->paginate($this->perpage);

             $pagination=(new Helper)->formatPagination($payments);       
            
            return view('admin.payment.payment-history', compact('payments','pagination'));
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function help()
    {
        try {
            $str = file_get_contents('http://appoets.com/help.json');
            $Data = json_decode($str, true);
            return view('admin.help', compact('Data'));
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * User Rating.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_review()
    {
        try {
            $Reviews = UserRequestRating::where('user_id', '!=', 0)->with('user', 'provider')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($Reviews);
            return view('admin.review.user_review',compact('Reviews','pagination'));

        } catch(Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Provider Rating.
     *
     * @return \Illuminate\Http\Response
     */
    public function provider_review()
    {
        try {
            $Reviews = UserRequestRating::where('provider_id','!=',0)->with('user','provider')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($Reviews);
            return view('admin.review.provider_review',compact('Reviews','pagination'));
        } catch(Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProviderService
     * @return \Illuminate\Http\Response
     */
    public function destory_provider_service($id){
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Testing page for push notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function push_index()
    {

        $data = \PushNotification::app('AndroidUser')
            ->to('c2CHAz2meU8:APA91bHrRIMEu9gioDLIpo5ez-9exHgRlL5tlFfyXZ28me0dkQIUDdGzHXCI6mTyI9gZh4IEnPXSqrekavC22KcQYxbo5ql0Uahfh4mfKEL0ziAsDQwnv4ySQPDNnW5Wfcuc1C4GYipp')
            ->send('Hello World, i`m a push message');
        dd($data);
    }

    /**
     * Testing page for push notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function push_store(Request $request)
    {
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * privacy.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */

    public function cmspages(){
        return view('admin.pages.static');
    }

    /**
     * pages.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function pages(Request $request){
        $this->validate($request, [
                'types' => 'required|not_in:select',
            ]);

        Setting::set($request->types, $request->content);
        Setting::save();

        return back()->with('flash_success', 'Content Updated!');
    }

    public function pagesearch($request){
        $value = Setting::get($request);        
        return $value;        
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement($type = '', $request = null){

        try{

            $page = trans('admin.include.overall_ride_statments');
            $listname = trans('admin.include.overall_ride_earnings');
            if($type == 'individual'){
                $page = trans('admin.include.provider_statement');
                $listname = trans('admin.include.provider_earnings');
            }elseif($type == 'today'){
                $page = trans('admin.include.today_statement').' - '. date('d M Y');
                $listname = trans('admin.include.today_earnings');
            }elseif($type == 'monthly'){
                $page = trans('admin.include.monthly_statement').' - '. date('F');
                $listname = trans('admin.include.monthly_earnings');
            }elseif($type == 'yearly'){
                $page = trans('admin.include.yearly_statement').' - '. date('Y');
                $listname = trans('admin.include.yearly_earnings');
            }elseif($type == 'range'){
                $page = trans('admin.include.statement_from').' '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').'  '.trans('admin.include.statement_to').' '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
            }
            
            $rides = UserRequests::with('payment')->orderBy('id','desc');

            $cancel_rides = UserRequests::where('status','CANCELLED');
            $revenue = UserRequestPayment::select(\DB::raw(
                           'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
                       ));

            if($type == 'today'){

                $rides->where('created_at', '>=', Carbon::today());
                $cancel_rides->where('created_at', '>=', Carbon::today());
                $revenue->where('created_at', '>=', Carbon::today());

            }elseif($type == 'monthly'){

                $rides->where('created_at', '>=', Carbon::now()->month);
                $cancel_rides->where('created_at', '>=', Carbon::now()->month);
                $revenue->where('created_at', '>=', Carbon::now()->month);

            }elseif($type == 'yearly'){

                $rides->where('created_at', '>=', Carbon::now()->year);
                $cancel_rides->where('created_at', '>=', Carbon::now()->year);
                $revenue->where('created_at', '>=', Carbon::now()->year);

            }elseif ($type == 'range') {                
                if($request->from_date == $request->to_date) {
                    $rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $cancel_rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $revenue->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                } else {
                    $rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $cancel_rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $revenue->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                }
            }

            $rides = $rides->paginate($this->perpage);
            if ($type == 'range'){
                $path='range?from_date='.$request->from_date.'&to_date='.$request->to_date;
                $rides->setPath($path);
            }
            $pagination=(new Helper)->formatPagination($rides);
            $cancel_rides = $cancel_rides->count();
            $revenue = $revenue->get();

            return view('admin.providers.statement', compact('rides','cancel_rides','revenue','pagination'))
                    ->with('page',$page)->with('listname',$listname);

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * account statements today.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_today(){
        return $this->statement('today');
    }

    /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_monthly(){
        return $this->statement('monthly');
    }

     /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_yearly(){
        return $this->statement('yearly');
    }


    /**
     * account statements range.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_range(Request $request){
        return $this->statement('range', $request);
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_provider(){

        try{

            $Providers = Provider::paginate($this->perpage);

            $pagination=(new Helper)->formatPagination($Providers);

            foreach($Providers as $index => $Provider){

                $Rides = UserRequests::where('provider_id',$Provider->id)
                            ->where('status','<>','CANCELLED')
                            ->get()->pluck('id');

                $Providers[$index]->rides_count = $Rides->count();

                $Providers[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
                                ->select(\DB::raw(
                                   'SUM(ROUND(provider_pay)) as overall, SUM(ROUND(provider_commission)) as commission' 
                                ))->get();
            }

            return view('admin.providers.provider-statement', compact('Providers','pagination'))->with('page','Providers Statement');

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function translation(){

        try{
            return view('admin.translation');
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function push(){

        try{
            $Pushes = CustomPush::orderBy('id','desc')->get();
            return view('admin.push',compact('Pushes'));
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * pages.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function send_push(Request $request){


        $this->validate($request, [
                'send_to' => 'required|in:ALL,USERS,PROVIDERS',
                'user_condition' => ['required_if:send_to,USERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
                'provider_condition' => ['required_if:send_to,PROVIDERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
                'user_active' => ['required_if:user_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
                'user_rides' => 'required_if:user_condition,RIDES',
                'user_location' => 'required_if:user_condition,LOCATION',
                'user_amount' => 'required_if:user_condition,AMOUNT',
                'provider_active' => ['required_if:provider_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
                'provider_rides' => 'required_if:provider_condition,RIDES',
                'provider_location' => 'required_if:provider_condition,LOCATION',
                'provider_amount' => 'required_if:provider_condition,AMOUNT',
                'message' => 'required|max:100',
            ]);

        try{

            $CustomPush = new CustomPush;
            $CustomPush->send_to = $request->send_to;
            $CustomPush->message = $request->message;

            if($request->send_to == 'USERS'){

                $CustomPush->condition = $request->user_condition;

                if($request->user_condition == 'ACTIVE'){
                    $CustomPush->condition_data = $request->user_active;
                }elseif($request->user_condition == 'LOCATION'){
                    $CustomPush->condition_data = $request->user_location;
                }elseif($request->user_condition == 'RIDES'){
                    $CustomPush->condition_data = $request->user_rides;
                }elseif($request->user_condition == 'AMOUNT'){
                    $CustomPush->condition_data = $request->user_amount;
                }

            }elseif($request->send_to == 'PROVIDERS'){

                $CustomPush->condition = $request->provider_condition;

                if($request->provider_condition == 'ACTIVE'){
                    $CustomPush->condition_data = $request->provider_active;
                }elseif($request->provider_condition == 'LOCATION'){
                    $CustomPush->condition_data = $request->provider_location;
                }elseif($request->provider_condition == 'RIDES'){
                    $CustomPush->condition_data = $request->provider_rides;
                }elseif($request->provider_condition == 'AMOUNT'){
                    $CustomPush->condition_data = $request->provider_amount;
                }
            }

            if($request->has('schedule_date') && $request->has('schedule_time')){
                $CustomPush->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
            }

            $CustomPush->save();

            if($CustomPush->schedule_at == ''){
                $this->SendCustomPush($CustomPush->id);
            }

            return back()->with('flash_success', 'Message Sent to all '.$request->segment);
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    public function SendCustomPush($CustomPush){

        try{

            \Log::notice("Starting Custom Push");

            $Push = CustomPush::findOrFail($CustomPush);

            if($Push->send_to == 'USERS'){

                $Users = [];

                if($Push->condition == 'ACTIVE'){

                    if($Push->condition_data == 'HOUR'){

                        $Users = User::whereHas('trips', function($query) {
                            $query->where('created_at','>=',Carbon::now()->subHour());
                        })->get();
                        
                    }elseif($Push->condition_data == 'WEEK'){

                        $Users = User::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subWeek());
                        })->get();

                    }elseif($Push->condition_data == 'MONTH'){

                        $Users = User::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subMonth());
                        })->get();

                    }

                }elseif($Push->condition == 'RIDES'){

                    $Users = User::whereHas('trips', function($query) use ($Push){
                                $query->where('status','COMPLETED');
                                $query->groupBy('id');
                                $query->havingRaw('COUNT(*) >= '.$Push->condition_data);
                            })->get();


                }elseif($Push->condition == 'LOCATION'){

                    $Location = explode(',', $Push->condition_data);

                    $distance = Setting::get('provider_search_radius', '10');
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $Users = User::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                            ->get();

                }

                
               \Log::notice($users);

                foreach ($Users as $key => $user) {
                    (new SendPushNotification)->sendPushToUser($user->id, $Push->message);
                }

            }elseif($Push->send_to == 'PROVIDERS'){


                $Providers = [];

                if($Push->condition == 'ACTIVE'){

                    if($Push->condition_data == 'HOUR'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subHour());
                        })->get();
                        
                    }elseif($Push->condition_data == 'WEEK'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subWeek());
                        })->get();

                    }elseif($Push->condition_data == 'MONTH'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subMonth());
                        })->get();

                    }

                }elseif($Push->condition == 'RIDES'){

                    $Providers = Provider::whereHas('trips', function($query) use ($Push){
                               $query->where('status','COMPLETED');
                                $query->groupBy('id');
                                $query->havingRaw('COUNT(*) >= '.$Push->condition_data);
                            })->get();

                }elseif($Push->condition == 'LOCATION'){

                    $Location = explode(',', $Push->condition_data);

                    $distance = Setting::get('provider_search_radius', '10');
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $Providers = Provider::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                            ->get();

                }


                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
                }

            }elseif($Push->send_to == 'ALL'){

                $Users = User::all();
                foreach ($Users as $key => $user) {
                    (new SendPushNotification)->sendPushToUser($user->id, $Push->message);
                }

                $Providers = Provider::all();
                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
                }

            }
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    public function transactions(){

        try{
            $wallet_transation = AdminWallet::orderBy('id','desc')
                                ->paginate(Setting::get('per_page', '10'));
            
            $pagination=(new Helper)->formatPagination($wallet_transation);   
            
            $wallet_balance=AdminWallet::sum('amount');

            return view('admin.wallet.wallet_transation',compact('wallet_transation','pagination','wallet_balance'));
            
        }

        catch (Exception $e) {
             return back()->with('flash_error',$e->getMessage());
        }
    }

    public function transferlist(Request $request){

        $croute= Route::currentRouteName();
        
        if($croute=='admin.fleettransfer')
            $type='fleet';
        else
            $type='provider';

        $pendinglist = WalletRequests::where('request_from',$type)->where('status',0);
        if($croute=='admin.fleettransfer')
            $pendinglist = $pendinglist->with('fleet');
        else
            $pendinglist = $pendinglist->with('provider');

        $pendinglist = $pendinglist->get();
               
        return view('admin.wallet.transfer',compact('pendinglist','type'));
    }

    public function approve(Request $request, $id){

        if($request->send_by == "online") {
            $response=(new PaymentController)->send_money($request, $id);
        }
        else{
            (new TripController)->settlements($id);
            $response['success']='Amount successfully send';
        }    

        if(!empty($response['error']))
            $result['flash_error']=$response['error'];
        if(!empty($response['success']))
            $result['flash_success']=$response['success'];
       
        return redirect()->back()->with($result);
        
    }

    public function requestcancel(Request $request)
    {
        
        $cancel=(new TripController())->requestcancel($request);
        $response=json_decode($cancel->getContent(),true);
        
        if(!empty($response['error']))
            $result['flash_error']=$response['error'];
        if(!empty($response['success']))
            $result['flash_success']=$response['success'];

        return redirect()->back()->with($result);
    }

    public function transfercreate(Request $request, $id){
        $type=$id;
        return view('admin.wallet.create',compact('type'));        
    }

    public function search(Request $request){

        $results=array();

        $term =  $request->input('stext');       
        $sflag =  $request->input('sflag');

        if($sflag==1)
            $queries = Provider::where('first_name', 'LIKE', $term.'%')->take(5)->get();
        else
            $queries = Fleet::where('name', 'LIKE', $term.'%')->take(5)->get();

        foreach ($queries as $query)
        {
            $results[]=$query;
        }    

        return response()->json(array('success' => true, 'data'=>$results));

    }

    public function transferstore(Request $request){

        try{
            if($request->stype==1)
                $type='provider';
            else
                $type='fleet';

            $nextid=Helper::generate_request_id($type); 

            $amountRequest=new WalletRequests;
            $amountRequest->alias_id=$nextid;
            $amountRequest->request_from=$type;          
            $amountRequest->from_id=$request->from_id;
            $amountRequest->type=$request->type;
            $amountRequest->send_by=$request->by;
            $amountRequest->amount=$request->amount;

            $amountRequest->save();

            //create the settlement transactions
            (new TripController)->settlements($amountRequest->id);            

            return back()->with('flash_success','Settlement processed successfully');
            
        }

        catch (Exception $e) {
             return back()->with('flash_error',$e->getMessage());
        }      
    }

    public function download(Request $request, $id)
    {

        $documents = ProviderDocument::where('provider_id', $id)->get();

        if(!empty($documents->toArray())){

           
            $Provider = Provider::findOrFail($id);

            // Define Dir Folder
            $public_dir=public_path();

            // Zip File Name
            $zipFileName = $Provider->first_name.'.zip';

            // Create ZipArchive Obj
            $zip = new ZipArchive;

            if ($zip->open($public_dir . '/storage/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
                // Add File in ZipArchive
                foreach($documents as $file){
                    $zip->addFile($public_dir.'/storage/'.$file->url);
                }
                
                // Close ZipArchive     
                $zip->close();
            }
            // Set Header
            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $filetopath=$public_dir.'/storage/'.$zipFileName;
            
            // Create Download Response
            if(file_exists($filetopath)){
                return response()->download($filetopath,$zipFileName,$headers)->deleteFileAfterSend(true);
            }            

            return redirect()
                ->route('admin.provider.document.index', $id)
                ->with('flash_success', 'documents downloaded successfully.');   
        }
        
        return redirect()
                ->route('admin.provider.document.index', $id)
                ->with('flash_error', 'failed to downloaded documents.');      
        
    } 
    public function ongoing(Request $request,$id){

        try{  

            $userrequest = UserRequests::with('provider')->findOrFail($id); 

            if($request->ajax()) {
                return $userrequest;
            }else{
                return view('admin.request.ongoing', compact('userrequest')); 
            }
            
        } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
    public function addtickets(){

         try{  


         } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }


    }
   
   public function tickets(Request $request){
		
		
		if($request->ajax()){
			
		  if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.district',$request->district_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.district',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

              }	else {
				  $tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();

                }
			   return response()->json(array('success' => true, 'data'=>$tickets));
		}

         

             if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.district',$request->district_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

                $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                // $pagination=(new Helper)->formatPagination($tickets);


              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.district',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();
             
               $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));


               //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

                 $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);


              }
			  
		 try{
			 
             $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->paginate($this->perpage);
                 $pagination=(new Helper)->formatPagination($tickets);
                  $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','pagination','districts','blocks'));        
        } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


   public function sendpushnotifications1(){
               $user_id = "78";
               $message = "Hi Hello all";
              (new SendPushNotification)->sendPushToProvider($user_id, $message);
     }


   function sendpushnotifications() {

        $fcm_token="d9ozrOxafRY:APA91bETpL1tQVYyHC7HCMfP4cgRW0DSuj1UUJr6sIIjNXLnvMd_ooqcweF0CU8XqBJBSZ0Kjfv28eK6sNc8Q59WiEGyG5NWtifbdutp4xg2iMYJHo3LNzorg5AgKTGHSHld_fSOYEk3";
        $title="TeraOdisha";
        $message="Hi, You have recieved request form odisha fleet.Please open the app and accept the request!... ";
        $id="78";  
        $push_notification_key = "AAAAsJwKEBE:APA91bGhn0cYMrBr0A4so53XYTYQxe7BOexk_UytxVT8CZQAkyw-No53yy2N49SYQxk4nIgVR7MQcucH7Qz8AW8IGCmlrTBV9Wb2eY5rCsLxNNqItgjQksHgHftX6jXkEenMTCf6Qi5s";    
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );    

        $postdata = '{
            "to" : "' . $fcm_token . '",
                "notification" : {
                    "title":"' . $title . '",
                    "text" : "' . $message . '"
                },
            "data" : {
                "id" : "'.$id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public function getajaxblocks($id)
   {
    $blocks = \App\Block::where('district_id',$id)->select('name','id')->get();
    return view('admin.providers.ajaxblock', compact('blocks'));
  }

  public function getajaxgps($id){

    $gpslist= DB::table('gp_list')->where('block_id',$id)->select('gp_name','id','latitude','longitude')->get();
    return view('admin.providers.ajaxgps', compact('gpslist'));

   }



  public function getSearchblocklist($id)
{
     $blocks= DB::table("blocks")
                 ->where("district_id",$id)
                 ->pluck("name","id");
      //$blocks=DB::table('master_tickets')->select(DB::raw('DISTINCT mandal'))->where("district",$id);
      //$blocks= DB::select("mandal,district")->from('master_tickets')->where("district",$id) ->groupBy('mandal');
       //print_r( $blocks);exit;
     return response()->json($blocks);    
}

 public function getSearchproviderlist($id)
{
     $providers= DB::table("providers")
                 ->where("district_id",$id)
                 ->pluck("first_name","id");      
     return response()->json($providers);    
}


public function deleteticket($id)
{

      try {
            DB::table('master_tickets')->where('ticketid', $id)->delete();
            DB::table('user_requests')->where('booking_id', $id)->delete();
            return back()->with('message', 'Ticket deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
}


  public function searchproviders(Request $request){
      $search_name = $request->search_name;
      $type = $request->type;
      if(!empty($search_name) && $search_name !=''){
        $AllProviders = Provider::where('first_name', 'like', "%$search_name%")->orWhere('last_name', 'like', "%$search_name%")->with('service','accepted','cancelled')
                    ->orderBy('id', 'DESC');
      } else {
        $AllProviders = Provider::with('service','accepted','cancelled')
                    ->orderBy('id', 'asc');
      }

      // Paginating the generated results
      if(!empty($type) && $type !=''){
        if(request()->has('fleet')){
            $providers = $AllProviders->where('type',$type)->where('fleet',$request->fleet)->paginate($this->perpage);
        }else{
            $providers = $AllProviders->where('type',$type)->paginate($this->perpage);
        }
      } else {
        if(request()->has('fleet')){
            $providers = $AllProviders->where('fleet',$request->fleet)->paginate($this->perpage);
        }else{
            $providers = $AllProviders->paginate($this->perpage);
        }
      }

        $total_documents=Document::count();        
        
        $pagination=(new Helper)->formatPagination($providers);

        $url = $providers->url($providers->currentPage());

        $request->session()->put('providerpage', $url);
                    
        return view('admin.providers.index', compact('providers','pagination','total_documents')); 
 }

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public function addNewTicket()
{
    $districts = District::get();
    $blocks= Block::get();
    $gplist = $users = DB::table('gp_list')->get();
    return view('admin.tickets.create',compact('districts','blocks','gplist'));
}

public function storeTicket(Request $request)
{
    $this->validate($request, [
        'ticketid' => 'required',
        'district' => 'required',
        'gpname' => 'required',
        'mandal' => 'required',
        'gpname' => 'required',
        'lat' => 'required',
        'log' => 'required',
        'downdate' => 'required',
        'downtime' => 'required',        
    ]);

    try{
        $data = array(
            'district' => $request->district, 
            'mandal' => $request->mandal, 
            'gpname' => $request->gpname,
            'lat' => $request->lat,
            'log' => $request->log, 
            'downtime' => date('h:i:s a',strtotime($request->downtime)),
            'downdate' => date('Y-m-d',strtotime($request->downdate)),
            'downreason' => $request->downreason,
            'downreasonindetailed' => $request->downreasonindetailed,
            'ticketid' => $request->ticketid,
            'ticketinsertstage' =>1
        );
        //DB::table('master_tickets')->insert($data);
        if(DB::table('master_tickets')->insert($data)){

            $latitude = $request->lat;
            $longitude = $request->log;
            $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');
            $json = curl($destinationgeocodeFromLatLong);

              $desdetails = json_decode($json, TRUE);
	      $desstatus = $desdetails['status'];
		  //dd($status);
	      $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';


            $UserRequest = new UserRequests;
            $UserRequest->booking_id = $request->ticketid;
            $UserRequest->gpname = $request->gpname;

            $UserRequest->downreason = $request->downreasonindetailed;
            $UserRequest->downreasonindetailed = $request->downreasonindetailed;

            $UserRequest->user_id =45;

            // $UserRequest->current_provider_id = $getproviderdetails->id;
            // $UserRequest->provider_id = $getproviderdetails->id;

            $UserRequest->service_type_id = 2;
            $UserRequest->rental_hours = 10;
            $UserRequest->payment_mode = 'CASH';
            $UserRequest->promocode_id = 0;
            
            $UserRequest->status = 'SEARCHING';
            $UserRequest->s_address =" ";
            $UserRequest->d_address =$daddress;

            $UserRequest->s_latitude = " ";
            $UserRequest->s_longitude = " ";

            $UserRequest->d_latitude = $request->lat;
            $UserRequest->d_longitude = $request->log;
            $UserRequest->distance = 1;
            $UserRequest->unit = Setting::get('distance', 'Kms');
           
            $UserRequest->use_wallet = 0;

            if(Setting::get('track_distance', 0) == 1){
                $UserRequest->is_track = "YES";
            }

            $UserRequest->otp = mt_rand(1000 , 9999);

            $UserRequest->assigned_at = Carbon::now();
            // $UserRequest->route_key = $route_key;

            $UserRequest->save();
        }

        return redirect()
                ->route('admin.tickets')
                ->with('flash_success', 'New Ticket Details Saved Successfully');
    } catch (Exception $e) {  
        return back()->with('flash_error', 'Issue while saving the ticket details');
    }
}

public function editTicket($id)
{
    try {
        $districts = District::get();
        $blocks = Block::get();
        $ticket = MasterTicket::findOrFail($id);
        return view('admin.tickets.edit',compact('ticket','districts','blocks'));
    } catch (ModelNotFoundException $e) {
        return $e;
    }
}

public function updateTicket(Request $request, $id)
{
    $this->validate($request, [
        'district' => 'required',
        'mandal' => 'required',
        'gpname' => 'required',
        'lat' => 'required',
        'log' => 'required',
        'downdate' => 'required',
        'downtime' => 'required',        
    ]);

    try {
        $updateinput = array(
                  'district' => $request->district,
                  'mandal' => $request->mandal,
                  'gpname' => $request->gpname,
                  'lat' => $request->lat,
                  'log' => $request->log,
                  'downdate' => date('Y-m-d',strtotime($request->downdate)),
                  'downtime' => date('h:i:s a',strtotime($request->downtime)),
                  'downreason'=> $request->downreason,
                  'downreasonindetailed'=>$request->downreasonindetailed
                );
        DB::table('master_tickets')
            ->where('id',$id)
            ->update($updateinput);

        return redirect()
                ->route('admin.tickets')
                ->with('flash_success', 'Ticket Details Updated Successfully');
    } 
    catch (ModelNotFoundException $e) {
        return back()->with('flash_error', 'Issue while updating the ticket details');
    }

}

public function import_data(Request $request)
{
     $this->validate($request, [
        'import_file' => 'required|file', 
    ]);

    $response = (object)array();
    $lgd_code = '';
    try{
        $response = (object)array();
        
        $file = $request->file('import_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            // Validating file size and extension.
            $valid_extension = array("csv"); //Only want csv and excel files
            $maxFileSize = 10097152; // Uploaded file size limit is 10mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize > $maxFileSize){
                    $response->error = "File size should be less than 10 MB";
                    $response->status = 404;
                    return response()->json($response, 404);
                }
            } else {
                $response->error = "Invalid file extension. Accepts only .csv";
                $response->status = 404;
                return response()->json($response, 404);
            }

            $importData_arr = array(); // Read through the file and store the contents as an array
            
            // Read the contents of the file
            $i = 0;
            $j = 0;
            $k = 0;
            $ready_to_import = 0;
            $ignored = 0;
            $creates = 0;
            $updates = 0;
            $gp_type = '';
            $file=fopen($tempPath, 'r');
            while (($filedata = fgetcsv($file)) !== FALSE) {
                $num = count($filedata);
                $data = array(); 
                // Skip first row
                if ($i == 0) {
                    if (str_contains(strtolower($filedata[1]), 'up')) {
                        $gp_type = 'up';
                    } else if (str_contains(strtolower($filedata[1]), 'down')) {
                        $gp_type = 'down';
                    } else {
                        fclose($file);
                        $response->error = "Unable to identify Up or Down gps";
                        $response->status = 404;
                        return response()->json($response, 404);
                        exit();
                    }
                    $i++;
                    continue;
                }
                if(empty($filedata[0]) && empty($filedata[1]) && empty($filedata[2]))
                    break;
                
                $check_lgd_code = DB::table('gp_list')->where('lgd_code', $filedata[0])->first();
                
                if($check_lgd_code){                    
                    if($gp_type == 'down'){
                        $check_ticket_exisits = DB::table('master_tickets')
                                                ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                                ->where('lat', $check_lgd_code->latitude)
                                                ->where('log', $check_lgd_code->longitude)
                                                ->where('lgd_code', $check_lgd_code->lgd_code)
                                                ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED'])
                                                ->orderBy('master_tickets.id', 'DESC')
                                                ->first();
                        ($check_ticket_exisits)? $updates++: $creates++;
                    } else if($gp_type == 'up'){
                        $updates++;
                    }

                    $ready_to_import++;
                } else {                    
                    $ignored++;
                    $lgd_code .= (!empty($lgd_code))?(" , ".$filedata[0]):$filedata[0];
                }

                $i++;
            }

            fclose($file);

            $response->ready_to_import = $ready_to_import;
            $response->ignored = $ignored;
            $response->creates = $creates;
            $response->updates = $updates;
            $response->lgd_codes = $lgd_code;
            $response->status       = 200;
            return response()->json($response, 200);

        } else {
            $response->error = "Unable to find the file.";
            $response->status = 404;
            return response()->json($response, 404);
        }
        
    } catch (Exception $e) {  
        $response = (object)array();
        $response->error = $e->getMessage();
        $response->line = $e->getLine();
        $response->code = $lgd_code;
        $response->status = 500;
        return response()->json($response, 500);
    }
}

public function process(Request $request)
{
     $this->validate($request, [
        'import_file' => 'required|file', 
    ]);

    $lgd_code = '';
    try{
        
        $file = $request->file('import_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            // Validating file size and extension.
            $valid_extension = array("csv"); //Only want csv and excel files
            $maxFileSize = 10097152; // Uploaded file size limit is 10mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize > $maxFileSize){
                    return back()->with('flash_error', 'File size should be less than 10 MB');
                }
            } else {
                return back()->with('flash_error', 'Invalid file extension. Accepts only .csv');
            }

            $importData_arr = array(); // Read through the file and store the contents as an array
            
            // Read the contents of the file
            $i = 0;
            $ready_to_import = 0;
            $ignored = 0;
            $creates = 0;
            $updates = 0;
            $gp_type = '';
            $file=fopen($tempPath, 'r');
            while (($filedata = fgetcsv($file)) !== FALSE) {
                $num = count($filedata);
                $data = array(); 
                // Skip first row
                if ($i == 0) {                    
                    if (str_contains(strtolower($filedata[1]), 'up')) {
                        $gp_type = 'up';
                    } else if (str_contains(strtolower($filedata[1]), 'down')) {
                        $gp_type = 'down';
                    } else {
                        fclose($file);
                        return back()->with('flash_error', 'Unable to identify Up or Down time');
                        exit();
                    }
                    $i++;
                    continue;
                }
                if(empty($filedata[0]) && empty($filedata[1]) && empty($filedata[2]))
                    break;
                
                $check_lgd_code = DB::table('gp_list')->where('lgd_code', $filedata[0])->first();
                
                if($check_lgd_code){
                    $distict = District::findOrFail($check_lgd_code->district_id);
                    $block = Block::findOrFail($check_lgd_code->block_id);
                    $tkt_id = 'TKTN'.mt_rand(100000, 999999);
                    $data['ticketid'] = $tkt_id;
                    $data['district'] = $distict->name;
                    $data['mandal'] = $block->name;
                    $data['gpname'] = $check_lgd_code->gp_name;
                    $data['lgd_code'] = $check_lgd_code->lgd_code;
                    $data['subsategory'] = "";
                    $formats = ['m-d-y H:i', 'm-d-y H:i:s', 'm-d-Y H:i', 'm-d-Y H:i:s', 'm/d/y H:i', 'm/d/y H:i:s', 'm/d/Y H:i', 'm/d/Y H:i:s'];
                    $formattedDate = '';
                    $formattedTime = '';
                    foreach($formats as $format){
                        try{
                            $date = Carbon::createFromFormat($format, $filedata[1]);
                            if ($date instanceof Carbon) {
                                if($formattedDate == ''){
                                  $formattedDate = $date->format('Y-m-d');
                                  $formattedTime = $date->format('h:i:s a');
                                }
                            }
                        } catch (Exception $e) { }
                    }

                    if($gp_type == 'down'){
                        $data['downdate'] = $formattedDate;
                        $data['downtime'] = $formattedTime;
                    } else if($gp_type == 'up'){
                        $data['up_date'] = $formattedDate;
                        $data['up_time'] = $formattedTime;
                        $data['status'] = 1;
                    }
                    $data['downreason'] = $filedata[2];
                    $data['downreasonindetailed'] = $filedata[2];
                    $data['lat'] = $check_lgd_code->latitude;
                    $data['log'] = $check_lgd_code->longitude;
                    $data['ticketinsertstage'] = 1;

                    // $is_created = MasterTicket::updateOrCreate($data);

                    if($gp_type == 'down'){
                        $masterticket = DB::table('master_tickets')
                                            ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED'])
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } else if($gp_type == 'up'){
                        $masterticket = DB::table('master_tickets')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereNull('up_date')
                                            ->whereNull('up_time')
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } 
                    if ($masterticket !== null) {
                        $data['ticketid'] = $masterticket->ticketid;
                        if($gp_type == 'up'){
                            $Request = UserRequests::where('booking_id', $masterticket->ticketid)
                                                    ->orderBy('id', 'DESC')->first();
                            if ($masterticket !== null){
                                $Request->downreason = $data['downreason'];
                                $Request->downreasonindetailed = $data['downreasonindetailed'];
                                $Request->started_at= Carbon::now();
                                $Request->finished_at= Carbon::now();
                                $Request->status = 'COMPLETED';
                                $Request->save();

                                DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 0]);
                            } else {
                                continue;
                            }
                        }

                        DB::table('master_tickets')
                            ->where('ticketid', $masterticket->ticketid)
                            ->update($data);

                    } else {
                        if($gp_type == 'up')
                            continue;

                        // DB::table('master_tickets')->insert($data);
                        if(DB::table('master_tickets')->insert($data)){
                            // UserRequest related data starts
                            $mobile = $check_lgd_code->contact_no;;
                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('mobile','=',$mobile)->first();
                            $provider_id = $getproviderdetails->id;
                            $latitude = $check_lgd_code->latitude;
                            $longitude = $check_lgd_code->longitude;

                            // Destination address
                            $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');
                            $json = curl($destinationgeocodeFromLatLong);
                            $desdetails = json_decode($json, TRUE);
                            $desstatus = $desdetails['status'];
                            $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';

                            // Source address
                            $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&key=".Setting::get('map_key');
                            $json = curl($sourcegeocodeFromLatLong);
                            $srcdetails = json_decode($json, TRUE);
                            $srcstatus = $srcdetails['status'];
                            $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
                            
                            // Route Key
                            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');
                            $json = curl($details);
                            $details = json_decode($json, TRUE);
                            if(isset($details['routes'][0]))
                                $route_key = $details['routes'][0]['overview_polyline']['points'];
                            else
                                $route_key = null;

                            $UserRequest = new UserRequests;
                            $UserRequest->booking_id = $tkt_id;
                            $UserRequest->gpname = $check_lgd_code->gp_name;
                            $UserRequest->downreason = $filedata[2];
                            $UserRequest->downreasonindetailed = $filedata[2];
                            $UserRequest->user_id =45;                    
                         
                            $UserRequest->current_provider_id = $getproviderdetails->id;
                            $UserRequest->provider_id = $getproviderdetails->id;

                            $UserRequest->service_type_id = 2;
                            $UserRequest->rental_hours = 10;
                            $UserRequest->payment_mode = 'CASH';
                            $UserRequest->promocode_id = 0;
                            
                            $UserRequest->status = 'INCOMING';
                            $UserRequest->s_address =$saddress;
                            $UserRequest->d_address =$daddress;

                            $UserRequest->s_latitude = $getproviderdetails->latitude;
                            $UserRequest->s_longitude = $getproviderdetails->longitude;

                            $UserRequest->d_latitude = $latitude;
                            $UserRequest->d_longitude = $longitude;
                            $UserRequest->distance = 1;
                            $UserRequest->unit = Setting::get('distance', 'Kms');
                   
                            $UserRequest->use_wallet = 0;

                            if(Setting::get('track_distance', 0) == 1){
                                $UserRequest->is_track = "YES";
                            }

                            $UserRequest->otp = mt_rand(1000 , 9999);

                            $UserRequest->assigned_at = Carbon::now();
                            $UserRequest->route_key = $route_key;
                            $UserRequest->save();
                            // UserRequest related data end

                            DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 1]);
                        } 
                    }

                    $ready_to_import++;
                } else {                    
                    $ignored++;
                    $lgd_code .= (!empty($lgd_code))?(" , ".$filedata[0]):$filedata[0];
                }

                $i++;
            }

            fclose($file);

            return redirect()
                ->route('admin.tickets')
                ->with('flash_success', "Files uploaded successfully!");

        } else {

            return back()->with('flash_error', 'unable to find the file.');
        }
        
    } catch (Exception $e) {  
        echo $e->getLine();
        dd($e);
        return back()->with('flash_error', 'Issue while saving the ticket details');
    }
}

public function list_schedules()
{   
    $schedules= DB::table('schedule_auto_assign')->get();
    return view('admin.schedules.index',compact('schedules'));
}

public function edit_schedules($id)
{
    try {
        $schedule= DB::table('schedule_auto_assign')->find($id);
        return view('admin.schedules.edit',compact('schedule'));
    } catch (ModelNotFoundException $e) {
        return redirect()
            ->route('admin.schedulers')
            ->with('flash_success', trans('admin.schedule_msgs.schedule_not_found'));
    }
}

public function schedule_autoassign(Request $request, $id)
{
    $this->validate($request, [
        'schedule_time' => 'required', 
    ]);

    try {
        try {
            $schedule_auto_assign = DB::table('schedule_auto_assign')->find($id);
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.schedulers')
                ->with('flash_success', trans('admin.schedule_msgs.schedule_not_found'));
        }
        $current_time = Carbon::now();
        $current_timestamp = $current_time->timestamp;
        $schedule_time = $current_time;

        $schedule_cron = (trim($request->schedule_time) == 'custom')?trim($request->cst):trim($request->schedule_time);

        if($schedule_cron == '* * * * *') // Every Minute
            $schedule_time = $schedule_time->addMinutes(1);
        else if($schedule_cron == '0 * * * *') // Every Hour
            $schedule_time = $schedule_time->addHours(1); 
        else if($schedule_cron == '0 0 * * *') // Every Day/Mid_Night
            $schedule_time = $schedule_time->addHours(24);
        else if($schedule_cron == '0 0 * * 0') // Every Week
            $schedule_time = $schedule_time->addWeeks(1);
        else {
            $cron_array = explode(' ', $schedule_cron);
            if (!empty($cron_array)) {
                if($cron_array[0] != '*' && $cron_array[0] != '0')
                    $schedule_time = $schedule_time->addMinutes(explode('/', $cron_array[0])[1]);
                if($cron_array[1] != '*' && $cron_array[1] != '0')
                    $schedule_time = $schedule_time->addHours(explode('/', $cron_array[1])[1]);
                if($cron_array[2] != '*' && $cron_array[2] != '0')
                    $schedule_time = $schedule_time->addDays($cron_array[2]);
                if($cron_array[3] != '*' && $cron_array[3] != '0')
                    $schedule_time = $schedule_time->addMonths($cron_array[3]);
                if($cron_array[4] != '*' && $cron_array[4] != '0')
                    $schedule_time = $schedule_time->addWeeks($cron_array[4]);
            }
        }

        $schedule_interval = $schedule_time->timestamp;
        $next_interval = $schedule_time->timestamp - $current_timestamp;
     

        $data = array();
        $data['schedule_time'] = $schedule_cron;
        $data['schedule_interval'] = $schedule_interval;
        $data['next_interval'] = $next_interval;
        $data['is_custom'] = (trim($request->schedule_time) == 'custom')?'custom':'';

        DB::table('schedule_auto_assign')->where('id',$id)->update($data);
        return redirect()
                ->route('admin.schedulers')
                ->with('flash_success', trans('admin.schedule_msgs.schedule_saved'));

    } catch(Exception $e) {
        return back()->with('flash_error', trans('admin.schedule_msgs.schedule_not_found'));
    }
}


// New tickets page
public function tickets1(Request $request){

    try{  

        $query_params = array();
        $tickets = DB::table('master_tickets')
         ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','providers.first_name','providers.last_name')
         ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
         ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id');

         if(isset($request->ticket_id) && !empty($request->ticket_id)){
            $query_params['ticket_id'] = $request->ticket_id;
            $tickets->where('master_tickets.ticketid',$request->ticket_id);
         }

         if(isset($request->district_id) && !empty($request->district_id)){
            $query_params['district_id'] = $request->district_id;
            $tickets->where('master_tickets.district',$request->district_id);
         }
         if(isset($request->block_id) && !empty($request->block_id)){
            $query_params['block_id'] = $request->block_id;
            $tickets->where('master_tickets.mandal',$request->block_id);
         }
         if(isset($request->status) && !empty($request->status)){
            $query_params['status'] = $request->status;
            $tkt_status = array('OnGoing' => 'INCOMING', 'Completed' => 'COMPLETED', 'On Hold' => 'ONHOLD');                
                        
            if($request->status == 'OnGoing'){
                $tickets->where(function ($query) {
                    $query->where('user_requests.status', '=', 'PICKEDUP')
                          ->orWhere('user_requests.status', '=', 'INCOMING');
                    });
            } else {
                $tickets->where('user_requests.status',$tkt_status[$request->status]);
            }
         }
         if(isset($request->from_date) && !empty($request->from_date)){
            $query_params['from_date'] = $request->from_date;
            $tickets->where('master_tickets.downdate', '>=', $request->from_date);
         }

        $tickets = $tickets->orderBy('downdate','desc')
                         ->orderBy('downtime','asc')
                         ->get();
                    // dd($tickets);

        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get();

        $ticket_status = array('OnGoing', 'Completed', 'On Hold');

        return view('admin.tickets_new', compact('tickets','districts','blocks', 'ticket_status', 'query_params'));

    } catch (Exception $e) { 
        dd($e);
        return back()->with('flash_error', trans('admin.something_wrong'));
    }
}


}
