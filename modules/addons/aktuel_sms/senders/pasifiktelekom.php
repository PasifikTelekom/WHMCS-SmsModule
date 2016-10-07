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
        $header = "Content-Type: application/json"."\r\n".
                "Accept: application/json"."\r\n".
                'Content-Length: ' . strlen($json_data);
        $result = file_get_contents($URL, null, 
            stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => $header,
                        'content' => $json_data
                    ),
                )
            )
        );
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
        $header = "Content-Type: application/json"."\r\n".
                "Accept: application/json"."\r\n".
                'Content-Length: ' . strlen($json_data);
        $result = file_get_contents($URL, null, 
            stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => $header,
                        'content' => $json_data
                    ),
                )
            )
        );
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
            $header = "Content-Type: application/json"."\r\n".
                "Accept: application/json"."\r\n".
                'Content-Length: ' . strlen($json_data);
            $result = file_get_contents($URL, null, 
                stream_context_create(
                    array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => $header,
                            'content' => $json_data
                        ),
                    )
                )
            );
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
