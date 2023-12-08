<?php

# Curl, CurlResponse
#
# Author  Sean Huber - shuber@huberry.com
# Date    May 2008
#
# A basic CURL wrapper for PHP
#
# See the README for documentation/examples or http://php.net/curl for more information about the libcurl extension for PHP

namespace App\Helpers;
use App\Helpers\CurlResponse;

class Curl 
{
	public $cookie;
    public $cookie_file;
    public $headers = array();
    public $options = array();
    public $referer;
    public $user_agent;
    public $authenticate;
    public $proxy;
    public $proxy_port;
    public $proxy_userpass;
    public $cainfo;
    public $capath;
    public $sslcert;
    public $sslcertpasswd; 
    public $curl_timeout;
    public $destroy_cookie = true;
    public $curl_decode = false;

    protected $error = '';
    public $handle;


    public function __construct()
    {
		$this->cookie_file = realpath('.').'/curl_cookie' . time() . '.txt';
        $this->user_agent = (isset($this->user_agent) && $this->user_agent ? $this->user_agent : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)');
    }

    public function delete($url, $vars = array()) 
    {
        return $this->request('DELETE', $url, $vars);
    }

    public function error() 
    {
        return $this->error;
    }

    public function get($url, $vars = array()) 
    {
    	if (!empty($vars)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= http_build_query($vars, '', '&');
        }
        return $this->request('GET', $url);
    }

    public function post($url, $vars = array()) 
    {
        return $this->request('POST', $url, $vars);
    }

    public function put($url, $vars = array()) 
    {
        return $this->request('PUT', $url, $vars);
    }

    protected function request($method, $url, $vars = array()) 
    {
    	$this->handle = curl_init();

        # Set some default CURL options
        curl_setopt($this->handle, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($this->handle, CURLOPT_COOKIEJAR, $this->cookie_file);
        if($this->cookie)
        	curl_setopt($this->handle, CURLOPT_COOKIE, $this->cookie);
        	
        curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->handle, CURLOPT_HEADER, true);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, (is_array($vars) ? http_build_query($vars, '', '&') : $vars));
        curl_setopt($this->handle, CURLOPT_REFERER, $this->referer);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($this->handle, CURLOPT_USERAGENT, $this->user_agent);
        //curl_setopt($this->handle, CURLOPT_VERBOSE, 1);
        
        if($this->cainfo) {
        	curl_setopt($this->handle, CURLOPT_CAINFO, $this->cainfo);
        }
        
        if($this->capath) {
        	curl_setopt($this->handle, CURLOPT_CAPATH, $this->capath);
        }
        
        if($this->sslcert) {
        	curl_setopt($this->handle, CURLOPT_SSLCERT, $this->sslcert);
        }
        
        if($this->sslcertpasswd) {
        	curl_setopt($this->handle, CURLOPT_SSLCERTPASSWD, $this->sslcertpasswd);
        }
        
        if($this->authenticate) {
        	curl_setopt($this->handle, CURLOPT_USERPWD, $this->authenticate);
        }
                
		if($this->proxy) {
			curl_setopt($this->handle, CURLOPT_PROXY, $this->proxy);
		}
		
		if($this->proxy_port) {
			curl_setopt($this->handle, CURLOPT_PROXYPORT, $this->proxy_port);
		}
			
		if($this->proxy_userpass) {
			curl_setopt($this->handle, CURLOPT_PROXYUSERPWD, $this->proxy_userpass);
		}
		
		if($this->curl_timeout) {
			curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->curl_timeout);
		} else {
			curl_setopt($this->handle, CURLOPT_TIMEOUT, 10);
		}
		        
        # Format custom headers for this request and set CURL option
        //$this->headers = array_merge($this->headers, array("Expect: "));        
        
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $value;
        }
        
        if(count($headers))
        	curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        
        # Determine the request method and set the correct CURL option
        switch ($method) {
            case 'GET':
                curl_setopt($this->handle, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($this->handle, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        # Set any custom CURL options
        foreach ($this->options as $option => $value) {
            curl_setopt($this->handle, constant('CURLOPT_'.str_replace('CURLOPT_', '', strtoupper($option))), $value);
        }
        
        $response = curl_exec($this->handle);
        
        if ($response) {	
            $response = new CurlResponse($response);
        } else {
        	$this->error = curl_errno($this->handle).' - '.curl_error($this->handle);
        }
        
        $this->curl_info = curl_getinfo($this->handle);
        curl_close($this->handle);
        
        
        if($this->curl_decode) {
			$response->body = $this->gzdecode($response->body);	
		}
                
        return $response;
    }

    public function info()
    {
    	return $this->curl_info;	
    }   
    
    public function __destruct()
    {
    	if($this->destroy_cookie) {
     		@unlink($this->cookie_file);
    	}
    }
    
    public function gzdecode($data) 
    {
	  $g=tempnam('/tmp','unzip');
	  @file_put_contents($g, $data);
	  ob_start();
	  readgzfile($g);
	  $d=ob_get_clean();
	  unlink($g);
	  return $d;
	}
}
?>
