<?php
if(!class_exists("HpsServicesConfig")) {
	require_once (dirname(__FILE__).'/lib/Hps.php');
}

class Espresso_ClsSecureSubmit
{	
	function do_transaction($amount ,$tokenValue, $cardHolder, $description)
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
		
		return $result;
	}
}