$(document).ready(function () {
    
    $(".sale_price").keyup(function() {
        var salePrice = $(this).val();
        var productId = $(this).data('idp');
        $('#product-' + productId).data('price',salePrice);
        if (salePrice > 0) {
            $('#product-' + productId).removeClass('disabled');
        } else {
            $('#product-' + productId).addClass('disabled');
        }
    });
    
/**********************************************************************************************************/
    
    //add product btn
    $('.add-product-btn').on('click', function (e) {
        e.preventDefault();
        var name = $(this).data('name'); 
        var id = $(this).data('id');
        var price = $(this).data('price');
        var cprice = $(this).data('cprice');
        var stock = $(this).data('stock');

        $(this).addClass('disabled');
        $('#product-' + id).addClass('disabled');

        var html =
            `<tr>
                <td>${name}</td>
                <td><input type="number" name="products[${id}][quantity]" data-price="${price}" data-cprice="${cprice}" class="form-control input-sm product-quantity" min="1" max="${stock}" value="1"></td>
                <td>${price}</td>        
                <td class="product-price">${price}</td>  
                <td class="product-price-c hidden">${cprice}</td>   
                <input type="hidden" name="products[${id}][price]" value="${price}">
                <td><button class="btn btn-danger btn-sm remove-product-btn" data-id="${id}"><span class="fa fa-trash"></span></button></td>
            </tr>`;

         $('.order-list').append(html);
         $('#product-' + id).css("display", "none");
         
        //to calculate total price
        calculateTotal();
        calculateTotalC();
        
        //Edit Order Btn
        $('.edit-order').removeClass('disabled');
        $('.total-price-edit').addClass('hidden');
        $('.total-price-new').removeClass('hidden');
        
    });
    
/**********************************************************************************************************/

    //disabled btn
    $('body').on('click', '.disabled', function(e) {
        e.preventDefault();
    });//end of disabled

/**********************************************************************************************************/ 

    //remove product btn
    $('body').on('click', '.remove-product-btn', function(e) {

        e.preventDefault();
        var id = $(this).data('id');

        $(this).closest('tr').remove();
        $('#product-' + id).removeClass('disabled');
        $('#product-' + id).css("display", "inherit");
        if($('#product-' + id).data('price') == 0 || isNaN(parseFloat($('#product-' + id).data('price')))){
            $('#product-' + id).addClass('disabled');
        }
        //to calculate total price
        calculateTotal();
        calculateTotalC();

    });//end of remove product btn
    
    $('body').on('click', '.remove-product-btn-two', function(e) {

        e.preventDefault();
        var id = $(this).data('id');
        var dataq = $(this).data('quantity');
        
        $(this).closest('tr').remove();
        $('#product-' + id).removeClass('disabled');
        $('#product-' + id).css("display", "inherit");
        var newStock = $('#product-' + id).data('stock') + dataq
        $('#product-' + id).data('stock', newStock);
        $('#old-stock-' + id).remove();
        $('#stock-'+ id).append(newStock);
        if($('#product-' + id).data('price') == 0 || isNaN(parseFloat($('#product-' + id).data('price')))){
            $('#product-' + id).addClass('disabled');
        }
        //to calculate total price
        calculateTotal();
        calculateTotalC();
        
        // Edit Order Btn
        $('.edit-order').removeClass('disabled');
        $('.total-price-edit').addClass('hidden');
        $('.total-price-new').removeClass('hidden');

    });
    

/**********************************************************************************************************/ 

     //change product quantity
     $('body').on('keyup change', '.product-quantity', function() {

        var quantity = Number($(this).val()); 
        //var unitPrice = parseFloat($(this).data('price').replace(/,/g, '')); 
        //var unitPriceC = parseFloat($(this).data('cprice').replace(/,/g, '')); 
        var unitPrice = parseFloat($(this).data('price')); 
        var unitPriceC = parseFloat($(this).data('cprice')); 
        
        console.log(quantity);
        console.log(unitPrice);
        
        //$(this).closest('tr').find('.product-price').html($.number(quantity * unitPrice, 2));
        //$(this).closest('tr').find('.product-price-c').html($.number(quantity * unitPriceC, 2));
        $(this).closest('tr').find('.product-price').html(quantity * unitPrice);
        $(this).closest('tr').find('.product-price-c').html(quantity * unitPriceC);
        calculateTotal();
        calculateTotalC();
        // Edit Order Btn
        $('.edit-order').removeClass('disabled');
        $('.total-price-edit').addClass('hidden');
        $('.total-price-new').removeClass('hidden');

    });//end of product quantity change



/**********************************************************************************************************/ 


    //list all order products
    $('.order-products').on('click', function(e) {

        e.preventDefault();

        $('#loading').css('display', 'flex');
        $('#order-product-list').css('display', 'none');
        
        var url = $(this).data('url');
        var method = $(this).data('method');
        $.ajax({
            url: url,
            method: method,
            success: function(data) {

                $('#loading').css('display', 'none');
                $('#order-product-list').css('display', 'block');
                $('#order-product-list').empty();
                $('#order-product-list').append(data);

            }
        })

    });//end of order products click

    //print order
    $(document).on('click', '.print-btn', function() {

        $('#print-area').printThis();

    });//end of click function
    
});//end of document ready


