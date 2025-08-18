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

class ReturnNoteResource extends Controller
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
        $return_notes = DB::table('return_note')
                        ->select('return_note.*', 'material_consume.name as material_name', 'material_consume.id as material_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'return_note.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'return_note.district_id', '=', 'districts.id')
                        ->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($return_notes);
        return view('admin.return_note.index',compact('return_notes','pagination'));
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
        return view('admin.return_note.create',compact('materials', 'districts'));
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
            $return_note = DB::table('return_note')
                        ->select('return_note.*', 'material_consume.name as material_name', 'material_consume.id as material_id', 'districts.name as districts_name', 'districts.id as districts_id')
                        ->leftJoin('material_consume', 'return_note.material_id', '=', 'material_consume.id')
                        ->leftJoin('districts', 'return_note.district_id', '=', 'districts.id')
                        ->where('return_note.id', '=', $id)
                        ->first();
            // dd($return_note);
            if($return_note == NULL)
                return redirect()
                ->route('admin.return_note.index')
                ->with('flash_success', trans('admin.return_note_msgs.return_note_not_found'));

            return view('admin.return_note.show', compact('return_note'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.return_note.index')
                ->with('flash_success', trans('admin.return_note_msgs.return_note_not_found'));
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
	              $return_note= array(
			         'date' => $request->get('date'),
					 'material_id' => $request->get('materials')[$i],
					 'uom' => $request->get('uom')[$i],
					 'material_indent_note' => $request->get('material_indent_note'),
					 'mrn' => $request->get('mrn'),
					 'district_id' => $request->get('districts_name'),
					 //'block_id' => $request->get('block_id'),
					 'mr_person' => $request->get('mr_person'),
					 'issue_note_no' => $request->get('issue_note_no'),
					 'issued_qty' => $request->get('issued_qty')[$i],
					 'returned_qty' => $request->get('returned_qty')[$i],
					 'balance_at_location' => $request->get('balance_at_location')[$i],
					 'remarks' => $request->get('remarks')
				   );

                            //dd($parts);
		
                          $return_note = DB::table('return_note')->insert($return_note);

                            $stock_stmnt= array(
                              'mrn_qty' => $request->get('returned_qty')[$i],
			   );
                            
                           DB::table('inventory')
                          ->where('material_id',$request->get('materials')[$i])
                          ->where('district_id', '=', $request->get('districts_name'))
                          ->whereDate('date', '=', $request->get('date'))
                          ->update($stock_stmnt);

			}
			
			}

         
            // Updating The Stock Statement
            //$stock_stmnt = array();
            //$stock_stmnt['mrn_qty'] = $request->returned_qty;

            //DB::table('inventory')
              //  ->where('material_id',$request->material_name)
                //->where('district_id', '=', $request->districts_name)
                //->whereDate('date', '=', $request->date)
                //->update($stock_stmnt);

            return redirect()
                ->route('admin.return_note.index')
                ->with('flash_success', trans('admin.return_note_msgs.return_note_saved'));
        } 
        catch (Exception $e) {  
             dd($e->getMessage());
            return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
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
            $return_note = DB::table('return_note')->find($id);
            if($return_note == NULL)
                return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
            
            return view('admin.return_note.edit',compact('materials', 'return_note', 'districts'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
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
            'material_name' => 'required|numeric'
        ]);

        try {

            $return_note = DB::table('return_note')->find($id);
            if($return_note == NULL)
                return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
            
            $return_note = array();
            $return_note['date'] = $request->date;            
            $return_note['mrn'] = $request->mrn;
            $return_note['material_id'] = $request->material_name;
            $return_note['district_id'] = $request->districts_name;
            $return_note['uom'] = $request->uom;
            $return_note['mr_person'] = $request->mr_person;
            $return_note['material_indent_note'] = $request->material_indent_note;
            $return_note['issue_note_no'] = $request->issue_note_no;
            $return_note['issued_qty'] = $request->issued_qty;
            $return_note['consumed_qty'] = $request->consumed_qty;
            $return_note['balance_at_location'] = $request->balance_at_location;
            $return_note['returned_qty'] = $request->returned_qty;
            $return_note['remarks'] = $request->remarks;

            $return_note = DB::table('return_note')->where('id',$id)->update($return_note);

            // Updating The Stock Statement
            $stock_stmnt = array();
            $stock_stmnt['mrn_qty'] = $request->returned_qty;

            DB::table('inventory')
                ->where('material_id',$request->material_name)
                ->where('district_id', '=', $request->districts_name)
                ->whereDate('date', '=', $request->date)
                ->update($stock_stmnt);

            return redirect()->route('admin.return_note.index')->with('flash_success', trans('admin.return_note_msgs.return_note_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
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
            DB::table('return_note')->delete($id);
            return back()->with('message', trans('admin.return_note_msgs.return_note_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
        }
    }

}