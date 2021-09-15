<?php

namespace App\Http\Controllers\Dashboard;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Historydelete;
use App\User;
use App\Order;

class ClientController extends Controller
{
 
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $clients = Client::when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%']])
                ->orWhere([['phone', 'like', '%' . $request->search . '%']])
                ->orWhere([['address', 'like', '%' . $request->search . '%']]);

            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.clients.index', compact('clients','users'));
        }else{
            $clients = Client::where('add_by',Auth::id())->when($request->search, function($q) use ($request){

            return $q->where([['name', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['phone', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                ->orWhere([['address', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);

            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.clients.index', compact('clients','users'));
        }
    }

    public function create()
    {
        return view('dashboard.clients.create');
    }

 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
            'address' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);
        $request_data['add_by'] = Auth::id();

        Client::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.clients.index');
    }


    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }


    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
            'address' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);
        $request_data['update_by'] = Auth::id();
        
        $client->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.clients.index');
    }

    
    public function destroy(Client $client)
    {
        $ordersC = Order::where('client_id',$client->id)->count();
        if($ordersC == 0)
        {
            //Historydelete
            $historydeleteInput = ['type' => 3, 'type_id' => $client->id, 'info_one' => $client->name, 'info_two' => json_encode($client->phone), 'user_id' => Auth::id()];
            Historydelete::create($historydeleteInput);
            
            $client->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.clients.index'); 
        }else{
           return redirect()->route('dashboard.clients.index');
        }
    }
    
    public function transfersClient($userId,$clientId)
    {
        $users = User::all();
        return view('dashboard.transfers.client', compact('users','userId','clientId'));
    }
    
     public function postTransfersClient(Request $request)
    {
       try {
            $client = Client::find($request->clientId);
            if (!$client)
                return abort(404);

           $client->update(['add_by' =>$request->userId, 'trans'=>Auth::id()]);
           session()->flash('success', __('site.updated_successfully'));
           return redirect()->route('dashboard.clients.index');

        } catch (\Exception $ex) {
            return abort(404);
        }
    }
}
