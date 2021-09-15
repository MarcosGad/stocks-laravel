<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use App\Backorder;
use App\Historydelete;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2 || $id == 3){
            
            $ordersDate = Order::select('id', DB::raw('DATE(created_at) as date'))
             ->orderBy('date', 'desc')
             ->get()
             ->groupBy('date');
             
            $ordersDateO = Order::select('id', DB::raw('DATE(date) as dateO'))
             ->where('date','!=',null)
             ->orderBy('dateO', 'desc')
             ->get()
             ->groupBy('dateO');
             
            $orders = Order::whereHas('client', function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
            })->when($request->created_at, function ($q) use ($request) {
                return $q->where('created_at', 'like', '%' . $request->created_at . '%');
            })->when($request->dateO, function ($q) use ($request) {
                return $q->where('date', 'like', '%' . $request->dateO . '%');
            })->when($request->id, function ($q) use ($request) {
                return $q->where('id',$request->id);
            })->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })->orderBy('date', 'desc')->paginate(6);
            $users = User::all();
            $ordersIds = Order::orderBy('id', 'desc')->get();
            return view('dashboard.orders.index', compact('orders','users','ordersDate','ordersIds','ordersDateO'));
        }else{
            
             $ordersDate = Order::where('user_id',Auth::id())->select('id', DB::raw('DATE(created_at) as date'))
              ->orderBy('date', 'desc')
              ->get()
              ->groupBy('date');
              
            $ordersDateO = Order::where('user_id',Auth::id())->select('id', DB::raw('DATE(date) as dateO'))
             ->where('date','!=',null)
             ->orderBy('dateO', 'desc')
             ->get()
             ->groupBy('dateO');
            
            $orders = Order::where('user_id',Auth::id())->whereHas('client', function ($q) use ($request) {
               return $q->where([['name', 'like', '%' . $request->search . '%'],['user_id',Auth::id()]]);
            })->when($request->created_at, function ($q) use ($request) {
                return $q->where([['created_at', 'like', '%' . $request->created_at . '%'],['user_id',Auth::id()]]);
            })->when($request->dateO, function ($q) use ($request) {
                return $q->where([['date', 'like', '%' . $request->dateO . '%'],['user_id',Auth::id()]]);
            })->when($request->id, function ($q) use ($request) {
                return $q->where([['id',$request->id],['user_id',Auth::id()]]);
            })->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })->orderBy('date', 'desc')->paginate(6);
            $users = User::all();
            $ordersIds = Order::where('user_id',Auth::id())->orderBy('id', 'desc')->get();
            return view('dashboard.orders.index', compact('orders','users','ordersDate','ordersIds','ordersDateO'));
        }
        
    }

    public function products(Order $order)
    {
        $products = $order->products;
        return view('dashboard.orders._products', compact('order', 'products'));
    }

    public function destroy(Order $order)
    {
        foreach ($order->products as $product) {
            $product->update([
                'stock' => $product->stock + $product->pivot->quantity
            ]);
        }
        
        DB::table('product_order')->select('*')->where('order_id', $order->id)->delete();
        DB::table('last_price_product_order')->select('*')->where('order_id', $order->id)->delete();
        DB::table('sumorders')->select('*')->where('order_id', $order->id)->where('type', 1)->delete();
        
        if($order->payment_type == 2 || $order->payment_type == 3){
           Backorder::where('type', 1)->where('number', $order->id)->delete();
        };
        
        //Historydelete
        $historydeleteInput = ['type' => 1, 'type_id' => $order->id, 'info_one' => $order->client->name, 'info_two' => $order->payment_type, 'info_three' => $order->total_price + $order->transport - $order->discount, 'user_id' => Auth::id()];
        Historydelete::create($historydeleteInput);
        
        $order->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    public function status($id)
    {
        try {
            $order = Order::find($id);
            if (!$order)
                return abort(404);

           $status = $order->status == 0 ? 1 : 0;

           $order->update(['status' =>$status, 'status_userId'=>Auth::id()]);
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.orders.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function attainment($id)
    {
        try {
            $order = Order::find($id);
            if (!$order)
                return abort(404);

          $status = $order->attainment == 0 ? 1 : 0;

          $order->update(['attainment' =>$status]);
          session()->flash('success', __('site.updated_successfully'));
          return redirect()->route('dashboard.orders.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function transfersOrder($userId,$orderId)
    {
        $users = User::all();
        return view('dashboard.transfers.order', compact('users','userId','orderId'));
    }
    
    public function postTransfersOrder(Request $request)
    {
       try {
            $order = Order::find($request->orderId);
            if (!$order)
                return abort(404);

           $order->update(['user_id' =>$request->userId, 'trans'=>Auth::id()]);
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.orders.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function discount($orderId)
    {
        try {
            $order = Order::find($orderId);
            if (!$order)
                return abort(404);
                
         return view('dashboard.discount.order', compact('order'));

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function postDiscount(Request $request)
    {
        try {
            $order = Order::find($request->orderId);
            if (!$order)
                return abort(404);

           $order->update(['transport' =>$request->transport,'discount' =>$request->discount,'notes' =>$request->notes]);
           
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.orders.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
}
