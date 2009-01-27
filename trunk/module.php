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
$_group_tool = $_student_tool = 'mods/remote_lab/index.php';

/*******
 * instructor Manage section:
 */

$this->_pages['mods/remote_lab/index_instructor.php']['title_var'] = 'remote_lab';
$this->_pages['mods/remote_lab/index_instructor.php']['parent'] = 'tools/index.php';
$this->_pages['mods/remote_lab/index_instructor.php']['children'] = array('mods/remote_lab/remote_labs.php','mods/remote_lab/experiment_sets.php','mods/remote_lab/experiments.php','mods/remote_lab/reservations.php','mods/remote_lab/reports.php');

$this->_pages['mods/remote_lab/remote_labs.php']['title_var'] = 'laboratories';
$this->_pages['mods/remote_lab/remote_labs.php']['parent'] = 'tools/index.php';
$this->_pages['mods/remote_lab/experiment_sets.php']['title_var'] = 'experiment_sets';
$this->_pages['mods/remote_lab/experiment_sets.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remote_lab/experiments.php']['title_var'] = 'experiments';
$this->_pages['mods/remote_lab/experiments.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remote_lab/reservations.php']['title_var'] = 'reservations';
$this->_pages['mods/remote_lab/reservations.php']['parent']   = 'tools/index.php';
$this->_pages['mods/remote_lab/reports.php']['title_var'] = 'reports';
$this->_pages['mods/remote_lab/reports.php']['parent']   = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/remote_lab/index.php']['title_var'] = 'remote_lab';
$this->_pages['mods/remote_lab/index.php']['children'] = array('mods/remote_lab/experiments_student.php','mods/remote_lab/reservations_student.php','mods/remote_lab/reports_student.php');
$this->_pages['mods/remote_lab/experiments_student.php']['title_var'] = 'my_experiments';
$this->_pages['mods/remote_lab/experiments_student.php']['parent'] = 'mods/remote_lab/index.php';
$this->_pages['mods/remote_lab/reservations_student.php']['title_var'] = 'my_reservations';
$this->_pages['mods/remote_lab/reservations_student.php']['parent'] = 'mods/remote_lab/index.php';
$this->_pages['mods/remote_lab/reports_student.php']['title_var'] = 'my_experiments_reports';
$this->_pages['mods/remote_lab/reports_student.php']['parent'] = 'mods/remote_lab/index.php';
//$this->_pages['mods/remote_lab/index.php']['img']       = 'mods/remote_lab/remote_lab.jpg';


/* public pages */
//$this->_pages[AT_NAV_PUBLIC] = array('mods/hello_world/index_public.php');
//$this->_pages['mods/hello_world/index_public.php']['title_var'] = 'hello_world';
//$this->_pages['mods/hello_world/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/hello_world/index_mystart.php');
//$this->_pages['mods/hello_world/index_mystart.php']['title_var'] = 'hello_world';
//$this->_pages['mods/hello_world/index_mystart.php']['parent'] = AT_NAV_START;

?>