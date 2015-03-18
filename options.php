<?php 
new VT_Web_Options;

class VT_Web_Options 
{
	var $option_group = 'vt_web_options';
	var $page_id = 'vt-web';

	function __construct()
	{
		add_action( 'admin_init', array( &$this, 'admin_init') );
		add_action( 'admin_menu', array( &$this, 'admin_menu') );
	}

	function admin_menu() 
	{  
		add_options_page( 'Veritrans Payment Setting', 'VT Payment', 'manage_options', $this->page_id, array( $this, 'page_options') );
	}

	function admin_footer() 
	{
		?>
		<script>
		jQuery(document).ready(function($){
			$('.nav-tab').click(function(){
				$('.nav-tab').removeClass('nav-tab-active');
				$('.content-tab').removeClass('content-tab-active');
				$(this).addClass('nav-tab-active');
				$('.content-tab' + $(this).attr('href') ).addClass('content-tab-active');
				return false;
			});	

			$('#generate').click(function(){
				var url = "<?php echo home_url('/'); ?>?vt_web";

				$('.product_item').each(function(){ 
					var vt_name = $('[name=vt_name]', $(this)).val();
					var vt_price = $('[name=vt_price]', $(this)).val();
					var vt_qty = $('[name=vt_qty]', $(this)).val();

					url = url + '&vt_name[]=' + encodeURI(vt_name);
					url = url + '&vt_price[]=' + vt_price;
					url = url + '&vt_qty[]=' + vt_qty;
				});

				$('#generated_url').removeAttr('readonly');

				$('#generated_url').html(url).attr('readonly', 'readonly');
				
			});

			$('#add_product_item').click(function(){
				var form = $('#product_items .product_item:first-child').clone();

				$('input[type=text]', form).val('');
				$('.remove_item', form).click(function(){
					$(this).parents('.product_item').remove();
				}).show();

				$(form).appendTo('#product_items');
			});

		});
		</script>
		<?php 
	}

	function admin_init() 
	{  
		register_setting( $this->option_group, $this->option_group );
	}

	function page_options() 
	{
		add_action( 'admin_footer', array( &$this, 'admin_footer') );

		$options = get_option($this->option_group);
		?>

<div class="wrap"> 
	<h2>Veritrans Payment Setting</h2>
	
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="#setting">Setting</a>
		<a class="nav-tab" href="#url_generator">URL Generator</a>
	</h2>

	<div class="content-tab content-tab-active" id="setting">
		<form method="post" action="options.php">
			<?php settings_fields( $this->option_group ); ?>
			<?php do_settings_sections( $this->option_group ); ?>

			<h3>General Setting</h3>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="sandbox_mode">Sandbox</label></th>
					<td>
						<label><input type="checkbox" id="sandbox_mode" name="<?php echo $this->option_group; ?>[sandbox_mode]" value="1" <?php checked(1, $options['sandbox_mode']); ?>/>
							Use in sandbox mode.</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="server_key">Server Key</label></th>
					<td>
						<input type="text" class="regular-text" placeholder="Server Key" id="server_key" name="<?php echo $this->option_group; ?>[server_key]" value="<?php echo esc_attr($options['server_key']); ?>">
						<p class="description">Input server key of Veritrans account (Setting &raquo; Access Key)</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>

	<div class="content-tab" id="url_generator">
		<div id="product_items">
			<div class="product_item">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label>Product Name</label></th>
						<td>
							<input type="text" class="regular-text" placeholder="Product Name" name="vt_name">
							<input type="button" value="Remove" class="button button-secondary remove_item" style="display:none;">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>Price</label></th>
						<td>
							<input type="text" class="regular-text" placeholder="Price Eg: 1000000" name="vt_price">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>Quantity</label></th>
						<td>
							<input type="number" placeholder="Qty" name="vt_qty" value="1">
						</td>
					</tr>
				</table>
			<hr/>
			</div>
		</div>

		<p class="submit">
			<input type="button" value="Add Item Product" class="button button-secondary" id="add_product_item">
			<input type="submit" value="Generate!" class="button button-primary" id="generate">
			&nbsp; &nbsp; 
			<span class="description">Make sure to recheck fields before generating URL.</span>
		</p>
			
		<p>
			<label for="generated_url">Generated URL:</label>
			<textarea class="large-text" id="generated_url" readonly="readonly"></textarea>
			<span class="description">Copy and paste this URL to your checkout link.</span>
		</p>
	</div>

</div>
		<?php 
	}

}
	

