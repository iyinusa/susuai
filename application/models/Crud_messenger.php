<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud_messenger extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    ////////////////// CLEAR CACHE ///////////////////
	public function clear_cache() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }
	
	//////////////////// SUBSCRIBE TO BOT ///////////////////////
	public function subscribe($accessToken) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/subscribed_apps?access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_POST, 1);
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200) {
			return $result;
		}
	}
	
	//////////////////// SETTINGS ///////////////////////
	public function settings($accessToken, $response) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/thread_settings?access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200) {
			return $result;
		}
	}
	
	//////////////////// REMOVE SETTINGS ///////////////////////
	public function remove_settings($accessToken, $response) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/thread_settings?access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200) {
			return $result;
		}
	}
	
	//////////////////// USER PROFILE ///////////////////////
	public function user_profile($accessToken, $psid) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/'.$psid.'?fields=first_name,last_name,locale,timezone,gender&access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200) {
			return $result;
		}
	}
	
	//////////////////// MESSAGE PROFILE ///////////////////////
	public function msg_profile($accessToken, $response) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/messenger_profile&access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
		$result = curl_exec($ch);
		//$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $result;
	}
	
	//////////////////// MESSAGE ///////////////////////
	public function message($accessToken, $response) {
		// create a new cURL resource
		$ch = curl_init();
		
		// parameters
		$token = $accessToken;
		$chead = array();
		$chead[] = 'Content-Type: application/json';
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/messages?access_token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $chead);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if($http_code == 200) {
			return $result;
		}
		curl_close($ch);
	}
}
