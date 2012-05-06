<?php
/**
* $Header$
* $Id$
*/

/**
* Copyright (c) 2009 Citizens Union Foundation
* Funding for the development of this package was provided by the John S. and James L. Knight Foundation.
* All Rights Reserved. See below for details and a complete list of authors.
* Licensed under the GNU GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/gpl.html for details 
*
* Developed by Tekimaki LLC http://tekimaki.com
* Date created 2009/9/1
*
* @author Will James <will@tekimaki.com>
* @version $Revision$
* @class BitEnergyMeasures
*/

require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );

/**
* This is used to uniquely identify the object
*/
define( 'BITENERGYMEASURES_CONTENT_TYPE_GUID', 'energymeasures' );

class BitEnergyMeasures extends LibertyMime {
	/**
	 * mEnergyMeasuresId Primary key for our mythical EnergyMeasures class object & table
	 * 
	 * @var array
	 * @access public
	 */
	private $mEnergyMeasuresId;

	private $mEnergyMeasures;

	/**
	 * mCache instance of BitCache
	 */
	private $mCache;

	/**
	 * BitEnergyMeasures During initialisation, be sure to call our base constructors
	 * 
	 * @param numeric $pEnergyMeasuresId 
	 * @param numeric $pContentId 
	 * @access public
	 * @return void
	 */
	function BitEnergyMeasures( $pEnergyMeasuresId=NULL, $pContentId=NULL ) {
		parent::__construct();
		$this->mEnergyMeasuresId = $pEnergyMeasuresId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITENERGYMEASURES_CONTENT_TYPE_GUID;
		$this->registerContentType( BITENERGYMEASURES_CONTENT_TYPE_GUID, array(
			'content_type_guid'   => BITENERGYMEASURES_CONTENT_TYPE_GUID,
			'content_name' => 'Energy Measure',
			'handler_class'       => 'BitEnergyMeasures',
			'handler_package'     => 'energymeasures',
			'handler_file'        => 'BitEnergyMeasures.php',
			'maintainer_url'      => 'http://www.bitweaver.org'
		));
		// Permission setup
		$this->mViewContentPerm    = 'p_energymeasures_view';
		$this->mCreateContentPerm  = 'p_energymeasures_create';
		$this->mUpdateContentPerm  = 'p_energymeasures_update';
		$this->mAdminContentPerm   = 'p_energymeasures_admin';
		$this->mExpungeContentPerm = 'p_energymeasures_expunge';
	}

