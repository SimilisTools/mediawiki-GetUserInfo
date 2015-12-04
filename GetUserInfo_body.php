<?php
/*
 * 2011-2015
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

		global $wgUser;
		global $wgGUAllowedGroups, $wgGUWhiteListPages, $wgGUWhiteListNS;
		global $wgGUOnlyActualUser, $wgGUOnlyUserPage;
		
		$parser->disableCache();

		if ( isset( $args[0] ) ) {
			$param1 = trim( $frame->expand( $args[0] ) );
		} else {
			return false;
		}
		
		$param2 = "email";
		
		if ( isset( $args[1] ) ) {
			$param2 = trim( $frame->expand( $args[1] ) );

			// Fix problem with existing values -> Values maybe other place
			if ( ! in_array( array( 'email', 'realname', 'groups' ), $param2 ) ) {
				$param2 = "email";
			}
		}
				
		$user = $wgUser;
		// TODO: Check for maintenace mode

		// Get title and NS of page
		$titlepage = $parser->getTitle();
		$namespace = $titlepage->getNamespace();
		$fulltext  = $titlepage->getFullText();
		$titletext = $titlepage->getText();
	
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;

		foreach ( $cur_gps as $cur_gp ) {
			if ( in_array( $cur_gp, $wgGUAllowedGroups[ $param2 ] ) ) {
				$ingroup = true;
				break;
			}
		}

		# Check NS
		if ( in_array( $namespace, $wgGUWhiteListNS[ $param2 ] ) ) {
			$ingroup = true;
		}
		
		# Check pages
		if ( in_array( $fulltext, array_map( "self::allWS", $wgGUWhiteListPages[ $param2 ] ) ) ) {
			$ingroup = true;
		}

		// If true show only if the same user
		if ( $wgGUOnlyActualUser ) {
			if ( str_replace( "_", " ", $param1 ) != $user->getName() ) {
				$ingroup = false;
			}
		}
		
		// If true show only in user page
		if ( $wgGUOnlyUserPage ) {
			if ( $namespace != NS_USER && $titletext != str_replace( "_", " ", $param1 ) ) {
				$ingroup = false;
			}
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

	/** We allow title to be stored in different ways **/
	private static function allWS( $n ) {
		return( str_replace( "_", " ", $n ) );
	}
	
}
