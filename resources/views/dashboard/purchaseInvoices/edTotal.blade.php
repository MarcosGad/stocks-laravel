@extends('layouts.dashboard.app')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
    <style>
        #datepicker > span:hover{cursor: pointer;}
        .datepicker.dropdown-menu{
            right: 80%;
            width: 220px;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>تعديل فاتورة الشراء</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تعديل فاتورة الشراء</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">تعديل الفاتورة</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.purchaseInvoices.postEdTotal') }}" method="post">
                        
                        {{ csrf_field() }}
                        
                        <input type="hidden" name="billId" value="{{ $bill->id }}">
                        
                        <div class="form-group">
                            <div id="datepicker" class="input-group date" data-date-format="yyyy-mm-dd">
                                <input class="form-control" type="text" readonly name="date" value="{{$bill->date}}"/>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>رقم الفاتورة</label>
                            <input type="text" name="invoice_number" required class="form-control" readonly value="{{ $bill->invoice_number }}">
                        </div>
                        
                        <div class="form-group">
                            <label>الأجمالى</label>
                            <input type="text" name="total" required class="form-control" readonly value="{{ $bill->total }}">
                        </div>
                        
                        <div class="form-group">
                            <label>أسم الموارد</label>
                            <input type="text" name="supplier_name" required class="form-control" value="{{ $bill->supplier_name }}">
                        </div>
                        
                        <div class="form-group">
                            <label>أسم مسؤال الشراء</label>
                            <input type="text" name="sales_officer" required class="form-control" value="{{ $bill->sales_officer }}">
                        </div>
                        
                        <div class="form-group">
                            <label>عنوان الموارد</label>
                            <input type="text" name="supplier_address" required class="form-control" value="{{ $bill->supplier_address }}">
                        </div>
                        
                        @for ($i = 0; $i < 2; $i++)
                            <div class="form-group">
                                <label>@lang('site.phone')</label>
                                <input type="text" name="supplier_phone[]" class="form-control" value="{{ $bill->supplier_phone[$i] ?? '' }}">
                            </div>
                        @endfor
                        
                        @if($bill->payment_method == 1)
                            <div class="form-group">
                                <label>طريقة الدفع</label>
                               <select name="payment_method" class="form-control" id="payment_methodd">
                                   <option value="1">نقديا</option>
                                   <option value="2">أجل</option>
                                   <option value="3">مدفوعة جزائيا</option>
                               </select>
                            </div>
                        
                            <div class="form-group" id="number_of_days" hidden="hidden">
                                <label>مدة الاجل بالأيام</label>
                                <input type="number" name="number_of_days" class="form-control">
                            </div>
                            
                            <div class="form-group partially_price" hidden="hidden">
                                <label>المبلغ المدفوع</label>
                                <input type="number" name="partially_price" class="form-control">
                            </div>
                            <div class="form-group partially_price" hidden="hidden">
                                <label>الباقى فى خلال بالأيام</label>
                                <input type="number" name="the_rest_in_through" class="form-control">
                            </div>
        				@endif
        				@if($bill->payment_method == 2)
        				    <div class="form-group">
                                <label>طريقة الدفع</label>
                               <select name="payment_method" class="form-control" id="payment_methodd">
                                   <option value="1">نقديا</option>
                                   <option value="2" selected>أجل</option>
                                   <option value="3">مدفوعة جزائيا</option>
                               </select>
                            </div>
                        
                            <div class="form-group" id="number_of_days">
                                <label>مدة الاجل بالأيام</label>
                                <input type="number" name="number_of_days" class="form-control" value="{{$bill->number_of_days}}">
                            </div>
                            
                            <div class="form-group partially_price" hidden="hidden">
                                <label>المبلغ المدفوع</label>
                                <input type="number" name="partially_price" class="form-control">
                            </div>
                            <div class="form-group partially_price" hidden="hidden">
                                <label>الباقى فى خلال بالأيام</label>
                                <input type="number" name="the_rest_in_through" class="form-control">
                            </div>
        				@endif
        				@if($bill->payment_method == 3)
        				    <div class="form-group">
                                <label>طريقة الدفع</label>
                               <select name="payment_method" class="form-control" id="payment_methodd">
                                   <option value="1">نقديا</option>
                                   <option value="2">أجل</option>
                                   <option value="3" selected>مدفوعة جزائيا</option>
                               </select>
                            </div>
                        
                            <div class="form-group" id="number_of_days" hidden="hidden">
                                <label>مدة الاجل بالأيام</label>
                                <input type="number" name="number_of_days" class="form-control">
                            </div>
                            
                            <div class="form-group partially_price">
                                <label>المبلغ المدفوع</label>
                                <input type="number" name="partially_price" class="form-control" value="{{$bill->partially_price}}">
                            </div>
                            <div class="form-group partially_price">
                                <label>الباقى فى خلال بالأيام</label>
                                <input type="number" name="the_rest_in_through" class="form-control" value="{{$bill->the_rest_in_through}}">
                            </div>
        				@endif
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>تعديل</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script>
$(function () {
  $("#datepicker").datepicker({ 
        autoclose: true, 
        todayHighlight: true
  });
});
</script>
@endsection