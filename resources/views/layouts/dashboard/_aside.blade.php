<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('uploads/user_images') }}/{{ auth()->user()->image }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i><span class="net" style="font-size: 13px;"></span></a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">

            <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-home"></i><span>@lang('site.dashboard')</span></a></li>
            
            @if (auth()->user()->hasPermission('read-categories'))
                <li><a href="{{ route('dashboard.categories.index') }}"><i class="fa fa-th"></i><span>@lang('site.categories')</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read-products'))
                <li><a href="{{ route('dashboard.products.index') }}"><i class="fa fa-bar-chart"></i><span>@lang('site.products')</span></a></li>
                <li><a href="{{ route('dashboard.purchaseInvoices.index') }}"><i class="fa fa-calculator"></i><span>فواتير الشراء</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read-clients'))
                <li><a href="{{ route('dashboard.clients.index') }}"><i class="fa fa-handshake-o"></i><span>@lang('site.clients')</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read-orders'))
                <li><a href="{{ route('dashboard.orders.index') }}"><i class="fa fa-cart-arrow-down"></i><span>@lang('site.orders')</span></a></li>
                <li><a href="{{ route('dashboard.returns.index') }}"><i class="fa fa-undo"></i><span>المرتجعات</span></a></li>
            @endif
            
            @if (auth()->user()->hasPermission('read-users'))
            <li><a href="{{ route('dashboard.stores.index') }}"><i class="fa fa-truck"></i><span>المخازن</span></a></li>
            <li><a href="{{ route('dashboard.representatives.index') }}"><i class="fa fa-user"></i><span>المناديب</span></a></li>
            <li><a href="{{ route('dashboard.shippingmethods.index') }}"><i class="fa fa-location-arrow"></i><span>طرق الشحن</span></a></li>
            <li><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-users"></i><span>@lang('site.users')</span></a></li>
            @endif
        
            @if (auth()->user()->hasPermission('read-products'))
                @php
                  $backorder = App\Backorder::where('date','<=',\Carbon\Carbon::today()->format('Y-m-d'))->where('status',0)->count();
                @endphp
                <style>
                    .badge{
                        background-color: #dd4b39;
                        font-size: 14px;
                        margin-top: 0 !important;
                    }
                </style>
                <li><a href="{{ route('dashboard.reports.paymentNotices') }}"><i class="fa fa-bell"></i><span> أشعارات المدفوعات <span class="badge badge-danger">{{$backorder}}</span></span></a></li>
                <li class="dropdown">
                 <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-archive"></i><span>التقارير</span> <span class="caret"></span></a>
                 <ul class="dropdown-menu sidebar-menu" role="menu" style="width: 100%;border-radius: 0;border: #000;background-color:#1e282c;">
                    <li><a href="{{ route('dashboard.reports.store') }}"><span>المخازن</span></a></li>
                    <li><a href="{{ route('dashboard.reports.clients') }}"><span>العملاء والطلبات</span></a></li>
                    <li><a href="{{ route('dashboard.reports.reportsPurchaseInvoices') }}"><span>فوااتير الشراء</span></a>
                    @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                    <li><a href="{{ route('dashboard.reports.profits') }}"><span>الأرباح اليومية والشهرية</span></a>
                    <li><a href="{{ route('dashboard.reports.historydeletes') }}"><span>عماليات الحذف</span></a>
                    <li><a href="{{ route('dashboard.reports.statistics') }}"><span>الأحصائيات</span></a>
                    @endif
                 </ul>
                </li>
            @endif
            
        </ul>

    </section>

</aside>

