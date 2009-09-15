<?php
// $Header: /cvsroot/bitweaver/_bit_energymeasures/admin/admin_energymeasures_inc.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $

require_once( ENERGYMEASURES_PKG_PATH.'BitEnergyMeasures.php' );

$formEnergyMeasuresLists = array(
	"energymeasures_list_energymeasure_id" => array(
		'label' => 'Id',
		'note' => 'Display the energymeasures id.',
	),
	"energymeasures_list_title" => array(
		'label' => 'Title',
		'note' => 'Display the title.',
	),
	"energymeasures_list_description" => array(
		'label' => 'Description',
		'note' => 'Display the description.',
	),
	"energymeasures_list_data" => array(
		'label' => 'Text',
		'note' => 'Display the text.',
	),
);
$gBitSmarty->assign( 'formEnergyMeasuresLists', $formEnergyMeasuresLists );

// Process package preferences 
if( !empty( $_REQUEST['energymeasures_settings'] )) {
	$energymeasuresToggles = array_merge( $formEnergyMeasuresLists );
	foreach( $energymeasuresToggles as $item => $data ) {
		simple_set_toggle( $item, ENERGYMEASURES_PKG_NAME );
	}

	
}

// Process javascript re-cache
if( !empty( $_REQUEST['energymeasures_refresh_js'] ) ){
	$em = new BitEnergyMeasures();
	$em->writeListJS();
}

// Check if game is registered and we have custom settings
require_once( GAMES_PKG_PATH.'BitGameSystem.php' );
$gameSystem = new BitGameSystem();
$infoFile = ENERGYMEASURES_PKG_PATH."games.yaml"; 
$gamesArray = $gameSystem->parseInfoFile( $infoFile ); 
// this sucks but I dont feel like writing the loop right now
$gameDefaults = $gamesArray[0];
$game = array_merge( $gameDefaults, $gameSystem->getGameType( 'switch' ) );

// Process game settings
if( !empty( $_REQUEST['game_settings'] ) ){
	$gameData = array_merge( $game, $_REQUEST['games']['switch'] ); 
	if( $gameSystem->registerGameType( $gameData ) ){
		$game = $gameData;
	}
}

$gBitSmarty->assign_by_ref( 'game', $game );

