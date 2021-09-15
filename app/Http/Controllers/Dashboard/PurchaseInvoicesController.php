<?php

namespace App\Http\Controllers\Dashboard;

use App\Bill;
use App\User;
use App\Product;
use App\Backorder;
use App\Historydelete;
use Carbon\Carbon; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class PurchaseInvoicesController extends Controller
{

    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            
            if($request->serial)
            {
              $purchase_invoice_number = Product::select('purchase_invoice_number')->where([['serial_numbers', 'like', '%' . $request->serial . '%']])->first();
              $serial_numbers = Product::select('serial_numbers')->where([['serial_numbers', 'like', '%' . $request->serial . '%']])->first();
              $array_ser = '';
              if($serial_numbers != null){
                 $array_ser = array_search($request->serial,$serial_numbers->serial_numbers);
              }
              if($array_ser)
              {
                $users = User::all();
                $bills = Bill::where([['invoice_number', 'like', '%' . $purchase_invoice_number->purchase_invoice_number . '%']])->latest()->paginate(8);
                return view('dashboard.purchaseInvoices.index', compact('bills','users'));
              }else
              {
                $users = User::all();
                $request->search = '9999999999999999999999';
                $bills = Bill::when($request->search, function($q) use ($request){
                    return $q->where([['supplier_name', 'like', '%' . $request->search . '%']])
                    ->orWhere([['invoice_number', 'like', '%' . $request->search . '%']]);
                })->latest()->paginate(8);
                $request->search = '';
                return view('dashboard.purchaseInvoices.index', compact('bills','users'));
              }
            }
            
            $users = User::all();
            $bills = Bill::when($request->search, function($q) use ($request){
                return $q->where([['supplier_name', 'like', '%' . $request->search . '%']])
                ->orWhere([['invoice_number', 'like', '%' . $request->search . '%']]);
            })->latest()->paginate(8);
            return view('dashboard.purchaseInvoices.index', compact('bills','users'));
            
        }else{
            
            if($request->serial)
            {
              $purchase_invoice_number = Product::select('purchase_invoice_number')->where([['serial_numbers', 'like', '%' . $request->serial . '%'],['add_by',Auth::id()]])->first();
              $serial_numbers = Product::select('serial_numbers')->where([['serial_numbers', 'like', '%' . $request->serial . '%'],['add_by',Auth::id()]])->first();
              $array_ser = '';
              if($serial_numbers != null){
                 $array_ser = array_search($request->serial,$serial_numbers->serial_numbers);
              }
              if($array_ser)
              {
                $users = User::all();
                $bills = Bill::where([['invoice_number', 'like', '%' . $purchase_invoice_number->purchase_invoice_number . '%']])->latest()->paginate(8);
                return view('dashboard.purchaseInvoices.index', compact('bills','users'));
              }else
              {
                $users = User::all();
                $request->search = '9999999999999999999999';
                $bills = Bill::where('add_by',Auth::id())->when($request->search, function($q) use ($request){
                return $q->where([['supplier_name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['invoice_number', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
                })->latest()->paginate(8);
                $request->search = '';
                return view('dashboard.purchaseInvoices.index', compact('bills','users'));
              }
            }
            
            $users = User::all();
            $bills = Bill::where('add_by',Auth::id())->when($request->search, function($q) use ($request){
                return $q->where([['supplier_name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['invoice_number', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
            })->latest()->paginate(8);
            return view('dashboard.purchaseInvoices.index', compact('bills','users'));
        }
    }


    public function create()
    {
        return view('dashboard.purchaseInvoices.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'invoice_number' =>  'required',
            'supplier_address' => 'required',
            'supplier_phone' => 'required|array|min:1',
            'supplier_phone.0' => 'required',
            'payment_method' => 'required',
            'total' => 'required',
        ]);
        
        if($request->payment_method == 2){
            $request->validate([
                'number_of_days' => 'required',
            ]);
        }
        if($request->payment_method == 3){
            $request->validate([
                'partially_price' => 'required',
                'the_rest_in_through' =>  'required',
            ]);
        }
        
        $request_data = $request->all();
    
        $request_data['supplier_phone'] = array_filter($request->supplier_phone);
        $request_data['add_by'] = Auth::id();
        
        if ($request->image) {
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(base_path('uploads/purchaseInvoices_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        $bill = Bill::create($request_data);
        
        if($request->payment_method == 2){
             $cDate = Carbon::parse($bill->date)->format('d.m.Y');
             $date = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate);
             $daysToAdd = $request->number_of_days;
             $date = $date->addDays($daysToAdd);
             $inputs = ['type' => 2,'number' => $bill->invoice_number,'date'=>$date->format('Y-m-d')];
             Backorder::create($inputs);
        };
        if($request->payment_method == 3){
            $cDate2 = Carbon::parse($bill->date)->format('d.m.Y');
            $date2 = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate2);
            $daysToAdd2 = $request->the_rest_in_through;
            $date2 = $date2->addDays($daysToAdd2);
            $inputs = ['type' => 2,'number' => $bill->invoice_number,'date'=>$date2->format('Y-m-d')];
            Backorder::create($inputs);
        };
        
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.products.index');
    }


    public function destroy(Request $request)
    {
        $products = Product::where('purchase_invoice_number',$request->invoice_number)->count();
        if($products == 0){
            $bill = Bill::findOrFail($request->id);
            
            if($bill->payment_method == 2 || $bill->payment_method == 3){
               Backorder::where('type', 2)->where('number', $bill->invoice_number)->delete();
            };
        
            if ($bill->image != 'default.png') {
                Storage::disk('public_uploads')->delete('/purchaseInvoices_images/' . $bill->image);
            }
            
            //Historydelete
            $historydeleteInput = ['type' => 2, 'type_id' => $bill->id, 'info_one' => $bill->invoice_number, 'info_two' => $bill->supplier_name, 'info_three' => $bill->total, 'user_id' => Auth::id()];
            Historydelete::create($historydeleteInput);

            $bill->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.purchaseInvoices.index');
        }else{
            return redirect()->route('dashboard.purchaseInvoices.index');
        }
    }
    
    public function edTotal($orderId)
    {
        try {
            $bill = Bill::find($orderId);
            if (!$bill)
                return abort(404);
                
         return view('dashboard.purchaseInvoices.edTotal', compact('bill'));

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function postEdTotal(Request $request)
    {
        try {
            $bill = Bill::find($request->billId);
            if (!$bill)
                return abort(404);
                
           $bill->update([
               'supplier_name' =>$request->supplier_name,
               'sales_officer' =>$request->sales_officer,
               'supplier_address' =>$request->supplier_address,     
               'supplier_phone' => array_filter($request->supplier_phone),
               'payment_method' =>$request->payment_method,
               'number_of_days' =>$request->number_of_days,
               'partially_price' =>$request->partially_price,
               'the_rest_in_through' =>$request->the_rest_in_through,
               'date' =>$request->date,
               'update_by' => Auth::id()
           ]);
           
           if($request->payment_method == 2){
                $cDate =  Carbon::parse($bill->date)->format('d.m.Y');
                $date = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate);
                $daysToAdd = $request->number_of_days;
                $date = $date->addDays($daysToAdd);
                Backorder::where('type', 2)->where('number', $bill->invoice_number)->update(['date'=>$date->format('Y-m-d')]);
            };
            if($request->payment_method == 3){
                $cDate2 =  Carbon::parse($bill->date)->format('d.m.Y');
                $date2 = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate2);
                $daysToAdd2 = $request->the_rest_in_through;
                $date2 = $date2->addDays($daysToAdd2);
                Backorder::where('type', 2)->where('number', $bill->invoice_number)->update(['date'=>$date2->format('Y-m-d')]);
            };
           
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.purchaseInvoices.index');

        } catch (\Exception $ex) {
            dd($ex);
            return abort(404);
        }
    }
}
