<?php

namespace App\Http\Controllers\Dashboard;
use App\Bill;
use App\Order;
use App\Product;
use App\Bounced;
use App\User;
use App\Historydelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{

    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $users = User::all();
            $bounceds = Bounced::when($request->search, function($q) use ($request){
            return $q->where([['bill_number_o',$request->search]])
                     ->orWhere([['bill_number_b',$request->search]]);
            })->when($request->return_type, function ($q) use ($request) {
                return $q->where('return_type', $request->return_type);
            })->latest()->paginate(8);
            return view('dashboard.returns.index', compact('bounceds','users'));
        }else{
            $users = User::all();
            $bounceds = Bounced::where('add_by',Auth::id())->when($request->search, function($q) use ($request){
            return $q->where([['bill_number_o',$request->search],['add_by',Auth::id()]])
                     ->orWhere([['bill_number_b',$request->search],['add_by',Auth::id()]]);
            })->when($request->return_type, function ($q) use ($request) {
                return $q->where('return_type', $request->return_type);
            })->latest()->paginate(8);
            return view('dashboard.returns.index', compact('bounceds','users'));
        }
    }

    // public function create()
    // {
    //     $id = Auth::user()->id;
    //     if($id == 1 || $id == 2){
    //         $bills = Bill::orderBy('id', 'desc')->get();
    //         $orders = Order::orderBy('id', 'desc')->get();
    //         $products = Product::all();
    //         return view('dashboard.returns.create',compact('bills','orders','products'));
    //     }else{
    //         $bills = Bill::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
    //         $orders = Order::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
    //         $products = Product::all();
    //         return view('dashboard.returns.create',compact('bills','orders','products'));
    //     }
    // }
    
    public function purchaseInvoicesReturn($invoiceNumber)
    {
       $products = Product::where('purchase_invoice_number',$invoiceNumber)->orderBy('id', 'desc')->get();
       $productsC = Product::where('purchase_invoice_number',$invoiceNumber)->count();
       return view('dashboard.returns.purchaseInvoicesReturn',compact('invoiceNumber','products','productsC'));
    }
    
    public function orderReturn($orderNumber)
    {
        $order = Order::find($orderNumber);
        $products = $order->products;
        $productsC = $order->products->count();
        return view('dashboard.returns.orderReturn',compact('orderNumber','products','productsC'));
    }
    
    public function store(Request $request)
    {
       $rules = [
          'return_type' => 'required',
          'Varieties_number' => 'required',
          'products' => 'required|array|min:1',
          'products.0' => 'required',
          'quantity' => 'required|array|min:1',
          'quantity.0' => 'required',
        ];
        
        if($request->return_type == 1){
           $order = Order::find($request->bill_number_o);
           if($order){
               $bounceds_s = $order->bounceds_s + 1;
               $order->update(['bounceds_s' => $bounceds_s]);
               $request->validate($rules);
               $request_data = $request->all();
               $request_data['add_by'] = Auth::id();
               $request_data['products'] = array_filter($request->products);
               $request_data['quantity'] = array_filter($request->quantity);
               $request_data['serial_numbers'] = array_filter($request->serial_numbers);
               Bounced::create($request_data);
               session()->flash('success', __('site.added_successfully'));
               return redirect()->route('dashboard.returns.index');
           }
             return abort(404);
        }
        
        if($request->return_type == 2)
        {
          $bill = Bill::where('invoice_number',$request->bill_number_b)->get();
          if($bill){
              $bounceds_s = $bill[0]->bounceds_s + 1;
              Bill::where('invoice_number',$request->bill_number_b)->update(['bounceds_s'=>$bounceds_s]);
              $request->validate($rules);
              $request_data = $request->all();
              $request_data['add_by'] = Auth::id();
              $request_data['products'] = array_filter($request->products);
              $request_data['quantity'] = array_filter($request->quantity);
              $request_data['serial_numbers'] = array_filter($request->serial_numbers);
              Bounced::create($request_data);
              session()->flash('success', __('site.added_successfully'));
              return redirect()->route('dashboard.returns.index');
          }
             return abort(404);
        }
    }

    public function edit(Product $product){}

    public function update(Request $request){}

    public function destroy(Request $request)
    {
        if($request->return_type == 1){
           $order = Order::find($request->bill_number_o);
           $bounceds_s = $order->bounceds_s - 1;
           $order->update(['bounceds_s' => $bounceds_s]);
           
           $bounceds = Bounced::findOrFail($request->id);
           
           //Historydelete
           $historydeleteInput = ['type' => 6, 'type_id' => $request->id, 'info_one' => 'order', 'info_two' => $request->bill_number_o,'info_three' => json_encode($bounceds->products),'user_id' => Auth::id()];
           Historydelete::create($historydeleteInput);

           $bounceds->delete();
           session()->flash('success', __('site.deleted_successfully'));
           return redirect()->route('dashboard.returns.index');
        }
        
        if($request->return_type == 2)
        {
          $bill = Bill::where('invoice_number',$request->bill_number_b)->get();
          $bounceds_s = $bill[0]->bounceds_s - 1;
          Bill::where('invoice_number',$request->bill_number_b)->update(['bounceds_s'=>$bounceds_s]);
          
          $bounceds = Bounced::findOrFail($request->id);
          
          //Historydelete
          $historydeleteInput = ['type' => 6, 'type_id' => $request->id, 'info_one' => 'bill', 'info_two' => $request->bill_number_b, 'info_three' => json_encode($bounceds->products),'user_id' => Auth::id()];
          Historydelete::create($historydeleteInput);
           
          $bounceds->delete();
          session()->flash('success', __('site.deleted_successfully'));
          return redirect()->route('dashboard.returns.index');
        }
    }
    
    public function status($id)
    {
        try {
            $bounced = Bounced::find($id);
            if (!$bounced)
                return abort(404);

           $status = $bounced->status == 0 ? 1 : 0;

           $bounced->update(['status' =>$status, 'status_userId'=>Auth::id()]);
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.returns.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function returnsShow($id)
    {
        try {
            $bounced = Bounced::find($id);
            if (!$bounced)
                return abort(404);
                
            if($bounced->return_type == 1){
                $order = Order::find($bounced->bill_number_o);
                $bounceds_s = $order->bounceds_s;
                return view('dashboard.returns.returnsShow', compact('bounced','bounceds_s'));
            }
            if($bounced->return_type == 2){
                $bill = Bill::where('invoice_number',$bounced->bill_number_b)->get();
                $bounceds_s = $bill[0]->bounceds_s;
                return view('dashboard.returns.returnsShow', compact('bounced','bounceds_s'));
            }
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    

}