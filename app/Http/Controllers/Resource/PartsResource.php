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
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'equipment.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        //->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
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
        // $providers = Provider::get();
        $materials = DB::table('material_consume')->get();
        $fromgp_list =  DB::table('gp_list')->get();
        $togp_list =  DB::table('gp_list')->get();

        return view('admin.parts.create',compact('districts', 'blocks', 'materials','fromgp_list','togp_list'));
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
                        ->select('equipment.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'equipment.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'equipment.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'equipment.block_id', '=', 'blocks.id')
                        // ->leftJoin('providers', 'equipment.provider_id', '=', 'providers.id')
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
        // dd($request->all());
       
        $this->validate($request, [
            'materials' => 'required',
            'district_id' => 'required',
            'block_id' => 'required',
            'remarks' => 'max:500'         
        ]);

        try{
             if(($request->materials != ''))
	     {
                $msize= count($request->materials);
                //dd($msize);
	          for ($i = 0; $i < $msize; $i++)
	         { 
	              $parts= array(
			         'date' => $request->get('date'),
					 'material_id' => $request->get('materials')[$i],
					 'uom' => $request->get('uom')[$i],
					 'material_indent_note' => $request->get('material_indent_note'),
					 'min_no' => $request->get('min_no'),
					 'district_id' => $request->get('district_id'),
					 'block_id' => $request->get('block_id'),
					 'from_gp' => $request->get('from_gp'),
					 'to_gp' => $request->get('to_gp'),
					 'issued_qty' => $request->get('issued_qty')[$i],
					 'consumed_qty' => $request->get('consumed_qty')[$i],
					 'balance_at_location' => $request->get('balance_at_location')[$i],
					 'remarks' => $request->get('remarks')
				   );

                            //dd($parts);
		
                          $parts = DB::table('equipment')->insert($parts);
			
			}
			
			}
            return redirect()
                ->route('admin.parts.index')
                ->with('flash_success', trans('admin.part_msgs.part_saved'));
        } 
        catch (Exception $e) {  
            dd($e->getMessage());
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
            // $providers = Provider::get();
            $materials = DB::table('material_consume')->get();
            $part = DB::table('equipment')->find($id);
            if($part == NULL)
                return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
            
            return view('admin.parts.edit',compact('districts', 'blocks', 'materials', 'part'));
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
            'remarks' => 'max:500'       
        ]);

        try {

            $part = DB::table('equipment')->find($id);
            if($part == NULL)
                return back()->with('flash_error', trans('admin.part_msgs.part_not_found'));
            
            $parts = array();
            $parts['date'] = $request->date;
            $parts['material_id'] = $request->material_name;
            $parts['uom'] = $request->uom;
            $parts['material_indent_note'] = $request->material_indent_note;
            $parts['min_no'] = $request->min_no;
            $parts['district_id'] = $request->issued_district;
            $parts['block_id'] = $request->issued_block;
            $parts['from_gp'] = $request->from_gp;
            $parts['to_gp'] = $request->to_gp;
            $parts['issued_qty'] = $request->issued_qty;
            $parts['consumed_qty'] = $request->consumed_qty;
            $parts['balance_at_location'] = $request->balance_at_location;
            $parts['remarks'] = $request->remarks;

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