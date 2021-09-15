@extends('layouts.dashboard.app')

@section('content')
<style>
    .serial{
      padding: 10px;
      border: 1px solid #000;
      border-radius: 8px;
    }
    .serial p:last-child {
      margin-bottom: 0;
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">

            <h1>عرض الأرقام التسلسلية</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">عرض الأرقام التسلسلية</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">
                @foreach ($products as $product)
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-10">
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</a>
                        </div>
                    </div>
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
                    </style>

                <div class="box-body">
                    <p>أسم المنتج :- <a href="{{ route('dashboard.reports.historyproduct', $product->id) }}" target="_blank">{{ $product->name }}</a></p>
                    <p>كود المنتج :- {{ $product->productCode }}</p>
                    <div class="serial">
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
                    </div>
                </div><!-- end of box body -->
                @endforeach
            </div><!-- end of box -->
            </div>
        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection