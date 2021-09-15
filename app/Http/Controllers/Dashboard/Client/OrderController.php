<?php

namespace App\Http\Controllers\Dashboard\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon; 
use App\Client;
use App\Order;
use App\Category;
use App\Backorder;
use App\Sumorder;
use App\Product;
use App\Store;
use App\Representative;
use App\Shippingmethod;
use App\Historydelete;
use Auth;
use DB;

class OrderController extends Controller
{
    public function create(Client $client)
    {
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(5);
        $representatives = Representative::all();
        $shippingmethods = Shippingmethod::all();
        $stores = Store::all();
        return view('dashboard.clients.orders.create', compact( 'client', 'categories', 'orders', 'representatives','stores','shippingmethods'));
    }

    public function store(Request $request, Client $client)
    {
        $request->validate([
            'products' => 'required|array',
        ]);
        
        $this->attach_order($request, $client);
        
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    public function edit(Client $client, Order $order)
    {
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(5);
        $representatives = Representative::all();
        $shippingmethods = Shippingmethod::all();
        $stores = Store::all();
        return view('dashboard.clients.orders.edit', compact('client', 'order', 'categories', 'orders', 'representatives','stores','shippingmethods'));
    }

    public function update(Request $request, Client $client, Order $order)
    {
        $request->validate([
            'products' => 'required|array',
        ]);

        $this->detach_order($order);

        $this->attach_order($request, $client);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    private function attach_order($request, $client)
    {
        if($request->payment_type == 2){
            $request->validate([
                'number_of_days' => 'required',
            ]);
        }
        if($request->payment_type == 3){
            $request->validate([
                'partially_price' => 'required',
                'the_rest_in_through' =>  'required',
            ]);
        }
        
        $order = $client->orders()->create([]);
        $order->products()->attach($request->products);

        $order->user_id = Auth::user()->id;
        $order->notes = $request->notes;
        $order->serial_numbers = $request->serial_numbers;
        $order->shipping = $request->shipping;
        $order->representative_id = $request->representative_id;
        $order->discount = $request->discount;
        $order->payment_type = $request->payment_type;
        $order->number_of_days = $request->number_of_days;
        $order->partially_price = $request->partially_price;
        $order->the_rest_in_through = $request->the_rest_in_through;
        $order->transport = $request->transport;
        $order->date = $request->date;
        $total_price = 0;
        $total_cost = 0;
        
        foreach ($request->products as $id => $quantity) {

            $product = Product::FindOrFail($id);
            $price = DB::table('product_order')->select('price')->where('order_id', $order->id)->where('product_id', $id)->first();
            $total_price += $price->price * $quantity['quantity'];
            $total_cost += $product->purchase_price * $quantity['quantity'];
            $product->update([
                'stock' => $product->stock - $quantity['quantity'],
                'last_price' => $price->price
            ]);
            
            $values = array('product_id' => $id, 'order_id' => $order->id, 'last_price'=>$price->price);
            DB::table('last_price_product_order')->insert($values);
        }
        
        $order->update([
            'total_price' => $total_price,
            'total_cost' => $total_cost
        ]);
        
        if($request->payment_type == 2){
             $cDate = Carbon::parse($order->date)->format('d.m.Y');
             $date = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate);
             $daysToAdd = $request->number_of_days;
             $date = $date->addDays($daysToAdd);
             $inputs = ['type' => 1,'number' => $order->id,'date'=>$date->format('Y-m-d')];
             Backorder::create($inputs);
        };
        if($request->payment_type == 3){
            $cDate2 =  Carbon::parse($order->date)->format('d.m.Y');
            $date2 = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate2);
            $daysToAdd2 = $request->the_rest_in_through;
            $date2 = $date2->addDays($daysToAdd2);
            $inputs = ['type' => 1,'number' => $order->id,'date'=>$date2->format('Y-m-d')];
            Backorder::create($inputs);
        };
        
        Sumorder::create(['order_id'=>$order->id, 'number_items'=>count($request->products), 'total_price'=>$total_price, 'discount'=>$request->discount, 'transport'=>$request->transport, 'type' => 1]);
    }

    private function detach_order($order)
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
        $historydeleteInput = ['type' => 11, 'type_id' => $order->id, 'info_one' => $order->client->name, 'info_two' => $order->payment_type, 'info_three' => $order->total_price + $order->transport - $order->discount, 'user_id' => Auth::id()];
        Historydelete::create($historydeleteInput);

        $order->delete();
        
    }


}
