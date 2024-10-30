jQuery(document).ready(function(){
    // console.log( mctqfwproductid );
    var mctqfw_bar_width = mctqfwproductid.mctqfw_bar_width;

    var count = jQuery(".quote_products li").length
    jQuery('.quote_product_count').html(count);
    // jQuery('img.mcqfw_loader').css('display','none');

    jQuery('body').on('click', '.addtoquotes', function() {
        
    	var prod_id = jQuery(this).attr('product_id');

        jQuery(this).css('opacity', '0.5');
        jQuery("#mctqfw_" + prod_id).show();

        jQuery.ajax({
            url: mctqfwproductid.ajax_url,
            type: "post",
            data: {
            	'action': 'mctqfw_productidget',
                'id': prod_id
            },
            success: function(data) {
                // console.log(data);

                var obj = jQuery.parseJSON(data);
                // console.log(obj);
                jQuery(".quote_container").html(obj.htmlquote);
                jQuery(".quote_notice").hide();

                var count = jQuery(".quote_products li").length
                jQuery('.quote_product_count').html(count);

                jQuery(".quote_icon").trigger('click');

                jQuery("body").addClass("quote_sidebar");
                jQuery(".quote_container").addClass("product_detail");
                jQuery(".quote_icon").addClass("quote_custom");
                jQuery(".background_overlay").addClass("overlay_disable");

                // Hide the "Add To Quote" button for the current product
                jQuery('.addtoquotes[product_id="' + prod_id + '"]').closest('.mctq_quote_btn').addClass('added-to-quote');
            },
            complete: function() {
                // Hide the loader image when the AJAX request is complete
                jQuery("#mctqfw_" + prod_id).hide();
                jQuery('.addtoquotes').css('opacity', '1');
            }
    	});

        // Check the product type and redirect accordingly
        var productType = jQuery(this).attr('class').match(/product_type_([^ ]+)/);
        if (productType && (productType[1] === 'variable' || productType[1] === 'grouped')) {
            window.location.href = jQuery(this).attr('href');
        }
	return false;
    })

    jQuery(".quote_icon").on("click",function(){

        jQuery(".quote_container").css({'width': mctqfw_bar_width+'px' , 'right': '0px'});
        // console.log("click success.");
        jQuery("body").addClass("quote_sidebar");
        jQuery(".quote_container").addClass("product_detail");
        jQuery(".quote_icon").addClass("quote_custom");
        jQuery(".background_overlay").addClass("overlay_disable");
    });

    jQuery('body').on('click', '.background_overlay', function(){
        jQuery(".quote_container").css({'width': mctqfw_bar_width+'px' , 'right': '-'+mctqfw_bar_width+'px'});
        jQuery("body").removeClass("quote_sidebar");
        jQuery(".background_overlay").removeClass("overlay_disable");
        jQuery(".quote_container").removeClass("product_detail"); 
        jQuery(".quote_icon").removeClass("quote_custom");
    });

    jQuery('body').on('click', '.closesidebar', function(){
        // console.log('click generete');
        jQuery(".quote_container").css({'width': mctqfw_bar_width+'px' , 'right': '-'+mctqfw_bar_width+'px'});
        jQuery("body").removeClass("quote_sidebar");
        jQuery(".quote_container").removeClass("product_detail");
        jQuery(".quote_icon").removeClass("quote_custom");
        jQuery(".background_overlay").removeClass("overlay_disable");
    });

    jQuery('body').on('click', '.mctqfw_save_button', function() {
        var user_name = jQuery('#user_name').val();
        var user_email = jQuery('#user_email').val();
        // console.log( user_name );
        jQuery.ajax({
            url: mctqfwproductid.ajax_url,
            type: "post",
            data: {
                'action': 'mctqfw_save_quote',
                'user_name': user_name,
                'user_email': user_email
            },
            success: function(data) {

                if(user_name == ''){
                    jQuery(".name_notice").text('Please Enter Your Name.').show();
                    jQuery(".email_notice").text('Please Enter Your Email.').show();
                    jQuery(".email_notice").hide();
                    jQuery(".quote_notice").hide();
                }else if(user_email == ''){
                    jQuery(".email_notice").text('Please Enter Your Email.').show();
                    jQuery(".name_notice").hide();
                    jQuery(".quote_notice").hide();
                }else{
                    // jQuery('#user_name').val('');
                    // jQuery('#user_email').val('');
                    // jQuery('.mcfw_form_data').html('');
                    // jQuery('.quote_product_count').html(0);

                    jQuery(".name_notice").hide();
                    jQuery(".email_notice").hide();
                    jQuery(".quote_notice").text('Quote added successfully.').show();
                    // setInterval('window.location.reload()', 1000);
                    setTimeout(function() {
                        // jQuery('.closesidebar').click();
                        // jQuery(".quote_notice").fadeOut();
                        window.location.reload();
                    }, 1000);
                }
            }
        });
        return false;
    })

    jQuery('body').on('click', '.mctq_delete', function(e) {
        e.preventDefault();
        // console.log('scscsc');
        var atcproductContent = jQuery('.mcfw_form_data');
        atcproductContent.addClass('mctqfw_overlay');

        var count = jQuery(".mctqfw_quote").length
        // jQuery('.quote_product_count').html(count);
        var currnt_count = count-1;
        // console.log(currnt_count);
        var product_id = jQuery(this).attr('product_id');
        var cur = jQuery(this);

        jQuery.ajax({
            url: mctqfwproductid.ajax_url,
            type: "post",
            data: {
                'action': 'mctqfw_product_delete',
                'id': product_id
            },
            success: function(data) {
               atcproductContent.removeClass('mctqfw_overlay');

               cur.closest(".mctqfw_quote").remove();
               // var count = jQuery(".quote_products li").length
               jQuery('.quote_product_count').html(currnt_count);
               jQuery('.addtoquotes[product_id="' + product_id + '"]').closest('.mctq_quote_btn').removeClass('added-to-quote');
            }
        });
        // return false;
    })
    
});