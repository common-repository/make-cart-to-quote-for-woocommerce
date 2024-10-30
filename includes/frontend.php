<?php
$atqb_enable = get_option('atqb_enable');
if($atqb_enable == 'true'){
	add_action('init', 'mctqfw_register_my_session');
	add_action('woocommerce_after_shop_loop_item', 'MCTQFW_add_to_quote_btn', 15);
	add_action('wp_footer','MCTQFW_show_quotes_data',10);
	add_action('wp_ajax_mctqfw_save_quote', 'mctqfw_save_quote_email');
	add_action('wp_ajax_nopriv_mctqfw_save_quote', 'mctqfw_save_quote_email');
	add_action('woocommerce_product_meta_start','mctqfw_show_single_product_page');
	add_action('wp_ajax_mctqfw_woocommerce_ajax_add_to_cart', 'mctqfw_woocommerce_ajax_add_to_cart');
	add_action('wp_ajax_nopriv_mctqfw_woocommerce_ajax_add_to_cart', 'mctqfw_woocommerce_ajax_add_to_cart');
	add_action('wp_ajax_mctqfw_productidget', 'mctqfw_productidget');
	add_action('wp_ajax_nopriv_mctqfw_productidget', 'mctqfw_productidget');
	add_action('wp_ajax_mctqfw_product_delete', 'mctqfw_product_delete');
	add_action('wp_ajax_nopriv_mctqfw_product_delete', 'mctqfw_product_delete');
}

function mctqfw_register_my_session()
{
	/* Remove Product Add To Cart Button */
  	$remove_addtocart_btn = get_option('remove_add_to_cart');
  	if ($remove_addtocart_btn == 'remove_addtocart') {
  		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
 		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
 		add_filter('woocommerce_is_purchasable', '__return_false');
  	}

  	/* Remove Product Price */
  	$remove_woo_all_price = get_option('remove_woo_price');
  	if ($remove_woo_all_price == 'remove_wooallprice') {
  		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
  		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
  		add_filter( 'woocommerce_variable_sale_price_html', 'mctqfw_remove_product_price', 9999, 2 );
		add_filter( 'woocommerce_variable_price_html', 'mctqfw_remove_product_price', 9999, 2 );
		add_filter( 'woocommerce_get_price_html', 'mctqfw_remove_product_price', 9999, 2 );
  	}
  
}

function mctqfw_remove_product_price( $price, $product ) {
	$price = '';
	return $price;
}

/* Add To Quote In Shop Page */
function MCTQFW_add_to_quote_btn()
{
	global $product;
	$product_id = $product->get_id();
	//print_r($_SESSION['pruduct_id']);
	$quote_button_customize = get_option('quote_button_customize');
	$shop_page_option = get_option('atqb_remove_shop_page');
	// echo $quote_button_customize;
	if($shop_page_option == ''){
		$added_to_quote = '';
		if(!empty(WC()->session->get('woo_product_qoute'))){
	        if (in_array($product_id, WC()->session->get('woo_product_qoute'))) {
	            $added_to_quote = 'added-to-quote';
	        }
        }
		?>
			<div class="mctq_quote_btn <?php echo $added_to_quote; ?>">
				<a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="button product_type_simple addtoquotes" product_id="<?php echo esc_attr($product_id); ?>">
					<?php
						if(!empty($quote_button_customize)) {
							echo esc_attr($quote_button_customize);
						}else{
							echo "Add To Quote";
						}
					?>
				</a>
				<img class="mcqfw_loader" id="mctqfw_<?php echo esc_attr($product_id); ?>" src="<?php echo MCTQFW_PLUGIN_URL ;?>/public/img/loader1.gif">
			</div>
		<?php
	}
}

