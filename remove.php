<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_energymeasures/remove.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: remove.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $
 * @package energymeasures
 * @subpackage functions
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
