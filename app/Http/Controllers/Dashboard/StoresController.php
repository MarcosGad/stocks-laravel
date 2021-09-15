<?php

namespace App\Http\Controllers\Dashboard;

use App\Store;
use App\User;
use App\Product;
use App\Historydelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class StoresController extends Controller
{

    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $users = User::all();
            $products = Product::all();
            $stores = Store::when($request->search, function($q) use ($request){

            return $q->where([['store_name', 'like', '%' . $request->search . '%']]);
            })->latest()->paginate(8);
            return view('dashboard.stores.index', compact('stores','users','products'));
        }else{
            $users = User::all();
            $products = Product::all();
            $stores = Store::where('add_by',Auth::id())->when($request->search, function($q) use ($request){

            return $q->where([['store_name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
            })->latest()->paginate(8);
            return view('dashboard.stores.index', compact('stores','users','products'));
        }
    }


    public function create()
    {
        $users = User::all();
        return view('dashboard.stores.create', compact('users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required',
            'store_address' => 'required',
            'store_respon' => 'required',
        ]);
       
        $request_data = $request->all();
        $request_data['add_by'] = Auth::id();
        
        Store::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.stores.index');
    }


    public function destroy(Request $request)
    {
        $productsC = Product::where('store_id',$request->id)->count();
        if($productsC == 0)
        {
            $store = Store::findOrFail($request->id);
            //Historydelete
            $historydeleteInput = ['type' => 7, 'type_id' => $request->id, 'info_one' => $store->store_name, 'info_two' => $store->store_respon, 'info_three' => $store->add_by, 'user_id' => Auth::id()];
            Historydelete::create($historydeleteInput);
            $store->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.stores.index');
        }else{
            return redirect()->route('dashboard.stores.index');
        }
    }
    
    public function transfersStore($userId,$storeId)
    {
        $users = User::all();
        return view('dashboard.transfers.store', compact('users','userId','storeId'));
    }
    
     public function postTransfersStore(Request $request)
    {
       try {
            $store = Store::find($request->storeId);
            if (!$store)
                return abort(404);

           $store->update(['store_respon' =>$request->userId, 'update_by' => Auth::id()]);
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.stores.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
}