/*add quotes in shop page*/
function MCTQFW_show_quotes_data() {
	global $mctqfw_quote_icon;

	$bar_width = get_option('bar_width','400');
	$barhead_color = get_option('barhead_color','#ffffff');
	$barhead_border_color = get_option('barhead_border_color','#b7b7b7');
	$barhead_title_color = get_option('barhead_title_color','#000000');
	$barhead_border_style = get_option('barhead_border_style','solid');
	$quote_list_backcolor = get_option('quote_list_backcolor','#ffffff');
	$pro_list_title_kcolor = get_option('pro_list_title_kcolor','#000000');
	$pro_title_hover_color = get_option('pro_title_hover_color','#ff9065');
	$pro_price_color = get_option('pro_price_color','#000000');
	$pro_img_width = get_option('pro_img_width','100');
	$pro_img_radius = get_option('pro_img_radius','0');
	$footer_btn_text = get_option('footer_btn_text','SEND QUOTE');
	$footer_back_color = get_option('footer_back_color','#ffffff');
	$footer_border_color = get_option('footer_border_color','#b7b7b7');
	$footer_btn_back_color = get_option('footer_btn_back_color','#3cb247');
	$footer_btn_text_color = get_option('footer_btn_text_color','#ffffff');
	$footer_btn_back_hover_color = get_option('footer_btn_back_hover_color','#3cb247');
	$footer_btn_text_hover_color = get_option('footer_btn_text_hover_color','#000000');
	$quote_btn_bg_color = get_option('quote_btn_bg_color','#000000');
	$quote_btn_text_color = get_option('quote_btn_text_color','#ffffff');
	$quote_btn_font_size = get_option('quote_btn_font_size','16');
	$mctqfw_quote_color = get_option('mctqfw_quote_color','#000000');
	$mctqfw_quote_bg_color = get_option('mctqfw_quote_bg_color','#cccccc');
	$footer_form_back_color = get_option('footer_form_back_color','#f2f2f2');
	$form_title_color = get_option('form_title_color','#000000');

	?>
	<style type="text/css">
		#atc_iconimg svg {
			fill: <?php echo esc_attr($mctqfw_quote_color); ?>;
		}
		.quote_icon {
			background-color: <?php echo esc_attr($mctqfw_quote_bg_color); ?>;
		}
		.product_type_simple.addtoquotes {
			background-color: <?php echo esc_attr($quote_btn_bg_color); ?> !important;
			color: <?php echo esc_attr($quote_btn_text_color); ?> !important;
			font-size: <?php echo esc_attr($quote_btn_font_size); ?>px !important;
		}
		.quote_container {
			width: <?php echo esc_attr($bar_width); ?>px;
		    right: -<?php echo esc_attr($bar_width); ?>px;
		}
		.quote_login_main {
		    background-color: <?php echo esc_attr($footer_form_back_color); ?>;
		}
		label.mctqfw_field {
			color: <?php echo esc_attr($form_title_color); ?>;
		}
		.quote_header {
			background-color: <?php echo esc_attr($barhead_color); ?>;
			border-color: <?php echo esc_attr($barhead_border_color); ?>;
			border-style: <?php echo esc_attr($barhead_border_style); ?>;
		}
		.quote_heading {
			color: <?php echo esc_attr($barhead_title_color); ?>;
		}
		.mcfw_form_data {
			background-color: <?php echo esc_attr($quote_list_backcolor); ?>;
		}
		.p_detail a {
		    color: <?php echo esc_attr($pro_list_title_kcolor); ?>;
		}
		.p_detail a:hover {
		    color: <?php echo esc_attr($pro_title_hover_color); ?>;
		}
		.p_price {
		    color: <?php echo esc_attr($pro_price_color); ?>;
		}
		.product_img img {
		    border-radius: <?php echo esc_attr($pro_img_radius); ?>px;
		    max-width: <?php echo esc_attr($pro_img_width); ?>px;
		    max-height: <?php echo esc_attr($pro_img_width); ?>px;
		    display: inline-block;
		}
		.mctqfw_footer {
		    border-color: <?php echo esc_attr($footer_border_color); ?>;
		    background-color: <?php echo esc_attr($footer_back_color); ?>;
		}
		.mcfw_btn .mctqfw_save_button {
			background-color: <?php echo esc_attr($footer_btn_back_color); ?>;
			color: <?php echo esc_attr($footer_btn_text_color); ?>;
		}
		.mcfw_btn .mctqfw_save_button:hover {
		    background-color: <?php echo esc_attr($footer_btn_back_hover_color); ?>;
		    color: <?php echo esc_attr($footer_btn_text_hover_color); ?>;
		}
	</style>
	<div class="background_overlay"></div>
	<div class="quote_main_div">
		<form method="post">
			<div class="quote_container">
				<div class="closesidebar">
					<img class="quote_close" src="<?php echo MCTQFW_PLUGIN_URL ;?>/public/img/close-1.png">
				</div>
				<div class="quote_header">
					<h3 class="quote_heading"><?php echo esc_html('My Quote List','make-cart-to-quote-for-woocommerce'); ?></h3>
				</div>
				<div class="mcfw_form_data">
					<div class="atcproduct_info">
						<?php 
							// echo "<pre>";
							// print_r($_SESSION['woo_product_qoute']);
							// echo "</pre>";
							$retrive_data = WC()->session->get( 'woo_product_qoute' );
							if (!empty($retrive_data)) {
							foreach ($retrive_data as $value) {
						  		$product_data = wc_get_product($value);
						
							  	$product_id = $product_data->get_id();
							  	$img = $product_data->get_image();
							  	$permalink = $product_data->get_permalink( $value );

							  	if (!empty($product_data)) {
						?>
						<ul class="quote_products">
							<li class="mctqfw_quote" product_id="<?php echo esc_attr($product_id); ?>">
								<div class="product_img">
		                            <a href="<?php echo esc_url($permalink); ?>"><?php echo wp_kses_post($img); ?></a>
		                        </div>
			                    <div class="p_detail">
			                        <div class="p_title"><a href='<?php echo esc_url($permalink); ?>'><?php echo wp_kses_post($product_data->get_title());  ?></a>
				                        <div class="delete_quote">
		                                    <a class="mctq_delete" product_id="<?php echo esc_attr($product_id); ?>">
		                                    	<img class="mctq_remove" src="<?php echo MCTQFW_PLUGIN_URL ;?>/public/img/trash-1.png">
		                                    </a>
		                                </div>
	                                </div>
			                        <div class="p_price"><?php echo wp_kses_post($product_data->get_price_html()); ?></div>
			                    </div>
			                   
					  		</li>
					  	</ul>
					  	<?php
						  	}
						  }
						}else{
							?>
							<div class="ctqfw_notic">
	                            <div class="quote_empty_notice"><?php echo esc_html('Cart Quote is empty.','make-cart-to-quote-for-woocommerce'); ?></div>
	                        </div>
							<style type="text/css">
							 	.mcfw_form_data {
								 	display: flex;
								    flex-wrap: wrap;
								    justify-content: center;
								    align-items: center;
								}
							</style>
							
                            <?php
							}
					  	?> 
	                </div>
				</div>
				<?php 
					if (!empty($retrive_data)) {
				?>
				<div class="quote_login_main">
					<div class="quote_notice"></div>
					<div class="name_notice"></div>
					<div class="email_notice"></div>
				    <label for="fname" class="mctqfw_field"><?php echo esc_html('Name :','make-cart-to-quote-for-woocommerce'); ?></label>
				    <input type="text" class="cart_field" id="user_name" name="user_name" placeholder="Your name..">

				    <label for="lname" class="mctqfw_field"><?php  echo esc_html('Email :','make-cart-to-quote-for-woocommerce'); ?></label>
				    <input type="email" class="cart_field" id="user_email" name="user_email" placeholder="Your email..">
				</div>
			<?php } ?>
			<?php 
				if (!empty($retrive_data)) {
			?>
				<div class="mctqfw_footer">
					<div class="mctqfw_footer_detail">
						<div class="mcfw_btn">
						  	<input type="hidden" name="action_val" value="insert_quotes">
						  	<input class="mctqfw_save_button quote_btn" type="submit" value="<?php echo esc_attr($footer_btn_text); ?>">
					  	</div>
				  	</div>
				</div>
				<?php } ?>
			</div>
		</form>
		</div>	
		<?php 
			$quote_img = get_option('quote_img','quote_icon_1');
			$mob_enable = get_option('mob_enable');
		?>
		<div class="quote_icon <?php if($mob_enable == true){echo "mctqfwmob_disblock";} ?>">
			<div class="sidebar_qoute_count">
		        <div class="quote_product_count"></div>
		    </div>
		    <div id="atc_iconimg">
                <?php
                    if($quote_img == 'quote_icon_1'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_1']));
                    }else if($quote_img == 'quote_icon_2'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_2']));
                    }else if($quote_img == 'quote_icon_3'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_3']));
                    }else if($quote_img == 'quote_icon_4'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_4']));
                    }else if($quote_img == 'quote_icon_5'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_5']));
                    }else if($quote_img == 'quote_icon_6'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_6']));
                    }else if($quote_img == 'quote_icon_7'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_7']));
                    }else if($quote_img == 'quote_icon_8'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_8']));
                    }else if($quote_img == 'quote_icon_9'){
                        echo html_entity_decode(esc_attr($mctqfw_quote_icon['quote_icon_9']));
                    }
                ?>
            </div>
	    </div>
	<?php
}

