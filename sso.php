<?php
	function twf_decode($string,$key) {	
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    for ($i = 0; $i < $strLen; $i+=2) {
	        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= chr($ordStr - $ordKey);
	    }
	    return $hash;
	}

	$twf_user = $_POST['user'];	
	$twf_company = $_POST['company'];
	$twf_organisation = $_POST['organisation'];
	$url = $_POST['returnurl'];

	$twf_pwd = twf_decode($_POST['password'], 'TWF_' . $twf_organisation.$twf_user);
	$params = array(
			user => $twf_user,
			password => $twf_pwd,
			organisation => $twf_organisation
			);
	// Logon
	try {
		$session = new SoapClient("https://login.twinfield.com/webservices/singlesignon.asmx?wsdl");
		$result = $session->Prepare($params);
		$token = $result->token;		
		switch ($result->PrepareResult) {
		case "Ok":
			echo '<html>';
			echo '<body onload="document.forms[0].submit();">';
			echo '<form method="post" action="https://login.twinfield.com/logon/singlesignon.aspx">';
			echo '<input type="hidden" name="user" value="'. $params[user] .'">';
			echo '<input type="hidden" name="organisation" value="'. $params[organisation] .'">';
			echo '<input type="hidden" name="token" value="'. $token.'">';
			echo '<input type="hidden" name="returnurl" value="'. $url .'">';
			echo '<input type="hidden" name="company" value="'. $twf_company .'">';
			echo '</form>';
			echo '</body>';
			echo '</html>';
			break;
		default:
			header('Location: '. $url . '?msg='. $result->PrepareResult);
			break;
		}
	}
	catch (SoapFault $e) {
		echo $e->getMessage();
	}	
?>