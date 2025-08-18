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

class PartsResource extends Controller
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
        $parts = DB::table('equipment')
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id', 'providers.first_name as first_name', 'providers.last_name as last_name', 'providers.id as providers_id')
                        ->leftJoin('material_consume', 'equipment.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        ->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($parts);
        return view('admin.parts.index',compact('parts','pagination'));
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
        return view('admin.parts.create',compact('districts', 'blocks', 'providers', 'materials'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $parts
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $part = DB::table('equipment')
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_type', 'material_consume.id as material_id', 'providers.first_name as first_name', 'providers.last_name as last_name', 'providers.id as providers_id')
                        ->leftJoin('material_consume', 'equipment.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        ->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
                        ->where('equipment.id', '=', $id)
                        ->first();
            // dd($part);
            if($part == NULL)
                return redirect()
                ->route('admin.parts.index')
                ->with('flash_success', trans('admin.part_msgs.part_not_found'));

            return view('admin.parts.show', compact('part'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.parts.index')
                ->with('flash_success', trans('admin.part_msgs.part_not_found'));
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
            'material_name' => 'required',
            'issued_district' => 'required',
            'issued_block' => 'required',
            'status' => 'required|in:in-service,out-of-service,missing',
            'issued_comments' => 'max:500'         
        ]);

        try{
            $parts = array();
            $parts['material_id'] = $request->material_name;
            $parts['indent_person'] = $request->indent_person;
            $parts['issued_person'] = $request->issued_person;
            $parts['provider_id'] = $request->received_person;
            $parts['issued_date'] = $request->issued_date;
            $parts['issued_person_mobile'] = $request->issued_person_mobile;
            $parts['district_id'] = $request->issued_district;
            $parts['block_id'] = $request->issued_block;
            $parts['indent_date'] = $request->indent_date;
            $parts['indent_approve_name'] = $request->indent_approve_name;
            $parts['gp_to_gp'] = $request->gp_to_gp;
            $parts['uom'] = $request->uom;
            $parts['issued_qty'] = $request->issued_qty;
            $parts['is_good_condition'] = $request->issued_qty_good_condition;
            $parts['received_date'] = $request->received_date;
            $parts['store_note_no'] = $request->store_note_no;
            $parts['status'] = $request->status;
            $parts['issued_comments'] = $request->issued_comments;

            $parts = DB::table('equipment')->insert($parts);

            return redirect()
                ->route('admin.parts.index')
                ->with('flash_success', trans('admin.part_msgs.part_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
            return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
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
            $part = DB::table('equipment')->find($id);
            if($part == NULL)
                return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
            
            return view('admin.parts.edit',compact('districts', 'blocks', 'providers', 'materials', 'part'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
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
            'material_name' => 'required',
            'issued_district' => 'required',
            'issued_block' => 'required',
            'status' => 'required|in:in-service,out-of-service,missing',
            'issued_comments' => 'max:500'       
        ]);

        try {

            $part = DB::table('equipment')->find($id);
            if($part == NULL)
                return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
            
            $parts = array();
            $parts['material_id'] = $request->material_name;
            $parts['indent_person'] = $request->indent_person;
            $parts['issued_person'] = $request->issued_person;
            $parts['provider_id'] = $request->received_person;
            $parts['issued_date'] = $request->issued_date;
            $parts['issued_person_mobile'] = $request->issued_person_mobile;
            $parts['district_id'] = $request->issued_district;
            $parts['block_id'] = $request->issued_block;
            $parts['indent_date'] = $request->indent_date;
            $parts['indent_approve_name'] = $request->indent_approve_name;
            $parts['gp_to_gp'] = $request->gp_to_gp;
            $parts['uom'] = $request->uom;
            $parts['issued_qty'] = $request->issued_qty;
            $parts['is_good_condition'] = $request->issued_qty_good_condition;
            $parts['received_date'] = $request->received_date;
            $parts['store_note_no'] = $request->store_note_no;
            $parts['status'] = $request->status;
            $parts['issued_comments'] = $request->issued_comments;

            $parts = DB::table('equipment')->where('id',$id)->update($parts);

            return redirect()->route('admin.parts.index')->with('flash_success', trans('admin.part_msgs.part_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
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
            return back()->with('message', trans('admin.part_msgs.part_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
        }
    }

}