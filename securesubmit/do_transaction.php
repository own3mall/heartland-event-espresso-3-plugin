<?php

function espresso_transactions_securesubmit_get_attendee_id($attendee_id) {
    if (isset($_REQUEST['id']))
		$attendee_id = $_REQUEST['id'];
	return $attendee_id;
}

function espresso_process_securesubmit($payment_data) {
	global $wpdb;

	$payment_data['txn_details'] = serialize($_REQUEST);
	$payment_data['txn_id'] = 0;
	$payment_data['txn_type'] = 'SecureSubmit';
	$payment_data['payment_status'] = 'Incomplete';
	require_once(dirname(__FILE__) . '/securesubmit.class.php');

	$cls_securesubmit = new Espresso_Clssecuresubmit();
	$securesubmit_settings = get_option('event_espresso_securesubmit_settings');

    $token_value = $_POST['token_value'];
	$exp_month = $_POST['exp_month'];
	$exp_year = $_POST['exp_year'];
	$line_item = "LINEITEM~PRODUCTID=" . $payment_data['attendee_id'] . "+DESCRIPTION=" . $payment_data["event_name"] . "[" . date('m-d-Y', strtotime($payment_data['start_date'])) . "]" . " >> " . $payment_data["fname"] . " " . $payment_data["lname"] . "
							QUANTITY=1 UNITCOST=" . $payment_data['total_cost'];

    $address = new HpsAddress();
    $address->address = $_POST['address'];
    $address->city = $_POST['city'];
    $address->state = $_POST['state'];
    $address->zip = preg_replace('/[^0-9]/', '', $_POST['zip']);

    $cardHolder = new HpsCardHolder();
    $cardHolder->firstName = $_POST['first_name'];
    $cardHolder->lastName = $_POST['last_name'];
    $cardHolder->email = $_POST['email'];
    $cardHolder->address = $address;

	$response = $cls_securesubmit->do_transaction($payment_data['total_cost'], $token_value, $cardHolder, $line_item, $securesubmit_settings);
	if (!empty($response)) {
		$payment_data['txn_details'] = serialize($response->transactionId);
		if (isset($response['status'])) {
			echo "<div id='securesubmit_response'>";
			if ($response['status'] > 0) {
				echo "<div class='securesubmit_status'>" . $response['msg'] . "</div>";
				$payment_data['payment_status'] = 'Completed';
				$payment_data['txn_id'] = $response['txid'];
			}
			if (isset($response['error_msg']) && strlen(trim($response['error_msg'])) > 0) {
				echo "<div class='securesubmit_error'>ERROR: " . $response['error_msg'] . "  </div>";
			}
			echo "</div>";
		}
	}
	if ($payment_data['payment_status'] != 'Completed') {
		echo "<div id='securesubmit_response' class='securesubmit_error'>Looks like something went wrong.  Please try again or notify the website administrator.</div>";
	}
	add_action('action_hook_espresso_email_after_payment', 'espresso_email_after_payment');
	return $payment_data;
}