/* Update Form Value And Post Type */
function mctqfw_save_quote_email() {
    if (!empty($_REQUEST['user_name']) && !empty($_REQUEST['user_email'])) {
        $retrive_data = WC()->session->get('woo_product_qoute');
        if (!empty($retrive_data)) {
            $user_name = sanitize_text_field($_REQUEST['user_name']);
            $user_email = sanitize_email($_REQUEST['user_email']);
            
            // Save the quote to the database
            $new_post = array(
                'post_type' => 'wc-quotes', // Custom Post Type Slug
                'post_status' => 'publish',
                'post_title' => $user_email,
            );
            $post_id = wp_insert_post($new_post);
            
            update_post_meta($post_id, 'user_name', $user_name);
            update_post_meta($post_id, 'user_email', $user_email);
            
            if (isset($retrive_data)) {
                update_post_meta($post_id, 'woo_product_qoute', $retrive_data);
            } else {
                update_post_meta($post_id, 'woo_product_qoute', '');
            }
            
            // Send email with the data
            $to = $user_email;
			$subject = 'Quote Details';
			$message = "<p>User Name: $user_name</p>";
			$message .= "<p>User Email: $user_email</p>";

			$message .= "
			<html>
			<body>
			  <h2>Quote Details</h2>
			  <table border='1' cellpadding='10' cellspacing='0' class='widefat fixed'>
			    <tr>
			      <th>Product</th>
			      <th>List Price</th>
			      <th>Unit Cost</th>
			      <th>Discount %</th>
			      <th>New Unit Price</th>
			      <th>Total</th>
			    </tr>";

			foreach ($retrive_data as $prodid) {
			    $product_data = wc_get_product($prodid);
			    if (!empty($product_data)) {
			        $proddata = $product_data->get_data();

			        if ($product_data->get_regular_price() > 0) {
			            $discount_price = round(100 - ($proddata['price'] / (int) $product_data->get_regular_price() * 100), 1);
			        } else {
			            $discount_price = '0.00';
			        }

			        $unitcostprice = get_post_meta($prodid, 'product_unit_cost', true);
			        if (!empty($unitcostprice)) {
			        	$unitcost = get_post_meta($prodid,'product_unit_cost',true);
			            if (is_numeric($discount_price)) {
			                $discountunit = ($discount_price / 100) * floatval($unitcostprice);
			            }
			        } else {
			        	$unitcost = '-';
			            $discountunit = 0;
			        }

			        $product_title = $product_data->get_title();
			        $regular_price = wp_kses_post($proddata['regular_price']);
			        // $unit_cost = esc_attr(get_post_meta($prodid, 'product_unit_cost', true));
			        $new_unit_price = floatval($unitcostprice) - $discountunit;
			        if (!empty($proddata['price'])) {
						$totalproductprice = floatval($proddata['price']) + $new_unit_price;
					} else {
						$totalproductprice = '0.00';
					}
			        $price = wp_kses_post($totalproductprice);
			        $discount_price = wp_kses_post(number_format($discount_price, 2, '.', ''));

			        // $product_title = $product_data->get_title();
			        // $regular_price = $proddata['regular_price'];
			        // $unit_cost = get_post_meta($prodid, 'product_unit_cost', true);
			        // $discount_price = round(100 - ($proddata['price'] / (int) $product_data->get_regular_price() * 100), 1) . '.00';
			        // $new_unit_price = floatval($unit_cost) - (($discount_price / 100) * floatval($unit_cost));
			        // $price = $proddata['price'];

			        $message .= "
			        <tr>
			          <td>$product_title</td>
			          <td>$regular_price</td>
			          <td>$unitcost</td>
			          <td>$discount_price</td>
			          <td>$new_unit_price</td>
			          <td>$price</td>
			        </tr>";
			    }
			}

			$message .= "
			  </table>
			</body>
			</html>";
			

			$headers = array('Content-Type: text/html; charset=UTF-8');
			$admin_email = get_option( 'admin_email' );
			// $headers = array('Content-Type: text/plain; charset=UTF-8');

			wp_mail($to, $subject, $message, $headers);
			wp_mail($admin_email, $subject, $message, $headers);

            // Reset session data
            WC()->session->__unset('woo_product_qoute');
        }
    }
    
    exit;
}


