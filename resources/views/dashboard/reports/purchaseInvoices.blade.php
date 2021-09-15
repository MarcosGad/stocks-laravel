@extends('layouts.dashboard.app')

@section('content')
<style>
    #val{
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

            <h1>فواتير الشراء</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">فواتير الشراء</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.reports.reportsPurchaseInvoices') }}" method="get">

                        <div class="row">

                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="أسم المورد أو رقم الفاتورة" value="{{ request()->search }}">
                            </div>
                            
                            <div class="col-md-3">
                                <select name="date" class="form-control">
                                    <option value="">جميع التواريخ</option>
                                    @foreach ($billsDate as $billDate)
                                        <option value="{{ \Carbon\Carbon::parse($billDate[0]->date)->format('Y-m-d') }}" {{ request()->date == \Carbon\Carbon::parse($billDate[0]->date)->format('Y-m-d') ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($billDate[0]->date)->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select name="add_by" class="form-control">
                                    <option value="">كل المستخدمين</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request()->add_by == $user->id ? 'selected' : '' }}>{{ $user->email }}</option>
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
                .btn-primary{
                  display: none;
                }
                a[href]:after {
                  content: none !important;
                }
                #val{
                    border: 1px solid #222d32;
                    padding: 8px;
                    display: inline-block;
                    border-radius: 10px;
                    margin-top: 15PX;
                    font-size: 15px;
                }
                </style>
                <div class="box-body">

                    @if ($bills->count() > 0)

                        <table class="table table-hover" id="table">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>أسم المورد</th>
                                <th>مسؤال المبيعات</th>
                                <th>الفاتورة</th>
                                <th>طريقة الدفع</th>
                                <th>الأجمالى</th>
                                <th>الأضافة</th>
                                @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                <th></th>
                                @endif
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($bills as $index=>$bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="display: none;" id="loop">{{ $bill->total }}</td>
                                    <td>{{ $bill->supplier_name }}</td>
                                    <td>@if($bill->sales_officer) {{ $bill->sales_officer }} @else <p style="color:red">لا يوجد</p> @endif</td>
                                    <td>{{ $bill->invoice_number }}</td>
                                    <td>
                                        @if($bill->payment_method == 1)
                                          <span style="color:green">نقدا</span>
                                        @endif
                                        @if($bill->payment_method == 2)
                                         أجل
                                         لمدة {{ $bill->number_of_days }} يوم
                                        @endif
                                        @if($bill->payment_method == 3)
                                         مدفوعة جزائيا
                                         {{ $bill->partially_price }}
                                         الباقى فى خلال {{  $bill->the_rest_in_through}}يوم 
                                        @endif
                                    </td>
                                    <td>{{ number_format($bill->total, 2) }}</td>
                                    <td>{{ $bill->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                            <a href="{{ route('dashboard.reports.reportsPurchaseInvoicesShow', [$bill->id,$bill->invoice_number]) }}" target="_blank" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-check"></i> عرض الفاتورة </a>
                                        @endif
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        <span id="val"></span>
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    <script type="text/javascript">
        $(function() {
           var TotalValue = 0;
           $("tr #loop").each(function(index,value){
             currentRow = parseFloat($(this).text());
             TotalValue += currentRow
           });
           document.getElementById("val").innerHTML = "الأجمالى   = " + TotalValue.toFixed(2);
        });
    </script>

@endsection