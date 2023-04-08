<?php
include_once __DIR__."/../Context/PostaGuverciniContext.php";
include_once __DIR__."/../Context/Utilities.php";

class PostaGuverciniService extends PostaGuverciniContext{
    private $_utilities;
    public function __construct($_userName,$_password)
    {
        $this->setUserName($_userName);
        $this->setPassword($_password);
        $this->_utilities=new Utilities();
    }

    public function getCredit()
    {
        $this->setApiUrl("http://www.postaguvercini.com/api_xml/Cre_balreq.asp");
        $userName=$this->getUserName();
        $password=$this->getPassword();
        $creditXML="<CREDIT-BalRequest>
                        <CLIENT user='$userName' pwd='$password' />
                        <BALANCE req='1' />
                    </CREDIT-BalRequest>";
		$returnResponse = $this->_utilities->sendResponse($this->getApiUrl(),$creditXML);
		$result = simplexml_load_string($returnResponse);
		return $result->BALANCE->attributes()->res;
    }

    public function getAlphaNumeric()
    {
        $this->setApiUrl("http://www.postaguvercini.com/api_xml/Id_chkreq.asp");
        $userName=$this->getUserName();
        $password=$this->getPassword();
        $alphanumericXML="<ID-ChkRequest>
                            <CLIENT user=\"$userName\" pwd=\"$password\" />
                            <ID req=\"1\" />
                        </ID-ChkRequest>";
		$returnResponse = $this->_utilities->sendResponse($this->getApiUrl(),$alphanumericXML);
		$result = simplexml_load_string($returnResponse);
		return $result->ID->attributes()->res;
    }

    public function sendMessageWithDifferentNumberSameMessage($message,array $numberList)
    {
        $statusIDs=array();
        $userName=$this->getUserName();
        $password=$this->getPassword();
        $message=$this->_utilities->editMessage($message);
        $messageDate=date("Y/m/d H:i");

        $firstPartXML="<SMS-InsRequest>
                            <CLIENT user=\"$userName\" pwd=\"$password\" />
                            <INSERTMSG text=\"$message\" dt=\"$messageDate\">";
        $lastPartXML="</INSERTMSG>
        </SMS-InsRequest>";
        $middlePartXML="";
        $count=0;
        for ($i=0; $i < count($numberList) ; $i++) { 
            if($count==799){
                $statusIDs=array_merge($statusIDs,$this->sendMessage($firstPartXML.$middlePartXML.$lastPartXML));
                $count=0;
                $middlePartXML="";
            }
            $phoneNumber=$this->_utilities->editPhoneNumber($numberList[$i]);
            $middlePartXML .= "<TO>".$phoneNumber."</TO>";
            $count++;
        }
        $statusIDs=array_merge($statusIDs,$this->sendMessage($firstPartXML.$middlePartXML.$lastPartXML));
        return $statusIDs;
    }
   
    public function sendMessageWithDifferentNumberDifferentMessage(array $messagesWithNumbers)
    {
        $statusIDs=array();
        $userName=$this->getUserName();
        $password=$this->getPassword();
        $messageDate=date("Y/m/d H:i");

        $firstPartXML="<SMS-InsRequest>
                            <CLIENT user=\"$userName\" pwd=\"$password\" />";
        $lastPartXML="</SMS-InsRequest>";
        $middlePartXML="";
        $count=0;
        for ($i=0; $i < count($messagesWithNumbers) ; $i++) {
            if($count==799){
                $statusIDs=array_merge($statusIDs,$this->sendMessage($firstPartXML.$middlePartXML.$lastPartXML));
                $middlePartXML="";
                $count=0;
            }
            $phoneNumber=$this->_utilities->editPhoneNumber($messagesWithNumbers[$i]["phone"]);
            $message=$this->_utilities->editMessage($messagesWithNumbers[$i]["message"]);
            $middlePartXML .="<INSERT to=\"$phoneNumber\" text=\"$message\" dt=\"$messageDate\" />";
            $count++;
        }
        $statusIDs=array_merge($statusIDs,$this->sendMessage($firstPartXML.$middlePartXML.$lastPartXML));
        return $statusIDs;
    }

    private function sendMessage($xml)
    {
        $statusIDs=array();
        $this->setApiUrl("http://www.postaguvercini.com/api_xml/Sms_insreq.asp");
        $returnResponse = $this->_utilities->sendResponse($this->getApiUrl(),$xml);
        $result = simplexml_load_string($returnResponse);
        foreach ($result->INSERT as $insert) {
            array_push($statusIDs,$insert->attributes()->id);
        }
        return $statusIDs; 
    }

    public function getSMSStatus(array $statusIDs)
    {
        $result=array();
        $userName=$this->getUserName();
        $password=$this->getPassword();
        $firstPartXML="<SMS-StaRequest>
                            <CLIENT user=\"$userName\" pwd=\"$password\" />";
        $lastPartXML="</SMS-StaRequest>";
        $middlePartXML="";
        $count=0;
        for ($i=0; $i < count($statusIDs); $i++) { 
            if($count==799){
                $result=array_merge($result,$this->sendSMSStatus($firstPartXML.$middlePartXML.$lastPartXML));
                $middlePartXML="";
                $count=0;
            }
            $statusID=$statusIDs[$i];
            $middlePartXML.="<STATUS id=\"$statusID\" />";
            $count++;
        }
        $result=array_merge($result,$this->sendSMSStatus($firstPartXML.$middlePartXML.$lastPartXML));

    }
    private function sendSMSStatus($xml)
    {
        $result=array();
        $this->setApiUrl("http://www.postaguvercini.com/api_xml/Sms_stareqx.asp");
        $returnResponse = $this->_utilities->sendResponse($this->getApiUrl(),$xml);
        $convertResponse = simplexml_load_string($returnResponse);
        $count=0;
        foreach ($convertResponse->STATUS as $status) {
            $result[$count]["res"]=$status->attributes()->res;
            $result[$count]["gsmno"]=$status->attributes()->gsmno;
            $count++;
        }
        return $result;
    }

    
}



?>