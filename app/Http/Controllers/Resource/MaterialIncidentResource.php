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
use App\District;
use Mail;
use DateTime;

class MaterialIncidentResource extends Controller
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
        $material_incidents = DB::table('material_incident')
                        ->select('material_incident.*', 'material_consume.name as material_name', 'material_consume.id as material_id', 'districts.name as district_name', 'districts.id as districts_id','blocks.name as block_name', 'blocks.id as block_id','from_gp_list.gp_name as from_gp_name','to_gp_list.gp_name as to_gp_name')
                        ->leftJoin('material_consume', 'material_incident.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_incident.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'material_incident.block_id', '=', 'blocks.id')
                        ->leftJoin('gp_list as from_gp_list', 'material_incident.from_gp', '=', 'from_gp_list.id')
                        ->leftJoin('gp_list as to_gp_list', 'material_incident.to_gp', '=', 'to_gp_list.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($material_incidents);
        return view('admin.material_incident.index',compact('material_incidents','pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $materials = DB::table('material_consume')->get();
        $districts = District::get();
        $blocks =  DB::table('blocks')->get();
        $fromgp_list =  DB::table('gp_list')->get();
        $togp_list =  DB::table('gp_list')->get();

        return view('admin.material_incident.create',compact('materials', 'districts','blocks','fromgp_list','togp_list'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $return_note
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $material_incident = DB::table('material_incident')
                        ->select('material_incident.*', 'material_consume.name as material_type', 'material_consume.id as material_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'material_incident.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_incident.district_id', '=', 'districts.id')
                        ->where('material_incident.id', '=', $id)
                        ->first();
            // dd($return_note);
            if($material_incident == NULL)
                return redirect()
                ->route('admin.material_incident.index')
                ->with('flash_success', trans('admin.material_incident_msgs.material_incident_not_found'));

            return view('admin.material_incident.show', compact('material_incident'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.material_incident.index')
                ->with('flash_success', trans('admin.material_incident_msgs.material_incident_not_found'));
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
            'materials' => 'required'
        ]);

        try{

            if(($request->materials != ''))
	     {
                $msize= count($request->materials);
                //dd($msize);
	       for ($i = 0; $i < $msize; $i++)
	       {
			   
			   
			   $material_incident = array(
			         'date' => $request->get('date'),
					 'uom' => $request->get('uom')[$i],
					 'material_id' => $request->get('materials')[$i],
                                         'material_indent_note' => $request->get('material_indent_note'),
					 'district_id' => $request->get('district_id'),
                                         'block_id' => $request->get('block_id'),
					 'from_gp' => $request->get('from_gp'),
					 'to_gp' => $request->get('to_gp'),
					 'required_qty' => $request->get('required_qty')[$i],
					 'remarks' => $request->get('remarks'),
				   );
		
            $material_incident= DB::table('material_incident')->insert($material_incident);

                }

             }

            return redirect()
                ->route('admin.material_incident.index')
                ->with('flash_success', trans('admin.material_incident_msgs.material_incident_saved'));
        } 
        catch (Exception $e) {  
             dd($e->getMessage());
            return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $materials = DB::table('material_consume')->get();
            $districts = District::get();
            $blocks =  DB::table('blocks')->get();
            $fromgp_list =  DB::table('gp_list')->get();
            $togp_list =  DB::table('gp_list')->get();

            $material_incident = DB::table('material_incident')->find($id);
            if($material_incident == NULL)
                return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
            
            return view('admin.material_incident.edit',compact('materials', 'material_incident', 'districts','blocks','fromgp_list','togp_list'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'material_id' => 'required|numeric'
        ]);

        try {

            $material_incident = DB::table('material_incident')->find($id);
            if($material_incident == NULL)
                return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
            
            $material_incident = array();
            $material_incident['date'] = $request->date;            
            $material_incident['uom'] = $request->uom;
            $material_incident['material_id'] = $request->material_id;
            $material_incident['material_indent_note'] = $request->material_indent_note;
            $material_incident['district_id'] = $request->district_id;
            $material_incident['block_id'] = $request->block_id;
            $material_incident['from_gp'] = $request->from_gp;
            $material_incident['to_gp'] = $request->to_gp;
            $material_incident['required_qty'] = $request->required_qty;
            $material_incident['remarks'] = $request->remarks;

            $material_incident = DB::table('material_incident')->where('id',$id)->update($material_incident);

            return redirect()->route('admin.material_incident.index')->with('flash_success', trans('admin.material_incident_msgs.material_incident_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::table('material_incident')->delete($id);
            return back()->with('message', trans('admin.material_incident_msgs.material_incident_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.material_incident_msgs.material_incident_not_found'));
        }
    }

}