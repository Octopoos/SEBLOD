<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: image.php lionelratel $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// JCckDevPdf
class JCckDevPdf
{
	protected $_path	=	'';

	// combine
	public function combine( $files, $remove_files = false )
	{
		if ( !$this->_path ) {
			return false;
		}
		if ( !count( $files ) ) {
			return false;
		}

		$error 	=	'';
		$src	=	'';

		foreach ( $files as $file ) {
			if ( $file != '' && is_file( $file ) ) {
				$src	.=	$file.' ';
			}
		}

		$command	=	'pdftk '.$src.'cat output '.$this->_path;
		
		passthru( $command, $error );

		if ( (int)$error == 127 ) {
			return false;
		}

		if ( $remove_files ) {
			foreach ( $files as $file ) {
				if ( $file != '' && is_file( $file ) ) {
					JFile::delete( $file );
				}
			}			
		}

		return true;
	}

	// createFdf
	public function createFdf()
	{
		if ( !is_file( JPATH_SITE.'/libraries/cck_pdf/fdf/createFDF.php' ) ) {
			return false;
		}

		require_once JPATH_SITE.'/libraries/cck_pdf/fdf/createFDF.php';	
	}

	// createPdf
	public function createPdf()
	{
		if ( is_file( JPATH_SITE.'/libraries/cck_pdf/fpdi/src/autoload.php' ) ) {
			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/config/tcpdf_config.php';
			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/tcpdf.php';
			require_once JPATH_SITE.'/libraries/cck_pdf/fpdi/src/autoload.php';

			return new \setasign\Fpdi\Tcpdf\Fpdi;
		} elseif ( is_file( JPATH_SITE.'/libraries/cck_pdf/fpdi/fpdi.php' ) ) {
			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/config/tcpdf_config.php';
			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/tcpdf.php';
			require_once JPATH_SITE.'/libraries/cck_pdf/fpdi_1x/fpdi.php';

			return new FPDI;
		}

		return false;
	}

	// create
	public function create()
	{
		//
	}

	// load
	public function load()
	{
		//
	}

	// setPath
	public function setPath( $path )
	{
		$this->_path	=	$path;
	}

	// stamp
	public function stamp( $image, $x, $y, $width, $height, $remove_image = false )
	{
		if ( !( $this->_path && is_file( $this->_path ) ) ) {
			return false;
		}
		if ( !is_file( $image ) ) {
			return false;
		}
		
		$error		=	'';
		$extension	=	JFile::getExt( $image );

		if ( $extension != 'pdf' ) {
			$len		=	strlen( $extension );
			$image_tmp	=	substr( $image, 0, ( $len * -1 ) ).'pdf';

			if ( $image_tmp != '' && is_file( $image_tmp ) ) {
				JFile::delete( $image_tmp );
			}

			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/config/tcpdf_config.php';
			require_once JPATH_SITE.'/libraries/cck_pdf/tcpdf/tcpdf.php';

			$pdf	=	new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

			$pdf->setPrintHeader( false );
			$pdf->setPrintFooter( false );
			$pdf->SetDisplayMode( 'real' );
			$pdf->setImageScale( 1 );
			$pdf->AddPage();
			$pdf->setCellHeightRatio( 1 );
			$pdf->Image( $image, $x * 10, $y * 10, $width * 10, $height * 10, '', '', '', false, 150 );
			$pdf->lastPage();
			$pdf->Output( $image_tmp, 'F' );
		} else {
			$image_tmp	=	$image;
		}

		if ( $image_tmp != '' && is_file( $image_tmp ) ) {
			$file		=	$this->_path;
			$file_tmp	=	str_replace( '.pdf', '_tmp.pdf', $file );
			
			passthru( 'pdftk '.$file.' stamp '.$image_tmp.' output '.$file_tmp, $error );

			if ( (int)$error == 127 ) {
				JFactory::getApplication()->enqueueMessage( 'Cannot stamp the image.', 'error' );
			}
			if ( is_file( $file_tmp ) ) {
				JFile::move( $file_tmp, $file );
			}
			if ( $image_tmp != $image ) {
				JFile::delete( $image_tmp );
			}
			if ( $remove_image ) {
				JFile::delete( $image );
			}
		}
	}
}
?>