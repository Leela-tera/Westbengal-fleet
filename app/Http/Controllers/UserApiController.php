<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Log;
use Auth;
use Hash;
use Route;
use Storage;
use Setting;
use Exception;
use Validator;
use Notification;

use Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Notifications\ResetPasswordOTP;
use App\Helpers\Helper;

use App\Card;
use App\User;
use App\Work;
use App\Provider;
use App\Settings;
use App\Promocode;
use App\ServiceType;
use App\District;
use App\UserRequests;
use App\RequestFilter;
use App\PromocodeUsage;
use App\WalletPassbook;
use App\UserWallet;
use App\PromocodePassbook;
use App\ProviderService;
use App\UserRequestRating;
use App\SubmitFile;
use App\MasterTicket;
use App\MasterCoordinate;
use App\ProviderHistory;
use App\Http\Controllers\ProviderResources\TripController;
use App\Services\ServiceTypes;



class UserApiController extends Controller
{
    /**  Check Email/Mobile Availablity Of a User  **/

    public function verify(Request $request)
    {
        $this->validate($request, [
                'email' => 'required|email|unique:users',
                
            ]);

        try{
            
            return response()->json(['message' => trans('api.email_available')]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function checkUserEmail(Request $request)
    {
        $this->validate($request, [
                'email' => 'required|email',                
            ]);

        try{
            
            $email=$request->email;

            $results=User::where('email',$email)->first();

            if(empty($results))
                return response()->json(['message' => trans('api.email_available'),'is_available' => true]);                
            else        
                return response()->json(['message' => trans('api.email_not_available'),'is_available' => false]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function login(Request $request)
    {
        $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
        $request->request->add([
           "client_id"     => $request->client_id,
           "client_secret" => $request->client_secret,
           "grant_type"    => 'password',
           "code"          => '*',
        ]);
        $response = Route::dispatch($tokenRequest);

        $json = (array) json_decode($response->getContent());

        if(!empty($json['error'])){
            $json['error']=$json['message'];
        }

        // $json['status'] = true;
        $response->setContent(json_encode($json));

        $update = User::where('email', $request->username)->update(['device_token' => $request->device_token , 'device_id' => $request->device_id , 'device_type' => $request->device_type]);    

        return $response;
    }

    public function signup(Request $request)
    {
        $this->validate($request, [
                'social_unique_id' => ['required_if:login_by,facebook,google','unique:users'],
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'device_id' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'mobile' => 'required',
                'password' => 'required|min:6',
            ]);

            
            $User = $request->all();

            $User['payment_mode'] = 'CASH';
            $User['password'] = bcrypt($request->password);
            $User = User::create($User);

            $User=Auth::loginUsingId($User->id);
            $UserToken = $User->createToken('AutoLogin');
            $User['access_token'] = $UserToken->accessToken;
            $User['currency'] = Setting::get('currency');
            $User['sos'] = Setting::get('sos_number', '911');                
            $User['app_contact'] = Setting::get('app_contact', '5777');
            $User['measurement'] = Setting::get('distance', 'Kms');            

            if(Setting::get('send_email', 0) == 1) {
                // send welcome email here
                Helper::site_registermail($User);
            }    

            return $User;
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function logout(Request $request)
    {
        try {
            User::where('id', $request->id)->update(['device_id'=> '', 'device_token' => '']);
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function change_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required',
            ]);

        $User = Auth::user();

        if(Hash::check($request->old_password, $User->password))
        {
            $User->password = bcrypt($request->password);
            $User->save();

            if($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            }else{
                return back()->with('flash_success', trans('api.user.password_updated'));
            }

        } else {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.user.incorrect_old_password')], 422);
            }else{
                return back()->with('flash_error',trans('api.user.incorrect_old_password'));
            }
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_location(Request $request){

        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($user = User::find(Auth::user()->id)){

            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();

            return response()->json(['message' => trans('api.user.location_updated')]);

        }else{

            return response()->json(['error' => trans('api.user.user_not_found')], 422);

        }

    }

    public function update_language(Request $request){

        $this->validate($request, [
                'language' => 'required',                
            ]);

        if($user = User::find(Auth::user()->id)){

            $user->language = $request->language;           
            $user->save();

            return response()->json(['message' => trans('api.user.language_updated'),'language'=>$request->language]);

        }else{

            return response()->json(['error' => trans('api.user.user_not_found')], 422);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request){

        $this->validate($request, [
            'device_type' => 'in:android,ios',
        ]);

        try{

            if($user = User::find(Auth::user()->id)){

                if($request->has('device_token')){
                    $user->device_token = $request->device_token;
                }

                if($request->has('device_type')){
                    $user->device_type = $request->device_type;
                }

                if($request->has('device_id')){
                    $user->device_id = $request->device_id;
                }

                $user->save();

                $user->currency = Setting::get('currency');
                $user->sos = Setting::get('sos_number', '911');                
                $user->app_contact = Setting::get('app_contact', '5777');                
                $user->measurement = Setting::get('distance', 'Kms');                
                $user->stripe_secret_key = Setting::get('stripe_secret_key', '');
                $user->stripe_publishable_key = Setting::get('stripe_publishable_key', '');
                $user->driverapplink = Setting::get('driverapplink', '');
                return $user;

            } else {
                return response()->json(['error' => trans('api.user.user_not_found')], 422);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_profile(Request $request)
    {

        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'max:255',
                'email' => 'email|unique:users,email,'.Auth::user()->id,
                'mobile' => 'required',
                'picture' => 'mimes:jpeg,bmp,png',
            ]);

         try {

            $user = User::findOrFail(Auth::user()->id);

            if($request->has('first_name')){ 
                $user->first_name = $request->first_name;
            }
            
            if($request->has('last_name')){
                $user->last_name = $request->last_name;
            }
            
            if($request->has('email')){
                $user->email = $request->email;
            }
        
            if($request->has('mobile')){
                $user->mobile = $request->mobile;
            }
            
            if($request->has('gender')){
                $user->gender = $request->gender;
            }

            if($request->has('language')){
                $user->language = $request->language;
            }

            if ($request->picture != "") {
                Storage::delete($user->picture);
                $user->picture = $request->picture->store('user/profile');
            }

            $user->save();

            $user->currency = Setting::get('currency');
            $user->sos = Setting::get('sos_number', '911');                
            $user->app_contact = Setting::get('app_contact', '5777');
            $user->measurement = Setting::get('distance', 'Kms');

            if($request->ajax()) {
                return response()->json($user);
            }else{
                return back()->with('flash_success', trans('api.user.profile_updated'));
            }
        }

        catch (ModelNotFoundException $e) {
             return response()->json(['error' => trans('api.user.user_not_found')], 422);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function services() {

        if($serviceList = ServiceType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


    /**
     * get the district list.
     *
     * 29/4/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function districts() {

        if($districtList = District::all()) {
            return response()->json($districtList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request) {

        $this->validate($request, [
                's_latitude' => 'required|numeric',
                'd_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
                //'promo_code' => 'exists:promocodes,promo_code',
                'distance' => 'required|numeric',
                'use_wallet' => 'numeric',
                'payment_mode' => 'required|in:CASH,CARD,PAYPAL',
                'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        /*Log::info('New Request from User: '.Auth::user()->id);
        Log::info('Request Details:', $request->all());*/

        $ActiveRequests = UserRequests::PendingRequest(Auth::user()->id)->count();

        if($ActiveRequests > 0) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.ride.request_inprogress')], 422);
            } else {
                return redirect('dashboard')->with('flash_error', trans('api.ride.request_inprogress'));
            }
        }

        if($request->has('schedule_date') && $request->has('schedule_time')){
            $beforeschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->subHour(1);
            $afterschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->addHour(1);

            $CheckScheduling = UserRequests::where('status','SCHEDULED')
                            ->where('user_id', Auth::user()->id)
                            ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                            ->count();


            if($CheckScheduling > 0){
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.request_scheduled')], 422);
                }else{
                    return redirect('dashboard')->with('flash_error', trans('api.ride.request_scheduled'));
                }
            }

        }

        $distance = Setting::get('provider_search_radius', '10');
       
        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
        $service_type = $request->service_type;

        $Providers = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id')
            ->where('status', 'approved')
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->get();
   //  dd($Providers);
        // List Providers who are currently busy and add them to the filter list.

        if(count($Providers) == 0) {
            if($request->ajax()) {
                // Push Notification to User
                return response()->json(['error' => trans('api.ride.no_providers_found')], 422); 
            }else{
                return back()->with('flash_success', trans('api.ride.no_providers_found'));
            }
        }

        try{

            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$request->s_latitude.",".$request->s_longitude."&destination=".$request->d_latitude.",".$request->d_longitude."&mode=driving&key=".Setting::get('map_key');

            $json = curl($details);

            $details = json_decode($json, TRUE);

            $route_key = $details['routes'][0]['overview_polyline']['points'];

            $UserRequest = new UserRequests;
            $UserRequest->booking_id = Helper::generate_booking_id();
         

            $UserRequest->user_id = Auth::user()->id;
            
            if((Setting::get('manual_request',0) == 0) && (Setting::get('broadcast_request',0) == 0)){
                $UserRequest->current_provider_id = $Providers[0]->id;
            }else{
                $UserRequest->current_provider_id = 0;
            }

            $UserRequest->service_type_id = $request->service_type;
            $UserRequest->rental_hours = $request->rental_hours;
            $UserRequest->payment_mode = $request->payment_mode;
            $UserRequest->promocode_id = $request->promocode_id ? : 0;
            
            $UserRequest->status = 'SEARCHING';

            $UserRequest->s_address = $request->s_address ? : "";
            $UserRequest->d_address = $request->d_address ? : "";

            $UserRequest->s_latitude = $request->s_latitude;
            $UserRequest->s_longitude = $request->s_longitude;

            $UserRequest->d_latitude = $request->d_latitude;
            $UserRequest->d_longitude = $request->d_longitude;
            $UserRequest->distance = $request->distance;
            $UserRequest->unit = Setting::get('distance', 'Kms');

            if(Auth::user()->wallet_balance > 0){
                $UserRequest->use_wallet = $request->use_wallet ? : 0;
            }

            if(Setting::get('track_distance', 0) == 1){
                $UserRequest->is_track = "YES";
            }

            $UserRequest->otp = mt_rand(1000 , 9999);

            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->route_key = $route_key;

            if($Providers->count() <= Setting::get('surge_trigger') && $Providers->count() > 0){
                $UserRequest->surge = 1;
            }

            if($request->has('schedule_date') && $request->has('schedule_time')){
                $UserRequest->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
                $UserRequest->is_scheduled = 'YES';
            }

             if((Setting::get('manual_request',0) == 0) && (Setting::get('broadcast_request',0) == 0)){
                //Log::info('New Request id : '. $UserRequest->id .' Assigned to provider : '. $UserRequest->current_provider_id);
                (new SendPushNotification)->IncomingRequest($Providers[0]->id);
            }

            $UserRequest->save();
           

            // update payment mode
            User::where('id',Auth::user()->id)->update(['payment_mode' => $request->payment_mode]);

            if($request->has('card_id')){

                Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
            }

            if(Setting::get('manual_request',0) == 0){
                foreach ($Providers as $key => $Provider) {

                    if(Setting::get('broadcast_request',0) == 1){
                       (new SendPushNotification)->IncomingRequest($Provider->id); 
                    }

                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    
                    $Filter->request_id = $UserRequest->id;
                    $Filter->provider_id = $Provider->id; 
                    $Filter->save();
                }
            }

            if($request->ajax()) {
                return response()->json([
                        'message' => 'New request Created!',
                        'request_id' => $UserRequest->id,
                        'current_provider' => $UserRequest->current_provider_id,
                    ]);
            }else{
                return redirect('dashboard');
            }

        } catch (Exception $e) {            
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function cancel_request(Request $request) {

        $this->validate($request, [
            'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::user()->id,
        ]);

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);

            if($UserRequest->status == 'CANCELLED')
            {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_cancelled')], 422); 
                }else{
                    return back()->with('flash_error', trans('api.ride.already_cancelled'));
                }
            }

            if(in_array($UserRequest->status, ['SEARCHING','STARTED','ARRIVED','SCHEDULED'])) {

                if($UserRequest->status != 'SEARCHING'){
                    $this->validate($request, [
                        'cancel_reason'=> 'max:255',
                    ]);
                }

                $UserRequest->status = 'CANCELLED';
                $UserRequest->cancel_reason = $request->cancel_reason;
                $UserRequest->cancelled_by = 'USER';
                $UserRequest->save();

                RequestFilter::where('request_id', $UserRequest->id)->delete();

                if($UserRequest->status != 'SCHEDULED'){

                    if($UserRequest->provider_id != 0){

                        ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' => 'active']);

                    }
                }

                 // Send Push Notification to User
                (new SendPushNotification)->UserCancellRide($UserRequest);

                if($request->ajax()) {
                    return response()->json(['message' => trans('api.ride.ride_cancelled')]); 
                }else{
                    return redirect('dashboard')->with('flash_success',trans('api.ride.ride_cancelled'));
                }

            } else {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_onride')], 422); 
                }else{
                    return back()->with('flash_error', trans('api.ride.already_onride'));
                }
            }
        }

        catch (ModelNotFoundException $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')],500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    }

    /**
     * Show the request status check.
     *
     * @return \Illuminate\Http\Response
     */

    public function request_status_check() {

        try{
            $check_status = ['CANCELLED', 'SCHEDULED'];

            $UserRequests = UserRequests::UserRequestStatusCheck(Auth::user()->id, $check_status)
                                        ->get()
                                        ->toArray();
                                        

            $search_status = ['SEARCHING','SCHEDULED'];
            $UserRequestsFilter = UserRequests::UserRequestAssignProvider(Auth::user()->id,$search_status)->get(); 

             //Log::info($UserRequestsFilter);



            $Timeout = Setting::get('provider_select_timeout', 180);

            if(!empty($UserRequestsFilter)){
                for ($i=0; $i < sizeof($UserRequestsFilter); $i++) {
                    $ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->assigned_at));
                    if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
                        $Providertrip = new TripController();
                        $Providertrip->assign_next_provider($UserRequestsFilter[$i]->id);
                    }else if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0){
                        break;
                    }
                }
            }
          
            return response()->json(['data' => $UserRequests , 'sos' => Setting::get('sos_number', '911'), 'cash' => (int)Setting::get('CASH', 1), 'card' => (int)Setting::get('CARD', 0),'currency'=>Setting::get('currency','$')]);

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function rate_provider(Request $request) {

        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        $UserRequests = UserRequests::where('id' ,$request->request_id)
                ->where('status' ,'COMPLETED')
                ->where('paid', 0)
                ->first();

        if ($UserRequests) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.user.not_paid')], 422);
            } else {
                return back()->with('flash_error', trans('api.user.not_paid'));
            }
        }

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);
            
            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            }

            $UserRequest->user_rated = 1;
            $UserRequest->save();

            $average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('user_rating');

            Provider::where('id',$UserRequest->provider_id)->update(['rating' => $average]);

            // Send Push Notification to Provider 
            if($request->ajax()){
                return response()->json(['message' => trans('api.ride.provider_rated')]); 
            }else{
                return redirect('dashboard')->with('flash_success', trans('api.ride.provider_rated'));
            }
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function modifiy_request(Request $request) {

        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
                'latitude' => 'sometimes|nullable|numeric',
                'longitude' => 'sometimes|nullable|numeric',
                'address' => 'sometimes|nullable',
                'payment_mode' => 'sometimes|nullable|in:CASH,CARD,PAYPAL',
                'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
            ]);

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);

            if(!empty($request->latitude) && !empty($request->longitude)){
                $UserRequest->d_latitude = $request->latitude?:$UserRequest->d_latitude;
                $UserRequest->d_longitude = $request->longitude?:$UserRequest->d_longitude;
                $UserRequest->d_address =  $request->address?:$UserRequest->d_address;
            }

            if(!empty($request->payment_mode)){
                $UserRequest->payment_mode = $request->payment_mode?:$UserRequest->payment_mode;
                if($request->payment_mode=='CARD' && $UserRequest->status=='DROPPED'){
                    $UserRequest->status='COMPLETED';
                }
            }
                
            $UserRequest->save();

            

            if($request->has('card_id')){

                Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
            }

            // Send Push Notification to Provider 
            if($request->ajax()){
                return response()->json(['message' => trans('api.ride.request_modify_location')]); 
            }else{
                return redirect('dashboard')->with('flash_success', trans('api.ride.request_modify_location'));
            }
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    } 


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trips() {
    
        try{
            $UserRequests = UserRequests::UserTrips(Auth::user()->id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x191919|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function estimated_fare(Request $request){

        $this->validate($request,[
                's_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_latitude' => 'required|numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        try{       
            $response = new ServiceTypes();

            $responsedata=$response->calculateFare($request->all(), 1);

            if(!empty($responsedata['errors'])){
                throw new Exception($responsedata['errors']);
            }
            else{
                return response()->json($responsedata['data']);
            }

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x191919|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * get all promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function promocodes() {
        try{
            //$this->check_expiry();

            return PromocodeUsage::Active()
                    ->where('user_id', Auth::user()->id)
                    ->with('promocode')
                    ->get();

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    } 


    /*public function check_expiry(){
        try{
            $Promocode = Promocode::all();
            foreach ($Promocode as $index => $promo) {
                if(date("Y-m-d") > $promo->expiration){
                    $promo->status = 'EXPIRED';
                    $promo->save();
                    PromocodeUsage::where('promocode_id', $promo->id)->update(['status' => 'EXPIRED']);
                }else{
                    PromocodeUsage::where('promocode_id', $promo->id)
                            ->where('status','<>','USED')
                            ->update(['status' => 'ADDED']);

                    PromocodePassbook::create([
                            'user_id' => Auth::user()->id,
                            'status' => 'ADDED',
                            'promocode_id' => $promo->id
                        ]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }*/


    /**
     * add promo code.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_promocode(Request $request){
        try{

        $promo_list =Promocode::where('expiration','>=',date("Y-m-d H:i"))
                ->whereDoesntHave('promousage', function($query) {
                            $query->where('user_id',Auth::user()->id);
                        })
                ->get(); 
        if($request->ajax()){
            return response()->json([
                    'promo_list' => $promo_list
                ]);  
             }else{
                return $promo_list;
             }    
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }
    

    public function add_promocode(Request $request) {

        $this->validate($request, [
                'promocode' => 'required|exists:promocodes,promo_code',
            ]);

        try{

            $find_promo = Promocode::where('promo_code',$request->promocode)->first();

            if($find_promo->status == 'EXPIRED' || (date("Y-m-d") > $find_promo->expiration)){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_expired'), 
                        'code' => 'promocode_expired'
                    ]);

                }else{
                    return back()->with('flash_error', trans('api.promocode_expired'));
                }

            }elseif(PromocodeUsage::where('promocode_id',$find_promo->id)->where('user_id', Auth::user()->id)->whereIN('status',['ADDED','USED'])->count() > 0){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_already_in_use'), 
                        'code' => 'promocode_already_in_use'
                        ]);

                }else{
                    return back()->with('flash_error', trans('api.promocode_already_in_use'));
                }

            }else{

                $promo = new PromocodeUsage;
                $promo->promocode_id = $find_promo->id;
                $promo->user_id = Auth::user()->id;
                $promo->status = 'ADDED';
                $promo->save();
                
                $count_id = PromocodePassbook::where('promocode_id' , $find_promo->id)->count();
                //dd($count_id); 
                if($count_id == 0){

                   PromocodePassbook::create([
                            'user_id' => Auth::user()->id,
                            'status' => 'ADDED',
                            'promocode_id' => $find_promo->id
                        ]);
                }
                if($request->ajax()){

                    return response()->json([
                            'message' => trans('api.promocode_applied') ,
                            'code' => 'promocode_applied'
                         ]); 

                }else{
                    return back()->with('flash_success', trans('api.promocode_applied'));
                }
            }

        }

        catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
            $UserRequests = UserRequests::UserUpcomingTrips(Auth::user()->id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserUpcomingTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the nearby providers.
     *
     * @return \Illuminate\Http\Response
     */

    public function show_providers(Request $request) {

        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'service' => 'numeric|exists:service_types,id',
            ]);

        try{

            $distance = Setting::get('provider_search_radius', '10');
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            if($request->has('service')){

                $ActiveProviders = ProviderService::AvailableServiceProvider($request->service)
                                    ->get()->pluck('provider_id');

                $Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
                    ->where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->get();

            } else {

                $ActiveProviders = ProviderService::where('status', 'active')
                                    ->get()->pluck('provider_id');

                $Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
                    ->where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->get();
            }

        
            return $Providers;

        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }


    /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */


    public function forgot_password(Request $request){

        $this->validate($request, [
                'email' => 'required|email|exists:users,email',
            ]);

        try{  
            
            $user = User::where('email' , $request->email)->first();

            $otp = mt_rand(100000, 999999);

            $user->otp = $otp;
            $user->save();

            Notification::send($user, new ResetPasswordOTP($otp));

            return response()->json([
                'message' => 'OTP sent to your email!',
                'user' => $user
            ]);

        }catch(Exception $e){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\Response
     */

    public function reset_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'id' => 'required|numeric|exists:users,id'

            ]);

        try{

            $User = User::findOrFail($request->id);
            // $UpdatedAt = date_create($User->updated_at);
            // $CurrentAt = date_create(date('Y-m-d H:i:s'));
            // $ExpiredAt = date_diff($UpdatedAt,$CurrentAt);
            // $ExpiredMin = $ExpiredAt->i;
            $User->password = bcrypt($request->password);
            $User->save();
            if($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            }
           
            

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }
        }
    }

    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request){

        try{

            if($request->ajax()) {
                return response()->json([
                    'contact_number' => Setting::get('contact_number',''), 
                    'contact_email' => Setting::get('contact_email','')
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }
        }
    }   



    /**
     * Show the wallet usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function wallet_passbook(Request $request)
    {
        try{
            $start_node= $request->start_node;
            $limit= $request->limit;
            
            $wallet_transation = UserWallet::where('user_id',Auth::user()->id);
            if(!empty($limit)){
                $wallet_transation =$wallet_transation->offset($start_node);
                $wallet_transation =$wallet_transation->limit($limit);
            }

            $wallet_transation =$wallet_transation->orderBy('id','desc')->get();

            return response()->json(['wallet_transation' => $wallet_transation,'wallet_balance'=>Auth::user()->wallet_balance]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the promo usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function promo_passbook(Request $request)
    {
        try{
            
            return PromocodePassbook::where('user_id',Auth::user()->id)->with('promocode')->get();

        } catch (Exception $e) {
             
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
         //$push =  (new SendPushNotification)->IncomingRequest($request->id); 
         $push = (new SendPushNotification)->Arrived($request->id);

         
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function pricing_logic($id)
    {
       //return $id;
       $logic = ServiceType::select('calculator')->where('id',$id)->first();
       return $logic;

    }

    public function fare(Request $request){

        $this->validate($request,[
                's_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_latitude' => 'required|numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        try{       
            $response = new ServiceTypes();
            $responsedata=$response->calculateFare($request->all());

            if(!empty($responsedata['errors'])){
                throw new Exception($responsedata['errors']);
            }
            else{
                return response()->json($responsedata['data']);
            }

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Show the wallet usage.
     *
     * @return \Illuminate\Http\Response
     */

    /*public function check(Request $request)
    {

        $this->validate($request, [
                'name' => 'required',
                'age' => 'required',
                'work' => 'required',
            ]);
         return Work::create(request(['name', 'age' ,'work']));
    }*/    

    public function chatPush(Request $request){

        $this->validate($request,[
                'user_id' => 'required|numeric',
                'message' => 'required',
            ]);       

        try{

            $user_id=$request->user_id;
            $message=$request->message;
            $sender=$request->sender;

            $message = \PushNotification::Message($message,array(
            'badge' => 1,
            'sound' => 'default',
            'custom' => array('type' => 'chat')
            ));

            (new SendPushNotification)->sendPushToUser($user_id, $message);         

            return response()->json(['success' => 'true']);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function CheckVersion(Request $request){

        $this->validate($request,[
                'sender' => 'in:user,provider',
                'device_type' => 'in:android,ios',
                'version' => 'required',
            ]);       

        try{

            $sender=$request->sender;
            $device_type=$request->device_type;
            $version=$request->version;

            if($sender=='user'){
                if($device_type=='ios'){
                    $curversion=Setting::get('version_ios_user');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_ios_user')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
                else{
                    $curversion=Setting::get('version_android_user');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){                        
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_android_user')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
            }
            else{
                if($device_type=='ios'){
                    $curversion=Setting::get('version_ios_provider');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){                        
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_ios_provider')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
                else{
                    $curversion=Setting::get('version_android_provider');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_android_provider')]);                        
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
            }           

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function checkapi(Request $request)
    {
        Log::info('Request Details:', $request->all());
        return response()->json(['sucess' => true]);        
       
    }
    /**
     * reassign request.
     *
     * @return \Illuminate\Http\Response
     */
    public function Reassign(Request $request){

        try{

            $data=$request->all();
            $request_id = $data['request_id'];
            $provider_id = $data['provider_id'];
            $downreason = $data['downreason'];
            $downreasonindetailed = $data['downreasonindetailed'];
            $category= $data['category'];
            $subcategory= $data['subcategory'];
            $description= $data['description'];



            $Request = UserRequests::findOrFail($request_id);
           
            $getticketdetails = DB::table('master_tickets')
		->where('ticketid','!=','')
                ->where('status','!=',1)
		->orderBy('created_at','desc')
        ->inRandomOrder()
		->take(1)
		->first();

                
                      //$Provider = Provider::findOrFail($provider_id);
           
           $distance = Setting::get('provider_search_radius', '10');
       
           $latitude = $getticketdetails->lat;
           $longitude = $getticketdetails->log; 
           $service_type = 2;

            /*$Provider = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id','latitude','longitude','mobile')
            ->where('status', 'approved')
            ->where('id', '!=' ,$provider_id)
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->first();*/

           $Provider = Provider::findOrFail($provider_id);

           //dd($Provider->mobile);
            

            if($Provider) {
            $api_key = '35FEABDB060BF6';
            $mobile= $Provider->mobile;
            $contacts = $Provider->mobile;
            $from = 'TERAOD';
            $template_id= ''; 
            $sms_text = urlencode('Hi,You have Recieved request for odisha fleet.Please open the App and Accept the Request !..');

            $api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text;

           //Submit to server

            $response = file_get_contents( $api_url);
             }

            $Request->provider_id =  $Provider->id;
            $Request->downreason = $downreason ;
            $Request->downreasonindetailed = $downreasonindetailed;
            $Request->status = 'INCOMING';
            $Request->current_provider_id = $Provider->id;
            $Request->save();

          
       
           // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $request_id)->delete();

 
            ProviderService::where('provider_id',$Provider->id)->update(['status' =>'active']);

            (new SendPushNotification)->IncomingRequest($Provider->id);

            try {
                RequestFilter::where('request_id', $Request->id)
                    ->where('provider_id', $Provider->id)
                    ->firstOrFail();
            } catch (Exception $e) {
                $Filter = new RequestFilter;
                $Filter->request_id = $Request->id;
                $Filter->provider_id = $Provider->id; 
                $Filter->status = 0;
                $Filter->save();
            }

            return response()->json(['message' => 'success','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['message' => 'failure','status'=>0,'error' => $e->getMessage()], 500);
        }

    }

       /**
     * save the upload documents
     *
     * @return \Illuminate\Http\Response
     */
    public function savedocuments(Request $request){

        try{

           ini_set('post_max_size', '100M');
           ini_set('upload_max_filesize', '100M');

            $documents = $request->all();
            $downreason = $request->category;
            $downreasondetailed = $request->description;
            
            $request_ids = $documents['request_id'];
            $requestids= explode(',',$request_ids);
             //dd($requestids);   
               $i=0;    
              $beforefile_names = [];
                 $afterfile_names = [];

             foreach($requestids as $request_id){
               
                 DB::table('user_requests')->where('id',$request_id)->update(array(
                                 'status'=>'COMPLETED',
                                 'downreason'=>$downreason,
                                 'downreasonindetailed'=>$downreasondetailed,
                                 'autoclose'=>'Manual',
                                 'finished_at'=> date('Y-m-d H:i:s')
                  ));

                 
                   if($i==0){

                if ($request->hasFile('before_image')) {
                        $before_image = $request->before_image;
                        foreach ($before_image as $image) {
                           $beforefilename = $image->getClientOriginalName();                         
                           $image->move(public_path('uploads/SubmitFiles'), $beforefilename);
                           array_push($beforefile_names, $beforefilename);

                          }
                }


                if ($request->hasFile('after_image')) {
                        $after_image = $request->after_image;

                        foreach ($after_image as $image) {
                           $afterfilename= $image->getClientOriginalName();                         
                           $image->move(public_path('uploads/SubmitFiles'), $afterfilename);
                           array_push($afterfile_names, $afterfilename);

                          }
                }

                 }  
                 $i++;
 

                $documents['request_id'] =$request_id;
                $documents['before_image'] =json_encode($beforefile_names);
                $documents['after_image'] =json_encode($afterfile_names);
                
                  //Log::info($documents);

                 
                SubmitFile::create($documents);

              $UserRequest = UserRequests::where('id', $request_id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

                        if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            } else {
                $UserRequest->rating->update([
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

           //MasterTicket::where('ticketid', 'like', '%TKTN1115%')->update(['status' =>1]);

            DB::table('master_tickets')->where('ticketid',$UserRequest->booking_id)->update(array(
                                 'status'=>1,
                  ));


            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

             }
                        



            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
              dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


      /**
     * save the multi upload documents
     *
     * @return \Illuminate\Http\Response
     */
    public function multiupload(Request $request){

        try{
            if ($request->hasFile('before_image')) {
            $before_images= $request->file('before_image');

             foreach($before_images as $image){
                       $filename = $image->getClientOriginalName();
                       $extension = $image->getClientOriginalExtension();
                       print_r($filename);
                }
              }
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }



    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function savehistory(Request $request){

        try{

            $history = $request->all();
           
            MasterCoordinate::create($history);

            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

     /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function providerhistory(Request $request){

        try{

            $history = $request->all();
           
            ProviderHistory::create($history);

            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }



    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function dhqhistory(Request $request){
            try{
           
            $history = $request->all();
            $user_id = $history['user_id'];
            $total_tickets = UserRequests::where('user_id',$user_id)->count();
            $total_tickets_data = UserRequests::where('user_id',$user_id)->get();
            $ongoing_tickets = UserRequests::where('status','PICKEDUP')->where('user_id',$user_id)->count();
            $ongoing_tickets_data = UserRequests::where('status','PICKEDUP')->where('user_id',$user_id)->get();
            $completed_tickets = UserRequests::where('status','COMPLETED')->where('user_id',$user_id)->count();
            $completed_tickets_data = UserRequests::where('status','COMPLETED')->where('user_id',$user_id)->get();
            $cancelled_tickets = UserRequests::where('status','CANCELLED')->where('user_id',$user_id)->count();
            $cancelled_tickets_data = UserRequests::where('status','CANCELLED')->where('user_id',$user_id)->get();
            $pending_tickets = UserRequests::where('status','REASSIGNED')->where('user_id',$user_id)->count();
            $pending_tickets_data = UserRequests::where('status','REASSIGNED')->where('user_id',$user_id)->get();

             $data = array(
             "total" =>  $total_tickets,
             "total_data" =>  $total_tickets_data,
             "ongoing" =>  $ongoing_tickets,
             "ongoing_data" =>  $ongoing_tickets_data,
             "completed" =>  $completed_tickets,
             "completed_data" =>  $completed_tickets_data,
             "cancelled" =>  $cancelled_tickets,
             "cancelled_data" =>  $cancelled_tickets_data,
             "pending" =>  $pending_tickets,
             "pending_data" =>  $pending_tickets_data
             );
            return response()->json(['success' => 'true','data'=>$data,'status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }



    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function userperformance(Request $request)
{
    try {
        $user_id = $request->input('user_id');

        // Base query for the user
        $userRequests = UserRequests::where('provider_id', $user_id)->get();

        $assigned = $userRequests->count();
        $resolved = $userRequests->where('status', 'COMPLETED')->count();

        // SLA Met: if 'finished_at' exists and is within X time (e.g., 4 hours) from 'started_at'
        $slaThresholdMinutes = 240; // example SLA window

        $slaMet = $userRequests->filter(function ($ticket) use ($slaThresholdMinutes) {
            if ($ticket->started_at && $ticket->finished_at) {
                $start = \Carbon\Carbon::parse($ticket->started_at);
                $end = \Carbon\Carbon::parse($ticket->finished_at);
                return $start->diffInMinutes($end) <= $slaThresholdMinutes;
            }
            return false;
        })->count();

        $slaMissed = $resolved - $slaMet;

        // Total Distance Traveled
        $totalDistance = $userRequests->sum('distance'); // assuming distance is logged per ticket

        // Total and Average Time Spent
        $timeDurations = $userRequests->filter(function ($ticket) {
            return $ticket->started_at && $ticket->finished_at;
        })->map(function ($ticket) {
            $start = \Carbon\Carbon::parse($ticket->started_at);
            $end = \Carbon\Carbon::parse($ticket->finished_at);
            return $start->diffInMinutes($end);
        });

        $totalTimeSpent = $timeDurations->sum();
        $avgTimePerTicket = $timeDurations->count() > 0 ? round($timeDurations->avg(), 2) : 0;

        return response()->json([
            'status' => true,
            'data' => [
                'tickets_assigned' => $assigned,
                'tickets_resolved' => $resolved,
                'sla_met' => $slaMet,
                'sla_missed' => $slaMissed,
                'total_distance_traveled' => round($totalDistance, 2) . ' Kms',
                'total_time_spent_minutes' => $totalTimeSpent,
                'avg_time_per_ticket_minutes' => $avgTimePerTicket,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
    }




    public function merge_tickets(Request $request){

         $this->validate($request, [
                'ticket_id' => 'required',                
            ]);

        try{
            $ticket_id = $request->ticket_id;
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;




             DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update(['status' => 'COMPLETED']);

             DB::table('master_tickets')
            ->where('ticketid',$ticket_id)
            ->update(['status' => 1]);

            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$provider_id)->update(['status' =>'active']);


           return response()->json(['success' => 'true','status'=>1]);

           
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }


   public function PilotAcceptedRejected(Request $request){
        try{
dd(asdasd);
            info($request->all());
            $booking_id = $request->booking_id;
            $provider_id = $request->provider_id;
            $status = $request->status;

             if($status == 'ACCEPTED'){

               $statusDetails = [
             'status' =>$status,
              ];

              DB::table('user_requests')
            ->where('booking_id',$booking_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

               return response()->json(['success' => 'true','status'=>1]);


             } else if($status == 'REJECTED'){
              
              
             $statusDetails = [
             'status' =>$status,
              ];

              DB::table('user_requests')
            ->where('booking_id',$booking_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

                return response()->json(['success' => 'true','status'=>0]);

             }
             else {
           
           return response()->json(['success' => 'true','status'=>1]);
             }

           
        } catch (Exception $e) {
              info($e);
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }



   public function ProviderRequestStatus(Request $request){
        try{

            info($request->all());
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;
            $status = $request->status;



            $statusDetails = [
           'status' =>$status,
            ];


             DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

           return response()->json(['success' => 'true','status'=>1]);
           
        } catch (Exception $e) {
              info($e);
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }


  public function ProviderWorkStatus(Request $request){
        try{
              info($request->all());
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;
            $status= $request->status;

           
           if($status == 'ONCALL'){
               $statusDetails = [
             'started_at' =>date('y-m-d H:i:s',strtotime($request->started_at)),
             'started_location' =>$request->started_location,
             'started_latitude' =>isset($request->started_latitude)?($request->started_latitude):'',
             'started_longitude' =>isset($request->started_longitude)?($request->started_longitude):'',
            ];

              DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

          return response()->json(['success' => 'true','status'=>1]);

           }
           else if($status == 'REACHED') {
             
            $statusDetails = [
           'reached_at' =>date('y-m-d H:i:s',strtotime($request->reached_at)),
           'reached_location' =>$request->reached_location,
            ];

             DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);
           
             return response()->json(['success' => 'true','status'=>1]);


          }
 
          else {
     
             return response()->json(['success' => 'true','status'=>1]);

            }
           
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }



   public function autoSubmit(Request $request){

         $this->validate($request, [
                'ticket_id' => 'required', 
                'message' => 'required',               
            ]);

        try{
            $ticket_id = $request->ticket_id;
            $message= $request->message;
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;


            $updateDetails = [
           'status' =>'COMPLETED',
           'downreason' => $message
            ];

           $masterupdateDetails = [
           'status' =>1,
           'downreason' => $message
            ];


             DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update($updateDetails);

             DB::table('master_tickets')
            ->where('ticketid',$ticket_id)
            ->update($masterupdateDetails);

            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$provider_id)->update(['status' =>'active']);


           return response()->json(['success' => 'true','status'=>1]);

           
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }

    
    /**
     * get the received ticket list.
     *
     * 25/08/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function receivedTicketList() {

        if($receivedTicketList= MasterTicket::select('ticketid','pop_map_key')->where('ticketinsertstage',1)->get()) {
            return response()->json($receivedTicketList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


  /**
     * get the complete ticket list.
     *
     * 25/08/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function completeTicketList() {

        if($completeTicketList= MasterTicket::select('ticketid','pop_map_key')->where('status',1)->get()) {
            return response()->json($completeTicketList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }

    
   /**
     * get the user assigned ticket list.
     *
     * 23/02/2023 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function userAssignedTicketList(Request $request) {

         $provider_id = $request->provider_id;
         $status = $request->status;

        if( $status == 'All'){

                $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'!=','COMPLETED')
                                 ->where('user_requests.status' ,'!=','SEARCHING')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);
        } else if($status == 'notstarted'){
                   $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','INCOMING')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);

       } else if($status == 'inprogress'){

             $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','PICKEDUP')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);


        } else if ($status == 'Completed'){
              $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','COMPLETED')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('finished_at','desc')
                ->get();

                 return response()->json($tickets);

         } else if ($status == 'Onhold'){
              $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','ONHOLD')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

                 return response()->json($tickets);


       } else {
            return response()->json(['error' => trans('api.user.user_not_found')], 422);

    }


  }

    public function auto_assign_tickets(Request $request)
    {
        $schdeuled_tasks = DB::table('schedule_auto_assign')->get();

        $now_ime = Carbon::now()->timestamp;
        foreach($schdeuled_tasks as $index => $schdeule){
            if ($now_ime >= $schdeule->schedule_interval)
            {   

                $schedule_interval = (int)$schdeule->schedule_interval + (int)$schdeule->next_interval;
                DB::table('schedule_auto_assign')
                    ->where('id', $schdeule->id)
                    ->update(['schedule_interval' => $schedule_interval]);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                   CURLOPT_URL => $schdeule->url,
                   CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_ENCODING => "",
                   CURLOPT_MAXREDIRS => 10,
                   CURLOPT_TIMEOUT => 0,
                   CURLOPT_FOLLOWLOCATION => true,
                   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                   CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $curl_resp = curl_exec($curl);
                $httpStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
                $err = curl_error($curl);
                curl_close($curl);

                echo json_encode("yes it runned");
               exit;
            }
        }
           echo json_encode("Nothing to run!");
               exit;
    }


}