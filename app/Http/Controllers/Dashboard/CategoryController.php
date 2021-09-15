<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Historydelete;
use App\User;
use DB;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $id = Auth::user()->id;
        if($id == 1 || $id == 2){
            $categories = Category::when($request->search, function ($q) use ($request) {
               return $q->whereTranslationLike('name', '%' . $request->search . '%');
            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.categories.index', compact('categories','users'));
        }else{
            
            $categories = Category::where('add_by',Auth::id())->when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%',['add_by',Auth::id()]);

            })->latest()->paginate(8);
            $users = User::all();
            return view('dashboard.categories.index', compact('categories','users'));
        }
    }


    public function create()
    {
        $categories = Category::all();
        return view('dashboard.categories.create',compact('categories'));
    }


    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required'
        ];

        foreach (config('translatable.locales') as $locale) {
            $rules += [$locale . '.name' => ['required', Rule::unique('category_translations', 'name')]];
        }

        $request->validate($rules);
        
        $request['father_cat'] = $request->category_id; 
        $request['add_by'] = Auth::id();
        Category::create($request->except(['category_id']));

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.categories.index');
    }

    public function edit(Category $category)
    {
        $categories = Category::all();
        return view('dashboard.categories.edit', compact('category','categories'));
    }

    public function update(Request $request, Category $category)
    {
        $rules = [
            'category_id' => 'required'
        ];

        foreach (config('translatable.locales') as $locale) {
            $rules += [$locale . '.name' => ['required', Rule::unique('category_translations', 'name')->ignore($category->id, 'category_id')]];
        }

        $request->validate($rules);

        $request['father_cat'] = $request->category_id;
        $request['update_by'] = Auth::id();
        $category->update($request->except(['category_id']));

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.categories.index');

    }


    public function destroy(Category $category)
    {
        //Historydelete
        $historydeleteInput = ['type' => 4, 'type_id' => $category->id, 'info_one' => $category->name, 'user_id' => Auth::id()];
        Historydelete::create($historydeleteInput);
        
        $categoryTranslations = DB::table('category_translations')->select('*')->where('category_id', $category->id)->delete();
        $category->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.categories.index');
    }
}
