<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/edit.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $
// Copyright (c) 2004 bitweaver EnergyMeasures
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

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
