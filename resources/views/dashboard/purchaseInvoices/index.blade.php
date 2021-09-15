@extends('layouts.dashboard.app')

@section('content')
<style>
    .btn-cs{
      display: block;
      margin: 6px;
      width: 100%;
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">

            <h1>فواتير الشراء  <small>{{ $bills->total() }}</small> </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">فواتير الشراء</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.purchaseInvoices.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="أسم المورد أو رقم فاتورة الشراء" value="{{ request()->search }}">
                            </div>
                            
                            <div class="col-md-4">
                                <input type="text" name="serial" class="form-control" placeholder="الرقم التسلسلى الخاص بالمنتج" value="{{ request()->serial }}">
                            </div>
 
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create-products'))
                                    <a href="{{ route('dashboard.purchaseInvoices.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($bills->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>أسم الموارد</th>
                                <th>رقم</th>
                                <th>@lang('site.image')</th>
                                <th>العنوان</th>
                                <th>رقم التيلفون</th>
                                <th>طريقة الدفع</th>
                                <th>الأجمالى</th>
                                <th>بواسطة</th>
                                <th>تعديل</th>
                                <th>المرتجع</th>
                                <th>الأضافة</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($bills as $index=>$bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->supplier_name }} @if($bill->sales_officer) - {{ $bill->sales_officer }} @endif</td>
                                    <td>{{ $bill->invoice_number }}</td>
                                    <td><img src="{{ $bill->image_path }}" style="width: 100px"  class="img-thumbnail" alt=""></td>
                                    <td>{{ $bill->supplier_address }}</td>
                                    <td>{{ is_array($bill->supplier_phone) ? implode('-', $bill->supplier_phone) : $bill->supplier_phone }}</td>
                                    <td>
                                        @if($bill->payment_method == 1)
                                          نقدا
                                        @endif
                                        @if($bill->payment_method == 2)
                                         أجل
                                         لمدة {{ $bill->number_of_days }} 
                                        @endif
                                        @if($bill->payment_method == 3)
                                         مدفوعة جزائيا
                                         {{ $bill->partially_price }}
                                         الباقى فى خلال {{  $bill->the_rest_in_through}}
                                        @endif
                                    </td>
                                    <td @if($bill->total == 0) style="color:red" @endif>{{ $bill->total }}</td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $bill->add_by)
                                                  <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                      @if($bill->update_by != 0)
                                      @foreach ($users as $user) 
                                         @if($user->id == $bill->update_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                      @else
                                      <span style="color:red">لم يعديل</span>
                                      @endif
                                    </td>
                                    <td>
                                        @if($bill->bounceds_s == 0) <apsn style="color:red">لا يوجد</apsn> @endif
                                        @if($bill->bounceds_s > 0) 
                                        <a href="{{ route('dashboard.returns.index') }}?search={{$bill->invoice_number}}" target="_blank">
                                            يوجد عدد {{$bill->bounceds_s}} مرتجع
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{ $bill->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        @if (auth()->user()->hasPermission('delete-products'))
                                            <form action="{{ route('dashboard.purchaseInvoices.destroy', $bill->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                 <input type="hidden" name="id" value="{{$bill->id}}">
                                                 <input type="hidden" name="invoice_number" value="{{$bill->invoice_number}}">
                                                <button type="submit" class="btn btn-danger delete btn-sm btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm btn-cs disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                        @if(in_array(Auth::id(),array(1,2)))
                                            <a href="{{ route('dashboard.purchaseInvoices.edTotal', $bill->id) }}" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                            <a href="{{ route('dashboard.reports.reportsPurchaseInvoicesShow', [$bill->id,$bill->invoice_number]) }}" target="_blank" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-check"></i> عرض الفاتورة </a>
                                        @endif
                                        <a href="{{ route('dashboard.purchaseInvoices.purchaseInvoicesReturn', $bill->invoice_number) }}" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-undo"></i> مرتجع</a>
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                        {{ $bills->appends(request()->query())->links() }}
                        
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
