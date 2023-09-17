<?php

namespace App\Http\Services\Utility;

use App\Http\Services\Utility\ResponseUtility;
use App\Http\Services\Utility\HTTPUtil;
use Illuminate\Support\Facades\Log;

/**
 * 
 */
class PostmarkService {
	
	//private
    var $CI;
    var $api_key = '';
    var $validation = FALSE;
    var $strip_html = FALSE;
    var $develop = FALSE;
    
    var $from_name;
    var $from_address;
    
    var $_reply_to_name;
    var $_reply_to_address;
    
    var $_to_name;
    var $_to_address;
    
    var $_cc_name;
    var $_cc_address;
    
    var $_subject;
    var $_message_plain;
    var $_message_html;

    var $_tag;
    

	/**
	 * Clear the Email Data
	 *
	 * @access	public
	 * @return	void
	 */	
    function clear() {
    
        $this->from_name = '';
    	$this->from_address = '';
    	
    	$this->_to_name = '';
    	$this->_to_address = '';
    	
    	$this->_cc_name = '';
    	$this->_cc_address = '';
    	
    	$this->_subject = '';
    	$this->_message_plain = '';
    	$this->_message_html = '';
    	
    	$this->_tag = '';	
    	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set Email FROM address
	 *
	 * This could also be set in the config file
	 *
	 * TODO:
	 * Validate Email Addresses ala CodeIgniter's Email Class
	 *
	 * @access	public
	 * @return	void
	 */	
	function from($address, $name = null)
	{
		
		if ( ! $this->validation == TRUE)
		{
            $this->from_address = $address;
            $this->from_name = $name;
		} 
		else
        {
            if ($this->_validate_email($address))
            {
                $this->from_address = $address;
                $this->from_name = $name;
            }
            else
            {
                return 'You have entered an invalid sender address.';
            }
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set Email TO address
	 *
	 * TODO:
	 * Validate Email Addresses ala CodeIgniter's Email Class
	 *
	 * @access	public
	 * @return	void
	 */	
	function to($address, $name = null)
	{
	        
		if ( ! $this->validation == TRUE)
		{
            $this->_to_address = $address;
            $this->_to_name = $name;
		} 
		else
        {
            if ($this->_validate_email($address))
            {
                $this->_to_address = $address;
                $this->_to_name = $name;
            }
            else
            {
                return 'You have entered an invalid recipient address.';
            }
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set Email ReplyTo address
	 *
	 * TODO:
	 * Validate Email Addresses ala CodeIgniter's Email Class
	 *
	 * @access	public
	 * @return	void
	 */	
	function reply_to($address, $name = null)
	{
	        
		if ( ! $this->validation == TRUE)
		{
            $this->_reply_to_address = $address;
            $this->_reply_to_name = $name;
		} 
		else
        {
            if ($this->_validate_email($address))
            {
                $this->_reply_to_address = $address;
                $this->_reply_to_name = $name;
            }
            else
            {
                return 'You have entered an invalid reply to address.';
            }
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Email CC address
	 *
	 * TODO:
	 * Validate Email Addresses ala CodeIgniter's Email Class
	 *
	 * @access	public
	 * @return	void
	 */	
	function cc($address, $name = null)
	{
	        
		if ( ! $this->validation == TRUE)
		{
            $this->_cc_address = $address;
            $this->_cc_name = $name;
		} 
		else
        {
            if ($this->_validate_email($address))
            {
                $this->_cc_address = $address;
                $this->_cc_name = $name;
            }
            else
            {
                return 'You have entered an invalid reply to address.';
            }
		}
	}
		
	// --------------------------------------------------------------------

	/**
	 * Set Email Subject
	 *
	 * @access	public
	 * @return	void
	 */	
	function subject($subject)
	{
		$this->_subject = $subject;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set Tag
	 *
	 * @access	public
	 * @return	void
	 */	
	function tag($tag)
	{
		$this->_tag = $tag;
	}	
	
	// --------------------------------------------------------------------

	/**
	 * Set Email Message in Plain Text
	 *
	 * @access	public
	 * @return	void
	 */	
	function message_plain($message)
	{
		if ( ! $this->strip_html )
		{
		  $this->_message_plain = $message;
		}
		else
		{
		  $this->_message_plain = $this->_strip_html($message);
		}
	}  

	// --------------------------------------------------------------------

	/**
	 * Set Email Message in HTML
	 *
	 * @access	public
	 * @return	void
	 */	
	function message_html($message)
	{
		$this->_message_html = $message;
	}

	// --------------------------------------------------------------------
    /**
    * Private Function to prepare and send email
    */
	function _prepare_data()
	{
        $data = array();
		$data['Subject'] = $this->_subject;
        		
		$data['From'] = is_null($this->from_name) ? $this->from_address : "{$this->from_name} <{$this->from_address}>";
		$data['To'] = is_null($this->_to_name) ? $this->_to_address : "{$this->_to_name} <{$this->_to_address}>";
		
		if (!is_null($this->_cc_address) && ($this->_cc_address != '')) {
            $data['Cc'] = is_null($this->_cc_name) ? $this->_cc_address : "{$this->_cc_name} <{$this->_cc_address}>";
		}

		if (!is_null($this->_reply_to_address) && ($this->_reply_to_address != '')) {
            $data['ReplyTo'] = is_null($this->_reply_to_name) ? $this->_reply_to_address : "{$this->_reply_to_name} <{$this->_reply_to_address}>";
		}
		
		if (!is_null($this->_tag) && ($this->_tag != '')) {
		  $data['tag'] = $this->_tag;
		}
		
		if (!is_null($this->_message_html)) {
			$data['HtmlBody'] = $this->_message_html;
		}
		
		if (!is_null($this->_message_plain)) {
			$data['TextBody'] = $this->_message_plain;
		}

		return $data;
	}
	
    function send($from_address = null, $from_name = null, $to_address = null, $to_name = null, $subject = null, $message_plain = null, $message_html = null)
	{
        if (!function_exists('curl_init'))
        {
            
            if(function_exists('log_message'))
            {
                Log::info('Postmark - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');            
            }
            
            return false;    
            
        }
	
		if(!is_null($from_address)){
			$this->from($from_address, $from_name);
		}
		else{
			$from_email = getenv('POSTMARK_FROM_EMAIL');
			$from_name = getenv('POSTMARK_FROM_NAME');
			$this->from($from_email, $from_name);
		}

		if (!is_null($to_address)) $this->to($to_address, $to_name);
		if (!is_null($subject)) $this->subject($subject);
		if (!is_null($message_plain)) $this->message_plain($message_plain);
		if (!is_null($message_html)) $this->message_html($message_html);
	
		// if (is_null($this->api_key)) {
		// 	show_error("Postmark API key is not set!");
		// }
		
		// if (is_null($this->from_address)) {
		// 	show_error("From address is not set!");
		// }
		
		// if (is_null($this->_to_address)) {
		// 	show_error("To address is not set!");
		// }
		
		// if (is_null($this->_subject)) {
		// 	show_error("Subject is not set!");
		// }
		
		// if (is_null($this->_message_plain) && is_null($this->_message_html)) {
		// 	show_error("Please either set plain message, HTML message or both!");
		// }
	
		$encoded_data = json_encode($this->_prepare_data());
		$apiKey = getenv('POSTMARK_API_KEY');

		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'X-Postmark-Server-Token: ' . $apiKey
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		
		$return = curl_exec($ch);
		//Log::info('debug', 'POSTMARK JSON: ' . $encoded_data . "\nHeaders: \n\t" . implode("\n\t", $headers) . "\nReturn:\n$return");
		
		if (curl_error($ch) != '') {
			return curl_error($ch);
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		Log::info('POSTMARK http code:' . $httpCode);
		
		if (intval($httpCode / 100) != 2) {
			$message = json_decode($return)->Message;
			//show_error('Error while mailing. Postmark returned HTTP code ' . $httpCode . ' with message "'.$message.'"');
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Email Validation
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function _validate_email($address)
	{
		$addresses = explode(',', $address);
				
		foreach($addresses as $k => $v) {
            if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", trim($v))) {
                return FALSE;
            }
		}
		
        return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Strip Html
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function _strip_html($message)
	{
        $message =  preg_replace('/\<br(\s*)?\/?\>/i', "\n", $message);
        return strip_tags($message);
	}
}