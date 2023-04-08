<?php
class Utilities{
    public function sendResponse($url,$xml)
    {
        $curlInıt=curl_init();
        curl_setopt($curlInıt, CURLOPT_URL,$url);
		curl_setopt($curlInıt, CURLOPT_POST, 1);
		curl_setopt($curlInıt, CURLOPT_POSTFIELDS,$xml);
		curl_setopt($curlInıt, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curlInıt, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curlInıt, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curlInıt, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
		curl_setopt($curlInıt, CURLOPT_HEADER, 0);
		curl_setopt($curlInıt, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curlInıt, CURLOPT_TIMEOUT, 120);
		$result = curl_exec($curlInıt);
		return $result;
    }

    public function editMessage($message)
    {
        $editedMessage=str_replace(
            ["&", "<", ">","\"", "'"],
            [ "&amp;","&lt;","&gt;","&quot;","&apos;"],
            $message
        );
        return $editedMessage;
    }
    public function editPhoneNumber($phoneNumber)
    {
        $phoneNumber=str_replace(array('(',')',' ','-'),'',$phoneNumber);
        if($phoneNumber[0]=="0"){
            $phoneNumber=substr($phoneNumber, 1);
        }
        return $phoneNumber;
    }
}

?>