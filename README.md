# GetUserInfo


Extension for providing user information.

This extension is potentially privacy risky, so great care should be taken to configure it.

## Usage
* {{#getuserinfo:email|UserName}}
* {{#getuserinfo:realname|UserName}}
* {{#getuserinfo:groups|UserName}}

## Configuration settings

Groups that can see the outcome
$GLOBALS['wgGUAllowedGroups']['email'] = array('sysop');
$GLOBALS['wgGUAllowedGroups']['realname'] = array('sysop');
$GLOBALS['wgGUAllowedGroups']['groups'] = array('sysop');

Namespaces that can render the outcome regardless of the user group
$GLOBALS['wgGUWhiteListNS']['email'] = array();
$GLOBALS['wgGUWhiteListNS']['realname'] = array();
$GLOBALS['wgGUWhiteListNS']['groups'] = array();

Additional pages than can render the outcome regardless of the namespace or user group
$GLOBALS['wgGUWhiteListPages']['email'] = array();
$GLOBALS['wgGUWhiteListPages']['realname'] = array();
$GLOBALS['wgGUWhiteListPages']['groups'] = array();

Whether only the same user can see their data
$GLOBALS['wgGUOnlyActualUser'] = false;

Whether data can only be seen in the same user page
$GLOBALS['wgGUOnlyUserPage'] = false;