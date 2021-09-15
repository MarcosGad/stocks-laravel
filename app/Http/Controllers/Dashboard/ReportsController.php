<?php

namespace App\Http\Controllers\Dashboard;
use App\Store;
use App\Product;
use App\Category;
use App\Backorder;
use App\Representative;
use App\Shippingmethod;
use App\Bill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use App\Order;
use App\User;
use App\Historydelete;
use Carbon\Carbon; 
use App\Sumorder;
use App\Client;
use App\Bounced;
use DB;

class ReportsController extends Controller
{

    public function store(Request $request)
    {
        $categories = Category::all();
        $stores = Store::all();
        $products = Product::when($request->search, function ($q) use ($request) {
            return $q->whereTranslationLike('name', '%' . $request->search . '%')
                     ->orWhere([['purchase_invoice_number', 'like', '%' . $request->search . '%']])
                     ->orWhere([['productCode', 'like', '%' . $request->search . '%']]);
        })->when($request->category_id, function ($q) use ($request) {
            return $q->where('category_id', $request->category_id);
        })->when($request->stock, function ($q) use ($request) {
            return $q->where('stock','>=',$request->stock);
        })->when($request->store_id, function ($q) use ($request) {
            return $q->where('store_id', $request->store_id);
        })->latest()->get();
        return view('dashboard.reports.store', compact('categories', 'products','stores'));
    }
    
