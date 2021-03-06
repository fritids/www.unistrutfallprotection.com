<?php
global $woocommerce_wpml;
if(get_option('wcml_products_to_sync') === false ){
    $woocommerce_wpml->troubleshooting->wcml_sync_variations_update_option();
}

$prod_with_variations = $woocommerce_wpml->troubleshooting->wcml_count_products_with_variations();

?>
<div class="wrap wcml_trblsh">
    <div id="icon-wpml" class="icon32"><br /></div>
    <h2><?php _e('Troubleshooting', 'wpml-wcml') ?></h2>
    <div class="wcml_trbl_warning">
        <h3><?php _e('Please make backup of your database before start synchronization', 'wpml-wcml') ?></h3>
    </div>
    <div class="trbl_variables_products">
        <h3><?php _e('Sync variables products', 'wpml-wcml') ?></h3>
        <ul>
            <li>
                <label>
                    <input type="checkbox" id="wcml_sync_update_product_count" />
                    <?php _e('Update products count:', 'wpml-wcml') ?>
                    <span class="var_status"><?php echo $prod_with_variations; ?></span>&nbsp;<span><?php  _e('products with variations', 'wpml-wcml'); ?></span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" id="wcml_sync_product_variations" checked="checked" />
                    <?php _e('Sync products variations:', 'wpml-wcml') ?>
                    <span class="var_status"><?php echo $prod_with_variations; ?></span>&nbsp;<span><?php _e('left', 'wpml-wcml') ?></span>
                </label>

            </li>
            <li>
                <button type="button" class="button-secondary" id="wcml_sync_variations"><?php _e('Start', 'wpml-wcml') ?></button>
        <input id="count_prod_variat" type="hidden" value="<?php echo $prod_with_variations; ?>"/>
        <span class="wcml_spinner"></span>
            </li>
        </ul>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        //troubleshooting page
        jQuery('#wcml_sync_variations').on('click',function(){
            var field = jQuery(this);
            field.attr('disabled', 'disabled');
            jQuery('.wcml_spinner').css('display','inline-block');

            if(jQuery('#wcml_sync_update_product_count').is(':checked')){
                update_product_count();
            }else{
            sync_variations();
            }
        });
        });

    function update_product_count(){
        jQuery.ajax({
            type : "post",
            url : ajaxurl,
            data : {
                action: "trbl_update_count",
                wcml_nonce: "<?php echo wp_create_nonce('trbl_update_count'); ?>"
            },
            success: function(response) {
                    jQuery('.var_status').each(function(){
                        jQuery(this).html(response);
                    })
                    jQuery('#count_prod_variat').val(response);
                    sync_variations();
            }
    });
    }

    function sync_variations(){
        jQuery.ajax({
            type : "post",
            url : ajaxurl,
            data : {
                action: "trbl_sync_variations",
                wcml_nonce: "<?php echo wp_create_nonce('trbl_sync_variations'); ?>"
            },
            success: function(response) {
                if(jQuery('#count_prod_variat').val() == 0){
                    jQuery('#wcml_sync_variations').removeAttr('disabled');
                    jQuery('.wcml_spinner').hide();
                    jQuery('#wcml_sync_variations').next().fadeOut();
                    jQuery('.var_status').each(function(){
                        jQuery(this).html(0);
                    });
                }else{
                    var left = jQuery('#count_prod_variat').val()-3;
                    if(left < 0 ){
                        left = 0;
                    }
                    jQuery('.var_status').each(function(){
                        jQuery(this).html(left);
                    });
                    jQuery('#count_prod_variat').val(left);
                    sync_variations();
                }
            }
        });
    }
</script>