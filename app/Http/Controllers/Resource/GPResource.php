<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Setting;
use Storage;

use App\Helpers\Helper;
use Mail;
use DateTime;
use App\District;
use App\Block;
use App\Provider;

class GPResource extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => [ 'store', 'update', 'destroy', 'disapprove']]);
        $this->perpage = Setting::get('per_page', '10');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $gps = DB::table('gp_list')
                        ->select('gp_list.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name','zonal_managers.Name as zonal_name','blocks.id as blocks_id')
                        ->leftJoin('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
                        ->leftJoin('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->get();
                        // ->paginate($this->perpage);
        // $pagination=(new Helper)->formatPagination($gps);
        // dd($pagination);
        return view('admin.gps.index',compact('gps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $districts = District::get();
        $blocks = Block::get();
        $providers = Provider::get();
        $zonals = DB::table('zonal_managers')->get();
        return view('admin.gps.create',compact('districts', 'blocks', 'providers', 'zonals'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $gps
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $gp = DB::table('gp_list')
                        ->select('gp_list.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id')
                        ->leftJoin('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->where('gp_list.id', '=', $id)
                        ->first();
            // dd($gp);
            if($gp == NULL)
                return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_not_found'));

            return view('admin.gps.show', compact('gp'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $this->validate($request, [
            'gp_name' => 'required',
            'district' => 'required',
            'block' => 'required',
            'zone' => 'required',
            'provider' => 'required',
            'contact' => 'required'        
        ]);

        try{
            $gp = array();
            $gp['gp_name'] = $request->gp_name;
            $gp['district_id'] = $request->district;
            $gp['block_id'] = $request->block;
            $gp['zonal_id'] = $request->zone;
            $gp['provider'] = $request->provider;
            $gp['contact_no'] = $request->contact;
            $gp['lgd_code'] = $request->lgd_code;
            $gp['phase'] = $request->phase;
            $gp['latitude'] = $request->latitude;
            $gp['longitude'] = $request->longitude;

            $gp = DB::table('gp_list')->insert($gp);

            return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $districts = District::get();
            $blocks = Block::get();
            $providers = Provider::select('*',\DB::Raw('concat(first_name," ",last_name) AS name'))->get();
            $zonals = DB::table('zonal_managers')->get();
            $gp = DB::table('gp_list')->find($id);            
            return view('admin.gps.edit',compact('districts', 'blocks', 'providers', 'gp','zonals'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'gp_name' => 'required',
            'district' => 'required',
            'block' => 'required',
            'zone' => 'required',
            'provider' => 'required',
            'contact' => 'required'        
        ]);

        try {

            $gp = array();
            $gp['gp_name'] = $request->gp_name;
            $gp['district_id'] = $request->district;
            $gp['block_id'] = $request->block;
            $gp['zonal_id'] = $request->zone;
            $gp['provider'] = $request->provider;
            $gp['contact_no'] = $request->contact;
            $gp['lgd_code'] = $request->lgd_code;
            $gp['phase'] = $request->phase;
            $gp['latitude'] = $request->latitude;
            $gp['longitude'] = $request->longitude;

            $gps = DB::table('gp_list')->where('id',$id)->update($gp);

            return redirect()->route('admin.gps.index')->with('flash_success', trans('admin.gp_msgs.gp_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::table('gp_list')->delete($id);
            return back()->with('message', trans('admin.gp_msgs.gp_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    public function gpreportsnew(Request $request)
{
    $tickets = DB::table('master_tickets')
    ->select(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name as zone_name',
        DB::raw("
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(MINUTE, 
            STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
            ), '%Y-%m-%d %H:%i:%s'), 
            COALESCE(user_requests.finished_at, NOW()))
        ) / 60), '.', 
        LPAD(
            MOD(
                SUM(TIMESTAMPDIFF(MINUTE, 
                    STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                    ), '%Y-%m-%d %H:%i:%s'), 
                    COALESCE(user_requests.finished_at, NOW()))
                ), 60
            ), 2, '0'
        )
    ) AS total_gps_down_hours
")
    )
    ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
    ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
    ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
    ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
    ->where('user_requests.default_autoclose','Auto');        

    // Apply Filters
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // Group by required fields
    $downreport = $tickets->groupBy('master_tickets.gpname','master_tickets.lgd_code', 'master_tickets.mandal', 'master_tickets.district', 'zonal_managers.Name')
        ->get();

 //dd($downreport );

        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get();
        $zonals= DB::table('zonal_managers')->get();


    return view('admin.reports.gpreports', compact('downreport','districts','blocks','zonals'));
}


public function gpreports_old1(Request $request)
{
    // Raw SQL for Working Minutes Calculation
    $workingMinutesSql = "
        CASE 
            WHEN DATE(STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
            ), '%Y-%m-%d %H:%i:%s')) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                TIMESTAMPDIFF(
                    MINUTE,
                    GREATEST(
                        STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                            DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                        ), '%Y-%m-%d %H:%i:%s'),
                        CONCAT(master_tickets.downdate, ' 10:00:00')
                    ),
                    LEAST(
                        COALESCE(user_requests.finished_at, NOW()),
                        CONCAT(master_tickets.downdate, ' 17:00:00')
                    )
                )
            ELSE
                TIMESTAMPDIFF(
                    MINUTE,
                    GREATEST(
                        STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                            DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                        ), '%Y-%m-%d %H:%i:%s'),
                        CONCAT(master_tickets.downdate, ' 10:00:00')
                    ),
                    CONCAT(master_tickets.downdate, ' 17:00:00')
                )
                +
                (DATEDIFF(
                    DATE(COALESCE(user_requests.finished_at, NOW())),
                    DATE(STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                    ), '%Y-%m-%d %H:%i:%s'))
                ) - 1) * 420
                +
                TIMESTAMPDIFF(
                    MINUTE,
                    CONCAT(DATE(COALESCE(user_requests.finished_at, NOW())), ' 10:00:00'),
                    LEAST(
                        COALESCE(user_requests.finished_at, NOW()),
                        CONCAT(DATE(COALESCE(user_requests.finished_at, NOW())), ' 17:00:00')
                    )
                )
        END
    ";

    // Final query
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ") // HH:MM Format
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.default_autoclose', 'Auto');

    // Filters
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // Group By
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )
    ->get();

    // Dropdown Data
    $districts = DB::table('districts')->get();
    $blocks = DB::table('blocks')->get();
    $zonals = DB::table('zonal_managers')->get();

    // Return view
    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}

