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

class EquipmentResource extends Controller
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
        $equipments = DB::table('equipment')
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as service_type', 'material_consume.id as service_id', 'providers.first_name as first_name', 'providers.last_name as last_name', 'providers.id as providers_id')
                        ->leftJoin('material_consume', 'equipment.service_type_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        ->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($equipments);
        return view('admin.equipment.index',compact('equipments','pagination'));
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
        $materials = DB::table('material_consume')->get();
        return view('admin.equipment.create',compact('districts', 'blocks', 'providers', 'materials'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $equipment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $equipment = DB::table('equipment')
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as service_type', 'material_consume.id as service_id', 'providers.first_name as first_name', 'providers.last_name as last_name', 'providers.id as providers_id')
                        ->leftJoin('material_consume', 'equipment.service_type_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        ->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
                        ->where('equipment.id', '=', $id)
                        ->first();
            // dd($equipment);
            if($equipment == NULL)
                return redirect()
                ->route('admin.equipment.index')
                ->with('flash_success', trans('admin.equipment_msgs.equipment_not_found'));

            return view('admin.equipment.show', compact('equipment'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.equipment.index')
                ->with('flash_success', trans('admin.equipment_msgs.equipment_not_found'));
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
            'equipment_name' => 'required|max:255',
            'service_type' => 'required',
            'district' => 'required',
            'block' => 'required',
            'status' => 'required|in:in-service,out-of-service,missing',
            'purchase_comments' => 'max:500'         
        ]);

        try{
            $equipment = array();
            $equipment['name'] = $request->equipment_name;
            $equipment['service_type_id'] = $request->service_type;
            $equipment['provider_id'] = $request->current_assignee;
            $equipment['service_number'] = $request->service_number;
            $equipment['service_model'] = $request->service_model;
            $equipment['district_id'] = $request->district;
            $equipment['block_id'] = $request->block;
            $equipment['status'] = $request->status;
            $equipment['purchase_price'] = $request->purchase_price;
            $equipment['purchase_date'] = $request->purchase_date;
            $equipment['warranty_exp_date'] = $request->warranty_exp_date;
            $equipment['purchase_comments'] = $request->purchase_comments;

            $equipment = DB::table('equipment')->insert($equipment);

            return redirect()
                ->route('admin.equipment.index')
                ->with('flash_success', trans('admin.equipment_msgs.equipment_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
            return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
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
            $providers = Provider::get();
            $materials = DB::table('material_consume')->get();
            $equipment = DB::table('equipment')->find($id);
            if($equipment == NULL)
                return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
            return view('admin.equipment.edit',compact('districts', 'blocks', 'providers', 'materials', 'equipment'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
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
            'equipment_name' => 'required|max:255',
            'service_type' => 'required',
            'district' => 'required',
            'block' => 'required',
            'status' => 'required|in:in-service,out-of-service,missing',
            'purchase_comments' => 'max:500'         
        ]);

        try {

            $equipment = DB::table('equipment')->find($id);
            if($equipment == NULL)
                return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
            
            $equipment = array();
            $equipment['name'] = $request->equipment_name;
            $equipment['service_type_id'] = $request->service_type;
            $equipment['provider_id'] = $request->current_assignee;
            $equipment['service_number'] = $request->service_number;
            $equipment['service_model'] = $request->service_model;
            $equipment['district_id'] = $request->district;
            $equipment['block_id'] = $request->block;
            $equipment['status'] = $request->status;
            $equipment['purchase_price'] = $request->purchase_price;
            $equipment['purchase_date'] = $request->purchase_date;
            $equipment['warranty_exp_date'] = $request->warranty_exp_date;
            $equipment['purchase_comments'] = $request->purchase_comments;

            $equipment = DB::table('equipment')->where('id',$id)->update($equipment);

            return redirect()->route('admin.equipment.index')->with('flash_success', trans('admin.equipment_msgs.equipment_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
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
            DB::table('equipment')->delete($id);
            return back()->with('message', trans('admin.equipment_msgs.equipment_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.equipment_msgs.equipment_not_found'));
        }
    }

}