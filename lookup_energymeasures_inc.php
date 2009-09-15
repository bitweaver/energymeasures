<?php
global $gContent;
require_once( ENERGYMEASURES_PKG_PATH.'BitEnergyMeasures.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if energymeasure_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['energymeasure_id'] ) ) {
		$gContent = new BitEnergyMeasures( $_REQUEST['energymeasure_id'] );

	// if content_id supplied, use that
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitEnergyMeasures( NULL, $_REQUEST['content_id'] );

	} elseif (@BitBase::verifyId( $_REQUEST['energymeasures']['energymeasure_id'] ) ) {
		$gContent = new BitEnergyMeasures( $_REQUEST['energymeasures']['energymeasure_id'] );

	// otherwise create new object
	} else {
		$gContent = new BitEnergyMeasures();
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
?>
