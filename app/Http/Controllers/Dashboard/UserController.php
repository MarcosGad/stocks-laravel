<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use App\Store;
use App\Order;
use App\Bill;
use App\Client;
use App\Historydelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserController extends Controller
{

    public function __construct()
    {
        //create read update delete
        $this->middleware(['permission:read-users'])->only('index');
        $this->middleware(['permission:create-users'])->only('create');
        $this->middleware(['permission:update-users'])->only('edit');
        $this->middleware(['permission:delete-users'])->only('destroy');

    }
   
    public function index(Request $request)
    {

        $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {
                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        })->latest()->paginate(10);
        $orders = Order::all();
        $bills = Bill::all();
        $clients = Client::all();
        $stores = Store::all();
        $userFs = User::all();
        return view('dashboard.users.index', compact('users','orders','bills','clients','stores','userFs'));
    }

    
    public function create()
    {
        $stores = Store::all();
        return view('dashboard.users.create',compact('stores'));
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'image' => 'image',
            'password' => 'required|confirmed',
            'permissions' => 'required|min:1',
            'store_official' => 'required',
        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        $request_data['add_by'] = Auth::id();

        if ($request->image) {

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();

        }
        
        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);
        
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');
     
    }

    
    public function edit(User $user)
    {
        $stores = Store::all();
        return view('dashboard.users.edit', compact('user','stores'));
    }

   
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($user->id),],
            'image' => 'image',
            'permissions' => 'required|min:1',
            'store_official' => 'required',
        ]);

        $request_data = $request->except(['permissions', 'image']);
        $request_data['update_by'] = Auth::id();
        if ($request->image) {

            if ($user->image != 'default.png') {
                Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
            }

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }
        
        $user->update($request_data);
        $user->syncPermissions($request->permissions);
        
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');
    }

   
    public function destroy(User $user)
    {
        if ($user->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
        }
        
        //Historydelete
        $historydeleteInput = ['type' => 10, 'type_id' => $user->id, 'info_one' => $user->email, 'info_two' => $user->store_official,'user_id' => Auth::id()];
        Historydelete::create($historydeleteInput);

        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');
    }
    
    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.users.profile',compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id,
            'image' => 'image',
        ]);
        
        try {
            $user = Auth::user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();
           
            if ($request->has('password') && $request->password != null&& $request->new_password != null) {
            
                if (Hash::check($request->password , $user->password)) { 
                    
                  $user->fill([
                    'password' => bcrypt($request->new_password)
                    ])->save();
                
                } else {
                    return redirect()->back()->with('error', 'من فضلك تأكد من كلمة السر القديمة');      
                }
        
            }
            
            if ($request->image) {

            if ($user->image != 'default.png') {
                    Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
                }
    
                Image::make($request->image)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save(public_path('uploads/user_images/' . $request->image->hashName()));
    
                $request_data['image'] = $request->image->hashName();
            }

            session()->flash('success', __('site.updated_successfully'));
            return back();
            
        } catch (\Exception $ex) {
            return abort(404);
            //dd($ex);
        }
    }
}
