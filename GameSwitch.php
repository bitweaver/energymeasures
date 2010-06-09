<?php
/**
* $Header$
* $Id$
*/

/**
* date created 2009/9/1
* @author Will James <will@tekimaki.com>
* @version $Revision$
* @class BitEnergyMeasures
*/

require_once( GAMES_PKG_PATH.'BitGame.php' );

class GameSwitch extends BitGame {
	/* private classes */
	/**
	 * mEnergyMeasures hash of data necessary to play the game
	 */
	private $mEnergyMeasures;

	/**
	 * __construct
	 */
	function __construct(){
		$this->mPackageName = 'energymeasures';//ENERGYMEASURES_PKG_NAME;
		$this->mGameType = "switch";
		$this->mDisplayTemplate = "play_switch.tpl";
		parent::__construct();
	}

	/**
	 * load up data necessary to play the game
	 */
	function load(){
	}

	/**
	 * render the page to play
	 */
	public function display(){
		global $gBitSystem, $gBitThemes, $gBitSmarty;

		$gBitSmarty->assign_by_ref( 'game', $this->mGameSettings );

		$gBitThemes->loadJavascript( UTIL_PKG_PATH."/javascript/libs/jquery/min/jquery.js", FALSE, 10 );

		$gBitThemes->loadJavascript( STORAGE_PKG_PATH."/".ENERGYMEASURES_CACHE_DIR."/energymeasures.js", TRUE, 900);

		$gBitThemes->loadJavascript( ENERGYMEASURES_PKG_PATH.'scripts/memory.js', TRUE, 910 );

		// suggestion pkg dependency
		if( $gBitSystem->isPackageActive( 'suggestion' ) ){
			$gBitThemes->loadJavascript( SUGGESTION_PKG_PATH.'scripts/suggestion.js', TRUE, 920 );
		}

		// this will call the display template
		parent::display();
	}

	public function storeScore( &$pParamHash ){
		if( $this->verifyScore( $pParamHash ) ){
			// store each
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."switch_score_em_map";
			foreach( $pParamHash['store_switch_score']['selected'] as $emId ){
				$store = array( 'energymeasure_id' => $emId,
								'score_id' => $pParamHash['store_switch_score']['score_id'],
								'selected' => 1 );
				$result = $this->mDb->associateInsert( $table, $store );
			}
			foreach( $pParamHash['store_switch_score']['rejected'] as $emId ){
				$store = array( 'energymeasure_id' => $emId,
								'score_id' => $pParamHash['store_switch_score']['score_id'],
								'selected' => 0 );
				$result = $this->mDb->associateInsert( $table, $store );
			}
			$this->mDb->CompleteTrans();
		}
		return( count( $this->mErrors )== 0 );
	}

	public function verifyScore( &$pParamHash ){
		if( !empty( $pParamHash['store_score']['score_id'] ) ){
			$pParamHash['store_switch_score']['score_id'] = $pParamHash['store_score']['score_id'];
		}else{
			$this->mErrors['score_id'] = tra( 'Required Score Id not provided' );
		}
		if( !empty( $pParamHash['store_score']['user_id'] ) ){
			$pParamHash['store_switch_score']['user_id'] = $pParamHash['store_score']['user_id'];
		}else{
			$this->mErrors['user_id'] = tra( 'Required Score Id not provided' );
		}
		$pParamHash['store_switch_score']['selected'] = array();
		if( !empty( $pParamHash['selected'] ) ){
			$pParamHash['store_switch_score']['selected'] = explode( ",", $pParamHash['selected'] );
		}else{
			$gBitSystem->fatalError( tra('At least one energymeasure must be selected') );
		}
		$pParamHash['store_switch_score']['rejected'] = array();
		if( !empty( $pParamHash['rejected'] ) ){
			$pParamHash['store_switch_score']['rejected'] = explode( ",", $pParamHash['rejected'] );
		}
		return( count( $this->mErrors )== 0 );
	}
}
