<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

if ( $this->getType() != '' ) {
	$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/after_delete/'.$this->getType().'.php';

	if ( is_file( $path ) ) {
		include $path;
	} else {
		$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/after_delete.php';

		if ( is_file( $path ) ) {
			include $path;
		}
	}
}
?>