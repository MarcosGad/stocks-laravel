<?php

namespace App\Http\Controllers\Dashboard;

use App\Shippingmethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Historydelete;
use App\Order;
use App\User;

class ShippingmethodsController extends Controller
{
 
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $shippingmethods = Shippingmethod::when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%']])
                ->orWhere([['phone', 'like', '%' . $request->search . '%']]);
                
            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.shippingmethods.index', compact('shippingmethods','users'));
        }else{
            $shippingmethods = Shippingmethod::where('add_by',Auth::id())->when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['phone', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
                
            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.shippingmethods.index', compact('shippingmethods','users'));
        }
    }

    public function create()
    {
       return view('dashboard.shippingmethods.create');
    }

 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);
        $request_data['add_by'] = Auth::id();

        Shippingmethod::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.shippingmethods.index');
    }


    public function edit(Shippingmethod $shippingmethod)
    {
        return view('dashboard.shippingmethods.edit', compact('shippingmethod'));
    }


    public function update(Request $request, Shippingmethod $shippingmethod)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);
        $request_data['update_by'] = Auth::id();
        
        $shippingmethod->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.shippingmethods.index');
    }

    
    public function destroy(Shippingmethod $shippingmethod)
    {
        $ordersC = Order::where('shipping',$shippingmethod->id)->count();
        if($ordersC == 0)
        {
           //Historydelete
           $historydeleteInput = ['type' => 9, 'type_id' => $shippingmethod->id,'info_one' => $shippingmethod->name,'info_two' => json_encode($shippingmethod->phone),'info_three' => $shippingmethod->add_by,'user_id' => Auth::id()];
           Historydelete::create($historydeleteInput);
           $shippingmethod->delete();
           session()->flash('success', __('site.deleted_successfully'));
           return redirect()->route('dashboard.shippingmethods.index');
        }else{
           return redirect()->route('dashboard.shippingmethods.index');
        }
    }
    
}