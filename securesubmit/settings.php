<?php

function event_espresso_securesubmit_payment_settings() {
    global $espresso_premium, $active_gateways;

    if (isset($_POST['update_securesubmit'])) {
        $securesubmit_settings['securesubmit_public_key'] = $_POST['securesubmit_public_key'];
        $securesubmit_settings['securesubmit_secret_key'] = $_POST['securesubmit_secret_key'];
        $securesubmit_settings['securesubmit_currency_symbol'] = $_POST['securesubmit_currency_symbol'];
        $securesubmit_settings['securesubmit_enable_giftcard'] = array_key_exists("securesubmit_enable_giftcard", $_POST) ? $_POST['securesubmit_enable_giftcard'] : 0;
        $securesubmit_settings['header'] = $_POST['header'];
        $securesubmit_settings['force_ssl_return'] = empty($_POST['force_ssl_return']) ? false : true;
        $securesubmit_settings['display_header'] = empty($_POST['display_header']) ? false : true;
        update_option('event_espresso_securesubmit_settings', $securesubmit_settings);
        echo '<div id="message" class="updated fade"><p><strong>' . __('SecureSubmit settings saved.', 'event_espresso') . '</strong></p></div>';
    }
    $securesubmit_settings = get_option('event_espresso_securesubmit_settings');
    if (empty($securesubmit_settings)) {
        $securesubmit_settings['securesubmit_public_key'] = '';
        $securesubmit_settings['securesubmit_secret_key'] = '';
        $securesubmit_settings['securesubmit_currency_symbol'] = 'usd';
        $securesubmit_settings['securesubmit_enable_giftcard'] = 0;
        $securesubmit_settings['header'] = 'Payment Transactions by SecureSubmit';
        $securesubmit_settings['force_ssl_return'] = false;
        $securesubmit_settings['display_header'] = false;
        if (add_option('event_espresso_securesubmit_settings', $securesubmit_settings, '', 'no') == false) {
            update_option('event_espresso_securesubmit_settings', $securesubmit_settings);
        }
    }

    //Open or close the postbox div
    if (empty($_REQUEST['deactivate_securesubmit'])
        && (!empty($_REQUEST['activate_securesubmit'])
            || array_key_exists('securesubmit', $active_gateways))) {
        $postbox_style = '';
    } else {
        $postbox_style = 'closed';
    }
    ?>

    <div class="metabox-holder">
        <div class="postbox <?php echo $postbox_style; ?>">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle">
                <?php _e('SecureSubmit Settings', 'event_espresso'); ?>
            </h3>
            <div class="inside">
                <div class="padding">
                    <?php
                    if (!empty($_REQUEST['activate_securesubmit'])) {
                        $active_gateways['securesubmit'] = dirname(__FILE__);
                        update_option('event_espresso_active_gateways', $active_gateways);
                    }
                    if (!empty($_REQUEST['deactivate_securesubmit'])) {
                        unset($active_gateways['securesubmit']);
                        update_option('event_espresso_active_gateways', $active_gateways);
                    }
                    echo '<ul>';
                    if (array_key_exists('securesubmit', $active_gateways)) {
                        echo '<li id="deactivate_securesubmit" style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&deactivate_securesubmit=true\';" class="red_alert pointer"><strong>' . __('Deactivate SecureSubmit?', 'event_espresso') . '</strong></li>';
                        event_espresso_display_securesubmit_settings();
                    } else {
                        echo '<li id="activate_securesubmit" style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&activate_securesubmit=true\';" class="green_alert pointer"><strong>' . __('Activate SecureSubmit?', 'event_espresso') . '</strong></li>';
                    }
                    echo '</ul>';
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
}

//SecureSubmit Settings Form
function event_espresso_display_securesubmit_settings() {
    $securesubmit_settings = get_option('event_espresso_securesubmit_settings');
    ?>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <table width="99%" border="0" cellspacing="5" cellpadding="5">
            <tr>
                <td valign="top">
                    <ul>
                        <li>
                            <label for="securesubmit_public_key">
                                <?php _e('SecureSubmit Public Key', 'event_espresso'); ?> <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=securesubmit_public_key"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>/images/question-frame.png" width="16" height="16" /></a>
                            </label>
                            <input type="text" name="securesubmit_public_key" size="35" value="<?php echo $securesubmit_settings['securesubmit_public_key']; ?>">
                        </li>
                        <li>
                            <label for="securesubmit_secret_key">
                                <?php _e('SecureSubmit Secret Key', 'event_espresso'); ?> <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=securesubmit_secret_key"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>/images/question-frame.png" width="16" height="16" /></a>
                            </label>
                            <input type="text" name="securesubmit_secret_key" size="35" value="<?php echo $securesubmit_settings['securesubmit_secret_key']; ?>">
                        </li>
                        <li>
                            <label for="securesubmit_enable_giftcard">
                                <?php _e('SecureSubmit Enable Gift Cards', 'event_espresso'); ?> <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=securesubmit_enable_giftcard"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>/images/question-frame.png" width="16" height="16" /></a>
                            </label>
                            <input type="checkbox" name="securesubmit_enable_giftcard" value="1" <?php if(array_key_exists("securesubmit_enable_giftcard", $securesubmit_settings) && $securesubmit_settings['securesubmit_enable_giftcard']){ echo "checked"; }?>>
                        </li>
                    </ul>
                </td>
                <td>
                    <ul>
                    </ul>
                </td>
            </tr>
        </table>
        <p>
            <input type="hidden" name="update_securesubmit" value="update_securesubmit">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update SecureSubmit Settings', 'event_espresso') ?>" id="save_securesubmit_settings" />
        </p>
    </form>
    <div id="securesubmit_public_key" style="display:none">
        <h2>
            <?php _e('SecureSubmit Public Key', 'event_espresso'); ?>
        </h2>
        <p>
            <?php _e('Enter your <a href="https://developer.heartlandpaymentsystems.com/SecureSubmit/Account/" target="_blank">Public Key</a> here.  If you are testing the SecureSubmit gateway, use your Cert public Key, otherwise use your Live public Key.', 'event_espresso'); ?>
        </p>
    </div>
    <div id="securesubmit_secret_key" style="display:none">
        <h2>
            <?php _e('SecureSubmit Secret Key', 'event_espresso'); ?>
        </h2>
        <p>
            <?php _e('Enter your <a href="https://developer.heartlandpaymentsystems.com/SecureSubmit/Account/" target="_blank">Secret Key</a> here.  If you are testing the SecureSubmit gateway, use your Certification Secret Key, otherwise use your Live Secret Key.', 'event_espresso'); ?>
        </p>
    </div>
    <div id="securesubmit_enable_giftcard" style="display:none">
        <h2>
            <?php _e('SecureSubmit Enable Gift Cards', 'event_espresso'); ?>
        </h2>
        <p>
            <?php _e('Check the box to support accepting SecureSubmit gift cards.', 'event_espresso'); ?>
        </p>
    </div>
<?php
}

add_action('action_hook_espresso_display_gateway_settings','event_espresso_securesubmit_payment_settings');
