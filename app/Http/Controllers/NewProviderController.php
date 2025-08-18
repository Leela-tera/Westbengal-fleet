<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestFilter;
use Carbon\Carbon;
use Auth;
use Setting;
use App\Helpers\Helper;
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
use Illuminate\Support\Arr;
use Log;


class NewProviderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('provider');
        $this->middleware('demo', ['only' => [
                'update_password',
            ]]);
       $this->perpage = Setting::get('per_page', '10');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('provider.index');
    }

public function dashboard()
    { 
        try{
           
            Session::put('user', Auth::User());
              
            //dd(auth()->id());
           
            $user_id= auth()->id();
            /*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

            echo "<pre>";
            print_r($UserRequest->toArray());exit;

            return view('emails.invoice',['Email' => $UserRequest]);*/

            $masterQuery = UserRequests::with('masterticket')->where('user_requests.provider_id',$user_id);

            $master_tickets =  $masterQuery->count();
            $clonemaster = clone $masterQuery;
            $ongoing_tickets = $clonemaster->Where('status','INCOMING')->count();
            $clonemaster1 = clone $masterQuery;
            $onhold_tickets = $clonemaster1->where('status','ONHOLD')->count();
            $clonemaster2 = clone $masterQuery;
            $scheduled_rides =$clonemaster2->where('status','SCHEDULED')->count();
            $clonemaster3 = clone $masterQuery;
            $completed_tickets = $clonemaster3->where('status','COMPLETED')->count();
           
            $powerQuery = UserRequests::with('masterticket')->where('downreason', 'like', '%Power%')->where('user_requests.provider_id',$user_id);
            $clonepower2 = clone $powerQuery;
            $holdups = $clonepower2->where('status','=','ONHOLD')->count();

           
            $electronicsQuery = UserRequests::with('masterticket')->where('downreason', 'like', '%ONT%')->where('user_requests.provider_id',$user_id);
            $cloneelectronics2 = clone $electronicsQuery;
            $holdelectronics  = $cloneelectronics2->where('status','=','ONHOLD')->count();


            $solorQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'SOLAR|SPV|SLA')->where('user_requests.provider_id',$user_id);
            $clonesolor2= clone $solorQuery;
            $holdsolar = $clonesolor2->where('status', 'ONHOLD')->count();


            $oltQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'OLT')->where('user_requests.provider_id',$user_id);
            $cloneolt2 = clone $oltQuery;
            $holdolt = $cloneolt2->where('status', 'ONHOLD')->count();
      

            $ccuQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'CCU|Battery')->where('user_requests.provider_id',$user_id);
            $cloneccu2 = clone $ccuQuery;
            $holdccu = $cloneccu2->where('status', 'ONHOLD')->count();
    
            $fiberQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'FIBER')->where('user_requests.provider_id',$user_id);
            $clonefiber2 = clone $fiberQuery;
            $holdfiber  = $clonefiber2->where('status','=','ONHOLD')->count();
         

  
            $otherQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other')->where('user_requests.provider_id',$user_id);
           
            $clonepower2 = clone $otherQuery;
            $holdothers = $clonepower2->where('status','=','ONHOLD')->count();
        


          
            $masterbase = UserRequests::with('masterticket')->where('user_requests.provider_id',$user_id);
            $clonemasterbase = clone $masterbase;
            $yesterdayclosed_tickets = $clonemasterbase->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonemasterbase1 = clone $masterbase;
            $todayclosed_tickets = $clonemasterbase1->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $clonemasterbase2 = clone $masterbase;
            $totalongoing_tickets = $clonemasterbase2->where('status','=','PICKEDUP')->count();
            $clonemasterbase3 = clone $masterbase;
            $todayongoing_tickets = $clonemasterbase3->where('status','=','PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $clonemasterbase4 = clone $masterbase;
            $yesterdayongoing_tickets = $clonemasterbase4->where('status','=','PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::Yesterday())->count();
            $clonemasterbase5 = clone $masterbase;
            $notstarted_tickets = $clonemasterbase5->where('status','=','INCOMING')->count();
            $clonemasterbase6 = clone $masterbase;
            $yesterdayonhold_tickets = $clonemasterbase6->where('status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
            $clonemasterbase7 = clone $masterbase;
            $todayonhold_tickets = $clonemasterbase7->where('status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->count();

            

            //dd("asdasdsad");
            return view('admin.newproviders.dashboard',compact('yesterdayongoing_tickets','totalongoing_tickets','yesterdayonhold_tickets','todayonhold_tickets','yesterdayclosed_tickets','todayclosed_tickets','todayongoing_tickets','notstarted_tickets','providers','fleet','provider','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue', 'wallet','master_tickets','completed_tickets','pending_tickets','ongoing_tickets','onhold_tickets','ups','electronics','fiber','poles','others','notstartedups','ongoingups','holdups','completedups','notstartedelectronics','ongoingelectronics','holdelectronics','completedelectronics','notstartedfiber','ongoingfiber','holdfiber','completedfiber','notstartedpoles','ongoingpoles','holdpoles','completedpoles','notstartedothers','ongoingothers','holdothers','completedothers','notworkedteamscount','solar','notstartedsolar','ongoingsolar','holdsolar',
                         'completedsolar','olt','notstartedolt','ongoingolt','holdolt','completedolt','ccu','notstartedccu','ongoingccu','holdccu','completedccu','completedothers_yesterday','completedfiber_yesterday','completedccu_yesterday','completedolt_yesterday','completedsolar_yesterday','completedelectronics_yesterday','completedups_yesterday'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }

    public function tickets(Request $request){

    try{  

         Session::put('user', Auth::User());
         $user_id= auth()->id();

        
        $serch_term = $request->searchinfo;
        $status=$request->get('status');
        $district_id=$request->get('district_id');
        $zone_id=$request->get('zone_id');
        $block_id=$request->get('block_id');
        $from_date=$request->get('from_date');
        $autoclose=$request->get('autoclose');
        $default_autoclose=$request->get('default_autoclose');
        $category=$request->get('category');
        $team_id=$request->get('team_id');
        $newfrom_date=$request->get('newfrom_date');
        $newto_date=$request->get('newto_date');
        $to_date=$request->get('to_date');
        $range=$request->get('range');




        $status_get = $status;
        $district_id_get = $district_id;
        $zone_id_get = $zone_id;
        $block_id_get = $block_id;
        $from_date_get = $from_date;
        $to_date_get = $to_date;
        $autoclose_get = $autoclose;
        $default_autoclose_get = $default_autoclose;
        $category_get = $category;
        $team_id_get =$team_id;
        $newfrom_date_get=$newfrom_date;
        $newto_date_get=$newto_date;
        $serch_term_get=$serch_term;
        $range_get=$range;


        $query_params = array();
        $tickets = DB::table('master_tickets')
         //->select('master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','providers.first_name','providers.last_name','providers.mobile','user_requests.started_at','user_requests.finished_at')
          ->select('master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.lgd_code','master_tickets.subsategory','user_requests.downreason','user_requests.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','zonal_managers.Name as zone_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','providers.zone_id','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at','user_requests.autoclose',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as duringhours'))
         ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
         ->leftjoin('providers', 'providers.id', '=', 'user_requests.provider_id')
         ->leftjoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
         ->leftjoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')->where('user_requests.provider_id',$user_id);
     
          $ticketsQueryClone = clone $tickets;
          $pickedUpCount = $ticketsQueryClone->where('user_requests.status', 'PICKEDUP')->count();


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

          if(isset($request->zone_id) && !empty($request->zone_id)){
            $query_params['zone_id'] = $request->zone_id;
            $tickets->where('gp_list.zonal_id',$request->zone_id);
         }
     
          if(isset($request->team_id) && !empty($request->team_id)){
            $query_params['team_id'] = $request->team_id;
            $tickets->where('providers.team_id',$request->team_id);
         }


         if(isset($request->autoclose) && !empty($request->autoclose)){
            $query_params['autoclose'] = $request->autoclose;
            $tickets->where('user_requests.autoclose',$request->autoclose);
         }
 
         if(isset($request->default_autoclose) && !empty($request->default_autoclose)){
            $query_params['default_autoclose'] = $request->default_autoclose;
            $tickets->where('user_requests.default_autoclose',$request->default_autoclose);
         }

         //if(isset($request->category) && !empty($request->category)){
         //   $query_params['category'] = $request->category;
         //   $tickets->where('user_requests.downreason','like', '%'.$request->category.'%');
        // }

          if (isset($request->category) && !empty($request->category)) {
               $query_params['category'] = $request->category;
    
       switch (strtolower($request->category)) {
        case 'power':
            $tickets->where('user_requests.downreason', 'like', '%Power%');
            break;
            
        case 'solar':
            $tickets->where('user_requests.downreason', 'regexp', 'SOLAR|SPV|SLA');
            break;
            
        case 'software/hardware':
            $tickets->where('user_requests.downreason', 'like', '%ONT%');
            break;
            
        case 'ccu/battery':
            $tickets->where('user_requests.downreason', 'regexp', 'CCU|Battery');
            break;

        case 'others':
            $tickets->where('user_requests.downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other');
            break;
            
        default:
            // Default case if category doesn't match any specific pattern
            $tickets->where('user_requests.downreason', 'like', '%'.$request->category.'%');
        }
     }



         if(isset($request->status) && !empty($request->status)){
            $query_params['status'] = $request->status;
            $tkt_status = array('NotStarted' => 'INCOMING','OnGoing' => 'PICKEDUP', 'Completed' => 'COMPLETED', 'Onhold' => 'ONHOLD');                         
              $tickets->where('user_requests.status',$tkt_status[$request->status]);
           }
         if(isset($request->from_date) && !empty($request->to_date)){
                 $query_params['from_date'] = $request->from_date;
                 $query_params['to_date'] = $request->to_date;
                 $fromDate = $request->from_date . ' 00:00:00'; // Start of the day
                 $toDate = $request->to_date . ' 23:59:59'; // End of the day
     
               if(isset($request->status) && $request->status == 'Completed')
              {
                $tickets->whereBetween('user_requests.finished_at', [$fromDate, $toDate ]);

               } else if(isset($request->status) && $request->status == 'NotStarted'){
                $tickets->whereBetween('master_tickets.downdate', [$fromDate , $toDate ]);

               } else {
                 $tickets->whereBetween('user_requests.started_at', [$fromDate , $toDate ]);

                 }
                 }
          
           if (!empty($request->newfrom_date) && !empty($request->newto_date)) {

             $nfromDate = Carbon::parse($request->newfrom_date)->startOfDay()->toDateTimeString();
            $ntoDate = Carbon::parse($request->newto_date)->endOfDay()->toDateTimeString();

    if (isset($request->status)) {
        if ($request->status == 'Completed') {
            $tickets->whereBetween('user_requests.finished_at', [$nfromDate , $ntoDate ]);
        } elseif ($request->status == 'Onhold') {
            $tickets->whereBetween('user_requests.started_at', [$nfromDate , $ntoDate ]);
        } else {
            $tickets->whereDate('user_requests.started_at', '=', $request->newfrom_date);
        }
    } else {
        $tickets->whereDate('user_requests.started_at', '=', $request->newfrom_date);
    }
}

        if(isset($request->range) && !empty($request->range)){
            $query_params['range'] = $request->range;
                                     
              $tickets->whereRaw('STR_TO_DATE(CONCAT(master_tickets.downdate, " ", master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p") < DATE_SUB(NOW(), INTERVAL 24 HOUR)');    
       }



         // Search functionality
         if(isset($request->searchinfo) && !empty($request->searchinfo))
         {
            $query_params['searchinfo'] = $request->searchinfo;
            //$serch_term = $request->searchinfo;
            $tickets->where(function ($query) use($serch_term){
                    $query->where('master_tickets.ticketid', 'like', '%'.$serch_term.'%')
                        ->orWhere('zonal_managers.Name', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.district', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.mandal', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.gpname', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.lgd_code', 'like', '%'.$serch_term.'%')
                        ->orWhere('providers.first_name', 'like', '%'.$serch_term.'%')
                        ->orWhere('providers.last_name', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.downreason', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.downreasonindetailed', 'like', '%'.$serch_term.'%')
                        ->orWhere('user_requests.autoclose', 'like', '%'.$serch_term.'%');
                });
         }

        $tickets = $tickets->orderBy('downdate','desc')
                         ->orderBy('downtime','asc');
                         //->get();
                         //->toSql();

         

        if($request->ajax()) {
            $tickets = $tickets->get();
            return response()->json(array('success' => true, 'data'=>$tickets));

        } else {
            $tickets = $tickets->paginate($this->perpage);
        }  
                                          
     // dd($tickets);
         $pagination=(new Helper)->formatPagination($tickets);
        //$url = $tickets->url($tickets->currentPage());

       //$request->session()->put('ticketspage', $url);


        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get();
        $zonals= DB::table('zonal_managers')->get();
        $services= DB::table('service_types')->get();

        $ticket_status = array('NotStarted', 'OnGoing','Completed', 'Onhold');

        

        return view('admin.newproviders.tickets_new', compact('services','tickets','districts','blocks', 'zonals','ticket_status', 'query_params','pagination','status_get','district_id_get','zone_id_get','team_id_get','block_id_get','from_date_get','to_date_get','autoclose_get','default_autoclose_get','category_get','newfrom_date_get','newto_date_get','serch_term_get','range_get','pickedUpCount'));

    } catch (Exception $e) { 
        dd($e);
        return back()->with('flash_error', trans('admin.something_wrong'));
    }
}


public function signout(Request $request)
{
    Auth::guard('provider')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

   
    return redirect('/provider'); // or any route you want to redirect to
}




    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function incoming(Request $request)
    {
        return (new TripController())->index($request);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function accept(Request $request, $id)
    {
        return (new TripController())->accept($request, $id);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function reject($id)
    {
        return (new TripController())->destroy($id);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        return (new TripController())->update($request, $id);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function rating(Request $request, $id)
    {
        return (new TripController())->rate($request, $id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function earnings()
    {
        $provider = Provider::where('id',\Auth::guard('provider')->user()->id)
                    ->with('service','accepted','cancelled')
                    ->get();

        $weekly = UserRequests::where('provider_id',\Auth::guard('provider')->user()->id)
                    ->with('payment')
                    ->where('created_at', '>=', Carbon::now()->subWeekdays(7))
                    ->get();

        $weekly_sum = UserRequestPayment::whereHas('request', function($query) {
                        $query->where('provider_id',\Auth::guard('provider')->user()->id);
                        $query->where('created_at', '>=', Carbon::now()->subWeekdays(7));
                    })
                        ->sum('provider_pay');

        $today = UserRequests::where('provider_id',\Auth::guard('provider')->user()->id)
                    ->where('created_at', '>=', Carbon::today())
                    ->count();

        $fully = UserRequests::where('provider_id',\Auth::guard('provider')->user()->id)
                    ->with('payment','service_type')->orderBy('id','desc')
                    ->get();

        $fully_sum = UserRequestPayment::whereHas('request', function($query) {
                        $query->where('provider_id', \Auth::guard('provider')->user()->id);
                        })
                        ->sum('provider_pay');

        return view('provider.payment.earnings',compact('provider','weekly','fully','today','weekly_sum','fully_sum'));
    }

    /**
     * available.
     *
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
    {
        (new ProviderResources\ProfileController)->available($request);
        return back();
    }

    /**
     * Show the application change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function change_password()
    {
        return view('provider.profile.change_password');
    }

    /**
     * Change Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request)
    {
        $this->validate($request, [
                'password' => 'required|confirmed',
                'old_password' => 'required',
            ]);

        $Provider = \Auth::user();

        if(password_verify($request->old_password, $Provider->password))
        {
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            return back()->with('flash_success', trans('admin.password_update'));
        } else {
            return back()->with('flash_error', trans('admin.password_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function location_edit()
    {
        return view('provider.location.index');
    }

    /**
     * Update latitude and longitude of the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function location_update(Request $request)
    {
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($Provider = \Auth::user()){

            $Provider->latitude = $request->latitude;
            $Provider->longitude = $request->longitude;
            $Provider->save();

            return back()->with(['flash_success' => trans('api.provider.location_updated')]);

        } else {
            return back()->with(['flash_error' => trans('admin.provider_msgs.provider_not_found')]);
        }
    }

    /**
     * upcoming history.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_trips()
    {
        $fully = (new ProviderResources\TripController)->upcoming_trips();
        return view('provider.payment.upcoming',compact('fully'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function cancel(Request $request) {
        try{

            (new TripController)->cancel($request);
            return back()->with(['flash_success' => trans('admin.provider_msgs.trip_cancelled')]);
        } catch (ModelNotFoundException $e) {
            return back()->with(['flash_error' => trans('admin.something_wrong')]);
        }
    }

    public function wallet_transation(Request $request){

        try{
            $wallet_transation = ProviderWallet::where('provider_id',Auth::user()->id)
                                ->orderBy('id','desc')
                                ->paginate(Setting::get('per_page', '10'));
            
            $pagination=(new Helper)->formatPagination($wallet_transation);   
            
            $wallet_balance=Auth::user()->wallet_balance;

            return view('provider.wallet.wallet_transation',compact('wallet_transation','pagination','wallet_balance'));
          
        }catch(Exception $e){
            return back()->with(['flash_error' => trans('admin.something_wrong')]);
        }
        
    }

    public function transfer(Request $request){

        $pendinglist = WalletRequests::where('from_id',Auth::user()->id)->where('request_from','provider')->where('status',0)->get();
        $wallet_balance=Auth::user()->wallet_balance;
        return view('provider.wallet.transfer',compact('pendinglist','wallet_balance'));
    }

    public function requestamount(Request $request)
    {
        
        
        $send=(new TripController())->requestamount($request);
        $response=json_decode($send->getContent(),true);
        
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


    public function stripe(Request $request)
    {
        return (new ProviderResources\ProfileController)->stripe($request);
    }

    public function cards()
    {
        $cards = (new Resource\ProviderCardResource)->index();
        return view('provider.wallet.card',compact('cards'));
    }

    public function updateLocation(Request $request)
{
    Session::put('user', Auth::user());  
    $user_id = auth()->id();

    Provider::where('id', $user_id)->update([
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);

    return response()->json(['success' => true]);
}



}