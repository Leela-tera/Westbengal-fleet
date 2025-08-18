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
                        ->select('return_note.*', 'material_consume.name as material_name', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'return_note.material_id', '=', 'material_consume.id')
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
        return view('admin.return_note.create',compact('materials'));
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
                        ->select('return_note.*', 'material_consume.name as material_type', 'material_consume.id as material_id')
                        ->leftJoin('material_consume', 'return_note.material_id', '=', 'material_consume.id')
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
            'material_name' => 'required|numeric',
            'received_loc' => 'required'       
        ]);

        try{
            $return_note = array();
            $return_note['material_id'] = $request->material_name;
            $return_note['supplier_name'] = $request->supplier_name;
            $return_note['isn'] = $request->issue_note_no;
            $return_note['issue_note_date'] = $request->issued_date;            
            $return_note['received_loc'] = $request->received_loc;
            $return_note['uom'] = $request->uom;
            $return_note['received_qty'] = $request->received_qty;
            $return_note['rejected_qty'] = $request->rejected_qty;
            $return_note['good_condition'] = $request->issued_qty_good_condition;
            $return_note['received_date'] = $request->received_date;
            $return_note['mrn'] = $request->mrn;

            $return_note = DB::table('return_note')->insert($return_note);

            return redirect()
                ->route('admin.return_note.index')
                ->with('flash_success', trans('admin.return_note_msgs.return_note_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
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
            $return_note = DB::table('return_note')->find($id);
            if($return_note == NULL)
                return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
            
            return view('admin.return_note.edit',compact('materials', 'return_note'));
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
            'material_name' => 'required|numeric',
            'received_loc' => 'required'       
        ]);

        try {

            $return_note = DB::table('return_note')->find($id);
            if($return_note == NULL)
                return back()->with('flash_error', trans('admin.return_note_msgs.return_note_not_found'));
            
            $return_note = array();
            $return_note['material_id'] = $request->material_name;
            $return_note['supplier_name'] = $request->supplier_name;
            $return_note['isn'] = $request->issue_note_no;
            $return_note['issue_note_date'] = $request->issued_date;            
            $return_note['received_loc'] = $request->received_loc;
            $return_note['uom'] = $request->uom;
            $return_note['received_qty'] = $request->received_qty;
            $return_note['rejected_qty'] = $request->rejected_qty;
            $return_note['good_condition'] = $request->issued_qty_good_condition;
            $return_note['received_date'] = $request->received_date;
            $return_note['mrn'] = $request->mrn;

            $return_note = DB::table('return_note')->where('id',$id)->update($return_note);

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