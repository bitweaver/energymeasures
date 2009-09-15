<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/index.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $
// Copyright (c) 2004 bitweaver EnergyMeasures
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'energymeasures' );

if( !empty( $_REQUEST['energymeasure_id'] ) || !empty( $_REQUEST['content_id'] ) ){
	// Look up the content
	require_once( ENERGYMEASURES_PKG_PATH.'lookup_energymeasures_inc.php' );

	if( !$gContent->isValid() ) {
		$gBitSystem->setHttpStatus( 404 );
		$msg = "The requested energymeasure (id=".$_REQUEST['energymeasure_id'].") could not be found.";
		$gBitSystem->fatalError( tra( $msg ) ); 
	}else{
		// Check permissions to access this content 
		$gContent->verifyViewPermission();

		// Add a hit to the counter
		$gContent->addHit();

		// Display options
		$displayOptions = array( 'display_mode' => 'display' );
		$displayTemplate = 'display_energymeasures.tpl';
		if ( $gBitThemes->isAjaxRequest() ){
			// Special format for the game if we're ajaxing the request
			$displayOptions['format'] = 'center_only';
			$displayTemplate = 'display_energymeasure_dialog.tpl';
		}

		// Display the template
		$gBitSystem->display( 'bitpackage:energymeasures/'.$displayTemplate, tra( 'EnergyMeasures' ).' - '.$gContent->getTitle() , $displayOptions );
	}
}else{
	include_once( ENERGYMEASURES_PKG_PATH.'list.php' );
	die;
}

