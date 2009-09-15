<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_energymeasures/admin/schema_inc.php,v 1.1 2009/09/15 15:01:13 wjames5 Exp $
 * @package energymeasures
 */
$tables = array(
	'energymeasures_data' => "
		energymeasure_id I4 PRIMARY,
		content_id I4 NOTNULL,
		type C(40) NOTNULL,
		mwh I4 NOTNULL
		CONSTRAINT '
			, CONSTRAINT `em_data_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
	",
	'switch_score_em_map' => "
		score_id I4 NOTNULL,
		energymeasure_id I4 NOTNULL,
		selected I1 NOTNULL DEFAULT 1
		CONSTRAINT '
			, CONSTRAINT `switch_score_game_score_ref` FOREIGN KEY (`score_id`) REFERENCES `".BIT_DB_PREFIX."games_scores` (`score_id`)
			, CONSTRAINT `switch_score_em_id_ref` FOREIGN KEY (`energymeasure_id`) REFERENCES `".BIT_DB_PREFIX."energymeasures_data` (`energymeasure_id`)'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( ENERGYMEASURES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( ENERGYMEASURES_PKG_NAME, array(
	'description' => "EnergyMeasures package to demonstrate how to build a bitweaver package.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
));

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ARTICLES_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( ENERGYMEASURES_PKG_NAME, array (
	'energymeasures_data_id_seq' => array( 'start' => 1 )
));

/* // Schema defaults
$gBitInstaller->registerSchemaDefault( ENERGYMEASURES_PKG_NAME, array(
	"INSERT INTO `".BIT_DB_PREFIX."bit_energymeasures_types` (`type`) VALUES ('EnergyMeasures')",
)); */

// User Permissions
$gBitInstaller->registerUserPermissions( ENERGYMEASURES_PKG_NAME, array(
	array ( 'p_energymeasures_admin'  , 'Can admin energymeasures'           , 'admin'      , ENERGYMEASURES_PKG_NAME ),
	array ( 'p_energymeasures_create' , 'Can create a energymeasures entry'  , 'registered' , ENERGYMEASURES_PKG_NAME ),
	array ( 'p_energymeasures_update' , 'Can update any energymeasures entry', 'editors'    , ENERGYMEASURES_PKG_NAME ),
	array ( 'p_energymeasures_view'   , 'Can view energymeasures data'       , 'basic'      , ENERGYMEASURES_PKG_NAME ),
	array ( 'p_energymeasures_expunge', 'Can delete any energymeasures entry', 'admin'      , ENERGYMEASURES_PKG_NAME ),
));

// Default Preferences
$gBitInstaller->registerPreferences( ENERGYMEASURES_PKG_NAME, array(
	array ( ENERGYMEASURES_PKG_NAME , 'energymeasures_default_ordering' , 'energymeasure_id_desc' ),
	array ( ENERGYMEASURES_PKG_NAME , 'energymeasures_list_energymeasure_id'   , 'y'              ),
	array ( ENERGYMEASURES_PKG_NAME , 'energymeasures_list_title'       , 'y'              ),
	array ( ENERGYMEASURES_PKG_NAME , 'energymeasures_list_description' , 'y'              ),
	array ( ENERGYMEASURES_PKG_NAME , 'energymeasures_home_id'          , 0                ),
));

// Version - now use upgrades dir to set package version number.
// $gBitInstaller->registerPackageVersion( ENERGYMEASURES_PKG_NAME, '0.5.1' );

// Requirements
$gBitInstaller->registerRequirements( ENERGYMEASURES_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.0' ),
));
?>
