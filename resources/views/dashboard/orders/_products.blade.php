<div id="print-area">
<style type="text/css">
	div,
	p,
	a,
	li,
	td {
		-webkit-text-size-adjust: none;
	}
	p {
		padding: 0 !important;
		margin-top: 0 !important;
		margin-right: 0 !important;
		margin-bottom: 0 !important;
		margin- left: 0 !important;
	}

	.visibleMobile {
		display: none;
	}

	.hiddenMobile {
		display: block;
	}
	.bor{
	  border: 1px solid #000;
      padding: 8px;
      width: auto;
      border-radius: 10px;
      width: 300px;
	}
	* {
  box-sizing: border-box;
}

.roww {
  margin-bottom: 8px;
  display: flex;
}

.roww .roww {
  margin: 0;
}

.roww > * {
  padding: 15px;
  flex: 1;
  background-color: rgba(86,61,124,.15);
  border: 1px solid rgba(86,61,124,.2);
}

.one-half {
  width: calc(100% * 1/2);
  flex: none;
}

.two-thirds {
  width: calc(100% * 2/3);
  flex: none;
  padding: 10px;
}
.table {
  margin-bottom: 8px;
}
@media (max-width: 600px) {
    .roww > * {
      width: 100%;
      flex: none;
    }
      
    .roww .roww {
      margin: 15px -15px -15px;
    }
}
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
  padding: 5px;
  margin-bottom: 5px;
}
.brand{
    font-size: 14px;
    color: red;
    font-weight: bold;
}
.v-hide{
   display: none;
}
.m-10{
   margin-top: 10px !important;
}

.container {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
}
.header {
  grid-column: span 12;
}
.navigation-menu {
  grid-column: span 9;
}
.main-content {
  grid-column: span 3;
}
.footer {
  grid-column: span 12;
}

</style>
<style type="text/css" media="print">
@page {
    size: auto;   
    margin: 30px; 
    margin-top:5px;
}
.brand-print{
   font-size: 32px;
}
.font-print{
   font-size: 23px;
}
.font-print-two{
   font-size: 20px;
}
.font-print-three{
  font-size: 26px;
}
.font-print-three-top{
  font-size: 20px;
}
.p-show{
   display: block;
}
.p-padding{
   padding: 5px !important;
}
.p-marg {
  margin-top:20px
}
.sing-c{
    font-size: 22px;
    color: red;
    font-weight: bold;
    float: left;
}
</style>

<div class="roww">
    <div class="two-thirds">
        <p class="brand brand-print"></p>
        <div class="font-print-three-top">
           تاريخ الطلب :@if($order->date) {{$order->date}} @else {{ $order->created_at->toFormattedDateString() }} @endif
		</div>
        </div>
        <div>
    </div>
</div>

<div class="container">
    <div class="header"></div>
    <div class="navigation-menu">
        <p class="brand brand-print">النور تكنولوجى لخدمات الحاسب والأكسسوارت</p>
        <div class="font-print-three">
             رقم الفاتورة : {{ $order->
             id }}
    	 	@php
    	 	$cDate =  \Carbon\Carbon::parse($order->date)->format('d.m.Y');
            $date = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate);
            $daysToAdd = $order->number_of_days;
            $date = $date->addDays($daysToAdd);
            
            $cDate2 =  \Carbon\Carbon::parse($order->date)->format('d.m.Y');
            $date2 = \Carbon\Carbon::createFromFormat('d.m.Y', $cDate2);
            $daysToAdd2 = $order->the_rest_in_through;
            $date2 = $date2->addDays($daysToAdd2);
    	 	@endphp
    	 	
    	 	<br> الأسم : {{ $order->client->name }}
    		<br>رقم التليفون  : {{ is_array($order->client->phone) ? implode('-', $order->client->phone) : $order->client->phone }}
    		<br>العنوان    : {{ $order->client->address }}
    		@if($order->payment_type == 1)
    		   <br>طريقة الدفع نقديا
    		@endif
    		@if($order->payment_type == 2)
    		   <br>طريقة الدفع أجل لمدة {{$order->number_of_days}}يوم
    		   <br> يوم : {{$date->format('Y-m-d') }}
    		@endif
    		@if($order->payment_type == 3)
    		   <br>طريقة الدفع مدفوعة جزائيا {{$order->partially_price}} الباقى فى خلال {{$order->the_rest_in_through	}}يوم
    		   <br> يوم : {{$date2->format('Y-m-d') }}
    		   <br> الباقى من الفاتورة {{($order->total_price + $order->transport - $order->discount) -$order->partially_price}}
    		@endif
    		@php
    	 	$shippingmethods = DB::table('shippingmethods')->get();
    	 	@endphp
    		@if($order->shipping)
    		 <br>شحن عن طريق
    		 @foreach ($shippingmethods as $shippingmethod) 
                @if($shippingmethod->id == $order->shipping)
                   {{$shippingmethod->name }}
                @endif
             @endforeach
    		@endif
    		@php
    	 	$representatives = DB::table('representatives')->get();
    	 	@endphp
    		@if($order->representative_id)
    		<br> شحن عن طريق مندوب :- 
    		 @foreach ($representatives as $representative) 
                @if($representative->id == $order->representative_id)
                   {{$representative->name }}
                @endif
             @endforeach
    		@endif
		</div>
    </div>
    <div class="main-content">
        <img src="{{ asset('dashboard_files/img/user2-160x160.jpg') }}" style="width: 150px;height: 150px;margin-top: 30px;" class="v-hide p-show">
    </div>
    <div class="footer"></div>
