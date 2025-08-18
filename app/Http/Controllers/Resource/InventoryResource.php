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

class InventoryResource extends Controller
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
        $inventorys = DB::table('inventory')
                        ->select('inventory.*', 'material_consume.name as service_type', 'material_consume.id as service_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'inventory.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'inventory.district_id', '=', 'districts.id')
                        // ->leftJoin('blocks', 'inventory.block_id', '=', 'blocks.id')
                        // ->leftJoin('providers', 'inventory.provider_id', '=', 'providers.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($inventorys);
        return view('admin.inventory.index',compact('inventorys','pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $districts = District::get();
        // $blocks = Block::get();
        // $providers = Provider::get();
        $materials = DB::table('material_consume')->get();
        return view('admin.inventory.create',compact('districts', 'materials'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $inventory = DB::table('inventory')
                        ->select('inventory.*', 'material_consume.name as service_type', 'material_consume.id as service_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'inventory.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'inventory.district_id', '=', 'districts.id')
                        // ->leftJoin('blocks', 'inventory.block_id', '=', 'blocks.id')
                        // ->leftJoin('providers', 'inventory.provider_id', '=', 'providers.id')
                        ->where('inventory.id', '=', $id)
                        ->first();
            // dd($inventory);
            if($inventory == NULL)
                return redirect()
                ->route('admin.inventory.index')
                ->with('flash_success', trans('admin.inventory_msgs.inventory_not_found'));

            return view('admin.inventory.show', compact('inventory'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.inventory.index')
                ->with('flash_success', trans('admin.inventory_msgs.inventory_not_found'));
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
            // 'part_name' => 'required|max:255',
            'district' => 'required',
            // 'block' => 'required',
            // 'dc_lr_no' => 'integer',
            // 'status' => 'in:in-stock,out-of-stock,missing',
            // 'received_qty' => 'required|integer',
            'comments' => 'max:500'         
        ]);

        try{
            // $material_id = 0;
            // $material = DB::table('material_consume')
            //         ->where('name', $request->part_name)
            //         ->first();
            // if($material)
            //     $material_id = $material->id;
            // else{
            //     $material_id = DB::table('material_consume')->insertGetId(
            //                         array('name' => $request->part_name)
            //                     );
            // }

            $inventory = array();
            $inventory['date'] = $request->date;
            $inventory['material_id'] = $request->material_id; 
            $inventory['uom'] = $request->uom;     
            $inventory['district_id'] = $request->district;        
            $inventory['opening_stock'] = $request->opening_stock;
            $inventory['inward'] = $request->inward;
            $inventory['issued_qty'] = $request->issued_qty; 
            $inventory['mrn_qty'] = $request->mrn_qty;      
            $inventory['closing_stock'] = $request->closing_stock;
            $inventory['remarks'] = $request->comments;

            $inventory = DB::table('inventory')->insert($inventory);

            return redirect()
                ->route('admin.inventory.index')
                ->with('flash_success', trans('admin.inventory_msgs.inventory_saved'));
        } 
        catch (Exception $e) {  
            dd($e->getMessage());
            return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));
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
            // $blocks = Block::get();
            // $providers = Provider::get();
            $materials = DB::table('material_consume')->get();
            $inventory = DB::table('inventory')->find($id);
            if($inventory == NULL)
                return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));
            return view('admin.inventory.edit',compact('districts', 'materials', 'inventory'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));
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
            // 'part_name' => 'required|max:255',
            'material_id' => 'required',
            'district' => 'required',
            // 'block' => 'required',
            // 'dc_lr_no' => 'integer',
            // 'status' => 'in:in-stock,out-of-stock,missing',
            // 'received_qty' => 'required|integer',
            'comments' => 'max:500'    
        ]);

        try {

            $inventory = DB::table('inventory')->find($id);
            if($inventory == NULL)
                return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));

            $inventory = array();
            $inventory['date'] = $request->date;
            $inventory['material_id'] = $request->material_id; 
            $inventory['uom'] = $request->uom;     
            $inventory['district_id'] = $request->district;        
            $inventory['opening_stock'] = $request->opening_stock;
            $inventory['inward'] = $request->inward;
            $inventory['issued_qty'] = $request->issued_qty; 
            $inventory['mrn_qty'] = $request->mrn_qty;     
            $inventory['closing_stock'] = $request->closing_stock;
            $inventory['remarks'] = $request->comments;

            $inventory = DB::table('inventory')->where('id',$id)->update($inventory);

            return redirect()->route('admin.inventory.index')->with('flash_success', trans('admin.inventory_msgs.inventory_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::table('inventory')->delete($id);
            return back()->with('message', trans('admin.inventory_msgs.inventory_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.inventory_msgs.inventory_not_found'));
        }
    }

}