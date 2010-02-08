<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/list.php,v 1.4 2010/02/08 21:37:30 wjames5 Exp $

// Initialization
require_once( '../kernel/setup_inc.php' );
require_once( ENERGYMEASURES_PKG_PATH.'BitEnergyMeasures.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'energymeasures' );

// Look up the content
require_once( ENERGYMEASURES_PKG_PATH.'lookup_energymeasures_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// Remove energymeasures data if we don't want them anymore
if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_energymeasures_data" ) {

	// Now check permissions to remove the selected energymeasures data
	$gBitSystem->verifyPermission( 'p_energymeasures_update' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_energymeasures_data';
		foreach( $_REQUEST["checked"] as $del ) {
			$tmpPage = new BitEnergyMeasures( $del);
			if ( $tmpPage->load() && !empty( $tmpPage->mInfo['title'] )) {
				$info = $tmpPage->mInfo['title'];
			} else {
				$info = $del;
			}
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>'.$info;
		}
		$gBitSystem->confirmDialog( $formHash, 
			array(
				'warning' => tra('Are you sure you want to delete ').count( $_REQUEST["checked"] ).' energymeasures records?',
				'error' => tra('This cannot be undone!')
			)
		);
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpPage = new BitEnergyMeasures( $deleteId );
			if( !$tmpPage->load() || !$tmpPage->expunge() ) {
				array_merge( $errors, array_values( $tmpPage->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign_by_ref( 'errors', $errors );
		}
	}
}

// Create new energymeasures object
$energymeasures = new BitEnergyMeasures();
$energymeasuresList = $energymeasures->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'energymeasuresList', $energymeasuresList );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );

// Display the template
$gBitSystem->display( 'bitpackage:energymeasures/list_energymeasures.tpl', tra( 'EnergyMeasures' ) , array( 'display_mode' => 'list' ));