    public function historyproduct($id)  
    {
        try {
            $product = Product::find($id);
            if (!$product)
                return abort(404);

            $lastPrices = DB::table('last_price_product_order')->select('*')->where('product_id', $id)->get();
            $productQs = DB::table('product_order')->select('*')->where('product_id', $id)->get();
            $orders = Order::all();
            $stores = Store::all();
            return view('dashboard.reports.historyproduct', compact('product','lastPrices','productQs','orders','stores'));

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function clients(Request $request)
    {
            $ordersDate = Order::select('id', DB::raw('DATE(created_at) as date'))
              ->orderBy('date', 'desc')
              ->get()
              ->groupBy('date');
            
            $orders = Order::whereHas('client', function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%');
            })->when($request->date, function ($q) use ($request) {
                return $q->where('created_at', 'like', '%' . $request->date . '%');
            })->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })->when($request->id, function ($q) use ($request) {
                return $q->where('id',$request->id);
            })->when($request->representative_id, function ($q) use ($request) {
                return $q->where('representative_id', $request->representative_id);
            })->when($request->shipping, function ($q) use ($request) {
                return $q->where('shipping', $request->shipping);
            })->orderBy('id', 'desc')->get();
            $users = User::all();
            $ordersIds = Order::orderBy('id', 'desc')->get();
            $representatives = Representative::all();
            $shippingmethods = Shippingmethod::all();
            return view('dashboard.reports.clients', compact('orders','users','ordersDate','representatives','ordersIds','shippingmethods'));
    }
    
    public function historyclient($id)  
    {
        try {
            $client = Client::find($id);
            if (!$client)
                return abort(404);
                
            $orders = Order::where('client_id', $id)->get();
            $users = User::all();
            return view('dashboard.reports.historyclient', compact('client','orders','users'));
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function historyDelegate($id)  
    {
        try {
            $representative = Representative::find($id);
            if (!$representative)
                return abort(404);
                
            $orders = Order::where('representative_id', $id)->get();
            $users = User::all();
            return view('dashboard.reports.historyDelegate', compact('representative','orders','users'));
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function historyShippingmethod($id)  
    {
        try {
            $shippingmethod = Shippingmethod::find($id);
            if (!$shippingmethod)
                return abort(404);
               
            $orders = Order::where('shipping', $id)->get();
            $users = User::all();
            return view('dashboard.reports.historyShippingmethod', compact('shippingmethod','orders','users'));
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
            
    public function reportsPurchaseInvoices(Request $request)
    {
        $billsDate = Bill::select('id', DB::raw('DATE(created_at) as date'))
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');
        
        $bills = Bill::when($request->search, function($q) use ($request){
        return $q->where([['supplier_name', 'like', '%' . $request->search . '%']])
            ->orWhere([['invoice_number', 'like', '%' . $request->search . '%']]);
        })->when($request->date, function ($q) use ($request) {
            return $q->where('created_at', 'like', '%' . $request->date . '%');
        })->when($request->add_by, function ($q) use ($request) {
            return $q->where('add_by', $request->add_by);
        })->orderBy('id', 'desc')->get();
        $users = User::all();
        
        return view('dashboard.reports.purchaseInvoices', compact('bills','users','billsDate'));
    }
    
    public function reportsPurchaseInvoicesShow($id,$invoiceNumber)
    {
        try {
            $bill = Bill::find($id);
            if (!$bill)
                return abort(404);
                
            $products = Product::where('purchase_invoice_number',$invoiceNumber)->orderBy('id', 'desc')->get();
            $productsAll = Product::all();
          return view('dashboard.reports.purchaseInvoicesShow', compact('bill','products','productsAll'));
          
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function paymentNotices()
    {
      $backorderOs = Backorder::where('date','<=',Carbon::today()->format('Y-m-d'))->where('status',0)->where('type',1)->orderBy('date', 'desc')->get();
      $backorderBs = Backorder::where('date','<=',Carbon::today()->format('Y-m-d'))->where('status',0)->where('type',2)->orderBy('date', 'desc')->get();
      return view('dashboard.reports.paymentNotices', compact('backorderOs','backorderBs'));
    }
    
    public function paymentNoticesStatus($id)
    {
        try {
            $backorder = Backorder::find($id);
            if (!$backorder)
                return abort(404);

           $status = $backorder->status == 0 ? 1 : 0;
           $backorder->update(['status' =>$status]);
           
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.reports.paymentNotices');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function paymentNoticesAttainment($id,$backorderId)
    {
        try {
            $order = Order::find($id);
            if (!$order)
                return abort(404);

          $status = $order->attainment == 0 ? 1 : 0;

          $order->update(['attainment' =>$status]);
          
          $this->paymentNoticesStatus($backorderId);
                  
          session()->flash('success', __('site.updated_successfully'));
          return redirect()->route('dashboard.reports.paymentNotices');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function profits()
    {
        $day = Sumorder::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total_price'),
            DB::raw('SUM(discount) as discount'),
            DB::raw('SUM(transport) as transport'),
            DB::raw('created_at as date')
        )->where('type',1)->groupBy('day')->orderBy('date', 'desc')->get();
        
        $tomonth = Carbon::today()->format('m');
        $month = Sumorder::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total_price'),
            DB::raw('SUM(discount) as discount'),
            DB::raw('SUM(transport) as transport'),
            DB::raw('created_at as date')
        )->where('type',1)->groupBy('month')->orderBy('date', 'desc')->get();
        /**********************************************************************/
        $dayIn = Bill::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('created_at as date')
        )->groupBy('day')->orderBy('date', 'desc')->get();
        
        $monthIn = Bill::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        return view('dashboard.reports.profits', compact('month','day','monthIn','dayIn','tomonth'));
    }
    
    public function historyUser($id)  
    {
        try {
            $user = User::find($id);
            if (!$user)
                return abort(404);
                
            $month = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as total_price'),
                DB::raw('SUM(discount) as discount'),
                DB::raw('SUM(transport) as transport'),
                DB::raw('COUNT(id) as numberOrders'),
                DB::raw('created_at as date')
            )->where('user_id',$id)->groupBy('month')->orderBy('date', 'desc')->get();
            
            $monthIn = Bill::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(id) as numberBills'),
                DB::raw('created_at as date')
            )->where('add_by',$id)->groupBy('month')->orderBy('date', 'desc')->get();
            
            $clients = Client::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(id) as numberClients'),
                DB::raw('created_at as date')
            )->where('add_by',$id)->groupBy('month')->orderBy('date', 'desc')->get();
            $stores = Store::all();
            return view('dashboard.reports.historyUser', compact('user','month','monthIn','clients','stores'));
        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function historydeletes(Request $request)
    {
        $HistorydeletesDate = Historydelete::select('id', DB::raw('DATE(created_at) as date'))
            ->where('status',0)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');
            
        $historydeletes = Historydelete::where('status',0)->when($request->search, function ($q) use ($request) {
            return $q->Where([['info_one', 'like', '%' . $request->search . '%']]);
        })->when($request->date, function ($q) use ($request) {
                return $q->where('created_at', 'like', '%' . $request->date . '%');
        })->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
        })->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
        })->latest()->get();
        
        $users = User::all();
        $stores = Store::all();
        return view('dashboard.reports.historydeletes', compact('historydeletes','users','HistorydeletesDate','stores'));
    }
    
    public function historydeletesStatus($id)
    {
        try {
            $historydelete = Historydelete::find($id);
            if (!$historydelete)
                return abort(404);

           $status = $historydelete->status == 0 ? 1 : 0;
           $historydelete->update(['status' =>$status]);
           
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.reports.historydeletes');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
    
    public function statistics()
    {
        $monthProducts = Product::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as countProduct'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        $monthBills = Bill::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as countBill'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        $monthClients = Client::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as countClient'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        $monthOrders = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as countOrder'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        $monthBounceds = Bounced::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as countBounced'),
            DB::raw('created_at as date')
        )->groupBy('month')->orderBy('date', 'desc')->get();
        
        /***********************************************************************/
        
        $yearProducts = Product::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(id) as countProductyear'),
            DB::raw('created_at as date')
        )->groupBy('year')->orderBy('date', 'desc')->get();
        
        $yearBills = Bill::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(id) as countBillyear'),
            DB::raw('created_at as date')
        )->groupBy('year')->orderBy('date', 'desc')->get();
        
        $yearClients = Client::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(id) as countClientyear'),
            DB::raw('created_at as date')
        )->groupBy('year')->orderBy('date', 'desc')->get();
        
        $yearOrders = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(id) as countOrderyear'),
            DB::raw('created_at as date')
        )->groupBy('year')->orderBy('date', 'desc')->get();
        
        $yearBounceds = Bounced::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(id) as countBouncedyear'),
            DB::raw('created_at as date')
        )->groupBy('year')->orderBy('date', 'desc')->get();
        
        return view('dashboard.reports.statistics', compact('monthProducts','monthBills','monthClients','monthOrders','monthBounceds',
                                                            'yearProducts','yearBills','yearClients','yearOrders','yearBounceds'));
    }

}
