<?php
/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

$_course_privilege = 'new'; // 0/false | 1/AT_PRIV_ADMIN | 'new'/TRUE
$_admin_privilege  = 'new'; // 0/false | 1/AT_ADMIN_PRIV_ADMIN | 'new'/TRUE

/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run any database queries it needs, ie. to create
 * its own tables.
 */
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>
