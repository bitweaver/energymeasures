<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_energymeasures/remove.php,v 1.2 2009/10/02 18:51:04 wjames5 Exp $
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
include_once( ENERGYMEASURES_PKG_PATH.'BitEnergyMeasures.php');
include_once( ENERGYMEASURES_PKG_PATH.'lookup_energymeasures_inc.php' );

$gBitSystem->verifyPackage( 'energymeasures' );

if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( "No energymeasures indicated" );
}

$gContent->verifyExpungePermission();

if( isset( $_REQUEST["confirm"] ) ) {
	if( $gContent->expunge()  ) {
		header ("location: ".BIT_ROOT_URL );
		die;
	} else {
		vd( $gContent->mErrors );
	}
}

$gBitSystem->setBrowserTitle( tra( 'Confirm delete of: ' ).$gContent->getTitle() );
$formHash['remove'] = TRUE;
$formHash['energymeasure_id'] = $_REQUEST['energymeasure_id'];
$msgHash = array(
	'label' => tra( 'Delete EnergyMeasures' ),
	'confirm_item' => $gContent->getTitle(),
	'warning' => tra( 'This energymeasures will be completely deleted.<br />This cannot be undone!' ),
);
$gBitSystem->confirmDialog( $formHash,$msgHash );

?>
