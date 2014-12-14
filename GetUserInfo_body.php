<?php
/*
 * 2011-2012
 * Provides user email or real name information
 *
*/

class ExtGetUserInfo
{      
	/**
         * The rendering object (skin)
         */
        private $display=NULL;

	/**
	 * @param $parser Parser
	 * @return bool
	 */
	function clearState(&$parser) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	public function getuserinfo ($parser, $param1="", $param2='realname') {

		global $wgUser, $wgGUAllowedGroups, $wgGUWhiteListPages;
		$parser->disableCache();
		
		$user = $wgUser;

		// Get title of page
		$titlepage = $parser->getTitle();	
	
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		foreach ($cur_gps as $cur_gp) {
			if (in_array($cur_gp, $wgGUAllowedGroups[$param2])) {
				$ingroup = true;
				break;
			}
		}

		if (in_array($titlepage, $wgGUWhiteListPages[$param2])) {
                	$ingroup = true;
                }


		if (!$ingroup) {
			return(false);
		}

		// Let's check if it exists	
		$userout = User::newFromName($param1);
		if (!method_exists($userout, "getID")) {
			return(false);
		}
		else {
			if ($userout->getID() == 0) {
				return(false);
			}
			
			//Now do
			return($this->userget($param1, $param2));
		}
        }

	
	private function userget($username, $param) {
		
		if ($param == 'email') { 
			return($this->getUserEmail($username));
		}
		elseif ($param == 'groups') {
			return($this->getUserGroups($username));
		}
		else {
			return($this->getUserRealName($username));
		}

	}	

	//Function for getting user email of that profile
	private function getUserEmail($username) {

		$user = User::newFromName($username);	
		return ($user->getEmail());
	}
	
	//Function for getting user groups of that profile
	private function getUserGroups($username) {
		
		$user = User::newFromName($username);
		return(implode(",", $user->getGroups()));
	}
	
	//Function for getting user real name of that profile
        private function getUserRealName($username) {

		$user = User::newFromName($username);	
		return ($user->getRealName());
        }

}
?>