public function gpreports_old2(Request $request)
{
    // 1) Convert downtime into 24-hour format once:
    //    downtime_24h = STR_TO_DATE(CONCAT(mt.downdate, ' ', 24hr_downtime_str), '%Y-%m-%d %H:%i:%s')
    //    We'll inline it in queries to keep it simple.
    // 2) Each partial day is wrapped with GREATEST(0, TIMESTAMPDIFF(...)) to prevent negative values.

    $workingMinutesSql = "
        CASE 
            -- =======================
            -- 1) SAME-DAY SCENARIO
            -- =======================
            WHEN DATE(
                STR_TO_DATE(
                    CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(
                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                            '%H:%i:%s'
                        )
                    ), 
                    '%Y-%m-%d %H:%i:%s'
                )
            ) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                -- For same-day, we clamp the start between [10:00, 17:00],
                -- and the end is min(finished_at, 17:00). Then we ensure no negative result.
                GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        MINUTE,
                        GREATEST(
                            LEAST(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                ),
                                CONCAT(master_tickets.downdate, ' 17:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 10:00:00')
                        ),
                        LEAST(
                            COALESCE(user_requests.finished_at, NOW()),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )

            -- =======================
            -- 2) MULTI-DAY SCENARIO
            -- =======================
            ELSE
                (
                    -- ---------- (a) First Partial Day ----------
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            GREATEST(
                                LEAST(
                                    STR_TO_DATE(
                                        CONCAT(master_tickets.downdate, ' ', 
                                            DATE_FORMAT(
                                                STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                                '%H:%i:%s'
                                            )
                                        ), 
                                        '%Y-%m-%d %H:%i:%s'
                                    ),
                                    CONCAT(master_tickets.downdate, ' 17:00:00')
                                ),
                                CONCAT(master_tickets.downdate, ' 10:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )
                +
                (
                    -- ---------- (b) Middle Full Days ----------
                    -- Each full day is 7 hours (420 minutes).
                    -- If DATEDIFF(...) - 1 is negative, clamp to 0.
                    GREATEST(
                        0,
                        (DATEDIFF(
                            DATE(COALESCE(user_requests.finished_at, NOW())),
                            DATE(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                )
                            )
                        ) - 1)
                    ) * 420
                )
                +
                (
                    -- ---------- (c) Last Partial Day ----------
                    -- From 10:00 on the final day up to min(finished_at, 17:00).
                    -- If finished_at < 10:00, we get negative => clamp to 0.
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            CONCAT(
                                DATE(COALESCE(user_requests.finished_at, NOW())), 
                                ' 10:00:00'
                            ),
                            LEAST(
                                COALESCE(user_requests.finished_at, NOW()),
                                CONCAT(
                                    DATE(COALESCE(user_requests.finished_at, NOW())), 
                                    ' 17:00:00'
                                )
                            )
                        )
                    )
                )
        END
    ";

    // ========== Final Query ==========
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ")
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.default_autoclose', 'Auto');

    // ---------- Filters ----------
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // ---------- Group By ----------
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )->get();

    // ---------- Dropdown Data ----------
    $districts = DB::table('districts')->get();
    $blocks    = DB::table('blocks')->get();
    $zonals    = DB::table('zonal_managers')->get();

    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}

