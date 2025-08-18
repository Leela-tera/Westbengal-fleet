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

class MaterialConsumptionResource extends Controller
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
        $materials_consumption = DB::table('material_consumption')
                        ->select('material_consumption.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'material_consumption.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_consumption.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'material_consumption.block_id', '=', 'blocks.id')
                        //->leftJoin('providers', 'material_consumption.provider_id', '=', 'providers.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($materials_consumption);
        return view('admin.material_consumption.index',compact('materials_consumption','pagination'));
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
        // $providers = Provider::get();
        $materials = DB::table('material_consume')->get();
        $drums = DB::table('drum_master')->get();
        return view('admin.material_consumption.create',compact('districts', 'blocks', 'materials','drums'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $material_consumption
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $material_consumption = DB::table('material_consumption')
                        ->select('material_consumption.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'material_consumption.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_consumption.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'material_consumption.block_id', '=', 'blocks.id')
                        // ->leftJoin('providers', 'material_consumption.provider_id', '=', 'providers.id')
                        ->where('material_consumption.id', '=', $id)
                        ->first();
            // dd($material_consumption);
            if($material_consumption == NULL)
                return redirect()
                ->route('admin.material_consumption.index')
                ->with('flash_success', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));

            return view('admin.material_consumption.show', compact('material_consumption'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.material_consumption.index')
                ->with('flash_success', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
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
            'materials' => 'required',
            'remarks' => 'max:500'         
        ]);

        try{
            if(($request->materials != ''))
	     {
                $msize= count($request->materials);
                //dd($msize);
	          for ($i = 0; $i < $msize; $i++)
	         { 
	              $material_consumption = array(
			         'date' => $request->get('date'),
					 'material_id' => $request->get('materials')[$i],
					 'uom' => $request->get('uom')[$i],
					 'material_indent_note' => $request->get('material_indent_note'),
					 'min_no' => $request->get('min_no'),
					 'district_id' => $request->get('issued_district'),
					 'block_id' => $request->get('issued_block'),
                                         'from_gp' => $request->get('from_gp'),
                                         'to_gp' => $request->get('to_gp'),
					 'link_name' => $request->get('link_name'),
					 'drum_number' => $request->get('drum_no')[$i],
                                         'start_meter' => $request->get('start_meter')[$i],
                                         'end_meter' => $request->get('end_meter')[$i],
                                         'gprs_coordinates' => $request->get('gprs_coordinates')[$i],  
					 'status_link_up_down' => $request->get('status_link_up_down'),
					 'consumed_qty' => $request->get('consumed_qty')[$i],
					 'remarks' => $request->get('remarks')
				   );

                            //dd($parts);
		
                          $material_consumption = DB::table('material_consumption')->insert($material_consumption);
			}
			
			}


            return redirect()
                ->route('admin.material_consumption.index')
                ->with('flash_success', trans('admin.mt_consumption_msgs.mt_consumption_saved'));
        } 
        catch (Exception $e) {  
            dd($e->getMessage());
            return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
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
            // $providers = Provider::get();
            $materials = DB::table('material_consume')->get();
            $material_consumption = DB::table('material_consumption')->find($id);
            if($material_consumption == NULL)
                return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
            
            return view('admin.material_consumption.edit',compact('districts', 'blocks', 'materials', 'material_consumption'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
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
            'remarks' => 'max:500'       
        ]);

        try {

            $material_consumption = DB::table('material_consumption')->find($id);
            if($material_consumption == NULL)
                return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
            
            $materials_consumption = array();
            $materials_consumption['date'] = $request->date;
            $materials_consumption['material_id'] = $request->material_name;
            $materials_consumption['uom'] = $request->uom;
            $materials_consumption['material_indent_note'] = $request->material_indent_note;
            $materials_consumption['min_no'] = $request->min_no;
            $materials_consumption['district_id'] = $request->issued_district;
            $materials_consumption['block_id'] = $request->issued_block;
            $materials_consumption['from_gp'] = $request->from_gp;
            $materials_consumption['to_gp'] = $request->to_gp;
            $materials_consumption['link_name'] = $request->link_name;
            $materials_consumption['drum_number'] = $request->drum_number;
            $materials_consumption['start_meter'] = $request->start_meter;
            $materials_consumption['end_meter'] = $request->end_meter;
            $materials_consumption['gprs_coordinates'] = $request->gprs_coordinates;
            $materials_consumption['consumed_qty'] = $request->consumed_qty;
            $materials_consumption['status_link_up_down'] = $request->status_link_up_down;
            $materials_consumption['remarks'] = $request->remarks;

            $materials_consumption = DB::table('material_consumption')->where('id',$id)->update($materials_consumption);

            return redirect()->route('admin.material_consumption.index')->with('flash_success', trans('admin.mt_consumption_msgs.mt_consumption_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
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
            DB::table('material_consumption')->delete($id);
            return back()->with('message', trans('admin.mt_consumption_msgs.mt_consumption_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.mt_consumption_msgs.mt_consumption_not_found'));
        }
    }

}