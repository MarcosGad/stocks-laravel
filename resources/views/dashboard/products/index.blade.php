
@extends('layouts.dashboard.app')

@section('content')
<style>
    .btn-cs{
      display: block;
      margin: 6px;
      width: 80%;
      font-size: 9px;
    }
    .trans {position:relative;color: #f39c12;}
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        border: 0;
    }
    .icon-trans + .sr-only {
        padding: 0.25em;
        margin: 0;
        color: #000;
        background: #eee;
        border: 1px solid #ccc;
        border-radius: 2px;
        font: 11px sans-serif;
        z-index: 2; 
    }
    .trans:focus .icon-trans + .sr-only, 
    .trans:hover .icon-trans + .sr-only {
      clip: auto;
      width: auto;
      height: auto;
      bottom: 100%;
      left: 100%;
    }
</style>

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.products') <small>{{ $products->total() }}</small> </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.products')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.products.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control" placeholder="أسم المنتج أو رقم فاتورة الشراء أو كود المنتج" value="{{ request()->search }}">
                            </div>

                            <div class="col-md-3">
                                <select name="category_id" class="form-control">
                                    <option value="">@lang('site.all_categories')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
 
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create-products'))
                                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($products->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.product_code')</th>
                                <th>@lang('site.description')</th>
                                <th>@lang('site.category')</th>
                                <th>@lang('site.image')</th>
                                <th>فاتورة</th>
                                <th>@lang('site.purchase_price')</th>
                                <th>@lang('site.sale_price')</th>
                                <th>الرصيد</th>
                                <th>@lang('site.stock')</th>
                                <th>المخزن</th>
                                <th>بواسطة</th>
                                <th>تعديل</th>
                                <th>@lang('site.profit_percent')</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($products as $index=>$product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('dashboard.reports.historyproduct', $product->id) }}" target="_blank">{{ $product->name }}</a></td>
                                    <td>{{ $product->productCode }}</td>
                                    <td>{!! $product->description !!}</td>
                                    <td><a href="{{ route('dashboard.products.index') }}?search=&category_id={{$product->category->id}}" target="_blank">{{ $product->category->name }}</a></td>
                                    <td><img src="{{ $product->image_path }}" style="width: 100px"  class="img-thumbnail" alt=""></td>
                                    <td><a href="{{ route('dashboard.purchaseInvoices.index') }}?search={{$product->purchase_invoice_number}}" target="_blank">{{ $product->purchase_invoice_number }}</a></td>
                                    <td>{{ $product->purchase_price }}</td>
                                    <td>{{ $product->sale_price }}</td>
                                    <td>{{ $product->real_stock }}</td>
                                    <td @if($product->stock == 0) style="color:red" @endif>{{ $product->stock }}</td>
                                    <td>
                                        @foreach ($stores as $store) 
                                            @if($store->id == $product->store_id)
                                                <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                      @foreach ($users as $user) 
                                         @if($user->id == $product->add_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                    </td>
                                    <td>
                                      @if($product->update_by != 0)
                                      @foreach ($users as $user) 
                                         @if($user->id == $product->update_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                      @else
                                      <span style="color:red">لم يعديل</span>
                                      @endif
                                    </td>
                                    <td style="color:green">{{ $product->profit_percent }}%</td>
                                    <td>
                                        @if($product->trans != 0)
                                        <a href="#" class="trans"><i class="fa fa-rocket icon-trans" aria-hidden="true"></i><span class="sr-only">
                                          @foreach ($users as $user) 
                                             @if($user->id == $product->trans)
                                                <span>{{strstr($user->email, '@', true)}}</span>
                                             @endif
                                          @endforeach
                                        </span></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->hasPermission('update-products'))
                                            <a href="{{ route('dashboard.products.edit', $product->id) }}" class="btn btn-info btn-sm btn-cs"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm btn-cs" style="cursor:no-drop;"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @php
                                           $productF = DB::table('last_price_product_order')->select('id')->where('product_id', $product->id)->count();
                                        @endphp
                                        @if (auth()->user()->hasPermission('delete-products') && $productF == 0)
                                            <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger delete btn-sm btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm btn-cs" style="cursor:no-drop;"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                        @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                            @if($product->stock != 0)
                                            <a href="{{ route('dashboard.product.transfersProduct', [$product->store_id,$product->id]) }}" target="_blank" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-rocket"></i> تحويل المنتج</a>
                                            @endif
                                        @endif
                                        @if($product->serial_numbers)
                                            @if(array_search(null, $product->serial_numbers) !== 0)
                                            <a href="{{ route('dashboard.products.showSerial', $product->id) }}" target="_blank" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-eye"></i> التسلسلية</a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                        {{ $products->appends(request()->query())->links() }}
                        
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