public function gpreports(Request $request)
{
    // 1) Convert downtime into 24-hour format once:
    //    downtime_24h = STR_TO_DATE(CONCAT(mt.downdate, ' ', 24hr_downtime_str), '%Y-%m-%d %H:%i:%s')
    //    We'll inline it in queries to keep it simple.
    // 2) Each partial day is wrapped with GREATEST(0, TIMESTAMPDIFF(...)) to prevent negative values.

    $workingMinutesSql = "
        CASE 
            -- =======================
            -- 1) SAME-DAY SCENARIO
            -- =======================
            WHEN DATE(
                STR_TO_DATE(
                    CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(
                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                            '%H:%i:%s'
                        )
                    ), 
                    '%Y-%m-%d %H:%i:%s'
                )
            ) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                -- For same-day, we clamp the start between [10:00, 17:00],
                -- and the end is min(finished_at, 17:00). Then we ensure no negative result.
                GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        MINUTE,
                        GREATEST(
                            LEAST(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                ),
                                CONCAT(master_tickets.downdate, ' 17:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 10:00:00')
                        ),
                        LEAST(
                            COALESCE(user_requests.finished_at, NOW()),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )

            -- =======================
            -- 2) MULTI-DAY SCENARIO
            -- =======================
            ELSE
                (
                    -- ---------- (a) First Partial Day ----------
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            GREATEST(
                                LEAST(
                                    STR_TO_DATE(
                                        CONCAT(master_tickets.downdate, ' ', 
                                            DATE_FORMAT(
                                                STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                                '%H:%i:%s'
                                            )
                                        ), 
                                        '%Y-%m-%d %H:%i:%s'
                                    ),
                                    CONCAT(master_tickets.downdate, ' 17:00:00')
                                ),
                                CONCAT(master_tickets.downdate, ' 10:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )
                +
                (
                    -- ---------- (b) Middle Full Days ----------
                    -- Each full day is 7 hours (420 minutes).
                    -- If DATEDIFF(...) - 1 is negative, clamp to 0.
                    GREATEST(
                        0,
                        (DATEDIFF(
                            DATE(COALESCE(user_requests.finished_at, NOW())),
                            DATE(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                )
                            )
                        ) - 1)
                    ) * 420
                )
                +
                (
                    -- ---------- (c) Last Partial Day ----------
                    -- From 10:00 on the final day up to min(finished_at, 17:00).
                    -- If finished_at < 10:00, we get negative => clamp to 0.
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            CONCAT(
                                DATE(COALESCE(user_requests.finished_at, NOW())), 
                                ' 10:00:00'
                            ),
                            LEAST(
                                COALESCE(user_requests.finished_at, NOW()),
                                CONCAT(
                                    DATE(COALESCE(user_requests.finished_at, NOW())), 
                                    ' 17:00:00'
                                )
                            )
                        )
                    )
                )
        END
    ";

    // ========== Final Query ==========
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(SUM($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(SUM($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ")
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.default_autoclose', 'Auto');

    // ---------- Filters ----------
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // ---------- Group By ----------
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )->get();

    // ---------- Dropdown Data ----------
    $districts = DB::table('districts')->get();
    $blocks    = DB::table('blocks')->get();
    $zonals    = DB::table('zonal_managers')->get();

    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}




}