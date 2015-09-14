<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Helper_Recaptcha extends Mage_Core_Helper_Abstract {

	/**
	 * @var string $privateKey
	 *
	 * @access protected
	 */
	protected $privateKey = '';

	/**
	 * @var string $publicKey
	 *
	 * @access protected
	 */
	protected $publicKey = '';

	protected $theme = '';

	/**
	 *
	 */
	public function setKeys( $privateKey, $publicKey ){

		$this->privateKey = ( $privateKey );
		$this->publicKey  =( $publicKey );

		return $this;
	}

	/**
	 *
	 */
	public function setTheme( $theme="" ){
		$this->theme = $theme;
		return $this;
	}

	/**
	 *
	 */
	public function getReCapcha() {
		$reCaptcha = '';
		if( $this->publicKey && $this->privateKey ) {
			$reCaptcha = new Zend_Service_ReCaptcha( $this->publicKey, $this->privateKey );
			if( $this->theme ) {
				$reCaptcha->setOptions(array('theme' => $this->theme) );
			}
		}
		return $reCaptcha;
	}

	/**
	 *
	 */
	public function isValid( $challengeField, $responseField ) {
		$response = $this->getReCapcha()->verify( $challengeField, $responseField );
		return $response->isValid();
	}
}