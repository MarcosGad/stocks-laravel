<?php

namespace App\Http\Controllers\Dashboard;
use App\Bill;
use App\User;
use App\Store;
use App\Product;
use App\Category;
use App\Historydelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use DB;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $categories = Category::all();
            $products = Product::when($request->search, function ($q) use ($request) {
                return $q->whereTranslationLike('name', '%' . $request->search . '%')
                         ->orWhere([['purchase_invoice_number', 'like', '%' . $request->search . '%']])
                         ->orWhere([['productCode', 'like', '%' . $request->search . '%']]);
            })->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })->latest()->paginate(8);
            
            $stores = Store::all();
            $users = User::all();
            return view('dashboard.products.index', compact('categories', 'products','stores','users'));
        }else{
            
            $categories = Category::where('add_by',Auth::id())->get();
            $products = Product::where('add_by',Auth::id())->when($request->search, function ($q) use ($request) {
                return $q->whereTranslationLike('name', '%' . $request->search . '%',['add_by',Auth::id()])
                         ->orWhere([['purchase_invoice_number', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]])
                         ->orWhere([['productCode', 'like', '%' . $request->search . '%'],['add_by',Auth::id()]]);
            })->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id,['add_by',Auth::id()]);
            })->latest()->paginate(8);
            
            $stores = Store::where('add_by',Auth::id())->get();
            $users = User::all();
            return view('dashboard.products.index', compact('categories', 'products','stores','users'));
        }
    }


    public function create()
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $categories = Category::all();
            $bills = Bill::orderBy('id', 'desc')->get();
            $stores = Store::all();
            return view('dashboard.products.create', compact('categories','bills','stores'));
        }else{
            $categories = Category::all();
            $bills = Bill::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
            $stores = Store::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
            return view('dashboard.products.create', compact('categories','bills','stores'));
        }
        
    }


    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required'
        ];
        
        $rules += [
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
        ];
        
        $request->validate($rules);
        $request_data = $request->all();
        $request_data['real_stock'] = $request->stock;
        $fatherCat = Category::where('id', $request->category_id)->first()->father_cat;
        $request_data['productCode'] = 'C' . $fatherCat . $request->category_id .'-P'. rand(1,10000);
        $request_data['add_by'] = Auth::id();
        $request_data['serial_numbers'] = array_filter($request->serial_numbers);

        if ($request->image) {
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(base_path('uploads/product_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }
        
        $totalPro = $request->stock * $request->purchase_price;
        Product::create($request_data);
        
        $bill = Bill::where('invoice_number',$request->purchase_invoice_number)->get();
        if($bill){
            $total = $bill[0]->total + $totalPro;
            Bill::where('invoice_number',$request->purchase_invoice_number)->update(['total'=>$total]);
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.products.index');
        }
        return redirect()->route('dashboard.products.index');
    }

    public function edit(Product $product)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $categories = Category::all();
            $bills = Bill::orderBy('id', 'desc')->get();
            $stores = Store::orderBy('id', 'desc')->get();
            return view('dashboard.products.edit', compact('categories', 'product','bills','stores'));
        }else{
            $categories = Category::all();
            $bills = Bill::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
            $stores = Store::where('add_by',Auth::id())->orderBy('id', 'desc')->get();
            return view('dashboard.products.edit', compact('categories', 'product','bills','stores'));
        }
    }


    public function update(Request $request, Product $product)
    {
        $rules = [
            'category_id' => 'required'
        ];

        $rules += [
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
        ];

        $request->validate($rules);

        $request_data = $request->all();
        $request_data['stock'] = ($request->real_stock - $product->real_stock) + $product->stock;
        $request_data['real_stock'] = $request->real_stock;
        $request_data['productCode'] = $request->productCode;
        $request_data['update_by'] = Auth::id();

        if ($request->image) {

            if ($product->image != 'default.png') {

                Storage::disk('public_uploads')->delete('/product_images/' . $product->image);       
            }

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(base_path('uploads/product_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }
        
        $bill = Bill::where('invoice_number',$product->purchase_invoice_number)->get();
        if($bill){
            $total = $bill[0]->total - $product->real_stock * $product->purchase_price;
            $newtotal = $total + $request->real_stock * $request->purchase_price;
            Bill::where('invoice_number',$product->purchase_invoice_number)->update(['total'=>$newtotal]);
            $product->update($request_data);
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.products.index');
        }
        return redirect()->route('dashboard.products.index');
    }


    public function destroy(Product $product)
    {
        if ($product->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/product_images/' . $product->image);
        }
            
        $bill = Bill::where('invoice_number',$product->purchase_invoice_number)->get();
        if($bill){
            $total = $bill[0]->total - $product->real_stock * $product->purchase_price;
            Bill::where('invoice_number',$product->purchase_invoice_number)->update(['total'=>$total]);
            
            //Historydelete
            $historydeleteInput = ['type' => 5,'type_id' => $product->id,'info_one' => $product->name,'info_two' => $product->productCode,'user_id' => Auth::id()];
            Historydelete::create($historydeleteInput);

            $productTranslations = DB::table('product_translations')->select('*')->where('product_id', $product->id)->delete();
            $product->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.products.index');
        }
        return redirect()->route('dashboard.products.index');
    }
    
    public function transfersProduct($storeId,$productId)
    {
        $product = Product::find($productId);
        if (!$product)
            return abort(404);
                
        $stores = Store::all();
        return view('dashboard.transfers.product', compact('stores','storeId','productId','product'));
    }
    
     public function postTransfersProduct(Request $request)
    {
 
      try {
            $product = Product::find($request->productId);
            if (!$product)
                return abort(404);
                
            //update     
            $newStock = $product->stock - $request->stock;
            $newRealstock = $product->real_stock - $request->stock;
            $product->update(['stock'=>$newStock,'real_stock'=>$newRealstock, 'transformer_product'=>2, 'store_to'=>$request->store_id, 'update_by' => Auth::id(), 'trans' => Auth::id()]);
            
            //create new
            $request_data = $request->all();
            $request_data['real_stock'] = $request->stock;
            $fatherCat = Category::where('id', $request->category_id)->first()->father_cat;
            $request_data['productCode'] = 'C' . $fatherCat . $request->category_id .'-P'. rand(1,10000);
            $request_data['add_by'] = Auth::id();
            $request_data['serial_numbers'] = array_filter($request->serial_numbers);
            $request_data['transformer_product'] = 1;
            $request_data['store_original'] = $product->store_id;
            
            if ($request->image) {
                Image::make($request->image)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save(base_path('uploads/product_images/' . $request->image->hashName()));
                $request_data['image'] = $request->image->hashName();
            }
            Product::create(collect($request_data)->except(['productId'])->toArray());
          
          session()->flash('success', __('site.updated_successfully'));
          return redirect()->route('dashboard.products.index');

        } catch (\Exception $ex) {
            dd($ex);
            return abort(404);
        }
    }
    
    public function showSerial($productId)
    {
       $products = Product::where('id', $productId)->get();
       return view('dashboard.products.showSerial', compact('products'));
    }
}
