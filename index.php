<?php
// $Header$

// Initialization
require_once( '../kernel/setup_inc.php' );

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

