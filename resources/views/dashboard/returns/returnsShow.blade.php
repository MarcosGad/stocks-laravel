@extends('layouts.dashboard.app')
@section('content')

<div class="content-wrapper">

        <section class="content-header">
            <h1>المرتجعات</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">المرتجعات</li>
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
                .mg{
                   margin: 0;
                }
                a[href]:after {
                  content: none !important;
                }
                </style>
                <div class="box-body">
                   <div class="font-print-three call">
                     رقم المرتجع : {{$bounced->id}}
                     <br> رقم الفاتورة :   @if($bounced->return_type == 1)  <a href="{{ route('dashboard.orders.index') }}?search=&id={{$bounced->bill_number_o}}&created_at=" target="_blank">{{$bounced->bill_number_o}}</a> @endif
                                       @if($bounced->return_type == 2) <a href="{{ route('dashboard.purchaseInvoices.index') }}?search={{$bounced->bill_number_b}}" target="_blank">{{$bounced->bill_number_b}}</a> @endif
                     <br> نوع المرتجع : @if($bounced->return_type == 1)من عميل@endif
                                     @if($bounced->return_type == 2)الى مورد@endif
                     <br> تاريخ المرتجع :{{ $bounced->created_at->toFormattedDateString() }}
    				 <br> عدد المرتجعات على هذه الفاتورة : {{$bounceds_s}}
    				</div>
    				
    					
    				<table class="table table-hover">

                            <thead>
                            <tr>
                                <th>الصنف وكود الصنف</th>
                                <th>@lang('site.quantity')</th>
                            </tr>
                            </thead>
                             
                            <tbody>
                                <tr>
                                <td class="font-print">
                                    @php
                                     if(is_array($bounced->products)){
                                         echo implode( "<hr/>", $bounced->products );
                                     }else{
                                         echo $bounced->products;
                                     }
                                    @endphp
                                </td>
                                <td class="font-print-two">
                                    @php
                                     if(is_array($bounced->quantity)){
                                         echo implode( "<hr/>", $bounced->quantity );
                                     }else{
                                         echo $bounced->quantity;
                                     }
                                    @endphp
                                </td>
                                </tr>
                            </tbody>

                        </table><!-- end of table -->
                        @if($bounced->serial_numbers)
                        <div class="call" style="margin-top: 12px;">
                        <p class="font-print-three p-padding">{{ is_array($bounced->serial_numbers) ? implode('-', $bounced->serial_numbers) : $bounced->serial_numbers }}</p>
                        </div>
                        @endif
                        @if($bounced->notes)
                        <div class="call">
                        <p class="font-print-three mg">{{$bounced->notes}}</p>
                        </div>
                        @endif
                        <div class="call v-hide p-show">
                        <p class="font-print-three mg">هذا المرتجع جزء من الفاتورة الموضح رقمها فى الأعلى ويتم خصم ثمن المنتجات المرجع من الفاتورة </p>
                        </div>
                        <div class="call v-hide p-show">
                        <p class="font-print-three mg">14 مصطفى أبو هيف، باب اللوق، القاهرة ، مصر</p>
                        </div>
                        <p class="web v-hide p-show">elnourtech.com</p>

                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection