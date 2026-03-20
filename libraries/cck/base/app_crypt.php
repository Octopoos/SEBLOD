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

use Joomla\Crypt\Cipher\Sodium;
use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;

// JCckAppCrypt
class JCckAppCrypt extends Sodium
{
	// init
	public function init( $base64 = false )
	{
		$key	=	$this->generateKey();
		$nonce	=	random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

		$this->setNonce( $nonce );

		$data	=	array(
							'nonce'=>$nonce,
							'private'=>$key->getPrivate(),
							'public'=>$key->getPublic()
						);

		if ( $base64 ) {
			$data['public']		=	bin2hex( $data['public'] );
			$data['private']	=	bin2hex( $data['private'] );
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