/* Add To Quote In Single Product Page */
function mctqfw_show_single_product_page(){
	global $product;
	$product_id = $product->get_id();
	$single_prod_option = get_option('atqb_remove_single_prod');
	$quote_button_customize = get_option('quote_button_customize');
	//echo $quote_button_customize;
	if ($single_prod_option == '') {
		$added_to_quote = '';
        if(!empty(WC()->session->get('woo_product_qoute'))){
	        if (in_array($product_id, WC()->session->get('woo_product_qoute'))) {
	            $added_to_quote = 'added-to-quote';
	        }
        }
		?>
		<div class="mctq_quote_btn <?php echo $added_to_quote; ?>">
			<a href="<?php echo get_permalink($product_id); ?>" class="button product_type_simple addtoquotes" product_id="<?php echo esc_attr($product_id); ?>">
				<?php
					if(!empty($quote_button_customize)) {
						echo esc_attr($quote_button_customize);
					}else{
						echo "Add To Quote";
					}
				?>
			</a>
		</div>
		<?php
	}
}
        
function mctqfw_woocommerce_ajax_add_to_cart() {

	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
	
	
	$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
	$variation_id = absint($_POST['variation_id']);
	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
	$product_status = get_post_status($product_id);

	if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

		do_action('woocommerce_ajax_added_to_cart', $product_id);

	  	if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
	      		wc_add_to_cart_message(array($product_id => $quantity), true);
	  	}

	  	WC_AJAX :: get_refreshed_fragments();
	} else {

	  	$data = array(
	      	'error' => true,
	      	'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

	  	echo wp_send_json($data);
	}

	wp_die();
}



