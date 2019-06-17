<?php

function espresso_display_securesubmit($payment_data) {
	extract($payment_data);
	global $org_options;
	$securesubmit_settings = get_option('event_espresso_securesubmit_settings');
	if ($securesubmit_settings['force_ssl_return']) {
			$home = str_replace('http://', 'https://', home_url());
		} else {
			$home = home_url();
		}
?>

<div id="securesubmit-payment-option-dv" class="payment-option-dv">

	<a id="securesubmit-payment-option-lnk" class="payment-option-lnk display-the-hidden" rel="securesubmit-payment-option-form" style="cursor:pointer;">
		<img alt="Pay using a Credit Card" src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL; ?>gateways/securesubmit/cards.png">
	</a>	

	<div id="securesubmit-payment-option-form-dv" class="hide-if-js">

<?php
		if ($securesubmit_settings['display_header']) { ?>
		<h3 class="payment_header"><?php echo $securesubmit_settings['header']; ?></h3>
<?php } ?>

		<div class = "event_espresso_form_wrapper">
            <script src="<?php echo plugins_url( 'js/secure.submit-1.1.1.js', __FILE__ ); ?>"></script>
			<form id="securesubmit_payment_form" name="securesubmit_payment_form" method="post" action="<?php echo $home . '/?page_id=' . $org_options['return_url'] . '&r_id=' . $registration_id; ?>">

				<fieldset id="securesubmit-billing-info-dv">
					<h4 class="section-title"><?php _e('Billing Information', 'event_espresso') ?></h4>
					<p>
						<label for="first_name"><?php _e('First Name', 'event_espresso'); ?></label>
						<input name="first_name" type="text" id="securesubmit_first_name" class="required" value="<?php echo $fname ?>" />
					</p>
					<p>
						<label for="last_name"><?php _e('Last Name', 'event_espresso'); ?></label>
						<input name="last_name" type="text" id="securesubmit_last_name" class="required" value="<?php echo $lname ?>" />
					</p>
					<p>
						<label for="email"><?php _e('Email Address', 'event_espresso'); ?></label>
						<input name="email" type="text" id="securesubmit_email" class="required" value="<?php echo $attendee_email ?>" />
					</p>
					<p>
						<label for="address"><?php _e('Address', 'event_espresso'); ?></label>
						<input name="address" type="text" id="securesubmit_address" class="required" value="<?php echo $address ?>" />
					</p>
					<p>
						<label for="city"><?php _e('City', 'event_espresso'); ?></label>
						<input name="city" type="text" id="securesubmit_city" class="required" value="<?php echo $city ?>" />
					</p>
					<p>
						<label for="state"><?php _e('State', 'event_espresso'); ?></label>
						<input name="state" type="text" id="securesubmit_state" class="required" value="<?php echo $state ?>" />
					</p>
					<p>
						<label for="zip"><?php _e('Zip', 'event_espresso'); ?></label>
						<input name="zip" type="text" id="securesubmit_zip" class="required" value="<?php echo $zip ?>" />
					</p>
				</fieldset>

				<fieldset id="securesubmit-credit-card-info-dv">
					<h4 class="section-title"><?php _e('Credit Card Information', 'event_espresso'); ?></h4>
					<p>
						<label for="card_num"><?php _e('Card Number', 'event_espresso'); ?></label>
						<input type="text" class="required" id="card_number" />
					</p>
					<p>
						<label for="card-exp"><?php _e('Expiration Month', 'event_espresso'); ?></label>
						<select id="exp_month" class="required">
							<?php
							$curr_month = date("m");
							for ($i = 1; $i < 13; $i++) {
								$val = $i;
								if ($i < 10) {
									$val = '0' . $i;
								}
								$selected = ($i == $curr_month) ? " selected" : "";
								echo "<option value='$val'$selected>$val</option>";
							}
							?>
						</select>
					</p>
					<p>
						<label for="exp-year"><?php _e('Expiration Year', 'event_espresso'); ?></label>
						<select id="exp_year" class="required">
							<?php
							$curr_year = date("Y");
							for ($i = 0; $i < 10; $i++) {
								$disp_year = $curr_year + $i;
								$selected = ($i == 0) ? " selected" : "";
								echo "<option value='$disp_year'$selected>$disp_year</option>";
							}
							?>
						</select>
					</p>
					<p>
						<label for="cvv"><?php _e('CVC Code', 'event_espresso'); ?></label>
						<input type="text" id="card_cvc"/>
					</p>
				</fieldset>
				
			<?php
			if ($securesubmit_settings['securesubmit_enable_giftcard']) {	
			?>
				<hr />
				<fieldset>
					<h4 class="section-title"><?php _e('Gift Card Information', 'event_espresso'); ?></h4>
					<p>
						<label for="securesubmit_giftcardnumber"><?php _e('Gift Card Number', 'event_espresso'); ?></label>
						<input name="securesubmit_giftcardnumber" type="text" id="securesubmit_giftcardnumber" value="<?php echo $securesubmit_giftcardnumber; ?>" />
					</p>
					<p>
						<label for="securesubmit_giftcardpin"><?php _e('Gift Card PIN', 'event_espresso'); ?></label>
						<input name="securesubmit_giftcardpin" type="text" id="securesubmit_giftcardpin" value="<?php echo $securesubmit_giftcardpin; ?>" />
					</p>
				</fieldset>
			<?php
			}
			?>
				<input name="amount" type="hidden" value="<?php echo number_format($event_cost, 2) ?>" />
				<input name="securesubmit" type="hidden" value="true" />
				<input name="id" type="hidden" value="<?php echo $attendee_id ?>" />
				<p class="event_form_submit">
					<input name="securesubmit_submit" id="securesubmit_submit" class="submit-payment-btn allow-leave-page" type="submit" value="<?php _e('Complete Purchase', 'event_espresso'); ?>" />
				</p>
			</form>
            <div>
                <script>
                    jQuery("#securesubmit_payment_form").SecureSubmit({
                        public_key: "<?php echo $securesubmit_settings['securesubmit_public_key']; ?>",
                        error: function (response) {
                            alert(response.message);
                        }
                    });
                </script>
            </div>
		</div>
		<br/>
		<p class="choose-diff-pay-option-pg">
			<a class="hide-the-displayed" rel="securesubmit-payment-option-form"

style="cursor:pointer;"><?php _e('Choose a different payment option', 'event_espresso'); ?></a>
		</p>

	</div>
</div>
	<?php
}

add_action('action_hook_espresso_display_onsite_payment_gateway', 'espresso_display_securesubmit');
