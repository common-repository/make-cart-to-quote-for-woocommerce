<?php 
add_action('admin_menu','mctqfw_quote_settings');
function mctqfw_quote_settings(){
    add_menu_page( 
        'Cart To Quote', // page <title>Title</title>
        'Cart To Quote', // menu link text
        'manage_options', // capability to access the page
        'mctqfw_cart_to_quote_generator', // page URL slug
        'mctqfw_cart_to_quote_settings', // callback function /w content
        'dashicons-format-quote', // menu icon
        5
    );
}


/* Custom Pust Type */
add_action( 'init', 'mctqfw_create_post_type' );
function mctqfw_create_post_type() {

	$supports = array(
		'title', // post title
	);
	$labels = array(
		'name'               => _x( 'Quotes', 'Plural Name' ),
		'singular_name'      => _x( 'Quotes', 'Singular Name' ),
		'add_new'            => _x( 'Add New', 'Quotes' ),
		'add_new_item'       => __( 'Add New Quotes' ),
		'edit_item'          => __( 'Edit Quotes' ),
		'new_item'           => __( 'New Quotes' ),
		'all_items'          => __( 'All Quotes' ),
		'view_item'          => __( 'View Quotes' ),
		'search_items'       => __( 'Search Quotes' ),
		'not_found'          => __( 'No Quotes found' ),
		'not_found_in_trash' => __( 'No Quotes found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Quotes List',
	);

	$args = array(
		'supports' => $supports,
		'labels'      => $labels,
		'public'      => true,
		'publicly_queryable' => false,
		'rewrite'     => array( 'slug' => 'wc-quotes' ),
		'has_archive' => true,
		'show_in_menu'      => false
	);

	register_post_type( 'wc-quotes', $args );
}



function mctqfw_register_my_custom_submenu_page() {
    add_submenu_page( 'mctqfw_cart_to_quote_generator', 'Quotes', 'Quotes List', 'manage_options','edit.php?post_type=wc-quotes'); 
}
add_action('admin_menu', 'mctqfw_register_my_custom_submenu_page',99);

add_action('add_meta_boxes', 'mctqfw_add_meta_box');
function mctqfw_add_meta_box()
{
	$screens = ['post', 'wc-quotes'];
    foreach ($screens as $screen) {
        add_meta_box(
            'box_id',                 // Unique ID
            'Users',      // Box title
            'mctqfw_custom_box_post_display',  // Content callback, must be of type callable
            'wc-quotes',
            // Post type
        );
    }
}

function mctqfw_custom_box_post_display($post){

	// $user_info = get_users();
	// foreach ($user_info as $user_data) {
 	// 	$user_email = $user_data->user_email;
	// 	echo "<pre>";
	// 	print_r($user_email);
	// 	echo "</pre>";
	// } 
	?>
        <table>
        	<tr>
        		<th><?php echo esc_html('User Name : ','make-cart-to-quote-for-woocommerce'); ?></th>
        		<td><?php echo wp_kses_post(get_post_meta($post->ID, "user_name", true)); ?></td>
        	</tr>
        	<tr>
        		<th><?php echo esc_html('User Email : ','make-cart-to-quote-for-woocommerce'); ?></th>
        		<td><?php echo wp_kses_post(get_post_meta($post->ID, "user_email", true)); ?></td>
        	</tr>
        </table>
	<?php
}

add_action('save_post','mctqfw_quotes_custom_meta_data_save');
function mctqfw_quotes_custom_meta_data_save($post){
	if (isset($_REQUEST['choose_user_emails'])) {
		update_post_meta($post, 'choose_user_emails', sanitize_text_field($_POST['choose_user_emails']));
	}else{
		echo "";
	}
	
}

/*product meta box in quotes*/
add_action('add_meta_boxes', 'mctqfw_add_product_meta_box');
function mctqfw_add_product_meta_box()
{
	$screens = ['post', 'wc-quotes'];
    foreach ($screens as $screen) {
        add_meta_box(
            'prod_box_id',                 // Unique ID
            'products',      // Box title
            'mctqfw_custom_box_product_list',  // Content callback, must be of type callable
            'wc-quotes',
            // Post type
        );
    }
}

function mctqfw_custom_box_product_list()
{
	global $post;
	// $args = array(
 //        'post_type' => 'product',
 //        'posts_per_page' => -1,
 //        'author'=>get_current_user_id()
 //    );
 //    $loop = new WP_Query( $args );
    $product_id = get_post_meta($post->ID,'woo_product_qoute',true);
	   
	// $product_unit_cost = get_post_meta($product_id,'product_unit_cost',true);
	// print_r($product_unit_cost);
    
	?>
		<table border="1" cellpadding="0" cellspacing="0" class="widefat fixed">
			<tr>
				<th><?php echo esc_html('Product','make-cart-to-quote-for-woocommerce'); ?></th>
				<th><?php echo esc_html('List Price','make-cart-to-quote-for-woocommerce'); ?></th>
				<th><?php echo esc_html('Unit Cost','make-cart-to-quote-for-woocommerce'); ?></th>
				<th><?php echo esc_html('Discount %','make-cart-to-quote-for-woocommerce'); ?></th>
				<th><?php echo esc_html('New Unit Price','make-cart-to-quote-for-woocommerce'); ?></th>
				<th><?php echo esc_html('Total','make-cart-to-quote-for-woocommerce'); ?></th>
			</tr>
			
				<?php  
				if (is_array($product_id) || is_object($product_id))
				{
					foreach($product_id as $prodid) { 
						$product_data = wc_get_product( $prodid);
						if (!empty($product_data)) {
						$proddata = $product_data->get_data();
						
						if ($product_data->get_regular_price() > 0) {
				            $discount_price = round(100 - ($proddata['price'] / (int) $product_data->get_regular_price() * 100), 1);
				        } else {
				            $discount_price = '0.00';
				        }
						$unitcostprice = get_post_meta($prodid,'product_unit_cost',true);
						if(!empty($unitcostprice)){
							$unitcost = get_post_meta($prodid,'product_unit_cost',true);
							if (is_numeric($discount_price)) {
								$discountunit = ($discount_price / 100) * floatval($unitcostprice);
							}
						}else{
							$unitcost = '-';
							$discountunit = 0;
						}
						if (!empty($proddata['price'])) {
							$newunitprice = floatval($unitcostprice)-$discountunit;
							$totalproductprice = floatval($proddata['price']) + $newunitprice;
						} else {
							$totalproductprice = '0.00';
						}
						?>
						<tr>
							<td><?php echo wp_kses_post($product_data->get_title()); ?></td>
							<td><?php echo wp_kses_post($proddata['regular_price']); ?></td>
							<td><?php echo esc_attr($unitcost); ?></td>
							<td><?php echo wp_kses_post(number_format($discount_price, 2, '.', '')); ?></td>
							<td><?php echo floatval($unitcostprice)-$discountunit; ?></td>
							<td><?php echo wp_kses_post($totalproductprice); ?></td>
						</tr>
					
					<?php } } 
					}
					?>
			
		</table>
	<?php
}

add_action('woocommerce_product_options_general_product_data', 'mctqfw_woocommerce_product_custom_fields');
function mctqfw_woocommerce_product_custom_fields()
{
	global $post;
	
	?>
		<div class=" product_custom_field ">
			<p class="form-field _unit_cost_field ">
				<label><?php echo esc_html('Unit Cost($)','make-cart-to-quote-for-woocommerce'); ?></label>
				<input type="text" name="product_unit_cost" value="<?php echo esc_attr(get_post_meta($post->ID,'product_unit_cost',true)); ?>">
				<span class="description"><?php echo esc_html('Enter The Cost Of The Product Unit Here','make-cart-to-quote-for-woocommerce'); ?></span>
			</p>
		</div>
	<?php
}

add_action('woocommerce_admin_process_product_object', 'mctqfw_woocommerce_product_custom_fields_save');
function mctqfw_woocommerce_product_custom_fields_save( $product ){
	// echo "<pre>";
	// print_r($product->get_id());
	// echo "</pre>";
	update_post_meta($product->get_id(),'product_unit_cost',sanitize_text_field($_REQUEST['product_unit_cost']));
}

add_action( 'woocommerce_variation_options_pricing', 'mctqfw_variation_settings_fields',10,3 );
function mctqfw_variation_settings_fields($loop, $variation_data, $variation){
	woocommerce_wp_text_input(
        array(
            'id'            => "product_variation_unit_cost{$loop}",
            'name'          => "product_variation_unit_cost[{$loop}]",
            'value'         => get_post_meta($variation->ID,'product_variation_unit_cost',true),
            'label'         => __( 'Unit Cost($)', 'woocommerce' ),
            'desc_tip'      => true,
            'description'   => __( 'Enter the cost of the product unit here', 'woocommerce' ),
            'wrapper_class' => 'form-row form-row-full',
        )
    );
}

add_action( 'woocommerce_save_product_variation', 'mctqfw_save_mctqfw_variation_settings_fields', 10, 2 );
function mctqfw_save_mctqfw_variation_settings_fields( $variation_id, $loop ){
	$product_variation_unit_cost = sanitize_text_field($_POST['product_variation_unit_cost'][ $loop ]);
	if ( ! empty( $product_variation_unit_cost ) ) {
        update_post_meta( $variation_id, 'product_variation_unit_cost', esc_attr( $product_variation_unit_cost ));
    }
}

add_filter( 'woocommerce_available_variation', 'load_mctqfw_variation_settings_fields' );
function load_mctqfw_variation_settings_fields( $variation ) {     
    $variation['product_variation_unit_cost'] = get_post_meta( $variation[ 'variation_id' ], 'product_variation_unit_cost', true );

    return $variation;
}

function mctqfw_cart_to_quote_settings(  ) { 
	global $wp_roles, $current_section, $mctqfw_quote_icon;
    $all_roles = $wp_roles->roles;
?>
<?php
if(isset($_REQUEST['message'])  && $_REQUEST['message'] == 'success'){ ?>
    <div class="notice notice-success is-dismissible"> 
        <p><strong><?php echo __( 'Setting saved successfully.', 'make-cart-to-quote-for-woocommerce' );?></strong></p>
    </div>
<?php } ?>

<div class="mctqfw_main_container">
    <ul class="nav-tab-wrapper woo-nav-tab-wrapper">
        <li class="nav-tab nav-tab-active" data-tab="mctqfw-tab-general"><?php echo __('General','make-cart-to-quote-for-woocommerce');?></li>
        <li class="nav-tab" data-tab="mctqfw-tab-style-settings"><?php echo __('Sidebar Style','make-cart-to-quote-for-woocommerce');?></li>
        <!-- <li class="nav-tab" data-tab="mctqfw-tab-text-url-settings"><?php //echo __('Text/ Url','make-cart-to-quote-for-woocommerce');?></li> -->
    </ul>
<?php
settings_fields( 'mctqfw_cart_to_quote_generator' );
do_settings_sections( 'mctqfw_cart_to_quote_generator' );
?>
	<form action='<?php echo get_permalink(); ?>' id="mctqfw-add-to-cart" method='post'>
		<div id="mctqfw-tab-general" class="tab-content current">
			<h2><?php echo esc_html('Cart To Quote Settings','make-cart-to-quote-for-woocommerce'); ?></h2>
			<table class="form-table">
				<tr>
					<th><label><?php echo esc_html('Enable Cart To Quote','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="atqb_enable" value="true" <?php checked('true', get_option('atqb_enable',true)) ?> ><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Single Product Page','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="atqb_remove_single_prod" value="remove_single_prod" <?php checked('remove_single_prod', get_option('atqb_remove_single_prod')) ?> ><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong>
						<p class="description"><?php echo esc_html('Remove ','make-cart-to-quote-for-woocommerce'); ?><strong><?php echo esc_html('"Add To Quote"','make-cart-to-quote-for-woocommerce'); ?></strong><?php echo esc_html(' Button On The Single Product Page.','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Product Archives Loop/Shop Page','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="atqb_remove_shop_page" value="remove_shop_page"<?php checked('remove_shop_page', get_option('atqb_remove_shop_page')) ?>><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong>
						<p class="description"><?php echo esc_html('Remove ','make-cart-to-quote-for-woocommerce'); ?><strong><?php echo esc_html('"Add To Quote"','make-cart-to-quote-for-woocommerce'); ?></strong><?php echo esc_html(' Button On The Product Archives Loop/Shop Page.','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Customize "Add To Quote" Button','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="text" name="quote_button_customize" value="<?php echo get_option('quote_button_customize'); ?>">
						<p class="description"><?php echo esc_html('Change ','make-cart-to-quote-for-woocommerce'); ?><strong><?php echo esc_html('"Add To Quote"','make-cart-to-quote-for-woocommerce'); ?></strong><?php echo esc_html(' Button Text.(Defualt text "Add To Quote"). ','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Remove "Add To Cart" Button','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="remove_add_to_cart" value="remove_addtocart"<?php checked('remove_addtocart',get_option('remove_add_to_cart')); ?>><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong>
						<p class="description"><?php echo esc_html('Remove ','make-cart-to-quote-for-woocommerce'); ?><strong><?php echo esc_html('Add To Cart','make-cart-to-quote-for-woocommerce'); ?></strong><?php echo esc_html(' Button On Your Site.','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Remove All Woocommerce Price','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="remove_woo_price" value="remove_wooallprice"<?php checked('remove_wooallprice',get_option('remove_woo_price')); ?>><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong>
						<p class="description"><?php echo esc_html('Remove All Woocommerce Price On Your Site.','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
				<tr>
					<th><label><?php echo esc_html('Enable Mobile','make-cart-to-quote-for-woocommerce'); ?></label></th>
					<td>
						<input type="checkbox" name="mob_enable" value="true" <?php checked('true', get_option('mob_enable',true)) ?> ><strong><?php echo esc_html('Enable/Disable','make-cart-to-quote-for-woocommerce'); ?></strong><p class="description"><?php echo esc_html('Enable quote list in mobile view.','make-cart-to-quote-for-woocommerce'); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<div id="mctqfw-tab-style-settings" class="tab-content">
	        <div class="quote_quote_btn"> 
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Add To Quote Button Style','make-cart-to-quote-for-woocommerce'); ?></h2>
					<tr>
		                <th><label><?php echo esc_html('Button Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="quote_btn_bg_color" value="<?php echo esc_attr(get_option('quote_btn_bg_color','#000000')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Text Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ffffff" name="quote_btn_text_color" value="<?php echo esc_attr(get_option('quote_btn_text_color','#ffffff')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Font Size','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="number" name="quote_btn_font_size" value="<?php echo esc_attr(get_option('quote_btn_font_size','16')); ?>">
		                </td>
		            </tr>
				</table>
	        </div>
	        <div class="quote_sidebar">
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Quote Sidebar Style Setting','make-cart-to-quote-for-woocommerce'); ?></h2>
					<tr>
						<th><label><?php echo esc_html('Select Quote','make-cart-to-quote-for-woocommerce'); ?></label></th>
						<td>
							<input type="radio" name="quote_img" value="quote_icon_1" <?php checked('quote_icon_1',get_option('quote_img')); ?> checked><label for="label-1" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_1']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_2" <?php checked('quote_icon_2',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_2']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_3" <?php checked('quote_icon_3',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_3']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_4" <?php checked('quote_icon_4',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_4']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_5" <?php checked('quote_icon_5',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_5']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_6" <?php checked('quote_icon_6',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_6']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_7" <?php checked('quote_icon_7',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_7']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_8" <?php checked('quote_icon_8',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_8']; ?></label>
							<input type="radio" name="quote_img" value="quote_icon_9" <?php checked('quote_icon_9',get_option('quote_img')); ?>><label for="label-2" class="quote_cart"><?php echo $mctqfw_quote_icon['quote_icon_9']; ?></label>
						</td>
					</tr>
					<tr>
		                <th><label><?php echo esc_html('Quote Icon Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="mctqfw_quote_color" value="<?php echo esc_attr(get_option('mctqfw_quote_color','#000000')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Quote Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#cccccc" name="mctqfw_quote_bg_color" value="<?php echo esc_attr(get_option('mctqfw_quote_bg_color','#cccccc')); ?>">
		                </td>
		            </tr>
					<tr>
		                <th><label><?php echo esc_html('Sidebar Width','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="number" name="bar_width" value="<?php echo esc_attr(get_option('bar_width','400')); ?>"><p class="description"><?php echo esc_html('Value in px (Default: 400).','make-cart-to-quote-for-woocommerce'); ?></p>
		                </td>
		            </tr>
				</table>
			</div>
			<div class="quote_body_header">
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Sidebar Header','make-cart-to-quote-for-woocommerce'); ?></h2>
					<tr>
		                <th><label><?php echo esc_html('Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ffffff" name="barhead_color" value="<?php echo esc_attr(get_option('barhead_color','#ffffff')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Header Border Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#b7b7b7" name="barhead_border_color" value="<?php echo esc_attr(get_option('barhead_border_color','#b7b7b7')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Header Title Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="barhead_title_color" value="<?php echo esc_attr(get_option('barhead_title_color','#000000')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Header Border Style','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <select name="barhead_border_style" class="shb_border">
		                        <option value="solid" <?php selected('solid', get_option("barhead_border_style","solid")) ?>><?php echo esc_html('Solid','make-cart-to-quote-for-woocommerce'); ?></option>
		                        <option value="dotted" <?php selected('dotted', get_option("barhead_border_style","solid")) ?>><?php echo esc_html('Dotted','make-cart-to-quote-for-woocommerce'); ?></option>
		                        <option value="dashed" <?php selected('dashed', get_option("barhead_border_style","solid")) ?>><?php echo esc_html('Dashed','make-cart-to-quote-for-woocommerce'); ?></option>
		                    </select>
		                </td>
		            </tr>
				</table>
			</div>
			<div class="quote_list">
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Quote List','make-cart-to-quote-for-woocommerce'); ?></h2>
					<tr>
		                <th><label><?php echo esc_html('Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ffffff" name="quote_list_backcolor" value="<?php echo esc_attr(get_option('quote_list_backcolor','#ffffff')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Product Title Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="pro_list_title_kcolor" value="<?php echo esc_attr(get_option('pro_list_title_kcolor','#000000')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Title Hover Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ff9065" name="pro_title_hover_color" value="<?php echo esc_attr(get_option('pro_title_hover_color','#ff9065')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Product Price Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="pro_price_color" value="<?php echo esc_attr(get_option('pro_price_color','#000000')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Image Width','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="number" name="pro_img_width" value="<?php echo esc_attr(get_option('pro_img_width','100')); ?>"><p class="description"><?php echo esc_html('Value in px (Default: 100).','make-cart-to-quote-for-woocommerce'); ?></p>
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Image Border Radius','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="number" name="pro_img_radius" value="<?php echo esc_attr(get_option('pro_img_radius','0')); ?>"><p class="description"><?php echo esc_html('Value in px.','make-cart-to-quote-for-woocommerce'); ?></p>
		                </td>
		            </tr>
		        </table>
			</div>
			<div class="quote_form_footer">
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Form Setting','make-cart-to-quote-for-woocommerce'); ?></h2>
		            <tr>
		                <th><label><?php echo esc_html('Form Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#f2f2f2" name="footer_form_back_color" value="<?php echo esc_attr(get_option('footer_form_back_color','#f2f2f2')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Footer Title Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="form_title_color" value="<?php echo esc_attr(get_option('form_title_color','#000000')); ?>">
		                </td>
		            </tr>
				</table>
			</div>
			<div class="quote_body_footer">
				<table class="form-table mctqfw_style">
					<h2><?php echo esc_html('Quote Sidebar Footer','make-cart-to-quote-for-woocommerce'); ?></h2>
					<tr>
		                <th><label><?php echo esc_html('Footer Button Text','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" name="footer_btn_text" value="<?php echo esc_attr(get_option('footer_btn_text','SEND QUOTE')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Footer Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ffffff" name="footer_back_color" value="<?php echo esc_attr(get_option('footer_back_color','#ffffff')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Footer Border Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#b7b7b7" name="footer_border_color" value="<?php echo esc_attr(get_option('footer_border_color','#b7b7b7')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#3cb247" name="footer_btn_back_color" value="<?php echo esc_attr(get_option('footer_btn_back_color','#3cb247')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Text Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#ffffff" name="footer_btn_text_color" value="<?php echo esc_attr(get_option('footer_btn_text_color','#ffffff')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Hover Background Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#3cb247" name="footer_btn_back_hover_color" value="<?php echo esc_attr(get_option('footer_btn_back_hover_color','#3cb247')); ?>">
		                </td>
		            </tr>
		            <tr>
		                <th><label><?php echo esc_html('Button Hover Text Color','make-cart-to-quote-for-woocommerce'); ?></label></th>
		                <td>
		                    <input type="text" data-alpha-enabled="true" class="color-picker" data-default-color="#000000" name="footer_btn_text_hover_color" value="<?php echo esc_attr(get_option('footer_btn_text_hover_color','#000000')); ?>">
		                </td>
		            </tr>
				</table>
			</div>
	    </div>
	    <p class="submit">
            <?php wp_nonce_field('mctqfw_save_options', 'mctqfw_nonce'); ?>
            <input type="hidden" name="action" value="mctqfw_save_option">
            <input type="submit" value="Save changes" name="submit" class="button-primary" id="mctqfw-btn-space">
        </p>
	</form>
</div>
<?php
}

add_action('init','mctqfw_save_setting_type');
function mctqfw_save_setting_type(){
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'mctqfw_save_option') {
    	if (isset($_REQUEST['mctqfw_nonce']) && wp_verify_nonce($_REQUEST['mctqfw_nonce'], 'mctqfw_save_options')) {
	    	if (isset($_REQUEST['atqb_enable'])) {
				update_option('atqb_enable',sanitize_text_field($_REQUEST['atqb_enable']));
			}else{
				update_option('atqb_enable','');
			}
			if (isset($_REQUEST['atqb_remove_single_prod'])) {
				update_option('atqb_remove_single_prod',sanitize_text_field($_REQUEST['atqb_remove_single_prod']));
			}else{
				update_option('atqb_remove_single_prod','');
			}
			if (isset($_REQUEST['atqb_remove_shop_page'])) {
				update_option('atqb_remove_shop_page',sanitize_text_field($_REQUEST['atqb_remove_shop_page']));
			}else{
				update_option('atqb_remove_shop_page','');
			}
			if (isset($_REQUEST['quote_button_customize'])) {
				update_option('quote_button_customize',sanitize_text_field($_REQUEST['quote_button_customize']));
			}else{
				update_option('quote_button_customize','');
			}
			if (isset($_REQUEST['remove_add_to_cart'])) {
				update_option('remove_add_to_cart',sanitize_text_field($_REQUEST['remove_add_to_cart']));
			}else{
				update_option('remove_add_to_cart','');
			}
			if (isset($_REQUEST['remove_woo_price'])) {
				update_option('remove_woo_price',sanitize_text_field($_REQUEST['remove_woo_price']));
			}else{
				update_option('remove_woo_price','');
			}
			if (isset($_REQUEST['mob_enable'])) {
				update_option('mob_enable',sanitize_text_field($_REQUEST['mob_enable']));
			}else{
				update_option('mob_enable','');
			}
			if (isset($_REQUEST['quote_btn_bg_color'])) {
				update_option('quote_btn_bg_color',sanitize_text_field($_REQUEST['quote_btn_bg_color']));
			}else{
				update_option('quote_btn_bg_color','');
			}
			if (isset($_REQUEST['quote_btn_text_color'])) {
				update_option('quote_btn_text_color',sanitize_text_field($_REQUEST['quote_btn_text_color']));
			}else{
				update_option('quote_btn_text_color','');
			}
			if (isset($_REQUEST['quote_btn_font_size'])) {
				update_option('quote_btn_font_size',sanitize_text_field($_REQUEST['quote_btn_font_size']));
			}else{
				update_option('quote_btn_font_size','');
			}
			if (isset($_REQUEST['quote_img'])) {
				update_option('quote_img',sanitize_text_field($_REQUEST['quote_img']));
			}else{
				update_option('quote_img','');
			}
			if (isset($_REQUEST['mctqfw_quote_color'])) {
				update_option('mctqfw_quote_color',sanitize_text_field($_REQUEST['mctqfw_quote_color']));
			}else{
				update_option('mctqfw_quote_color','');
			}
			if (isset($_REQUEST['mctqfw_quote_bg_color'])) {
				update_option('mctqfw_quote_bg_color',sanitize_text_field($_REQUEST['mctqfw_quote_bg_color']));
			}else{
				update_option('mctqfw_quote_bg_color','');
			}
			if (isset($_REQUEST['bar_width'])) {
				update_option('bar_width',sanitize_text_field($_REQUEST['bar_width']));
			}else{
				update_option('bar_width','');
			}
			if (isset($_REQUEST['barhead_color'])) {
				update_option('barhead_color',sanitize_text_field($_REQUEST['barhead_color']));
			}else{
				update_option('barhead_color','');
			}
			if (isset($_REQUEST['barhead_border_color'])) {
				update_option('barhead_border_color',sanitize_text_field($_REQUEST['barhead_border_color']));
			}else{
				update_option('barhead_border_color','');
			}
			if (isset($_REQUEST['barhead_title_color'])) {
				update_option('barhead_title_color',sanitize_text_field($_REQUEST['barhead_title_color']));
			}else{
				update_option('barhead_title_color','');
			}
			if (isset($_REQUEST['barhead_border_style'])) {
				update_option('barhead_border_style',sanitize_text_field($_REQUEST['barhead_border_style']));
			}else{
				update_option('barhead_border_style','');
			}
			if (isset($_REQUEST['quote_list_backcolor'])) {
				update_option('quote_list_backcolor',sanitize_text_field($_REQUEST['quote_list_backcolor']));
			}else{
				update_option('quote_list_backcolor','');
			}
			if (isset($_REQUEST['pro_list_title_kcolor'])) {
				update_option('pro_list_title_kcolor',sanitize_text_field($_REQUEST['pro_list_title_kcolor']));
			}else{
				update_option('pro_list_title_kcolor','');
			}
			if (isset($_REQUEST['pro_title_hover_color'])) {
				update_option('pro_title_hover_color',sanitize_text_field($_REQUEST['pro_title_hover_color']));
			}else{
				update_option('pro_title_hover_color','');
			}
			if (isset($_REQUEST['pro_price_color'])) {
				update_option('pro_price_color',sanitize_text_field($_REQUEST['pro_price_color']));
			}else{
				update_option('pro_price_color','');
			}
			if (isset($_REQUEST['pro_img_width'])) {
				update_option('pro_img_width',sanitize_text_field($_REQUEST['pro_img_width']));
			}else{
				update_option('pro_img_width','');
			}
			if (isset($_REQUEST['pro_img_radius'])) {
				update_option('pro_img_radius',sanitize_text_field($_REQUEST['pro_img_radius']));
			}else{
				update_option('pro_img_radius','');
			}
			if (isset($_REQUEST['footer_form_back_color'])) {
				update_option('footer_form_back_color',sanitize_text_field($_REQUEST['footer_form_back_color']));
			}else{
				update_option('footer_form_back_color','');
			}
			if (isset($_REQUEST['form_title_color'])) {
				update_option('form_title_color',sanitize_text_field($_REQUEST['form_title_color']));
			}else{
				update_option('form_title_color','');
			}
			update_option('footer_btn_text',sanitize_text_field($_REQUEST['footer_btn_text']));
			if (isset($_REQUEST['footer_back_color'])) {
				update_option('footer_back_color',sanitize_text_field($_REQUEST['footer_back_color']));
			}else{
				update_option('footer_back_color','');
			}
			if (isset($_REQUEST['footer_border_color'])) {
				update_option('footer_border_color',sanitize_text_field($_REQUEST['footer_border_color']));
			}else{
				update_option('footer_border_color','');
			}
			if (isset($_REQUEST['footer_btn_back_color'])) {
				update_option('footer_btn_back_color',sanitize_text_field($_REQUEST['footer_btn_back_color']));
			}else{
				update_option('footer_btn_back_color','');
			}
			if (isset($_REQUEST['footer_btn_text_color'])) {
				update_option('footer_btn_text_color',sanitize_text_field($_REQUEST['footer_btn_text_color']));
			}else{
				update_option('footer_btn_text_color','');
			}
			if (isset($_REQUEST['footer_btn_back_hover_color'])) {
				update_option('footer_btn_back_hover_color',sanitize_text_field($_REQUEST['footer_btn_back_hover_color']));
			}else{
				update_option('footer_btn_back_hover_color','');
			}
			if (isset($_REQUEST['footer_btn_text_hover_color'])) {
				update_option('footer_btn_text_hover_color',sanitize_text_field($_REQUEST['footer_btn_text_hover_color']));
			}else{
				update_option('footer_btn_text_hover_color','');
			}

			wp_redirect( admin_url( '/admin.php?page=mctqfw_cart_to_quote_generator&message=success' ));
		}
	}
}

register_activation_hook(MCTQFW_plugin_file, 'MCTQFW_install_default_value');

function MCTQFW_install_default_value() {
    update_option('atqb_enable','true');
    update_option('mob_enable','true');
    update_option('trigger_enable','true');
    update_option('quote_img','quote_icon_1');
}


add_action('init','mctqfw_quote_svg');
function mctqfw_quote_svg(){
    global $mctqfw_quote_icon;

    $mctqfw_quote_icon = [
        'quote_icon_1' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="35" height="35" id="Layer_1" x="0px" y="0px" viewBox="0 0 122.88 92.81" style="enable-background:new 0 0 122.88 92.81" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M106.97,92.81H84.89c-8.5,0-15.45-6.95-15.45-15.45c0-31.79-8.12-66.71,30.84-76.68 c17.65-4.51,22.25,14.93,3.48,16.27c-11.45,0.82-13.69,8.22-14.04,19.4h17.71c8.5,0,15.45,6.95,15.45,15.45v25.09 C122.88,85.65,115.72,92.81,106.97,92.81L106.97,92.81z M38.23,92.81H16.15c-8.5,0-15.45-6.95-15.45-15.45 c0-31.79-8.12-66.71,30.84-76.68C49.2-3.84,53.8,15.6,35.02,16.95c-11.45,0.82-13.69,8.22-14.04,19.4H38.7 c8.5,0,15.45,6.95,15.45,15.45v25.09C54.14,85.65,46.98,92.81,38.23,92.81L38.23,92.81z"/></g></svg>',

        'quote_icon_2' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" width="35" height="35" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 379.51"><path d="M212.27 33.98C131.02 56.52 78.14 103.65 64.99 185.67c-3.58 22.32 1.42 5.46 16.55-5.86 49.4-36.96 146.53-23.88 160.01 60.56 27.12 149.48-159.79 175.36-215.11 92.8-12.87-19.19-21.39-41.59-24.46-66.19C-11.35 159.99 43.48 64.7 139.8 19.94c17.82-8.28 36.6-14.76 56.81-19.51 10.12-2.05 17.47 3.46 20.86 12.77 2.87 7.95 3.85 16.72-5.2 20.78zm267.78 0c-81.25 22.54-134.14 69.67-147.28 151.69-3.58 22.32 1.42 5.46 16.55-5.86 49.4-36.96 146.53-23.88 160 60.56 27.13 149.48-159.78 175.36-215.1 92.8-12.87-19.19-21.39-41.59-24.46-66.19C256.43 159.99 311.25 64.7 407.58 19.94 425.4 11.66 444.17 5.18 464.39.43c10.12-2.05 17.47 3.46 20.86 12.77 2.87 7.95 3.85 16.72-5.2 20.78z"/></svg>',

        'quote_icon_3' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" width="35" height="35" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 358.87"><path fill-rule="nonzero" d="M128.09 358.87c-42.61 0-74.76-13.99-96.41-41.98-10.59-13.68-18.52-29.23-23.81-46.64C2.62 252.99 0 234.02 0 213.36c0-23.45 3.27-45.8 9.81-67.03 6.55-21.24 16.35-41.25 29.38-60 13.05-18.78 28.97-35.34 47.76-49.66 18.67-14.24 40.16-26.26 64.44-36.03a8.843 8.843 0 0 1 8.51 1.06l88.49 56.56c4.12 2.62 5.33 8.09 2.71 12.21a8.712 8.712 0 0 1-4.07 3.41c-16.64 6.98-31.71 14.08-45.19 21.31-13.47 7.21-25.48 14.61-36.02 22.19-10.22 7.33-19.04 15.44-26.45 24.29-2.46 2.94-4.76 5.95-6.9 9.03 4.65-1.44 9.61-2.16 14.88-2.16 13.55 0 26.28 2.12 38.18 6.36 11.9 4.25 22.93 10.62 33.08 19.1 10.43 8.72 18.26 19.37 23.47 31.94 5.13 12.35 7.69 26.44 7.69 42.26 0 18.18-2.7 34.19-8.08 48.01-5.51 14.18-13.82 25.97-24.88 35.33-10.85 9.17-23.79 16.05-38.81 20.61-14.74 4.48-31.38 6.72-49.91 6.72zm-82.4-52.81c18.08 23.36 45.55 35.05 82.4 35.05 16.93 0 31.86-1.96 44.77-5.89 12.64-3.84 23.48-9.58 32.5-17.21 8.79-7.45 15.41-16.84 19.82-28.18 4.55-11.69 6.83-25.57 6.83-41.63 0-13.52-2.1-25.34-6.3-35.45-4.11-9.9-10.27-18.28-18.48-25.15-8.49-7.1-17.72-12.43-27.67-15.97-9.95-3.56-20.7-5.33-32.21-5.33-9.05 0-16.67 3.33-22.85 9.99-3.14 3.35-8.37 3.77-12.01.87l-4.44-3.55c-3.17-2.43-4.4-6.79-2.71-10.6 5.26-11.83 12.07-22.74 20.42-32.72 8.33-9.93 18.24-19.03 29.72-27.28 11.16-8.02 23.84-15.83 38.03-23.44 9.78-5.24 20.23-10.36 31.34-15.36L153.82 18.8c-21.08 8.84-39.78 19.5-56.11 31.95-17.27 13.17-31.92 28.41-43.94 45.71-12.03 17.31-21.04 35.68-27.02 55.08-5.99 19.42-8.99 40.03-8.99 61.82 0 19.06 2.35 36.32 7.04 51.76 4.64 15.3 11.61 28.95 20.89 40.94zm341.92 52.81c-42.62 0-74.76-13.99-96.42-41.98-10.58-13.68-18.52-29.23-23.81-46.63-5.24-17.26-7.86-36.24-7.86-56.9 0-23.45 3.27-45.8 9.81-67.03 6.55-21.24 16.35-41.25 29.38-60 13.04-18.78 28.97-35.34 47.76-49.66 18.66-14.24 40.15-26.25 64.44-36.03a8.827 8.827 0 0 1 8.5 1.06l88.5 56.56c4.11 2.62 5.33 8.09 2.7 12.21a8.737 8.737 0 0 1-4.06 3.41c-16.64 6.98-31.71 14.08-45.2 21.31-13.46 7.21-25.48 14.61-36.02 22.19-10.21 7.33-19.03 15.44-26.45 24.29a128.55 128.55 0 0 0-6.94 9.09c4.71-1.48 9.69-2.22 14.93-2.22 13.54 0 26.28 2.12 38.17 6.36l.44.17c11.76 4.26 22.65 10.57 32.64 18.93 10.44 8.72 18.26 19.37 23.48 31.94 5.12 12.35 7.68 26.44 7.68 42.26 0 18.18-2.69 34.18-8.07 48.01-5.52 14.18-13.82 25.97-24.89 35.33-10.84 9.17-23.78 16.05-38.81 20.61-14.74 4.48-31.38 6.72-49.9 6.72zm-82.4-52.81c18.08 23.37 45.54 35.05 82.4 35.05 16.93 0 31.86-1.96 44.77-5.89 12.63-3.84 23.47-9.58 32.49-17.21 8.8-7.45 15.41-16.84 19.83-28.18 4.54-11.69 6.82-25.57 6.82-41.63 0-13.52-2.1-25.34-6.3-35.45-4.1-9.9-10.27-18.28-18.48-25.15-8.4-7.02-17.5-12.31-27.27-15.84l-.4-.13c-9.95-3.56-20.69-5.33-32.2-5.33-4.61 0-8.79.82-12.51 2.45-3.75 1.63-7.21 4.16-10.35 7.54-3.14 3.35-8.37 3.77-12.01.87l-4.44-3.55a8.86 8.86 0 0 1-2.71-10.6c5.26-11.83 12.07-22.73 20.43-32.71 8.32-9.94 18.23-19.04 29.71-27.29 11.16-8.02 23.85-15.83 38.04-23.44 9.77-5.24 20.22-10.36 31.34-15.36L413.33 18.8c-21.07 8.84-39.78 19.5-56.1 31.95-17.28 13.17-31.93 28.41-43.95 45.71-12.03 17.31-21.04 35.68-27.02 55.08-5.99 19.42-8.98 40.03-8.98 61.82 0 19.07 2.35 36.33 7.04 51.76 4.64 15.3 11.61 28.95 20.89 40.94z"/></svg>',

        'quote_icon_4' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="35" height="35" id="Layer_1" x="0px" y="0px" viewBox="0 0 122.88 92.81" style="enable-background:new 0 0 122.88 92.81" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M15.91,0h22.08c8.5,0,15.45,6.95,15.45,15.45c0,31.79,8.13,66.71-30.84,76.68 C4.94,96.64,0.34,77.2,19.12,75.86c11.45-0.82,13.69-8.22,14.04-19.4H15.45C6.95,56.45,0,49.5,0,41.01V15.91C0,7.16,7.16,0,15.91,0 L15.91,0z M84.65,0h22.08c8.5,0,15.45,6.95,15.45,15.45c0,31.79,8.13,66.71-30.84,76.68c-17.65,4.51-22.25-14.93-3.48-16.27 c11.45-0.82,13.69-8.22,14.04-19.4H84.18c-8.5,0-15.45-6.95-15.45-15.45V15.91C68.74,7.16,75.9,0,84.65,0L84.65,0z"/></g></svg>',

        'quote_icon_5' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" width="35" height="35" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 379.51"><path d="M299.73 345.54c81.25-22.55 134.13-69.68 147.28-151.7 3.58-22.31-1.42-5.46-16.55 5.86-49.4 36.97-146.53 23.88-160.01-60.55C243.33-10.34 430.24-36.22 485.56 46.34c12.87 19.19 21.39 41.59 24.46 66.19 13.33 106.99-41.5 202.28-137.82 247.04-17.82 8.28-36.6 14.76-56.81 19.52-10.12 2.04-17.47-3.46-20.86-12.78-2.87-7.95-3.85-16.72 5.2-20.77zm-267.78 0c81.25-22.55 134.14-69.68 147.28-151.7 3.58-22.31-1.42-5.46-16.55 5.86-49.4 36.97-146.53 23.88-160-60.55-27.14-149.49 159.78-175.37 215.1-92.81 12.87 19.19 21.39 41.59 24.46 66.19 13.33 106.99-41.5 202.28-137.82 247.04-17.82 8.28-36.59 14.76-56.81 19.52-10.12 2.04-17.47-3.46-20.86-12.78-2.87-7.95-3.85-16.72 5.2-20.77z"/></svg>',

        'quote_icon_6' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" width="35" height="35" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 358.88"><path fill-rule="nonzero" d="M383.91 0c42.61 0 74.76 14 96.41 41.99 10.59 13.68 18.53 29.23 23.81 46.63 5.25 17.26 7.87 36.24 7.87 56.9 0 23.45-3.27 45.79-9.81 67.02-6.55 21.24-16.35 41.25-29.38 60.01-13.05 18.77-28.97 35.33-47.76 49.66-18.67 14.24-40.16 26.25-64.44 36.03a8.869 8.869 0 0 1-8.51-1.06l-88.49-56.56c-4.12-2.63-5.33-8.1-2.7-12.21a8.769 8.769 0 0 1 4.06-3.42c16.64-6.98 31.71-14.08 45.19-21.3 13.47-7.22 25.48-14.62 36.02-22.19 10.22-7.34 19.04-15.44 26.45-24.3 2.46-2.93 4.76-5.94 6.9-9.03-4.65 1.45-9.61 2.17-14.88 2.17-13.52 0-26.24-2.13-38.14-6.37-11.93-4.24-22.97-10.61-33.12-19.1-10.43-8.71-18.26-19.37-23.47-31.94-5.12-12.35-7.69-26.44-7.69-42.26 0-18.17 2.7-34.18 8.08-48.01 5.51-14.18 13.82-25.97 24.88-35.33 10.85-9.17 23.79-16.05 38.81-20.61C348.74 2.24 365.38 0 383.91 0zm82.4 52.81c-18.08-23.36-45.55-35.05-82.4-35.05-16.93 0-31.86 1.97-44.78 5.89-12.63 3.84-23.47 9.58-32.49 17.22-8.79 7.44-15.41 16.84-19.82 28.18-4.55 11.69-6.83 25.57-6.83 41.62 0 13.52 2.11 25.35 6.3 35.46 4.11 9.89 10.27 18.28 18.48 25.14 8.49 7.1 17.72 12.43 27.67 15.98 9.95 3.55 20.69 5.33 32.21 5.33 9.05 0 16.67-3.34 22.85-10 3.14-3.34 8.37-3.77 12.01-.87l4.44 3.56c3.17 2.42 4.4 6.78 2.71 10.59-5.25 11.83-12.07 22.74-20.42 32.72-8.32 9.94-18.23 19.04-29.72 27.29-11.16 8.01-23.84 15.83-38.03 23.43-9.78 5.24-20.23 10.36-31.34 15.37l71.04 45.41c21.07-8.85 39.77-19.5 56.1-31.95 17.28-13.18 31.92-28.42 43.94-45.71 12.03-17.31 21.04-35.68 27.02-55.08 5.99-19.43 8.99-40.04 8.99-61.82 0-19.07-2.35-36.33-7.04-51.76-4.64-15.3-11.61-28.96-20.89-40.95zM124.39 0c42.62 0 74.76 14 96.42 41.99 10.58 13.68 18.52 29.23 23.81 46.63 5.24 17.26 7.87 36.24 7.87 56.9 0 47.08-13.07 89.43-39.2 127.03-13.04 18.77-28.97 35.33-47.76 49.66-18.66 14.24-40.15 26.25-64.44 36.03a8.876 8.876 0 0 1-8.51-1.06L4.09 300.62c-4.12-2.63-5.33-8.1-2.7-12.21a8.769 8.769 0 0 1 4.06-3.42c16.64-6.98 31.71-14.08 45.2-21.3 13.47-7.22 25.48-14.62 36.02-22.19 10.21-7.34 19.03-15.44 26.45-24.3 2.47-2.95 4.78-5.98 6.94-9.09-4.71 1.48-9.69 2.23-14.93 2.23-13.54 0-26.27-2.13-38.17-6.37l-.44-.17c-11.76-4.26-22.64-10.57-32.64-18.93-10.43-8.71-18.26-19.37-23.48-31.94-5.12-12.35-7.68-26.44-7.68-42.26 0-18.17 2.69-34.18 8.07-48.01 5.52-14.18 13.82-25.97 24.89-35.33 10.84-9.17 23.78-16.05 38.8-20.61C89.22 2.24 105.87 0 124.39 0zm82.4 52.81c-18.08-23.36-45.54-35.05-82.4-35.05-16.93 0-31.86 1.97-44.77 5.89-12.64 3.84-23.47 9.58-32.49 17.22-8.8 7.44-15.41 16.84-19.82 28.18-4.55 11.69-6.83 25.57-6.83 41.62 0 13.52 2.1 25.35 6.3 35.46 4.1 9.89 10.27 18.28 18.48 25.14 8.4 7.03 17.5 12.31 27.27 15.85l.4.13c9.95 3.55 20.69 5.33 32.2 5.33 4.61 0 8.79-.82 12.51-2.45 3.75-1.64 7.21-4.16 10.35-7.55 3.14-3.34 8.37-3.77 12-.87l4.45 3.56a8.843 8.843 0 0 1 2.71 10.59 138.097 138.097 0 0 1-20.43 32.72c-8.32 9.94-18.23 19.04-29.71 27.29-11.16 8.01-23.85 15.83-38.04 23.43-9.78 5.24-20.22 10.36-31.34 15.37l71.04 45.41c21.07-8.85 39.78-19.5 56.1-31.95 17.28-13.18 31.93-28.42 43.95-45.71 24-34.54 36-73.51 36-116.9 0-19.07-2.34-36.33-7.03-51.76-4.65-15.3-11.62-28.96-20.9-40.95z"/></svg>',

        'quote_icon_7' => '<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" id="Layer_1" data-name="Layer 1" viewBox="0 0 122.88 108.51"><title>quote</title><path d="M63.09,0h.07C79.87.55,94.88,6.5,105.63,15.71c11,9.46,17.65,22.35,17.23,36.44v0a43.29,43.29,0,0,1-7.66,23.16A55.15,55.15,0,0,1,95.63,92.62a72.81,72.81,0,0,1-28.39,8.64,62.2,62.2,0,0,1-26.46-3.42L5.31,108.51,15.49,82.85a49,49,0,0,1-10-12.29A40.8,40.8,0,0,1,.24,45.81,45.39,45.39,0,0,1,10,22.61,55.78,55.78,0,0,1,26.33,8.8,67.06,67.06,0,0,1,43.85,2,73.89,73.89,0,0,1,63.07,0ZM44,35.19h8.11a5.68,5.68,0,0,1,5.67,5.67c0,11.67,3,24.49-11.32,28.15-6.48,1.66-8.17-5.48-1.28-6,4.2-.3,5-3,5.16-7.12H43.79a5.68,5.68,0,0,1-5.67-5.67V41A5.86,5.86,0,0,1,44,35.19Zm26.77,0h8.1a5.69,5.69,0,0,1,5.68,5.67c0,11.67,3,24.49-11.33,28.15-6.48,1.66-8.17-5.48-1.27-6,4.2-.3,5-3,5.15-7.12h-6.5a5.68,5.68,0,0,1-5.67-5.67V41a5.85,5.85,0,0,1,5.84-5.84ZM62.86,9.4H62.8A64.24,64.24,0,0,0,46,11.1,57.64,57.64,0,0,0,31,17,46.7,46.7,0,0,0,17.38,28.41a35.94,35.94,0,0,0-7.81,18.4,31.49,31.49,0,0,0,4,19.12A41.23,41.23,0,0,0,24,77.77l2.73,2.16-5.55,14,20-6,1.5.59A52.84,52.84,0,0,0,66.47,91.9a63.56,63.56,0,0,0,24.72-7.54,45.76,45.76,0,0,0,16.26-14.27,34,34,0,0,0,6-18.17v0c.32-11.11-5-21.4-14-29-9.21-7.9-22.15-13-36.65-13.44Z"/></svg>',

        'quote_icon_8' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" width="35" height="35" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 451.17"><path fill-rule="nonzero" d="M262.8.14h.29c69.73 2.11 132.31 26.93 177.12 65.35 45.9 39.35 73.43 92.99 71.71 151.56v.29c-1.09 35.02-12.53 67.7-31.87 96.15-19.82 29.15-47.89 53.83-81.5 71.98-37.22 20.09-77.61 32.59-118.38 36-37.2 3.12-74.77-1.3-110.64-14.38L23.09 451.17l41.98-105.89c-18.02-16.02-32.08-33.47-42.44-51.71-18.67-32.9-25.39-68.32-21.62-102.85 3.72-34.27 17.8-67.57 40.74-96.53 17.64-22.28 40.51-41.97 67.88-57.5 22.12-12.56 46.75-22.28 73.01-28.53C207.81 2.18 234.78-.69 262.8.14zm-97.75 279.2c32.01-8.88 52.85-27.45 58.03-59.77 1.41-8.79-.56-2.15-6.52 2.31-19.47 14.56-57.74 9.41-63.05-23.86-10.69-58.91 62.96-69.1 84.76-36.57 5.07 7.56 8.43 16.38 9.64 26.08 5.25 42.16-16.36 79.7-54.31 97.34-7.02 3.27-14.41 5.81-22.38 7.69-3.99.81-6.89-1.36-8.22-5.04-1.13-3.12-1.52-6.58 2.05-8.18zm110.88 0c32.02-8.88 52.85-27.45 58.03-59.77 1.41-8.79-.56-2.15-6.52 2.31-19.47 14.56-57.74 9.41-63.05-23.86-10.68-58.91 62.96-69.1 84.76-36.57 5.07 7.56 8.43 16.38 9.64 26.08 5.25 42.16-16.35 79.7-54.31 97.34-7.02 3.27-14.42 5.81-22.38 7.69-3.99.81-6.88-1.36-8.22-5.04-1.14-3.12-1.52-6.58 2.05-8.18zM262.08 36.88h-.28c-24.47-.72-48.25 1.83-70.66 7.16-23.03 5.49-44.39 13.88-63.35 24.64-23.26 13.2-42.52 29.73-57.22 48.28-18.6 23.48-29.99 50.27-32.96 77.64-2.96 27.11 2.34 54.94 17.01 80.81 10.08 17.76 24.57 34.67 43.8 49.91l10.73 8.5-23.85 60.15 85.68-25.79 5.91 2.32c32.24 12.7 66.36 17.07 100.25 14.22 35.73-2.99 71.21-13.98 103.97-31.68 28.52-15.39 52.13-36.02 68.53-60.16 15.53-22.83 24.7-48.91 25.53-76.69l.01-.29c1.32-46.83-21.27-90.19-58.9-122.45-38.76-33.24-93.23-54.71-154.2-56.57z"/></svg>',

        'quote_icon_9' => '<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" width="35" height="35" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 440.9"><path fill-rule="nonzero" d="M489.67 388.48V42.37h-49.41c-4.49 11.72-15.85 20.03-29.15 20.03-13.3 0-24.65-8.31-29.14-20.03h-47.49c-4.5 11.72-15.85 20.03-29.15 20.03-13.3 0-24.65-8.31-29.15-20.03H228.7c-4.5 11.72-15.85 20.03-29.15 20.03-13.3 0-24.65-8.31-29.15-20.03h-47.48c-4.49 11.72-15.84 20.03-29.14 20.03-13.31 0-24.66-8.31-29.15-20.03H22.34v346.09c50.6 20.01 142.51 30.1 234.64 30.1 91.53 0 182.56-9.97 232.69-30.08zM411.11 0c13.3 0 24.66 8.32 29.15 20.04h60.57c6.17 0 11.17 5 11.17 11.16v364.64c-.02 4.25-2.47 8.31-6.59 10.17-51.39 23.32-150.02 34.89-248.43 34.89-98.33 0-197.25-11.57-249.39-34.48C3.18 404.93 0 400.76 0 395.84V31.2c0-6.16 5-11.16 11.17-11.16h53.46C69.12 8.32 80.47 0 93.78 0c13.3 0 24.65 8.32 29.14 20.04h47.48C174.9 8.32 186.25 0 199.55 0c13.3 0 24.65 8.32 29.15 20.04h47.48C280.68 8.32 292.03 0 305.33 0c13.3 0 24.65 8.32 29.15 20.04h47.49C386.46 8.32 397.81 0 411.11 0zM125.1 333.69l-11.34 3.07c-14.93 0-24.97-3.82-30.12-11.47-2.67-3.81-4.54-8.1-5.6-12.85-1.07-4.76-1.6-10.52-1.6-17.27 0-15.2 2.84-26.28 8.53-33.26 5.69-6.98 15.91-10.46 30.66-10.46s25.02 3.51 30.79 10.53c5.78 7.02 8.67 18.08 8.67 33.19 0 11.29-2.36 20.4-7.07 27.33l9.6 5.59-6.93 15.34-25.59-4.67v-5.07zm-20-17.73h11.06c3.65 0 6.29-.42 7.93-1.26 1.65-.84 2.47-2.78 2.47-5.8v-34.66h-11.2c-3.55 0-6.15.42-7.8 1.27-1.64.84-2.46 2.77-2.46 5.8v34.65zm84.65-45.85v49.18h4.12c3.21 0 5.54-.33 7.01-.99 1.46-.67 2.2-2.2 2.2-4.6v-43.59h26.65v66.65H216.4l-7.73-7.6c-5.33 6.4-12.93 9.6-22.79 9.6-7.91 0-13.69-1.69-17.33-5.07-3.65-3.37-5.47-9.01-5.47-16.93v-46.65h26.67zm58.98 59.92c-5.55-5.82-8.33-14.69-8.33-26.6 0-11.9 2.78-20.77 8.33-26.59 5.55-5.82 14.55-8.73 27-8.73 12.44 0 21.43 2.91 26.99 8.73 5.55 5.82 8.33 14.69 8.33 26.59 0 11.91-2.78 20.78-8.33 26.6-5.56 5.82-14.55 8.73-26.99 8.73-12.45 0-21.45-2.91-27-8.73zm20.33-36.86v26.12h4.13c3.2 0 5.54-.33 7-.99 1.47-.67 2.2-2.2 2.2-4.6v-26.13h-4.13c-3.2 0-5.53.34-7 1-1.47.67-2.2 2.2-2.2 4.6zm48.66-5.86v-17.2h6.66v-10.66l26.66-6.67v17.33h9.2v17.2h-9.2v30.39c2.31.44 4.93.75 7.86.93l-1.19 19.6c-12.18 0-20.09-.67-23.73-2.01-4.53-1.59-7.38-3.72-8.54-6.39-.71-1.6-1.06-3.46-1.06-5.6v-36.92h-6.66zm78.51 26.79v4.67c2.67.26 5.34.39 8 .39 8.27 0 16.61-1.33 25.06-3.99l3.2 19.59c-9.96 2.67-19.82 4-29.59 4-12.45 0-21.44-2.91-27-8.73-5.55-5.82-8.33-14.69-8.33-26.6 0-11.9 2.78-20.77 8.33-26.59 5.56-5.82 14.53-8.73 26.93-8.73s20.95 1.69 25.66 5.07c4.71 3.37 7.06 9.68 7.06 18.92 0 7.83-2.81 13.45-8.46 16.87-5.64 3.42-15.93 5.13-30.86 5.13zm0-21.99v7.06h5.47c3.19 0 5.55-.35 6.99-1 1.46-.65 2.2-3.79 2.2-6.19v-5.46h-5.46c-3.17 0-5.55.34-7 .99-1.45.65-2.2 2.24-2.2 4.6zM144.41 183.95h-28.74v39.22h-33.8V117.5h69.31l-4.23 25.99h-31.28v16.33h28.74v24.13zm109.05 39.22h-37.19l-13.87-31.45h-7.27v31.45h-33.81V117.5h53.26c24.23 0 36.35 12.35 36.35 37.03 0 16.9-5.25 28.06-15.73 33.47l18.26 35.17zm-58.33-79.68v23.26h7.78c4.06 0 7.03-.44 8.88-1.27 1.84-.83 2.79-2.79 2.79-5.83v-9.06c0-3.04-.95-5-2.79-5.83-1.85-.83-4.83-1.27-8.88-1.27h-7.78zm137.79 39.78H299.1v13.91h41.43v25.99H265.3V117.5h74.38l-4.22 25.99H299.1v15.27h33.82v24.51zm89.6 0h-33.81v13.91h41.42v25.99H354.9V117.5h74.38l-4.22 25.99h-36.35v15.27h33.81v24.51z"/></svg>',
    ];
}