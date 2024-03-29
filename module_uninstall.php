<?php
/*******
 * module_uninstall.php performs reversion of module_install.php
 */

/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::uninstall() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run reverted database queries of module.sql,
 * ie. "create table" statement in module.sql is run as drop according table.
 */
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
  // deal with the SQL file:
  require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
  $sqlUtility =& new SqlUtility();
  $sqlUtility->revertQueryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>
