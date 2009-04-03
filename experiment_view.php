<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_POST['id']) {
	header('Location: ../index.php');
	exit;
}

$resid = intval($_POST['id']);

$sql = "SELECT	first_name, last_name
	FROM	".TABLE_PREFIX."members
	WHERE	member_id = ".$_SESSION['member_id'];

$result = mysql_query($sql, $db);
$q_row  = mysql_fetch_assoc($result);
$student_name = $q_row['last_name'] . ', ' . $q_row['first_name'];

	$sql =  "SELECT
                ex.package_id,
                so.href,
                so.idx,
                so.org_id,
                so.item_id
            FROM
                ".TABLE_PREFIX."scorm_1_2_item AS so,
                ".TABLE_PREFIX."rl_experiments AS ex,
                ".TABLE_PREFIX."rl_reservations AS rs,
                ".TABLE_PREFIX."rl_experiments_es AS exes
            WHERE
                so.org_id=ex.package_org_id
            AND
                ex.experiment_id=exes.experiment_id            
            AND
                exes.experiments_es_id=rs.experiments_es_id
            AND
                rs.reservation_id=$resid
            AND
                so.scormtype='sco'";

	$result = mysql_query($sql, $db);
	$q_row  = mysql_fetch_assoc($result);    
	$pkg    = $q_row['package_id'];
    $shref  = $q_row['href'];
    $sidx   = $q_row['idx'];
    $sorg   = $q_row['org_id'];
    $sitemid= $q_row['item_id'];

    $sql = "SELECT	c.item_id,
			c.rvalue
		FROM  	".TABLE_PREFIX."cmi c,
			".TABLE_PREFIX."scorm_1_2_item i,
			".TABLE_PREFIX."scorm_1_2_org  o
		WHERE 	o.item_id    = $sitemid
		AND	i.org_id    = o.org_id
		AND	i.item_id   = c.item_id
		AND	c.member_id = $_SESSION[member_id]
		AND	c.lvalue    = 'cmi.core.lesson_status'";

    $result = mysql_query($sql, $db);

    // check if the org_id belongs to current course
	$sql = "SELECT course_id FROM ".TABLE_PREFIX."packages WHERE package_id = '".$pkg."'";
	$result = mysql_query($sql, $db);
	$row  = mysql_fetch_assoc($result);
	if ($row["course_id"] <> $_SESSION['course_id'])
	{
		$msg->addError('ACCESS_DENIED');

		$_pages['tools/packages/scorm-1.2/view.php']['title_var'] = _AT('scorm_packages');
		require (AT_INCLUDE_PATH.'header.inc.php');
		$msg->printAll();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<script src="prototype.js" language="JavaScript" type="text/javascript"></script>
<div id="rte">
<applet code="ATutorApiAdapterApplet"
id="RTE" name="RTE" mayscript="true"
codebase="tools/packages/scorm-1.2"
archive="java/ATutorApiAdapterApplet.jar,java/PfPLMS-API-adapter-core.jar,java/gnu.jar"
width="0" height="0" >
<param name="student_id"   value="<?php echo $_SESSION['member_id']?>" />
<param name="student_name" value="<?php echo $student_name?>" />
<param name="verbose" value="1" />
<?php	if ($prefs['show_rte_communication'] == 1) {
		echo '<param name="verbose" value="1" />' . "\n";
	}    
?>
</applet>
</div>
<script language="Javascript">

function getObj (o) {
	if(document.getElementById) return document.getElementById(o);
	if(document.all) return document.all[o];
}

scHREF = '<?php echo AT_PACKAGE_URL_BASE . $_SESSION['course_id'] .'/' . $pkg .'/'. $shref;  ?>';
scID   = '<?php echo $sitemid ?>';
scType = 'sco';

var isRunning   = false;
var isLaunching = false;
var initstat    = '';

var auto_advance = <?php
	echo ($prefs['auto_advance'] == 1 ?'true':'false');
?>;
var show_comm = <?php
       	echo ($prefs['show_rte_communication'] == 1?'true':'false');
?>;



function saveExperimentReport(){
    
}

function LMSInitialize (s) {
	rv = window.document.RTE.LMSInitialize (s);
	if (rv != 'true') return rv;

	isRunning   = true;
	isLaunching = false;

	initstat = window.document.RTE.ATutorGetValue (
		'cmi.core.lesson_status'
	);

    if (initstat == 'completed' ||
        initstat == 'passed'    ||
        initstat == 'browsed') {
        window.location = '<?php echo $_base_href; ?>mods/remotelab/reports_student.php';
    }
	return rv;
}

function LMSFinish (s) {
	
    var stat = window.document.RTE.ATutorGetValue ('cmi.core.lesson_status');
	if (stat == 'not attempted') stat = 'not-attempted';

	rv = window.document.RTE.LMSFinish (s);
	if (rv == 'true') {
		isRunning = false;
	}
	if(rv){
        window.location = '<?php echo $_base_href; ?>mods/remotelab/reports_student.php';
    }
    return rv;
}

function LMSSetValue (l, r) {
    resizeCaller();
	return window.document.RTE.LMSSetValue (l, r);
    
}

function LMSGetValue (l) {
	return window.document.RTE.LMSGetValue (l);
}

function LMSGetLastError () {
	return window.document.RTE.LMSGetLastError ();
}

function LMSGetErrorString (s) {
	return window.document.RTE.LMSGetErrorString (s);
}

function LMSGetDiagnostic (s) {
	return window.document.RTE.LMSGetDiagnostic (s);
}

function LMSCommit (s) {
	window.document.RTE.LMSCommit (s);
    saveExperimentReport();
}

this.API = this;
window.document.RTE.ATutorPrepare(scID);
</script>
<iframe id="expframe" src="" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; width:100%; display:none"></iframe>
<script type="text/javascript">

/***********************************************
* IFrame SSI script II- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************/

//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
var iframeids=["expframe"]

//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="yes"

var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

function resizeCaller() {
var dyniframe=new Array()
for (i=0; i<iframeids.length; i++){
if (document.getElementById)
resizeIframe(iframeids[i])
//reveal iframe for lower end browsers? (see var above):
if ((document.all || document.getElementById) && iframehide=="no"){
var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
tempobj.style.display="block"
}
}
}

function resizeIframe(frameid){
var currentfr=document.getElementById(frameid)
if (currentfr && !window.opera){
currentfr.style.display="block"
if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
currentfr.height = currentfr.Document.body.scrollHeight;
if (currentfr.addEventListener)
currentfr.addEventListener("load", readjustIframe, false)
else if (currentfr.attachEvent){
currentfr.detachEvent("onload", readjustIframe) // Bug fix line
currentfr.attachEvent("onload", readjustIframe)
}
}
}

function readjustIframe(loadevt) {
var crossevt=(window.event)? event : loadevt
var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
if (iframeroot)
resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
if (document.getElementById)
document.getElementById(iframeid).src=url
}

if (window.addEventListener)
window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent)
window.attachEvent("onload", resizeCaller)
else
window.onload=resizeCaller
loadintoIframe('expframe', '<?php echo AT_PACKAGE_URL_BASE . $_SESSION['course_id'] .'/' . $pkg .'/'. $shref;  ?>')
</script>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>