<?php

/*!
 * Jollof Framework (c) 2016
 *
 * {JollofSecureHeaders.php}
 *
 */

namespace Providers\Tools;

use \Providers\Tools\SecureHeaders as SecureHeaders;

class JollofSecureHeaders extends SecureHeaders {

	/**
	 * @var array
	 */

	protected $secConfig;

	/**
	 * @var array
	 */
	protected $nonceMap;

	/**
     * Constructor.
     *
     * @param void
     *
     * @scope public
     */

	 public function __construct(){

			//parent::__construct();

			$this->nonceMap = array();

			$this->secConfig = NULL;

			$this->done_on_output();

			$this->error_reporting(FALSE);

        	/*
        	enabling legacy support for deprecated
        	 CSP headers for borowser (e.g. X-Webkit-CSP:, X-Content-Security-Policy: ... )
        	*/

			$this->csp_legacy(TRUE);

			$this->safe_mode(TRUE);

			$this->protected_cookie('sess', SecureHeaders::COOKIE_REMOVE & SecureHeaders::COOKIE_SUBSTR);
	 }

	/**
	 *
	 *
	 *
	 *@param void
	 *@return void
 	 */

	 private function generateSourceNonces(){

	 	if(count($this->nonceMap) === 0){ // never generate more than once..
	 		$this->nonceMap['script'] = $this->csp_nonce('script');
	 		$this->nonceMap['style'] = $this->csp_nonce('style');
	 	}

	}

	/**
	 *
	 *
	 *
	 * @param void
	 * @return array
 	 */

	public function getSourceNonces(){

		return $this->nonceMap;

	}

	/**
	 *
	 *
	 *
	 * @param array $config
	 * @return void
 	 */

	public function installConfig($config){

		$cspConfig = $config['csp'];
        $hpkpConfig = $config['hpkp'];
        $strictModeConfig = $config['strict_mode'];
        $generateNoncesConfig = $config['noncify-inline-source'];

        if($cspConfig === TRUE){
            $cspConfig = array(
            	"default-src" => array(
            		"'none'"
            	),
		        "script-src" => ($generateNoncesConfig ?
		          array(
		          		"'self'"
		          ) :
		          array(
		            "'self'",
		            "'unsafe-inline'",
		            "'unsafe-eval'"
		          )
		        ),
		        "style-src" => ($generateNoncesConfig ?
		          array(
		          		"'self'",
		          		"https://fonts.googleapis.com"
		          ) :
		          array(
		            "'self'",
		            "'unsafe-inline'",
		            "'unsafe-eval'"
		          )
		        ),
		        "connect-src" => array(
		        	"'self'"
		        ),
		        "form-action" => array(
		        	"'self'"
		        ),
		        "font-src" => array(
		        	"'self'",
		        	"https://fonts.gstatic.com data:"
		        ),
		        "base-uri" => array(
		        	"'self'"
		        )
            );
        }else if(is_array($cspConfig)){
            ;
        }

        if($cspConfig !== FALSE){
            $this->csp($cspConfig);
        }

        if($hpkpConfig !== FALSE){
        	$this->hpkp($hpkpConfig, 1500, 1);
        }

        if($generateNoncesConfig === TRUE){
        	$this->generateSourceNonces();
        }

        if($strictModeConfig === TRUE){
            $this->strict_mode();
        }
	}

}


?>