/**********************************************************************************************************/ 


//calculate the total
function calculateTotal() {

    var price = 0;

    $('.order-list .product-price').each(function(index) {
        
           //price += parseFloat($(this).html().replace(/,/g, ''));
           price += parseFloat($(this).html());


    });//end of product price

    $('.total-price').html($.number(price, 2));

}//end of calculate total



//calculate the total cost
function calculateTotalC() {

    var cprice = 0;
    $('.order-list .product-price-c').each(function(index) {
         //cprice += parseFloat($(this).html().replace(/,/g, ''));
           cprice += parseFloat($(this).html());
    });//end of product price
    var tcprice = cprice * 5/100;
    var ttcprice = cprice + tcprice

    var price = 0;
    $('.order-list .product-price').each(function(index) {
    // price += parseFloat($(this).html().replace(/,/g, ''));
       price += parseFloat($(this).html());
    });//end of product price
    
    if (price > 0) {
        if (price > ttcprice) {
            $('#add-order-form-btn').removeClass('disabled');
            $('#w-order').addClass('hidden');
        } else {
            $('#add-order-form-btn').addClass('disabled');
            $('#w-order').removeClass('hidden');
        }
    }else{
       $('#w-order').addClass('hidden');
       $('#add-order-form-btn').addClass('disabled');
    }
    
}//end of calculate total

/*******************************************************************************************************/

$("#payment_methodd").change(function () {
  var selected_option = $('#payment_methodd').val();

  if (selected_option === '2') {
    $('#number_of_days').show();
  }
  if (selected_option != '2') {
    $("#number_of_days").hide();
  }
  
  if (selected_option === '3') {
    $('.partially_price').show();
  }
  if (selected_option != '3') {
    $(".partially_price").hide();
  }
  
})

$("#return_type").change(function () {
  var selected_option = $('#return_type').val();

  if (selected_option === '1') {
    $('#bill_number_one').show();
  }
  if (selected_option != '1') {
    $("#bill_number_one").hide();
  }
  
  if (selected_option === '2') {
    $('#bill_number_two').show();
  }
  if (selected_option != '2') {
    $("#bill_number_two").hide();
  }
  
})

/********************************************************************************************************/
$('.search_field').on('keyup', function() {
  var value = $(this).val();
  var patt = new RegExp(value, "i");
  
  console.log(value);
  
  $('.fid_table').find('tr').each(function() {
    var $table = $(this);
    
    if (!($table.find('td').text().search(patt) >= 0)) {
      $table.not('.t_head').hide();
    }
    if (($table.find('td').text().search(patt) >= 0)) {
      $(this).show();
    }
    
  });
 
});

/**********************************************************************************************************/
$(".edit_order_input").keyup(function() {
    // Edit Order Btn
    $('.edit-order').removeClass('disabled');
});
$(".edit_order_select").change(function () {
    // Edit Order Btn
    $('.edit-order').removeClass('disabled');
});
