<?php
/*
 * 2011-2014
 * Provides user email or real name information
 *
*/

class ExtGetUserInfo {
	
	/**
	* @param $parser Parser
	* @return bool
	*/
	public static function clearState( $parser ) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	/**
	 * Register ParserClearState hook.
	 * We defer this until needed to avoid the loading of the code of this file
	 * when no parser function is actually called.
	 */
	public static function registerClearHook() {
		static $done = false;
		if( !$done ) {
			global $wgHooks;
			$wgHooks['ParserClearState'][] = __CLASS__ . '::clearState';
			$done = true;
		}
	}
	
	public static function getUserInfo( $parser, $frame, $args ) {

		global $wgUser, $wgGUAllowedGroups, $wgGUWhiteListPages;
		
		if ( isset( $args[0] ) ) {
			$param1 = trim( $frame->expand( $args[0] ) );
		} else {
			return false;
		}
		
		$param2 = "email";
		
		if ( isset( $args[1] ) ) {
			$param2 = trim( $frame->expand( $args[1] ) );
		}
		
		$parser->disableCache();
		
		$user = $wgUser;

		// Get title of page
		$titlepage = $parser->getTitle();
	
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		// TODO: Play with priorities here
		
		foreach ( $cur_gps as $cur_gp ) {
			if ( in_array( $cur_gp, $wgGUAllowedGroups[ $param2 ] ) ) {
				$ingroup = true;
				break;
			}
		}

		if ( in_array( $titlepage, $wgGUWhiteListPages[ $param2 ] ) ) {
			$ingroup = true;
		}


		if ( !$ingroup ) {
			return false;
		}

		// Let's check if it exists	
		$userout = User::newFromName( $param1 );
		if ( !method_exists( $userout, "getID" ) ) {
			return false;
		}
		else {
			if ( $userout->getID() == 0 ) {
				return false;
			}
			
			//Now do
			return( self::userGet( $param1, $param2 ) );
		}
	}

	
	private static function userGet( $username, $param ) {
		
		$user = User::newFromName( $username );

		if ( is_object( $user ) ) {
		
			if ( $param == 'email' ) { 
				return ( $user->getEmail() );
			}
			elseif ( $param == 'groups' ) {
				return( implode( ",", $user->getGroups() ) );
			}
			else {
				return ( $user->getRealName() ) ;
			}
			
		}
		
		return false;
	}

}
