@extends('layouts.dashboard.app')

@section('content')
<style>
    #val,#valTwo{
        border: 1px solid #222d32;
        padding: 8px;
        display: inline-block;
        border-radius: 10px;
        margin-top: 15PX;
        font-size: 15px;
    }
</style>
<div class="content-wrapper">

        <section class="content-header">

            <h1>المخازن</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">المخازن</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.reports.store') }}" method="get">

                        <div class="row">

                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="أسم المنتج أو كود المنتج أو رقم فاتورة الشراء" value="{{ request()->search }}">
                            </div>

                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="">@lang('site.all_categories')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="store_id" class="form-control">
                                    <option value="">كل المخازن</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" {{ request()->store_id == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                             @php
                              $stocks = array(
                              "0"=>"الكل",
                              "1"=>"موجود");
                            @endphp
                            <div class="col-md-2">
                                <select name="stock" class="form-control">
                                     @foreach ($stocks as $stock=>$stocksValue)
                                        <option value="{{ $stock }}" {{ request()->stock == $stock ? 'selected' : '' }}>{{ $stocksValue }}</option>
                                     @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                <button type="button" class="btn btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</a>
                            </div>
                            
                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->
            <div id="print-area">
                <style type="text/css" media="print">
                @page {
                    size: auto;   
                    margin: 30px; 
                    margin-top:5px;
                }
                .box-body{
                    font-size:10px;
                }
                a[href]:after {
                  content: none !important;
                }
                #val,#valTwo{
                    border: 1px solid #222d32;
                    padding: 8px;
                    display: inline-block;
                    border-radius: 10px;
                    margin-top: 15PX;
                    font-size: 15px;
                }
                </style>
                <div class="box-body">

                    @if ($products->count() > 0)

                        <table class="table table-hover" id="table">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.product_code')</th>
                                <th>@lang('site.category')</th>
                                <th>الرصيد</th>
                                <th>@lang('site.stock')</th>
                                <th>@lang('site.stock')</th>
                                @if(in_array(Auth::id(),array(1,2)))
                                <th>الشراء</th>
                                @endif
                                <th>@lang('site.sale_price')</th>
                                <th>أضافة</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($products as $index=>$product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('dashboard.reports.historyproduct', $product->id) }}" target="_blank">{{ $product->name }}</a></td>
                                    <td>{{ $product->productCode }}</td>
                                    <td><a href="{{ route('dashboard.products.index') }}?search=&category_id={{$product->category->id}}" target="_blank">{{ $product->category->name}}</a></td>
                                    <td>{{ $product->real_stock }}</td>
                                    <td @if($product->stock == 0) style="color:red" @endif>{{ $product->stock }}</td>
                                    <td>
                                        @foreach ($stores as $store) 
                                            @if($store->id == $product->store_id)
                                                <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    @if(in_array(Auth::id(),array(1,2)))
                                    <td>{{ $product->purchase_price }}</td>
                                    <td style="display: none" id="purchase_price_loop">{{ $product->purchase_price * $product->real_stock }}</td>
                                    @else
                                    <td style="display: none" id="sale_price_user">{{ $product->sale_price * $product->real_stock}}</td>
                                    @endif
                                    <td>{{ $product->sale_price }}</td>
                                    <td style="display: none" id="sale_price_admin">{{ $product->sale_price * $product->real_stock }}</td>
                                    <td>{{ $product->created_at->toFormattedDateString() }}</td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        @if(in_array(Auth::id(),array(1,2)))
                        <span id="val"></span>
                        <span id="valTwo"></span>
                        @else
                        <span id="val"></span>
                        @endif
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    @if(in_array(Auth::id(),array(1,2)))
    <script>
        var table = document.getElementById("table"), sumVal = 0; sumValTwo = 0;
        
        for(var row = 1; row < table.rows.length; row++)
        {
            sumVal = sumVal + parseInt(table.rows[row].cells[7].innerHTML);
            sumValTwo = sumValTwo + parseInt(table.rows[row].cells[8].innerHTML);
        }
        
        document.getElementById("val").innerHTML = "اجمالى الشراء = " + sumVal.toFixed(2);
        document.getElementById("valTwo").innerHTML = " اجمالى البيع = " + sumValTwo.toFixed(2);
    </script>
    
    <script type="text/javascript">
        $(function() {
           var TotalValue = 0;
           var TotalValueTwo = 0;
           
           $("tr #purchase_price_loop").each(function(index,value){
             currentRow = parseFloat($(this).text());
             TotalValue += currentRow
           });
           
           $("tr #sale_price_admin").each(function(index,value){
             currentRowTwo = parseFloat($(this).text());
             TotalValueTwo += currentRowTwo
           });
           
           document.getElementById("val").innerHTML = "اجمالى الشراء = " + TotalValue.toFixed(2);
           document.getElementById("valTwo").innerHTML = " اجمالى البيع = " + TotalValueTwo.toFixed(2);
        });
    </script>
    
    @else
    <script type="text/javascript">
        $(function() {
           var TotalValue = 0;
           $("tr #sale_price_user").each(function(index,value){
             currentRow = parseFloat($(this).text());
             TotalValue += currentRow
           });
           document.getElementById("val").innerHTML = " اجمالى البيع = " + TotalValue.toFixed(2);
        });
    </script>
    @endif

@endsection