function mctqfw_productidget(){
    ob_start();

    $footer_btn_text = get_option('footer_btn_text','SEND QUOTE');
	$product_id = sanitize_text_field($_REQUEST['id']);
	$arry_idval = WC()->session->get( 'woo_product_qoute' );
	// print_r($arry_idval);
	//WC()->session->set( 'woo_product_qoute' , $product_id );
	$arry_idval[] = $product_id;
	
	WC()->session->set( 'woo_product_qoute' , $arry_idval );
	WC()->session->set_customer_session_cookie( true );
?>

	<div class="closesidebar">
		<img class="quote_close" src="<?php echo MCTQFW_PLUGIN_URL ;?>/public/img/close.png">
	</div>
	<div class="quote_header">
		<h3 class="quote_heading"><?php echo esc_html('My Quote List','make-cart-to-quote-for-woocommerce'); ?></h3>
	</div>
	<div class="mcfw_form_data">
		<div class="atcproduct_info">
			<?php 
			// echo "<pre>";
			// print_r($_SESSION['woo_product_qoute']);
			// echo "</pre>";
			$retrive_data = WC()->session->get( 'woo_product_qoute' );
			if (!empty($retrive_data)) {
			foreach ($retrive_data as $value) {
		  		$product_data = wc_get_product($value);
		
			  	$product_id = $product_data->get_id();
			  	$img = $product_data->get_image();
			  	$permalink = $product_data->get_permalink( $value );

			  	if (!empty($product_data)) {
			?>
			<ul class="quote_products">
				<li class="mctqfw_quote" product_id="<?php echo esc_attr($product_id); ?>">
					<div class="product_img">
                        <a href="<?php echo esc_url($permalink); ?>"><?php echo wp_kses_post($img); ?></a>
                    </div>
                    <div class="p_detail">
                        <div class="p_title"><a href='<?php echo esc_url($permalink); ?>'><?php echo wp_kses_post($product_data->get_title());  ?></a>
	                        <div class="delete_quote">
	                            <a class="mctq_delete" product_id="<?php echo esc_attr($product_id); ?>">
	                            	<img class="mctq_remove" src="<?php echo MCTQFW_PLUGIN_URL ;?>/public/img/trash-1.png">
	                            </a>
	                        </div>
	                    </div>
                        <div class="p_price"><?php echo wp_kses_post($product_data->get_price_html()); ?></div>
                    </div>
		  		</li>
		  	</ul>
		  	<?php
			  	}
			  }
			}else{
				 ?>
				<div class="ctqfw_notic">
                	<div class="quote_empty_notice"><?php echo esc_html('Cart Quote is empty.','make-cart-to-quote-for-woocommerce'); ?></div>
                </div>
                <style type="text/css">
				 	.mcfw_form_data {
					 	display: flex;
					    flex-wrap: wrap;
					    justify-content: center;
					    align-items: center;
					}
				</style>
                <?php
				}
		  	?>
        </div>
	</div>
	<?php 
		if (!empty($retrive_data)) {
	?>
	<div class="quote_login_main">
		<div class="quote_notice"></div>
		<div class="name_notice"></div>
		<div class="email_notice"></div>
	    <label for="fname" class="mctqfw_field"><?php echo esc_html('Name :','make-cart-to-quote-for-woocommerce'); ?></label>
	    <input type="text" class="cart_field" id="user_name" name="user_name" placeholder="Your name..">

	    <label for="lname" class="mctqfw_field"><?php  echo esc_html('Email :','make-cart-to-quote-for-woocommerce'); ?></label>
	    <input type="email" class="cart_field" id="user_email" name="user_email" placeholder="Your email..">
	</div>
	<?php } ?>
	<?php 
		if (!empty($retrive_data)) {
	?>
	<div class="mctqfw_footer">
		<div class="mctqfw_footer_detail">
			<div class="mcfw_btn">
			  	<input type="hidden" name="action_val" value="insert_quotes">
			  	<input class="mctqfw_save_button quote_btn" type="submit" value="<?php echo esc_attr($footer_btn_text); ?>">
		  	</div>
	  	</div>
	</div>
	<?php
	}
	$htmlquote = ob_get_contents();
    ob_end_clean();
    ob_start();
    ?>
    <div class="sidebar_qoute_count">
        <div class="quote_product_count"></div>
    </div>
    <?php
    ob_end_clean();
    $arr = array(
        "htmlquote" => $htmlquote,
    );
    echo json_encode($arr);
    exit;
}


/* Remove Quote Product */
function mctqfw_product_delete(){
	$retrive_data = WC()->session->get( 'woo_product_qoute' );
	// echo "<pre>";
	// print_r($retrive_data);
	// echo "</pre>";

	$pro_id = $_REQUEST['id'];
	// echo "<pre>";
	// print_r($pro_id);
	// echo "</pre>";
	
	$pro_key = array_search($pro_id,$retrive_data);
	if($pro_key !== true){
		unset($retrive_data[$pro_key]);
	}
	// echo "<pre>";
	// print_r($retrive_data);
	// echo "</pre>";
	WC()->session->set( 'woo_product_qoute' , $retrive_data );
	
	exit;
}