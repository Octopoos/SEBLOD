<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

if ( $this->getType() != '' ) {
	$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/after_store/'.$this->getType().'.php';

	if ( is_file( $path ) ) {
		include $path;
	} else {
		$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/after_store.php';

		if ( is_file( $path ) ) {
			include $path;
		}
	}
}
?>