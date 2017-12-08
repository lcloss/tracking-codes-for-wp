<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; 
}
?>
<div class="wrap">
	<?php if (!empty($_POST)) { ?>
		<div class="updated fade"><p><strong><?php _e('Tracking Codes for WP Options saved!', $this->plugin_slug); ?></strong></p></div>
 	<?php } ?>

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<div id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<form method="POST">
						<?php $options = $this->plugin_settings; ?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e('Google Analytics Code', $this->plugin_slug); ?></th>
								<td><input name="options[general][ga_code]" id="ga-code" value="<?php echo $options['general']['ga_code']; ?>" /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Google Tag Manager Code', $this->plugin_slug); ?></th>
								<td><input name="options[general][gtm_code]" id="gtm-code" value="<?php echo  $options['general']['gtm_code']; ?>" /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Google AdWords Remarketing Code', $this->plugin_slug); ?></th>
								<td><input name="options[general][gar_code]" id="gar-code" value="<?php echo  $options['general']['gar_code']; ?>" /></td>
							</tr>
						</table>
						<?php wp_nonce_field('tab-general'); ?>
						<input type="hidden" value="general" name="tab" />
						<input type="submit" value="<?php _e('Save settings', $this->plugin_slug); ?>" class="button button-primary" name="submit" />
						<input type="button" value="<?php _e('Reset settings', $this->plugin_slug); ?>" class="button button-secondary reset_settings" data-tab="general" name="submit">
					</form>
				</div>
			</div>
		</div>
