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

class MaterialIssueResource extends Controller
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
        $materials_issue = DB::table('material_issue')
                        ->select('material_issue.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'material_issue.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_issue.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'material_issue.block_id', '=', 'blocks.id')
                        //->leftJoin('providers', 'material_issue.provider_id', '=', 'providers.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($materials_issue);
        return view('admin.material_issue.index',compact('materials_issue','pagination'));
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

         return view('admin.material_issue.create',compact('districts', 'blocks', 'materials', 'drums'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $material_issue
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $material_issue = DB::table('material_issue')
                        ->select('material_issue.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'material_issue.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'material_issue.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'material_issue.block_id', '=', 'blocks.id')
                        // ->leftJoin('providers', 'material_issue.provider_id', '=', 'providers.id')
                        ->where('material_issue.id', '=', $id)
                        ->first();
            // dd($material_issue);
            if($material_issue == NULL)
                return redirect()
                ->route('admin.material_issue.index')
                ->with('flash_success', trans('admin.mt_issue_msgs.mt_issue_not_found'));

            return view('admin.material_issue.show', compact('material_issue'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.material_issue.index')
                ->with('flash_success', trans('admin.mt_issue_msgs.mt_issue_not_found'));
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
            //'issued_district' => 'required',
            //'issued_block' => 'required',
            'remarks' => 'max:500'         
        ]);

        try{

            
           if(($request->materials != ''))
	     {
                $msize= count($request->materials);
                //dd($msize);
	          for ($i = 0; $i < $msize; $i++)
	         { 
	              $material_issue= array(
			         'date' => $request->get('date'),
					 'material_id' => $request->get('materials')[$i],
					 'uom' => $request->get('uom')[$i],
					 'material_indent_note' => $request->get('material_indent_note'),
					 'min_no' => $request->get('min_no'),
					 'district_id' => $request->get('issued_district'),
					 'block_id' => $request->get('issued_block'),
					 'receiving_person_name' => $request->get('receiving_person_name'),
					 'drum_no' => $request->get('drum_no'),
					 'material_indent_qty' => $request->get('material_indent_qty')[$i],
					 'issued_qty' => $request->get('issued_qty')[$i],
					 'si_no_equipment' => $request->get('si_no_equipment'),
					 'remarks' => $request->get('remarks')
				   );

                            //dd($parts);
		
                          $material_issue = DB::table('material_issue')->insert($material_issue);

                            $stock_stmnt= array(
                              'issued_qty' => $request->get('issued_qty')[$i],
			   );
                            
                           DB::table('inventory')
                          ->where('material_id',$request->get('materials')[$i])
                          ->where('district_id', '=', $request->get('issued_district'))
                          ->whereDate('date', '=', $request->get('date'))
                          ->update($stock_stmnt);

			}
			
			}

           
            return redirect()
                ->route('admin.material_issue.index')
                ->with('flash_success', trans('admin.mt_issue_msgs.mt_issue_saved'));
        } 
        catch (Exception $e) {  
            dd($e->getMessage());
            return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
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
            $material_issue = DB::table('material_issue')->find($id);
            $drums = DB::table('drum_master')->get();

            if($material_issue == NULL)
                return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
            
            return view('admin.material_issue.edit',compact('districts', 'blocks', 'materials', 'material_issue','drums'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
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

            $material_issue_exists = DB::table('material_issue')->find($id);
            if($material_issue_exists == NULL)
                return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
            
            $material_issue = array();
            $material_issue['date'] = $request->date;
            $material_issue['material_id'] = $request->material_name;
            $material_issue['uom'] = $request->uom;
            $material_issue['material_indent_note'] = $request->material_indent_note;
            $material_issue['min_no'] = $request->min_no;
            $material_issue['district_id'] = $request->issued_district;
            $material_issue['block_id'] = $request->issued_block;
            $material_issue['material_indent_qty'] = $request->material_indent_qty;
            $material_issue['issued_qty'] = $request->issued_qty;
            $material_issue['drum_no'] = $request->drum_no;
            $material_issue['si_no_equipment'] = $request->si_no_equipment;
            $material_issue['receiving_person_name'] = $request->receiving_person_name;
            $material_issue['remarks'] = $request->remarks;

            $material_issue = DB::table('material_issue')->where('id',$id)->update($material_issue);

            // Updating The Stock Statement
            $stock_stmnt = array();
            $stock_stmnt['issued_qty'] = $request->issued_qty;

            DB::table('inventory')
                ->where('material_id',$request->material_name)
                ->where('district_id', '=', $request->issued_district)
                ->whereDate('date', '=', $request->date)
                ->update($stock_stmnt);

            return redirect()->route('admin.material_issue.index')->with('flash_success', trans('admin.mt_issue_msgs.mt_issue_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
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
            DB::table('material_issue')->delete($id);
            return back()->with('message', trans('admin.mt_issue_msgs.mt_issue_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.mt_issue_msgs.mt_issue_not_found'));
        }
    }

}