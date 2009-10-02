<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/edit_score.php,v 1.2 2009/10/02 18:51:04 wjames5 Exp $

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'games' );

// Load up a game
require_once( GAMES_PKG_PATH.'lookup_game_inc.php' );

// Check permissions to access this page
$gBitSystem->verifyPermission( 'p_games_scores_create' );

if( !$gGameSystem->isValid() ) {
	// if we dont have a valid game type to play return 404
	$gBitSystem->setHttpStatus( 404 );

	$msg = tra( "The requested game:".ucfirst($_REQUEST['game'])." could not be found." );

	$gBitSystem->fatalError( tra( $msg ) ); 
// Save 
}elseif( !empty( $_REQUEST["save_switch_score"] ) ) {
	$gBitUser->verifyTicket();

	// always an ajax request
	if( $gBitUser->isRegistered() ){ 
		$_REQUEST['user_id'] = $gBitUser->mUserId;
	}else{
		$gGameSystem->registerPlayer( $_REQUEST );
	}
	if( !empty( $_REQUEST['user_id'] ) && $gGameSystem->storeScore( $_REQUEST ) ) {
		// Display the template
		$gBitSystem->display( 'bitpackage:games/edit_score_thankyou.tpl', tra('Switch') , array( 'format' => 'center_only', 'display_mode' => 'display' ));
		die;
	}else{
		// vd( $gGameSystem->mErrors );
		$gBitSmarty->assign_by_ref( 'errors', $gGameSystem->mErrors );
	}
}
// Display the template
$gBitSystem->display( 'bitpackage:games/edit_score.tpl', tra('Switch') , array( 'format' => 'center_only', 'display_mode' => 'edit' ));
die;
