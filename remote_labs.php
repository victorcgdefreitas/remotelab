<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['add_remote_lab']) && isset($_POST['submit_new'])) {	
	$_POST['name'] 			= trim($_POST['name']);
	$_POST['gateway_url'] 	= trim($_POST['gateway_url']);
	$_POST['type'] 			= intval($_POST['type']);
	
	$missing_fields = array();

	if (!$_POST['name']) {
		$missing_fields[] = _AT('remote_lab_name');
	}
	
	if (!$_POST['gateway_url']) {
		$missing_fields[] = _AT('gateway_url');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_new'])) {
		
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['gateway_url']   = $addslashes($_POST['gateway_url']);
		$_POST['type']  = intval($_POST['type']);

		//The following checks if title length exceed 100, defined by DB structure
		$_POST['name'] = validate_length($_POST['name'], 100);

		$sql	= "INSERT INTO ".TABLE_PREFIX."rl_remote_labs (gateway_url, name, time, type, course_id) VALUES ('$_POST[gateway_url]', '$_POST[name]', NOW(), '$_POST[type]', '$_SESSION[course_id]')";
		mysql_query($sql, $db);		
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;	
		
	}
}

if (isset($_POST['save_remote_lab']) && isset($_POST['submit_save'])) {	
	$_POST['remote_lab_id'] = intval($_POST['remote_lab_id']);
	$_POST['name'] 			= trim($_POST['name']);
	$_POST['gateway_url'] 	= trim($_POST['gateway_url']);
	$_POST['type'] 			= intval($_POST['type']);
	
	$missing_fields = array();

	if (!$_POST['remote_lab_id']) {
		$missing_fields[] = _AT('remote_lab_id');
	}
	
	if (!$_POST['name']) {
		$missing_fields[] = _AT('remote_lab_name');
	}
	
	if (!$_POST['gateway_url']) {
		$missing_fields[] = _AT('gateway_url');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_save'])) {
		
		$_POST['remote_lab_id']  = intval($_POST['remote_lab_id']);
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['gateway_url']   = $addslashes($_POST['gateway_url']);
		$_POST['type']  = intval($_POST['type']);

		//The following checks if name length exceed 100, defined by DB structure
		$_POST['name'] = validate_length($_POST['name'], 100);

		$sql	= "UPDATE ".TABLE_PREFIX."rl_remote_labs SET gateway_url='$_POST[gateway_url]', name='$_POST[name]', type='$_POST[type]' WHERE remote_lab_id='$_POST[remote_lab_id]'";
		mysql_query($sql, $db);		
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;
		
	}
}

if(isset($_POST['edit']) && isset($_POST['id'])){
	$lab_id = $_POST['id'][0];
	$sql	= "SELECT *  FROM ".TABLE_PREFIX."rl_remote_labs  WHERE course_id=$_SESSION[course_id] AND remote_lab_id=$lab_id";
	$result	= mysql_query($sql, $db);	
	$row_edit = mysql_fetch_assoc($result);	
}

if (isset($_POST['submit_yes'])) {
	if (isset($_POST['listoflabs']))  {
		$list_of_labs = explode(',', $_POST['listoflabs']);
		$list_of_labs_in = implode('\',\'', $list_of_labs);
		$list_of_labs_in = "'" . $list_of_labs_in . "'";
		$sql = "DELETE FROM ".TABLE_PREFIX."rl_remote_labs WHERE remote_lab_id IN ($list_of_labs_in)";
		$result	= mysql_query($sql, $db);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;		
	}
}

