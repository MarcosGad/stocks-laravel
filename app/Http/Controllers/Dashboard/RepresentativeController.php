<?php

namespace App\Http\Controllers\Dashboard;

use App\Representative;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Historydelete;
use App\Order;
use App\User;

class RepresentativeController extends Controller
{
 
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $representatives = Representative::when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%']])
                ->orWhere([['phone', 'like', '%' . $request->search . '%']]);
                
            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.representatives.index', compact('representatives','users'));
        }else{
            $representatives = Representative::where('add_by',Auth::id())->when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['phone', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
                
            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.representatives.index', compact('representatives','users'));
        }
    }

    public function create()
    {
       return view('dashboard.representatives.create');
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

        Representative::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.representatives.index');
    }


    public function edit(Representative $representative)
    {
        return view('dashboard.representatives.edit', compact('representative'));
    }


    public function update(Request $request, Representative $representative)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);
        $request_data['update_by'] = Auth::id();
        
        $representative->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.representatives.index');
    }

    
    public function destroy(Representative $representative)
    {
        $ordersC = Order::where('representative_id',$representative->id)->count();
        if($ordersC == 0)
        {
           //Historydelete
           $historydeleteInput = ['type' => 8, 'type_id' => $representative->id, 'info_one' => $representative->name, 'info_two' => json_encode($representative->phone), 'info_three' => $representative->add_by, 'user_id' => Auth::id()];
           Historydelete::create($historydeleteInput);

           $representative->delete();
           session()->flash('success', __('site.deleted_successfully'));
           return redirect()->route('dashboard.representatives.index');
        }else{
           return redirect()->route('dashboard.representatives.index');
        }
    }
    
}