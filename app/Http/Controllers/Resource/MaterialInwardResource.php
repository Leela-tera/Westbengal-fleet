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

class MaterialInwardResource extends Controller
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
        $material_inwards = DB::table('material_inward')
                        ->select('material_inward.*', 'material_consume.name as material_name', 'material_consume.id as material_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'material_inward.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_inward.district_id', '=', 'districts.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($material_inwards);
        return view('admin.material_inward.index',compact('material_inwards','pagination'));
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
        return view('admin.material_inward.create',compact('materials', 'districts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $material_inward
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $material_inward = DB::table('material_inward')
                        ->select('material_inward.*', 'material_consume.name as material_type', 'material_consume.id as material_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'material_inward.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_inward.district_id', '=', 'districts.id')
                        ->where('material_inward.id', '=', $id)
                        ->first();
            // dd($return_note);
            if($material_inward == NULL)
                return redirect()
                ->route('admin.material_inward.index')
                ->with('flash_success', trans('admin.material_inward_msgs.material_inward_not_found'));

            return view('admin.material_inward.show', compact('material_inward'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.material_inward.index')
                ->with('flash_success', trans('admin.material_inward_msgs.material_inward_not_found'));
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

            //dd($request->all());
         if(($request->materials != ''))
	     {
                $msize= count($request->materials);
                //dd($msize);
	       for ($i = 0; $i < $msize; $i++)
	       {
			   
			   
			   $material_inward= array(
			         'date' => $request->get('date'),
					 'mrn' => $request->get('mrn'),
					 'material_id' => $request->get('materials')[$i],
					 'district_id' => $request->get('district_id'),
					 'uom' => $request->get('uom')[$i],
					 'location_name' => $request->get('location_name'),
					 'supplier_name' => $request->get('supplier_name'),
					 'invoice_no' => $request->get('invoice_no'),
					 'dc_no' => $request->get('dc_no'),
					 'waybil_no' => $request->get('waybil_no'),
					 'transport_name' => $request->get('transport_name'),
					 'lr_no' => $request->get('lr_no'),
					 'dc_qty' => $request->get('dc_qty')[$i],
					 'received_qty' => $request->get('received_qty'),
					 'rejacted_qty' => $request->get('rejacted_qty'),
					 'accepted_qty' => $request->get('accepted_qty')[$i],
					 'drum_no' => implode(', ', $request->get('drum_no')),
					 'no_of_equipment' => $request->get('no_of_equipment'),
					 'remarks' => $request->get('remarks')
				   );

           //echo "<pre>";      
           //print_r($material_inward);
           //exit();

		
            $material_inward = DB::table('material_inward')->insert($material_inward);

                      
             // Inserting Into Stock Statement
            $check_data = DB::table('inventory')
                                ->where('material_id',$request->get('materials')[$i])
                                ->where('district_id', '=', $request->get('district_id'))
                                ->orderBy('id', 'desc')
                                ->first();
            $opening_stock = (count($check_data) > 0)?$check_data->closing_stock:0;


                          $stock_stmnt= array(
			                 'date' => $request->get('date'),
					 'material_id' => $request->get('materials')[$i],
					 'district_id' => $request->get('district_id'),
					 'uom' => $request->get('uom')[$i],
					 'opening_stock' => $opening_stock,
					 'inward' => $request->get('accepted_qty')[$i],
                                         'closing_stock' => $request->get('accepted_qty')[$i],
				   );

                      DB::table('inventory')->insert($stock_stmnt);

 
               }

               if(($request->get('drum_no') != ''))
			 {
			   $size= count($request->get('drum_no'));
			   for ($i = 0; $i < $size; $i++)
			   {
				  $input= array(
			         'drum_no' => $request->get('drum_no')[$i]
				);
				$adddrum = DB::table('drum_master')->insert($input); 
			   }    
                          }       
              
			
	        }

            
            return redirect()
                ->route('admin.material_inward.index')
                ->with('flash_success', trans('admin.material_inward_msgs.material_inward_saved'));
        } 
        catch (Exception $e) {  
             dd($e->getMessage());
            return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
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
            $material_inward = DB::table('material_inward')->find($id);
            if($material_inward == NULL)
                return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
            
            return view('admin.material_inward.edit',compact('materials', 'material_inward', 'districts'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
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

            $material_inward = DB::table('material_inward')->find($id);
            if($material_inward == NULL)
                return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
            
            $material_inward = array();
            $material_inward['date'] = $request->date;            
            $material_inward['mrn'] = $request->mrn;
            $material_inward['material_id'] = $request->material_id;
            $material_inward['district_id'] = $request->district_id;
            $material_inward['uom'] = $request->uom;
            $material_inward['location_name'] = $request->location_name;
            $material_inward['supplier_name'] = $request->supplier_name;
            $material_inward['invoice_no'] = $request->invoice_no;
            $material_inward['dc_no'] = $request->dc_no;
            $material_inward['waybil_no'] = $request->waybil_no;
            $material_inward['lr_no'] = $request->lr_no;
            $material_inward['transport_name'] = $request->transport_name;
            $material_inward['dc_qty'] = $request->dc_qty;
            $material_inward['received_qty'] = $request->received_qty;
            $material_inward['rejacted_qty'] = $request->rejacted_qty;
            $material_inward['accepted_qty'] = $request->accepted_qty;
            $material_inward['drum_no'] = $request->drum_no;
            $material_inward['no_of_equipment'] = $request->no_of_equipment;
            $material_inward['remarks'] = $request->remarks;

            $material_inward = DB::table('material_inward')->where('id',$id)->update($material_inward);

            // Inserting Into Stock Statement
            $stock_stmnt = array();
            $stock_stmnt['date'] = $request->date;
            $stock_stmnt['material_id'] = $request->material_id; 
            $stock_stmnt['uom'] = $request->uom;     
            $stock_stmnt['district_id'] = $request->district_id;        
            $stock_stmnt['opening_stock'] = 0;
            $stock_stmnt['inward'] = $request->accepted_qty;           
            $stock_stmnt['closing_stock'] = $request->accepted_qty;

            DB::table('inventory')
                ->where('material_id',$request->material_id)
                ->where('district_id', '=', $request->district_id)
                ->whereDate('date', '=', $request->date)
                ->update($stock_stmnt);

            return redirect()->route('admin.material_inward.index')->with('flash_success', trans('admin.material_inward_msgs.material_inward_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
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
            DB::table('material_inward')->delete($id);
            return back()->with('message', trans('admin.material_inward_msgs.material_inward_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.material_inward_msgs.material_inward_not_found'));
        }
    }

}