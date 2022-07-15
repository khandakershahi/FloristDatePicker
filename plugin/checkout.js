jQuery(function($) {
$('#wpmc-next').attr('id', 'wpmc-nexte');
$('#wpmc-prev').attr('id', 'wpmc-prevv');
$('#wpmc-prevv').hide();
$("#wpmc-prevv").on('click',function(){
        $("html").animate({ scrollTop: 0 }, "slow");
    $("div.woocommerce-error").hide();
    $("tr.woocommerce-error-row").hide();
    var billing_class=$('.wpmc-step-item.wpmc-step-billing,.wpmc-tab-item.wpmc-billing').hasClass('current');
    var shipping_class=$('.wpmc-step-item.wpmc-step-shipping,.wpmc-tab-item.wpmc-shipping').hasClass('current');
    var review_class=$('.wpmc-step-item.wpmc-step-review,.wpmc-tab-item.wpmc-review').hasClass('current');
    var payment_class=$('.wpmc-step-item.wpmc-step-payment,.wpmc-tab-item.wpmc-payment').hasClass('current');
    if (shipping_class) {
        $('#wpmc-prevv').hide();
        $('.wpmc-step-billing,.wpmc-billing').addClass('current')
        $('.wpmc-step-shipping,.wpmc-shipping').removeClass('current')
    } 
    if (review_class) {
        $('.wpmc-step-shipping,.wpmc-shipping').addClass('current')
        $('.wpmc-step-review,.wpmc-review').removeClass('current')
    } 
    if (payment_class) {
        $('#wpmc-nexte').show();
        $('.wpmc-step-review,.wpmc-review').addClass('current')
        $('.wpmc-step-payment,.wpmc-payment').removeClass('current')
    }

})

$('#wpmc-nexte').click(function(){
     $("html").animate({ scrollTop: 0 }, "slow");
    $("div.woocommerce-error").hide();
    $("tr.woocommerce-error-row").hide();
    var billing_class=$('.wpmc-step-item.wpmc-step-billing,.wpmc-tab-item.wpmc-billing').hasClass('current');
    var shipping_class=$('.wpmc-step-item.wpmc-step-shipping,.wpmc-tab-item.wpmc-shipping').hasClass('current');
    var review_class=$('.wpmc-step-item.wpmc-step-review,.wpmc-tab-item.wpmc-review').hasClass('current');
    var payment_class=$('.wpmc-step-item.wpmc-step-payment,.wpmc-tab-item.wpmc-payment').hasClass('current');
  
    if (billing_class) {
         //check billing field exist
        var check_billing_first_name=$("#billing_first_name").length && $("label[for='billing_first_name'] .required");
        var check_billing_last_name=$("#billing_last_name").length && $("label[for='billing_last_name'] .required");
        var check_billing_address_1=$("#billing_address_1").length && $("label[for='billing_address_1'] .required");
        var check_billing_city=$("#billing_city").length && $("label[for='billing_city'] .required");
        var check_billing_postcode=$("#billing_postcode").length && $("label[for='billing_postcode'] .required");
        var check_billing_phone=$("#billing_phone").length && $("label[for='billing_phone'] .required");
        var check_billing_email=$("#billing_email").length && $("label[for='billing_email'] .required");

         //billing field
         if (check_billing_first_name) {
            var billing_first_name=$("#billing_first_name").val();
         }else{
            var billing_first_name="empty";
         }
         if (check_billing_last_name) {
            var billing_last_name=$("#billing_last_name").val();
         }else{
            var billing_last_name="empty";
         }
         if (check_billing_address_1) {
            var billing_address_1=$("#billing_address_1").val();
         }else{
            var billing_address_1="empty";
         }
         if (check_billing_city) {
            var billing_city=$("#billing_city").val();
         }else{
            var billing_city="empty";
         }
         if (check_billing_postcode) {
            var billing_postcode=$("#billing_postcode").val();
         }else{
            var billing_postcode="empty";
         }
         if (check_billing_phone) {
            var billing_phone=$("#billing_phone").val();
         }else{
            var billing_phone="0000000000";
         }

         if (check_billing_email) {
            var billing_email=$("#billing_email").val();
         }else{
            var billing_email="test@test.com";
         }
     
        //alert(billing_first_name)
        if ( billing_first_name==''||billing_last_name==''||billing_address_1==''||billing_city==''||billing_postcode==''||billing_phone==''||billing_email=='') {
                if (billing_first_name=='') {
                  $('#billing_first_name').after('<div class="woocommerce-error" role="alert">Billing First name is required.</div>');
                  
                }
                if(billing_last_name==''){
                    $('#billing_last_name').after('<div class="woocommerce-error" role="alert">Billing Last name is required.</div>');

                }
                if(billing_address_1==''){
                    $('#billing_address_1').after('<div class="woocommerce-error" role="alert">Billing number and street name is required.</div>');

                }
                if(billing_city==''){
                    $('#billing_city').after('<div class="woocommerce-error" role="alert">Billing city is required.</div>');

                }
                if(billing_postcode==''){
                    $('#billing_postcode').after('<div class="woocommerce-error" role="alert">Billing postcode is required.</div>');

                }
                if(billing_phone==''){
                    $('#billing_phone').after('<div class="woocommerce-error" role="alert">Billing telephone is required.</div>');

                }
                if(billing_email==''){
                    $('#billing_email').after('<div class="woocommerce-error" role="alert">Billing email address is required.</div>');

                }
                
        }else {
            
           var email=validateEmail(billing_email);
           var phone=validatePhone(billing_phone);
           if (email==true && phone==true) {
            $('.wpmc-step-billing,.wpmc-billing').removeClass('current')
            $('.wpmc-step-shipping,.wpmc-shipping').addClass('current')
            $('#wpmc-prevv').show()
           }else{

            if (email==false) {
                $('#billing_email').after('<div class="woocommerce-error" role="alert">Please enter a valid email address.</div>');
            }
            if (phone==false) {
                
                $('#billing_phone').after('<div class="woocommerce-error" role="alert">Please enter valid telephone numbers.</div>');
            }
                return false;
           }

        }
        
    }
    if (shipping_class) {
        var shipCheckbox=$("#ship-to-different-address-checkbox").is(':checked')
        if (shipCheckbox) {
             //check shipping field exist
        var check_shipping_first_name=$("#shipping_first_name").length && $("label[for='shipping_first_name'] .required");
        var check_shipping_last_name=$("#shipping_last_name").length && $("label[for='shipping_last_name'] .required");
        var check_shipping_address_1=$("#shipping_address_1").length && $("label[for='shipping_address_1'] .required");
        var check_shipping_city=$("#shipping_city").length && $("label[for='shipping_city'] .required");
        var check_shipping_postcode=$("#shipping_postcode").length && $("label[for='shipping_postcode'] .required");
        var check_shipping_phone=$("#shipping_phone").length && $("label[for='shipping_phone'] .required");     
        var check_shipping_email=$("#shipping_email").length && $("label[for='shipping_email'] .required");     
        //shipping field
        if (check_shipping_first_name) {
            var shipping_first_name=$("#shipping_first_name").val();
         }else{
            var shipping_first_name="empty";
         }
         if (check_shipping_last_name) {
            var shipping_last_name=$("#shipping_last_name").val();
         }else{
            var shipping_last_name="empty";
         }
         if (check_shipping_address_1) {
            var shipping_address_1=$("#shipping_address_1").val();
         }else{
            var shipping_address_1="empty";
         }
         if (check_shipping_city) {
            var shipping_city=$("#shipping_city").val();
         }else{
            var shipping_city="empty";
         }
         if (check_shipping_postcode) {
            var shipping_postcode=$("#shipping_postcode").val();
         }else{
            var shipping_postcode="empty";
         }
         if (check_shipping_phone) {
            var shipping_phone=$("#shipping_phone").val();
         }else{
            var shipping_phone="0000000000";
         }

         if (check_shipping_email) {
            var shipping_email=$("#shipping_email").val();
         }else{
            var shipping_email="test@test.com";
         }
        
        //alert(shipping_first_name)
        if (shipping_first_name==''||shipping_last_name==''||shipping_address_1==''||shipping_city==''||shipping_postcode==''|| shipping_phone=='' || shipping_email=='') {
                if (shipping_first_name=='') {
                  $('#shipping_first_name').after('<div class="woocommerce-error" role="alert">Delivery First name is required.</div>');
                  
                }
                if(shipping_last_name==''){
                    $('#shipping_last_name').after('<div class="woocommerce-error" role="alert">Delivery Last name is required.</div>');

                }
                if(shipping_address_1==''){
                    $('#shipping_address_1').after('<div class="woocommerce-error" role="alert">Delivery number and street name is required.</div>');

                }
                if(shipping_city==''){
                    $('#shipping_city').after('<div class="woocommerce-error" role="alert">Delivery city is required.</div>');

                }
                if(shipping_postcode==''){
                    $('#shipping_postcode').after('<div class="woocommerce-error" role="alert">Delivery postcode is required.</div>');

                }
                if(shipping_phone==''){
                    $('#shipping_phone').after('<div class="woocommerce-error" role="alert">Delivery telephone is required.</div>');

                } 
                if(shipping_email==''){
                    $('#shipping_email').after('<div class="woocommerce-error" role="alert">Delivery email address is required.</div>');

                }
                
                
        }else {
           var phone=validatePhone(shipping_phone);
           if (phone==true) {
            $('.wpmc-step-shipping,.wpmc-shipping').removeClass('current')
            $('.wpmc-step-review,.wpmc-review').addClass('current')

           }else{
            if (email==false) {
                $('#shipping_email').after('<div class="woocommerce-error" role="alert">Please enter a valid email address.</div>');
            }
            if (phone==false) {
                    //alert('done')
                $('#shipping_phone').after('<div class="woocommerce-error" role="alert">Please enter valid telephone numbers.</div>');
            }
                return false;
           }

        }
        
        }else{
            $('.wpmc-step-shipping,.wpmc-shipping').removeClass('current')
            $('.wpmc-step-review,.wpmc-review').addClass('current')
        }
        
    }
    if (review_class) {
        if ($('#shipping_method').length){
                $('.wpmc-step-review,.wpmc-review').removeClass('current')
                $('.wpmc-step-payment,.wpmc-payment').addClass('current')
                $('#wpmc-nexte').hide()
                $('#wpmc-prev').addClass('button_show')
        }else{
                $('tr.woocommerce-shipping-totals.shipping').after('<tr class="woocommerce-error-row"><th></th><td data-title="Delivery"><div class="woocommerce-error" role="alert">No delivery method has been selected. Please check your address, or contact us if you need help.</div></td></tr>');
                return false;
            }
    }
       
    });
   
    
    function validateEmail(email) {
      var EmailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return EmailRegex.test(email);
    }

    function validatePhone(phone) 
        { 
          var regexPattern=new RegExp(/^[0-9-+]+$/);    // regular expression pattern
          return regexPattern.test(phone); 
        } 
   
});