	/**
	 * load Load the data from the database
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->verifyId( $this->mEnergyMeasuresId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mEnergyMeasuresId ) ? 'energymeasure_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mEnergyMeasuresId ) ? $this->mEnergyMeasuresId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT energymeasures.*, lc.*,
					uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
					uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name,
					lfp.storage_path AS `image_attachment_path` 
				$selectSql
				FROM `".BIT_DB_PREFIX."energymeasures_data` energymeasures
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = energymeasures.`content_id` ) 
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )
					$joinSql
				WHERE energymeasures.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mEnergyMeasuresId = $result->fields['energymeasure_id'];

				$this->mInfo['creator'] = ( !empty( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = ( !empty( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_name'] = BitUser::getTitle( $this->mInfo );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();
				$this->mInfo['thumbnail_urls'] = $this->getThumbnailUrls( $this->mInfo );

				LibertyMime::load();
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	 * store Any method named Store inherently implies data will be written to the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This is the ONLY method that should be called in order to store( create or update )an energymeasures!
	 * It is very smart and will figure out what to do for you. It should be considered a black box.
	 * 
	 * @param array $pParamHash hash of values that will be used to store the page
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash )&& LibertyMime::store( $pParamHash ) ) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."energymeasures_data";
			if( $this->mEnergyMeasuresId ) {
				$locId = array( "energymeasure_id" => $pParamHash['energymeasure_id'] );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['energymeasures_store'], $locId );
			} else {
				$pParamHash['energymeasures_store']['content_id'] = $pParamHash['content_id'];
				if( @$this->verifyId( $pParamHash['energymeasure_id'] ) ) {
					// if pParamHash['energymeasure_id'] is set, some is requesting a particular energymeasure_id. Use with caution!
					$pParamHash['energymeasures_store']['energymeasure_id'] = $pParamHash['energymeasure_id'];
				} else {
					$pParamHash['energymeasures_store']['energymeasure_id'] = $this->mDb->GenID( 'energymeasures_data_id_seq' );
				}
				$this->mEnergyMeasuresId = $pParamHash['energymeasures_store']['energymeasure_id'];

				$result = $this->mDb->associateInsert( $table, $pParamHash['energymeasures_store'] );
			}

			$this->mDb->CompleteTrans();
			$this->load();
		} else {
			$this->mErrors['store'] = 'Failed to save this energymeasures.';
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * verify Make sure the data is safe to store
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	 * NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	 * 
	 * @param array $pParamHash reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @access private
	 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
	 */
	function verify( &$pParamHash ) {
		// make sure we're all loaded up of we have a mEnergyMeasuresId
		if( $this->verifyId( $this->mEnergyMeasuresId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			$pParamHash['energymeasures_store']['content_id'] = $pParamHash['content_id'];
		}

		if( !empty( $pParamHash['data'] ) ) {
			$pParamHash['edit'] = $pParamHash['data'];
		}

		// If title specified truncate to make sure not too long
		if( !empty( $pParamHash['title'] ) ) {
			if( strlen( $pParamHash['title'] ) > 160 ){
				$this->mErrors['title'] = 'The title is too long. Maximum title length is 160 characters.';
			}else{
				$pParamHash['content_store']['title'] = $pParamHash['title'];
			}
		} else if( empty( $pParamHash['title'] ) ) { // else is error as must have title
			$this->mErrors['title'] = 'You must enter a title.';
		}

		// required type 
		if( !empty( $pParamHash['type'] ) ){
			$pParamHash['energymeasures_store']['type'] = $pParamHash['type'];
		}else{
			$this->mErrors['type'] = "You must selected a type.";
		}

		// required MwH
		if( !empty( $pParamHash['mwh'] ) ){
			$mwh = $pParamHash['mwh'];
			// someone might have put in commas, remove them
			$mwh = str_replace( ',','',$mwh );
			if( is_int( (int)$mwh ) ){
				$pParamHash['energymeasures_store']['mwh'] = $mwh;
			}else{
				$this->mErrors['mwh'] = "MwH must be an Integer. Commas will be automatically stripped.";
			}
		}else{
			$this->mErrors['mwh'] = "You must provide a MwH value.";
		}

		// if we have an error we get them all by checking parent classes for additional errors
		if( count( $this->mErrors ) > 0 ){
			parent::verify( $pParamHash );
		}

		return( count( $this->mErrors )== 0 );
	}

	function preparePreview( &$pParamHash ){
		$this->verify( $pParamHash );

		if( !empty( $pParamHash['title'] ) ) {
			$this->mInfo['title'] = $pParamHash['title'];
		}
		if( isset( $pParamHash["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash["edit"];
			$this->mInfo['no_cache']    = TRUE;
			$this->mInfo['parsed_data'] = $this->parseData( $this->mInfo['data'], (!empty($this->mInfo['format_guid']) ? $this->mInfo['format_guid'] : 'tikiwiki' ));
		}
		if( !empty( $pParamHash['type'] ) ){
			$this->mInfo['type'] = $pParamHash['type'];
		}
		if( !empty( $pParamHash['mwh'] ) ){
			$this->mInfo['mwh'] = $pParamHash['mwh'];
		}
	}

	/**
	 * expunge 
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function expunge() {
		global $gBitSystem;
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."energymeasures_data` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
			}
			$this->mDb->CompleteTrans();
			// If deleting the default/home energymeasures record then unset this.
			if( $ret && $gBitSystem->getConfig( 'energymeasures_home_id' ) == $this->mEnergyMeasuresId ) {
				$gBitSystem->storeConfig( 'energymeasures_home_id', 0, ENERGYMEASURES_PKG_NAME );
			}
		}
		return $ret;
	}

	/**
	 * isValid Make sure energymeasures is loaded and valid
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mEnergyMeasuresId ) && @BitBase::verifyId( $this->mContentId ));
	}

	/**
	 * getList This function generates a list of records from the liberty_content database for use in a list page
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return array List of energymeasures data
	 */
	function getList( &$pParamHash ) {
		// this makes sure parameters used later on are set
		LibertyContent::prepGetList( $pParamHash );

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "
			SELECT energymeasures.*, lc.`content_id`, lc.`title`, lc.`data`,
				lf.storage_path AS `image_attachment_path`
			$selectSql
			FROM `".BIT_DB_PREFIX."energymeasures_data` energymeasures
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = energymeasures.`content_id` ) 
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`   la ON la.`content_id`         = lc.`content_id` AND la.`is_primary` = 'y'
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`         lf ON lf.`file_id`            = la.`foreign_id`
			$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."energymeasures_data` energymeasures
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = energymeasures.`content_id` ) $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {
			$res['thumbnail_urls'] = $this->getThumbnailUrls( $res );
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		// add all pagination info to pParamHash
		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	 * getDisplayUrl Generates the URL to the energymeasures page
	 * 
	 * @access public
	 * @return string URL to the energymeasures page
	 */
	function getDisplayUrl() {
		global $gBitSystem;
		$ret = NULL;
		if( @$this->isValid() ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
				$ret = ENERGYMEASURES_PKG_URL.$this->mEnergyMeasuresId;
			} else {
				$ret = ENERGYMEASURES_PKG_URL."index.php?energymeasure_id=".$this->mEnergyMeasuresId;
			}
		}
		return $ret;
	}


	/**
	* Get the URL for any given attachment
	* @param $pParamHash pass in full set of data returned from load query
	* @return array of thumbnail urlss
	* @access public
	**/
	function getThumbnailUrls( $pParamHash ) {
		global $gBitSystem, $gThumbSizes;
		$ret = NULL;
		if( !empty( $pParamHash['image_attachment_path'] )) {
			$thumbHash = array(
				'mime_image'   => FALSE,
				'storage_path' => $pParamHash['image_attachment_path']
			);
			$ret = liberty_fetch_thumbnails( $thumbHash );
			$ret['original'] = BIT_ROOT_URL.$pParamHash['image_attachment_path'];
		}
		return $ret;
	}

	/**
	 * getContentStatus
	 * 
	 * @access public
	 * @return an array of content_status_id, content_status_names the current 
	 * user can use on this content.  
	 * 
	 * NOTE: pUserMinimum and pUserMaximum are currently NOT inclusive in query, so these are one beyond the limit we desire
	 */
	function getAvailableContentStatuses( $pUserMinimum=-6, $pUserMaximum=51 ) {
		global $gBitUser;
		$ret =  $this->mDb->getAssoc( "SELECT `content_status_id`, `content_status_name` FROM `".BIT_DB_PREFIX."liberty_content_status` WHERE `content_status_id` > ? AND `content_status_id` < ? ORDER BY `content_status_id`", array( $pUserMinimum, $pUserMaximum ));
		// this is a little ugly as we manually trim the list to just what we need for energy measures
		if ( array_key_exists( -1, $ret ) ){
			unset( $ret[-1] );
		}
		return $ret;
	}

	/**
	 *
	 */
	function writeListJS(){
		global $gBitSmarty;

		if( empty( $this->mCache ) ){
			$this->mCache = new BitCache( ENERGYMEASURES_CACHE_DIR, TRUE );
		}

		$listHash = array( 'max_records' => 99999, 'enforce_status' => TRUE, 'max_status_id' => 50 );
		$listData = $this->getList( $listHash );

		// cache the javascript version of the energy measures if they have not been cached already
		$jsFile = "energymeasures.js";
		if( $this->mCache->isCached( $jsFile ) ){
			$this->mCache->expungeCacheFile( $jsFile );
		}
		$gBitSmarty->assign_by_ref( 'EnergyMeasures', $listData );
		$jsData = $gBitSmarty->fetch( 'bitpackage:energymeasures/list_energymeasures.js.tpl' );
		$this->mCache->writeCacheFile( $jsFile, $jsData );
	}

}
