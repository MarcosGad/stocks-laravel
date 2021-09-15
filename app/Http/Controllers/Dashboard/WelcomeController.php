<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Client;
use App\Order;
use App\Product;
use App\Bounced;
use App\User;
use App\Bill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function index()
    {
        $categories_count = Category::count();
        $products_count = Product::count();
        $clients_count = Client::count();
        $users_count = User::whereRoleIs('admin')->count();

        $sales_data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as sum')
        )->groupBy('month')->get();
        
        $clientInDay = Client::whereDate('created_at', Carbon::today())->count();
        $orderInDay = Order::whereDate('created_at', Carbon::today())->count();
        $billInDay = Bill::whereDate('created_at', Carbon::today())->count();
        $bouncedInDay = Bounced::whereDate('created_at', Carbon::today())->count();
        $productInDay = Product::whereDate('created_at', Carbon::today())->count();
        
        $clientInMonth = Client::whereMonth('created_at', Carbon::now()->month)->count();
        $orderInMonth = Order::whereMonth('created_at', Carbon::now()->month)->count();
        $billInMonth = Bill::whereMonth('created_at', Carbon::now()->month)->count();
        $bouncedInMonth = Bounced::whereMonth('created_at', Carbon::now()->month)->count();
        $productInMonth = Product::whereMonth('created_at', Carbon::now()->month)->count();
        
        $clientInYear = Client::whereYear('created_at', Carbon::now()->year)->count();
        $orderInYear = Order::whereYear('created_at', Carbon::now()->year)->count();
        $billInYear = Bill::whereYear('created_at', Carbon::now()->year)->count();
        $bouncedInYear = Bounced::whereYear('created_at', Carbon::now()->year)->count();
        $productInYear = Product::whereYear('created_at', Carbon::now()->year)->count();

        return view('dashboard.welcome', 
                        compact(
                            'categories_count', 
                            'products_count',
                            'clients_count', 
                            'users_count', 
                            'sales_data',
                            'clientInDay',
                            'orderInDay',
                            'billInDay',
                            'bouncedInDay',
                            'productInDay',
                            'clientInMonth',
                            'orderInMonth',
                            'billInMonth',
                            'bouncedInMonth',
                            'productInMonth',
                            'clientInYear',
                            'orderInYear',
                            'billInYear',
                            'bouncedInYear',
                            'productInYear'
                        )
                    );
    }
}
