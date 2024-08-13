<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Crypt\Cipher\SodiumCipher;
use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Crypt\Key;

// JCckAppCrypt
class JCckAppCrypt extends SodiumCipher
{
	// init
	public function init( $base64 = false )
	{
		$key	=	$this->generateKey();
		$nonce	=	random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

		$this->setNonce( $nonce );

		$data	=	array(
							'nonce'=>$nonce,
							'private'=>$key->private,
							'public'=>$key->public
						);

		if ( $base64 ) {
			$data['public']		=	base64_encode( $data['public'] );
			$data['private']	=	base64_encode( $data['private'] );
		}

		return $data;
	}

	// getKey
	public function getKey( $private_key, $public_key )
	{
		return new Key( 'sodium', $private_key, $public_key );
	}
}
?>