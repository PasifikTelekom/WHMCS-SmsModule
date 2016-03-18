<?php 

class pasifiktelekom extends AktuelSms {
	function __constract($message, $gsmnumber){
		$this->message = $this->utilmessage($message);
		$this->gsmnumber = $this->utilgsmnumber($gsmnumber);
	}

	function send(){
		if($this->gsmnumber == "numbererror"){
			$log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
		}

		$params = $this->getParams();

		$json_data = '{"username": "'.$params->user.'", "password": "'.$params->pass.'", "sender": "'.$params->senderid.'", "message": "'.$this->message.'", "msisdn_list": "'.$this->gsmnumber.'"}';
		$URL = "http://oim.pasifiktelekom.com.tr/en/api/sendsms/";
		$ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$json_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $return = $result;
        $log[] = ("Geri Dönüş Kodu: ".$result);

        $result = json_decode($result, true);
        if ($result["response_status_code"] == "200") {
        	$log[] = ($result["response_status_description"]);
        }else{
        	$log[] = ($result["response_status_description"]);
        	$error[] = ($result["response_status_description"]);
        }

        return array(
        	'log' => $log,
            'error' => $error,
            'msgid' => $result["response_message_id"]
        );
	}
	function balance(){
		$params = $this->getParams();
		$json_data = '{"username": "'.$params->user.'", "password": "'.$params->pass.'"}';
		$URL = "http://oim.pasifiktelekom.com.tr/en/api/getsettings/";
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$json_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$return = $result;
		$result = json_decode($result, true);

		$res_result = "SMS Packet ".$result["sms_balance_pkt"]." | Balance ".$result["balance"]." ".$result["currency"]."";
		return $res_result;
	}
	function report($msgid){
		$params = $this->getParams();
		if($params->user && $params->pass && $msgid){
			$json_data = '{"username": "'.$params->user.'", "password": "'.$params->pass.'", "sms_id": "'.$msgid.'"}';
			$URL = "http://oim.pasifiktelekom.com.tr/en/api/generalreport/id/";
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$json_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			$return = $result;
			$result = json_decode($result, true);
			if ($result["succeeded"] && ((int)$result["delivered_count"]) > 0 ) {
				return "success";
			}else{
				return "error";
			}
		}else{
			return null;
		}
	}
	function utilgsmnumber($number){
		return $number;
	}
	function utilmessage($message){
		return $message;
	}
} 
return array(
    'value' => 'pasifiktelekom',
    'label' => 'Pasifik Telekom',
    'fields' => array(
        'user','pass'
    )
);
?>
