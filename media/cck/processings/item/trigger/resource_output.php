<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

if ( $this->getType() != '' ) {
	$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/resource_output/'.$this->getType().'.php';

	if ( is_file( $path ) ) {
		include $path;
	} else {
		$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/resource_output.php';

		if ( is_file( $path ) ) {
			include $path;
		}
	}
}
?>