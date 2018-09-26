<?php
class api
{

    public $resultJSON = false;
    private $requestMethod = 'GET';
    private $requestURI = 'https://yesno.wtf/api';
    private $errorMessage = null;
    private $theCall;
    private $postJSON = null;
    private $userPwd = null;
    private $contentType = null;
    public function __construct()
    {
    }

    public function makeTheCall()
    {
        $this->theCall = curl_init();

        if($this->userPwd !== null) curl_setopt($this->theCall, CURLOPT_USERPWD, 'user:'.$this->userPwd);

        if($this->contentType !== null) curl_setopt($this->theCall, CURLOPT_HTTPHEADER, array('Content-Type: '.$this->contentType));

        curl_setopt($this->theCall, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->theCall, CURLOPT_TIMEOUT, 10);
        curl_setopt($this->theCall, CURLOPT_CUSTOMREQUEST, $this->requestMethod);
        curl_setopt($this->theCall, CURLOPT_URL, $this->requestURI);
        $this->handleRequestMethods();

        $this->resultJSON = curl_exec($this->theCall);
        if ($this->resultJSON === false) {
            $this->errorMessage = 'PHP API Class : Connection failure';
        }

        curl_close($this->theCall);

        if ($this->errorMessage === null) {
            return $this->resultJSON;
        } else {
            $errJSON = json_encode(array('Error'=>$this->errorMessage));
            return $errJSON;
        }

    }
    public function handleRequestMethods()
    {
        switch ($this->requestMethod) {
        case 'POST':
          curl_setopt($this->theCall, CURLOPT_POST, 1);
          curl_setopt($this->theCall, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($this->theCall, CURLOPT_POSTFIELDS, $this->postJSON);
          break;
        default:
          break;
      }
    }

    public function setUserPwd($userPwd = null)
    {
      if($userPwd !== null) $this->userPwd = $userPwd;
    }

    public function setContentType($contentType = null)
    {
      if($contentType !== null) $this->contentType = $contentType;
    }

    public function setPOSTfields($postFields = array())
    {
        $this->postJSON = json_encode($postFields);
    }

    public function setRequestMethod($rm = 'GET')
    {
        $methodArr = array('GET','POST','PUT','HEAD','DELETE','PATCH','OPTIONS');
        if (in_array(strtoupper($rm), $methodArr)) {
            $this->requestMethod = strtoupper($rm);
        } else {
            $this->errorMessage = 'PHP API Class : Invalid request method';
        }
    }

    public function setURI($ruri = 'https://yesno.wtf/api', $paramArr = array())
    {
        if ((strpos($ruri, 'https://') !== false) || (strpos($ruri, 'http://') !== false)) {
            $this->requestURI = $ruri;
            if (!empty($paramArr)) {
                if (substr($this->requestURI, -1) !== '?') {
                    $this->requestURI .= '?';
                }
                foreach ($paramArr as $key => $value) {
                    if (($key !== '') && ($value !== '')) {
                        $this->requestURI .= $key.'='.$value.'&';
                    }
                }
                $this->requestURI = rtrim($this->requestURI, '&');
            }
        } else {
            $this->errorMessage = 'PHP API Class : Invalid URI';
        }
    }
}
?>
