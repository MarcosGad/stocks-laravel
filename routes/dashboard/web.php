<?php 

Route::group(['prefix' => LaravelLocalization::setLocale()], function()
{
   
   Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function(){
      
      Route::get('/', 'WelcomeController@index')->name('welcome');
      
      //user routes
      Route::resource('users', 'UserController')->except(['show']);
      Route::get('profile', 'UserController@profile')->name('users.profile');
      Route::post('postProfile', 'UserController@updateProfile')->name('users.postProfile');

      //category routes
      Route::resource('categories', 'CategoryController')->except(['show']);

      //product routes
      Route::resource('products', 'ProductController')->except(['show']);
      Route::get('showSerial/{id}', 'ProductController@showSerial')->name('products.showSerial');

      //client routes
      Route::resource('clients', 'ClientController')->except(['show']);
      Route::resource('clients.orders', 'Client\OrderController')->except(['show']);

      Route::get('status/{id}', 'OrderController@status')->name('orders.status');
      Route::get('attainment/{id}', 'OrderController@attainment')->name('orders.attainment');

      //order routes
      Route::resource('orders', 'OrderController');
      Route::get('/orders/{order}/products', 'OrderController@products')->name('orders.products');
      Route::get('/orderReturn/{orderNumber}', 'ReturnController@orderReturn')->name('orders.orderReturn');
      
      //Purchase Invoices routes
      Route::resource('purchaseInvoices', 'PurchaseInvoicesController')->except(['show']);
      Route::get('/edTotal/{billId}', 'PurchaseInvoicesController@edTotal')->name('purchaseInvoices.edTotal');
      Route::post('/postEdTotal', 'PurchaseInvoicesController@postEdTotal')->name('purchaseInvoices.postEdTotal');
      Route::get('/purchaseInvoicesReturn/{invoiceNumber}', 'ReturnController@purchaseInvoicesReturn')->name('purchaseInvoices.purchaseInvoicesReturn');
      
      //Stores routes
      Route::resource('stores', 'StoresController')->except(['show']);
      
      //Representative routes
      Route::resource('representatives', 'RepresentativeController')->except(['show']);
      
      //Shippingmethods routes
      Route::resource('shippingmethods', 'ShippingmethodsController')->except(['show']);
      
      
      //Return routes
      Route::resource('returns', 'ReturnController');
      Route::get('statusReturn/{id}', 'ReturnController@status')->name('return.status');
      Route::get('returnsShow/{id}', 'ReturnController@returnsShow')->name('return.returnsShow');
      
      //Transfers routes
      Route::get('/transfers/{userId}/{orderId}', 'OrderController@transfersOrder')->name('orders.transfersOrder');
      Route::post('/postTransfersOrder', 'OrderController@postTransfersOrder')->name('orders.postTransfersOrder');
      
      Route::get('/transfersClient/{userId}/{clientId}', 'ClientController@transfersClient')->name('client.transfersClient');
      Route::post('/postTransfersClient', 'ClientController@postTransfersClient')->name('client.postTransfersClient');
      
      Route::get('/transfersStore/{userId}/{storeId}', 'StoresController@transfersStore')->name('store.transfersStore');
      Route::post('/postTransfersStore', 'StoresController@postTransfersStore')->name('store.postTransfersStore');
      
      Route::get('/transfersProduct/{storeId}/{productId}', 'ProductController@transfersProduct')->name('product.transfersProduct');
      Route::post('/postTransfersProduct', 'ProductController@postTransfersProduct')->name('product.postTransfersProduct');
      
      //Discount routes
      Route::get('/discount/{orderId}', 'OrderController@discount')->name('orders.discount');
      Route::post('/postDiscount', 'OrderController@postDiscount')->name('orders.postDiscount');
      
      //Reports routes
      Route::get('/reportsStore', 'ReportsController@store')->name('reports.store');
      Route::get('/reportsClients', 'ReportsController@clients')->name('reports.clients');
      Route::get('/reportsPurchaseInvoices', 'ReportsController@reportsPurchaseInvoices')->name('reports.reportsPurchaseInvoices');
      Route::get('reportsPurchaseInvoicesShow/{id}/{invoiceNumber}', 'ReportsController@reportsPurchaseInvoicesShow')->name('reports.reportsPurchaseInvoicesShow');
      
      Route::get('/paymentNotices', 'ReportsController@paymentNotices')->name('reports.paymentNotices');
      Route::get('paymentNoticesStatus/{id}', 'ReportsController@paymentNoticesStatus')->name('reports.paymentNoticesStatus');
      Route::get('paymentNoticesAttainment/{id}/{backorderId}', 'ReportsController@paymentNoticesAttainment')->name('reports.paymentNoticesAttainment');
      
      Route::get('/profits', 'ReportsController@profits')->name('reports.profits');
      Route::get('/statistics', 'ReportsController@statistics')->name('reports.statistics');
      Route::get('/historydeletes', 'ReportsController@historydeletes')->name('reports.historydeletes');
      Route::get('historydeletesStatus/{id}', 'ReportsController@historydeletesStatus')->name('reports.historydeletesStatus');
      
      Route::get('historyproduct/{id}', 'ReportsController@historyproduct')->name('reports.historyproduct');
      Route::get('historyclient/{id}', 'ReportsController@historyclient')->name('reports.historyclient');
      Route::get('historyDelegate/{id}', 'ReportsController@historyDelegate')->name('reports.historyDelegate');
      Route::get('historyShippingmethod/{id}', 'ReportsController@historyShippingmethod')->name('reports.historyShippingmethod');
      Route::get('historyUser/{id}', 'ReportsController@historyUser')->name('reports.historyUser');
      
   });

});
