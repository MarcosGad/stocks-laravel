@extends('layouts.dashboard.app')

@section('content')

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
                    <div class="col-md-2">
                        <a class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</a>
                    </div>
                </div><!-- end of box header -->
            <div id="print-area">
                <style type="text/css">
                .web{
                    font-size: 20px;
                    float: left;
                    font-weight: bold;
                    color: red;
                    border: 1px solid red;
                    padding: 7px !important;
                    margin-bottom: 6px !important;
                    border-radius: 5px;
                }
                .call{
                  border: 1px solid #000;
                  border-radius: 7px;
                  padding: 10px;
                  margin-bottom: 5px;
                }
                .v-hide{
                   display: none;
                }
                .bor{
            	  border: 1px solid #000;
                  padding: 8px;
                  width: auto;
                  border-radius: 10px;
                  width: 300px;
            	}
                </style>
                <style type="text/css" media="print">
                @page {
                    size: auto;   
                    margin: 30px; 
                    margin-top:5px;
                }
                .font-print{
                   font-size: 18px;
                }
                .font-print-two{
                   font-size: 18px;
                }
                .font-print-three{
                  font-size: 18px;
                }
                .p-show{
                   display: block;
                }
                a[href]:after {
                  content: none !important;
                }
                </style>
                <div class="box-body">
                    <div class="font-print-three call">
                     رقم الفاتورة : {{$bill->invoice_number}}
    			 	@php
    			 	$cDate =  \Carbon\Carbon::parse($bill->date)->format('d.m.Y');
                    $date = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate);
                    $daysToAdd = $bill->number_of_days;
                    $date = $date->addDays($daysToAdd);
                    
                    $cDate2 =  \Carbon\Carbon::parse($bill->date)->format('d.m.Y');
                    $date2 = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate2);
                    $daysToAdd2 = $bill->the_rest_in_through;
                    $date2 = $date2->addDays($daysToAdd2);
    			 	@endphp
    			 	
    			 	<br> الأسم : {{ $bill->supplier_name }}
    				<br>رقم التليفون  : {{ is_array($bill->supplier_phone) ? implode('-', $bill->supplier_phone) : $bill->supplier_phone }}
    				<br>العنوان    : {{ $bill->supplier_address}}
    				<br>تاريخ الطلب :@if($bill->date) {{$bill->date}} @else {{ $bill->created_at->toFormattedDateString() }} @endif
    				@if($bill->payment_method == 1)
    				   <br>طريقة الدفع نقديا
    				@endif
    				@if($bill->payment_method == 2)
    				   <br>طريقة الدفع أجل لمدة {{$bill->number_of_days}}يوم
    				   <br> يوم : {{$date->format('Y-m-d') }}
    				@endif
    				@if($bill->payment_method == 3)
    				   <br>طريقة الدفع مدفوعة جزائيا {{$bill->partially_price}} الباقى فى خلال {{$bill->the_rest_in_through	}}يوم
    				   <br> يوم : {{$date2->format('Y-m-d') }}
    			       <br> الباقى من الفاتورة {{($bill->total_price + $bill->transport - $bill->discount) -$bill->partially_price}}
    				@endif
    				</div>
    				
    				<table class="table table-hover">

                            <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>كود الصنف</th>
                                <th>@lang('site.quantity')</th>
                                <th>سعر الوحدة</th>
                                <th>@lang('site.price')</th>
                                <th>الأرقام التسلسلية</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                               @foreach ($products as $product)
                                    <tr>
                                        <td class="font-print"><a href="{{ route('dashboard.reports.historyproduct', $product->id) }}" target="_blank">{{ $product->name }}</a></td>
                                        <td class="font-print-two">{{ $product->productCode }}</td>
                                        <td class="font-print-two">{{ $product->real_stock }}</td>
                                        <td class="font-print-two">{{ $product->purchase_price }}</td>
                                        <td class="font-print-two">{{ number_format($product->real_stock * $product->purchase_price, 2) }}</td>
                                        <td>
                                            @foreach ($productsAll as $productAll)
                                              @if($productAll->id == $product->id)
                                                    @php
                                                      $arr = $product->serial_numbers;
                                                      if(is_array($arr)){
                                                        if(!empty($arr)) {
                                                            if(array_search(null, $arr) !== 0)
                                                            {
                                                               foreach($arr as $serial)
                                                               {
                                                                  if($serial != '')
                                                                  {
                                                                    echo "<p>".$serial."</p>";
                                                                  }
                                                               }
                                                            }else{
                                                                 echo "<span style='color:red'>لا يوجد</span>";
                                                            }
                                                        }elseif(empty($arr)) {
                                                          echo "<span style='color:red'>لا يوجد</span>";
                                                        }
                                                      }else{
                                                         echo $arr;
                                                      }
                                                    @endphp
                                              @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        <h3 class="bor font-print-three">@lang('site.total') :- <span>{{$bill->total}}</span></h3>
                        <!--<div class="call v-hide p-show">-->
                        <!--<p class="font-print-three">فى حالة وجود استفسار برجاء التواصل مع خدمة العملاء :- 01050678199</p>-->
                        <!--<p class="font-print-three">شكرا لتعاملكم وثقتكم فى شركة النور </p>-->
                        <!--</div>-->
                        <!--<div class="call v-hide p-show">-->
                        <!--<p class="font-print-three">14 مصطفى أبو هيف، باب اللوق، القاهرة ، مصر</p>-->
                        <!--</div>-->
                        <!--<p class="web v-hide p-show">elnourtech.com</p>-->
                        <!--</div>-->
       
                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection