<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_REMOTE_LAB',       $this->getPrivilege());
define('AT_ADMIN_PRIV_REMOTE_LAB', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/remotelab/index.php';

/*******
 * instructor Manage section:
 */

$this->_pages['mods/remotelab/index_instructor.php']['title_var'] = 'remotelab';
$this->_pages['mods/remotelab/index_instructor.php']['parent'] = 'tools/index.php';
$this->_pages['mods/remotelab/index_instructor.php']['children'] = array('mods/remotelab/remote_labs.php','mods/remotelab/experiment_sets.php','mods/remotelab/experiments.php','mods/remotelab/reservations.php','mods/remotelab/reports.php');

$this->_pages['mods/remotelab/remote_labs.php']['title_var'] = 'laboratories';
$this->_pages['mods/remotelab/remote_labs.php']['parent'] = 'tools/index.php';
$this->_pages['mods/remotelab/experiment_sets.php']['title_var'] = 'experiment_sets';
$this->_pages['mods/remotelab/experiment_sets.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remotelab/experiments.php']['title_var'] = 'experiments';
$this->_pages['mods/remotelab/experiments.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remotelab/reservations.php']['title_var'] = 'reservations';
$this->_pages['mods/remotelab/reservations.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remotelab/reports.php']['title_var'] = 'reports';
$this->_pages['mods/remotelab/reports.php']['parent']   = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/remotelab/index.php']['title_var'] = 'remote_lab';
$this->_pages['mods/remotelab/index.php']['children'] = array('mods/remotelab/experiments_student.php','mods/remotelab/reservations_student.php','mods/remotelab/reports_student.php');
$this->_pages['mods/remotelab/experiments_student.php']['title_var'] = 'my_experiments';
$this->_pages['mods/remotelab/experiments_student.php']['parent'] = 'mods/remotelab/index.php';
$this->_pages['mods/remotelab/reservations_student.php']['title_var'] = 'my_reservations';
$this->_pages['mods/remotelab/reservations_student.php']['parent'] = 'mods/remotelab/index.php';
$this->_pages['mods/remotelab/reports_student.php']['title_var'] = 'my_experiments_reports';
$this->_pages['mods/remotelab/reports_student.php']['parent'] = 'mods/remotelab/index.php';
//$this->_pages['mods/remotelab/index.php']['img']       = 'mods/remotelab/remote_lab.jpg';


/* public pages */
//$this->_pages[AT_NAV_PUBLIC] = array('mods/hello_world/index_public.php');
//$this->_pages['mods/hello_world/index_public.php']['title_var'] = 'hello_world';
//$this->_pages['mods/hello_world/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/hello_world/index_mystart.php');
//$this->_pages['mods/hello_world/index_mystart.php']['title_var'] = 'hello_world';
//$this->_pages['mods/hello_world/index_mystart.php']['parent'] = AT_NAV_START;

?>