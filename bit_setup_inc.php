<?php
global $gBitSystem;

define( 'ENERGYMEASURES_CACHE_DIR', 'games/energymeasures' );

$registerHash = array(
	'package_name' => 'energymeasures',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'energymeasures' ) && $gBitUser->hasPermission( 'p_energymeasures_view' ) ) {
	$menuHash = array(
		'package_name'  => ENERGYMEASURES_PKG_NAME,
		'index_url'     => ENERGYMEASURES_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:energymeasures/menu_energymeasures.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );
}
?>
