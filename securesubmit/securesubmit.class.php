<?php
if(!class_exists("HpsServicesConfig")) {
	require_once (dirname(__FILE__).'/lib/Hps.php');
}

class Espresso_ClsSecureSubmit
{	
	function do_transaction($amount ,$tokenValue, $cardHolder, $description, $securesubmit_settings)
	{
		$result = array();

		$securesubmit_settings = get_option('event_espresso_securesubmit_settings');
        $secretKey = $securesubmit_settings['securesubmit_secret_key'];
        $currencySymbol = $securesubmit_settings['securesubmit_currency_symbol'];
		
		$response = null;
        $config = new HpsServicesConfig();
        $config->secretApiKey = $secretKey;
        $config->versionNumber = '1741';
        $config->developerId = '002914';

        $token = new HpsTokenData();
        $token->tokenValue = $tokenValue;

		if(!array_key_exists("securesubmit_enable_giftcard", $securesubmit_settings) || !$securesubmit_settings["securesubmit_enable_giftcard"]){
			// Process credit card payment
			try {
				$creditService = new HpsCreditService($config);
				$response = $creditService->charge($amount,'usd',$token,$cardHolder);

				$result["status"] = 1;
				$result["msg"] = "Transaction was completed successfully [Transaction ID# ".$response->transactionId."]";
				$result['txid'] = $response->transactionId;
			} catch (Exception $e) {
				$result["status"] = 0;
				$result["error_msg"] = $e->getMessage();
			}
		}else{
			// Gift card was submitted?
			if(!empty($_POST['securesubmit_giftcardnumber'])){
				$gcNumber = $_POST['securesubmit_giftcardnumber'];
				$gcPin = array_key_exists("securesubmit_giftcardpin", $_POST) ? $_POST['securesubmit_giftcardpin'] : "";
				
				try {
					$gcService = new HpsGiftCardService($config);
					$giftCard = new HpsGiftCard($gcNumber);
					if(!empty($gcPin)){
						$giftCard->pin = $gcPin;
					}
					
					$response = $gcService->sale($giftCard, $amount, 'usd');
					
					$result["status"] = 1;
					$result["msg"] = "Transaction was completed successfully [Transaction ID# ".$response->transactionId."]";
					$result['txid'] = $response->transactionId;
				}catch (Exception $e) {
					$result["status"] = 0;
					$result["error_msg"] = $e->getMessage();
				}
			}
		}
		
		return $result;
	}
}