</div>
      
<table width="100%" border="0" cellpadding='2' cellspacing="2" align="center" bgcolor="#ffffff" style="padding-top:4px;">
	<tbody>
		<tr>
			<td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 

18px; vertical-align: bottom; text-align: center;">
			</td>
		</tr>
		<tr>
			<td height="2" colspan="0"></td>
		</tr>
	</tbody>
</table>
<table class="table table-hover table-bordered">

<thead>
<tr>
    <th class="font-print">الصنف</th>
    <th class="font-print">كود الصنف</th>
    <th class="font-print">@lang('site.quantity')</th>
    <th class="font-print">سعر الوحدة</th>
    <th class="font-print">@lang('site.price')</th>
</tr>
</thead>

<tbody>
@foreach ($products as $product)
    <tr>
        <td class="font-print">{{ $product->name }}</td>
        <td class="font-print-two">{{ $product->productCode }}</td>
        <td class="font-print-two">{{ $product->pivot->quantity }}</td>
        @php
           $last_price = DB::table('last_price_product_order')->select('last_price')->where('order_id', $order->id)->where('product_id', $product->id)->first();
        @endphp
        <td class="font-print-two">{{ $last_price->last_price }}</td>
        <td class="font-print-two">{{ number_format($product->pivot->quantity * $last_price->last_price, 2) }}</td>
    </tr>
@endforeach
</tbody>
</table>

@if($order->serial_numbers)
<p class="font-print-three p-padding">الأرقام التسلسلية بالترتيب :-</p>
<div class="call">
<p class="font-print-three p-padding">{{$order->serial_numbers}}</p>
</div>
@endif

<p class="sing-c v-hide p-show">امضاء استلام العميل لأصناف الأوردر تفصيلا وعددا <br> ............................................................</p>
@if($order->transport && $order->transport > 0)
<h4 class="bor font-print-three">مصاريف النقل :- <span>{{$order->transport}} </span></h4>
@endif
@if($order->discount && $order->discount > 0)
<h4 class="bor font-print-three"> خصم :- <span>{{$order->discount}} </span></h4>
@endif
<h3 class="bor font-print-three">@lang('site.total') :- <span>{{ number_format($order->total_price + $order->transport - $order->discount, 2) }}</span></h3>
@if($order->notes)
<div class="call p-marg">
<p class="font-print-three p-padding">{{ $order->notes }}</p>
</div>
@endif
<hr style="border-top: 3px solid #eee;" class="v-hide p-show">
<p class="font-print-three v-hide p-show">ضمان اكسسوار المحمول شهر من تاريخ الشراء </p>
<p class="font-print-three v-hide p-show">ضمان اكسسوار الكمبيوتر 6 شهور</p>
<p class="font-print-three v-hide p-show">الايربود والساعات السمارت بدون ضمان</p>
<p class="font-print-three v-hide p-show">ضمان الهاردات اسبواع من تاريخ الشراء</p>
<p class="font-print-three v-hide p-show">ضمان الهارد وير 11 شهر من تاريخ الشراء</p>
<hr style="border-top: 3px solid #eee;" class="v-hide p-show">
<div class="call v-hide p-show">
<p class="font-print-three p-padding">14 مصطفى أبو هيف، باب اللوق، القاهرة ، مصر</p>
</div>
<p class="web v-hide p-show">elnourtech.com</p>
</div>

<button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</button>