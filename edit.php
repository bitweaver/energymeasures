<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/edit.php,v 1.3 2010/02/08 21:37:30 wjames5 Exp $

// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'energymeasures' );

require_once( ENERGYMEASURES_PKG_PATH.'lookup_energymeasures_inc.php' );

// Check permissions to access this page
if( $gContent->isValid() ){
	$gContent->verifyUpdatePermission();
}else{
	$gContent->verifyCreatePermission();
}

// Preview
if( isset( $_REQUEST["preview"] ) ) {
	$gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
	$gContent->invokeServices( 'content_preview_function' );
// Save 
}elseif( !empty( $_REQUEST["save_energymeasures"] ) ) {
	if( $gContent->store( $_REQUEST ) ) {
		//clear game cache file
		$gContent->writeListJS();
		// redirect to View
		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	}
// Edit
} else {
	$gContent->invokeServices( 'content_edit_function' );
}

// Make any errors available to the tpls
$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );

// Display the template
$gBitSystem->display( 'bitpackage:energymeasures/edit_energymeasures.tpl', tra('EnergyMeasures') , array( 'display_mode' => 'edit' ));
?>