if(isset($_POST['delete']) && isset($_POST['id'])){	
	if (isset($_POST['id'])) {		
		$list_of_labs	= implode(',', $_POST['id']);
		$list_of_labs_in = implode('\',\'', $_POST['id']);
		$list_of_labs_in = "'" . $list_of_labs_in . "'";			
		$hidden_vars['listoflabs'] = $list_of_labs;
		
		$sql = "SELECT name FROM ".TABLE_PREFIX."rl_experiment_sets WHERE remote_lab_id IN ($list_of_labs_in)";
		
		$result	= mysql_query($sql, $db);
		$num_experiment_sets = mysql_num_rows($result);
		if($num_experiment_sets){
			$msg->addError('LABS_HAVE_EXPERIMENT_SETS');
			header("Location:" . $_SERVER['PHP_SELF']);		
			exit;
		}
		
		
		$sql = "SELECT name FROM ".TABLE_PREFIX."rl_remote_labs WHERE remote_lab_id IN ($list_of_labs_in)";
		
		$result	= mysql_query($sql, $db);
		
		while($lab=mysql_fetch_assoc($result)) {
			$lab_list_to_print .= '<li>'.$lab['name'].'</li>';
		}
		
		$msg->addConfirm(array('LAB_DELETE', $lab_list_to_print), $hidden_vars);
		
		require (AT_INCLUDE_PATH.'header.inc.php');
		
		$msg->printConfirm();
	
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');
/* get a list of all the experiment sets we have, and links to create, edit, delete, preview */

$sql	=   "SELECT
                *
            FROM
                ".TABLE_PREFIX."rl_remote_labs
            WHERE
                course_id=$_SESSION[course_id]
            ORDER BY
                remote_lab_id ASC,
                name ASC";
$result	= mysql_query($sql, $db);
$num_remote_labs = mysql_num_rows($result);

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form0">
<input type="hidden" name="add_remote_lab" value="true" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><a href="mods/remotelab/remote_labs.php" onclick="javascript:toggleform('new'); return false;" style="font-family: Helevetica, Arial, sans-serif;" onmouseover="this.style.cursor='pointer'"><?php echo _AT('remote_lab_new'); ?></a></h3>
	</div>
    <div id="new" style="display:none;">
        <div class="row">
            <label for="name"><?php echo _AT('remote_lab_name'); ?></label><br />
            <input type="text" name="name" id="name" size="70" />
        </div>
        <div class="row">
            <label for="gateway_url"><?php echo _AT('gateway_url'); ?></label><br />
            <input type="text" name="gateway_url" id="gateway_url" size="70"  />
        </div>
        <div class="row">
            <label for="type"><?php echo _AT('lab_type'); ?></label><br />
            <select name="type" id="type">
            	<option value="1"><?php echo _AT('remote_laboratory'); ?></option>
                <option value="2"><?php echo _AT('virtual_laboratory'); ?></option>
            </select>
        </div>
    
        <div class="row buttons">
            <input type="submit" name="submit_new" value="<?php echo _AT('add'); ?>" />
        </div>
    </div>	
</div>
</form>

<?php if(isset($_POST['edit']) && isset($_POST['id'])): ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form2">
<input type="hidden" name="save_remote_lab" value="true" />
<input type="hidden" name="remote_lab_id" value="<?php echo $row_edit['remote_lab_id']; ?>" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><?php echo _AT('remote_lab_update'); ?></h3>
	</div>
    <div id="update">
        <div class="row">
            <label for="name"><?php echo _AT('remote_lab_name'); ?></label><br />
            <input type="text" name="name" id="name" size="70" value="<?php echo $row_edit['name']; ?>"  />
        </div>
        <div class="row">
            <label for="gateway_url"><?php echo _AT('gateway_url'); ?></label><br />
            <input type="text" name="gateway_url" id="gateway_url" size="70" value="<?php echo $row_edit['gateway_url']; ?>" />
        </div>
        <div class="row">
            <label for="type"><?php echo _AT('lab_type'); ?></label><br />
            <select name="type" id="type">
            	<option value="1" <?php  if($row_edit['type'] == 1)echo "selected='selected'"; ?> ><?php echo _AT('remote_laboratory'); ?></option>
                <option value="2" <?php  if($row_edit['type'] == 2)echo "selected='selected'"; ?>><?php echo _AT('virtual_laboratory'); ?></option>
            </select>
        </div>
    
        <div class="row buttons">
            <input type="submit" name="submit_save" value="<?php echo _AT('save'); ?>" />
        </div>
    </div>	
</div>
</form>

<?php endif; ?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col"><input type="checkbox" value="select/unselect all" id="all" title="select/unselect all" name="selectall" onclick="CheckAll();" /></th>
	<th scope="col"><?php echo _AT('lab_name'); ?></th>
	<th scope="col"><?php echo _AT('lab_type'); ?></th>
    <th scope="col"><?php echo _AT('date'); ?></th>
</tr>
</thead>

<?php if ($num_remote_labs): ?>
	<tfoot>
	<tr>	
		<td colspan="7" style="padding-left:38px;">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />			
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			
		</td>
	</tr>
	</tfoot>
	<tbody>
	<?php while ($row = mysql_fetch_assoc($result)) : ?>
		<tr onmousedown="document.form.rl<?php echo $row['remote_lab_id']; ?>.checked = !document.form.rl<?php echo $row['remote_lab_id']; ?>.checked; togglerowhighlight(this, 'rl<?php echo $row['remote_lab_id']; ?>');">
			<td width="10"><input type="checkbox" name="id[]" value="<?php echo $row['remote_lab_id']; ?>" id="rl<?php echo $row['remote_lab_id']; ?>"  onmouseup="this.checked=!this.checked"/></td>
			<td><?php echo $row['name']; ?></td>
            <td><?php if($row['type'] == 1){ echo _AT('remote_laboratory'); }else{ echo _AT('virtual_laboratory'); } ?></td>
			<td><?php echo AT_date(_AT('filemanager_date_format'), $row['time'], AT_DATE_MYSQL_DATETIME); ?></td>		
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="7"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<script language="JavaScript" type="text/javascript">
//<!--

function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.form.selectall.checked;
			togglerowhighlight(document.getElementById(e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}

var selected;
function rowselect(obj) {
	obj.className = 'selected';
	if (selected && selected != obj.id)
		document.getElementById(selected).className = '';
	selected = obj.id;
}
function rowselectbox(obj, checked, handler) {
	var functionDemo = new Function(handler + ";");
	functionDemo();

	if (checked)
		obj.className = 'selected';
	else
		obj.className = '';
}

//-->
</script>
<script language="javascript" type="text/javascript">
//<!--


function hideform(id) {
	document.getElementById(id).style.display='none';
}

function toggleform(id) {
	if (document.getElementById(id).style.display == "none") {
		//show
		document.getElementById(id).style.display='';	

		if (id == "new") {
		
			document.form0.name.focus();
		}

	} else {
		//hide
		document.getElementById(id).style.display='none';
	}
}
//-->
</script>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>