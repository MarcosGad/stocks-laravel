@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>تنفيذ خصم</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تنفيذ خصم</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">تنفيذ خصم</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.orders.postDiscount') }}" method="post">
                        
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>رقم الفاتورة</label>
                            <input type="text" name="orderId" class="form-control" required readonly value="{{ $order->id }}">
                        </div>
                        
                        <div class="form-group">
                            <label>الأجمالى</label>
                            <input type="text" name="total_price" class="form-control" required readonly value="{{ $order->total_price }}">
                        </div>
                        
                        <div class="form-group">
                            <label>مصاريف النقل</label>
                            <input type="text" name="transport" class="form-control" value="{{ $order->transport }}" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label>الخصم</label>
                            <input type="text" name="discount" class="form-control" required value="{{ $order->discount }}" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">ملاحظات عن الفاتورة</label>
                            <textarea class="form-control" id="notes" rows="3" name="notes">{{ $order->notes }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> تنفيذ</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
