<?php
class PostaGuverciniContext
{
    private $userName = "";
    private $password = "";
    private $apiUrl = "";

    public function getUserName()
    {
        return $this->userName;
    }
    public function setUserName($_userName)
    {
        $this->userName = $_userName;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($_password)
    {
        $this->password = $_password;
    }
    
    public function getApiUrl()
    {
        return $this->apiUrl;
    }
    public function setApiUrl($_apiUrl)
    {
        $this->apiUrl = $_apiUrl;
    }
}
