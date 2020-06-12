<?php
	// $client_id, $redirect_uri & $client_secret come from the settings
	// $code is the code passed to the redirect url
	function GetAccessToken($code) {	
		$url = 'https://www.googleapis.com/oauth2/v4/token';			

		$curlPost = 'client_id=' . CLIENT_ID . '&redirect_uri=' . CLIENT_REDIRECT_URL . '&client_secret=' . CLIENT_SECRET . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to receieve access token');
		
		return $data;
	}

	function GetUserProfileInfo($access_token) {	
		$url = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=given_name,family_name,email,picture';	
			
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to get user information');
		
		return $data;
	}

?>