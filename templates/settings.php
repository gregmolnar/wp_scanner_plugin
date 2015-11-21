<div class="wrap">
    <h2>WP Scanner</h2>
    <form method="post" action="options.php">
        <?php @settings_fields('wp_scanner-group'); ?>
        <?php @do_settings_fields('wp_scanner-group'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="api_key">API key</label></th>
                <td><input type="text" name="api_key" id="api_key" value="<?php echo get_option('api_key'); ?>" /></td>
            </tr>
        </table>

        <?php @submit_button(); ?>
    </form>
</div>
