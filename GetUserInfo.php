<?php
/**
 * Copyright (C) 2011 Toni Hermoso Pulido <toniher@cau.cat>
 * http://www.cau.cat
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "Not a valid entry point";
	exit( 1 );
}

//self executing anonymous function to prevent global scope assumptions
call_user_func( function() {

	// At first, only allow to sysop to be checked
	$GLOBALS['wgGUAllowedGroups']['email'] = array('sysop');
	$GLOBALS['wgGUAllowedGroups']['realname'] = array('sysop');
	$GLOBALS['wgGUAllowedGroups']['groups'] = array('sysop');

	// WhiteListNS
	// This should be stored as NS_USER or so...
	$GLOBALS['wgGUWhiteListNS']['email'] = array();
	$GLOBALS['wgGUWhiteListNS']['realname'] = array();
	$GLOBALS['wgGUWhiteListNS']['groups'] = array();

	// WhiteListPages
	$GLOBALS['wgGUWhiteListPages']['email'] = array();
	$GLOBALS['wgGUWhiteListPages']['realname'] = array();
	$GLOBALS['wgGUWhiteListPages']['groups'] = array();
	
	$GLOBALS['wgGUOnlyActualUser'] = false;
	$GLOBALS['wgGUOnlyUserPage'] = false;

	
	$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
		'path' => __FILE__,
		'name' => 'GetUserInfo',
		'author' => 'Toni Hermoso',
		'version' => '0.1',
		'url' => 'https://github.com/SimilisTools/mediawiki-GetUserInfo',
		'descriptionmsg' => 'getuserinfo-desc',
	);
	
	$GLOBALS['wgAutoloadClasses']['ExtGetUserInfo'] = dirname(__FILE__) . '/GetUserInfo_body.php';
	$GLOBALS['wgExtensionMessagesFiles']['GetUserInfo'] = dirname( __FILE__ ) . '/GetUserInfo.i18n.php';
	$GLOBALS['wgExtensionMessagesFiles']['GetUserInfoMagic'] = dirname(__FILE__) . '/GetUserInfo.i18n.magic.php';
	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'wfRegisterGetUserInfo';

});

function wfRegisterGetUserInfo( $parser ) {

	$parser->setFunctionHook( 'getuserinfo', 'ExtGetUserInfo::getUserInfo', Parser::SFH_OBJECT_ARGS );

	return true;
}

