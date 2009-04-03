<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['add_experiment_set']) && isset($_POST['submit_new'])) {	
	$_POST['name'] 			= trim($_POST['name']);
	$_POST['set_code'] 	= trim($_POST['set_code']);
	$_POST['remote_lab_id'] = intval($_POST['remote_lab_id']);
	
	$missing_fields = array();

	if (!$_POST['set_code']) {
		$missing_fields[] = _AT('set_code');
	}
	
	if (!$_POST['remote_lab_id']) {
		$missing_fields[] = _AT('remote_lab_id');
	}
	
	if (!$_POST['name']) {
		$missing_fields[] = _AT('experiment_set_name');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_new'])) {
		
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['remote_lab_id']   = $addslashes($_POST['remote_lab_id']);
		$_POST['set_code']  = $addslashes($_POST['set_code']);

		//The following checks if title length exceed 100, defined by DB structure
		$_POST['name'] = validate_length($_POST['name'], 100);

		$sql	= "INSERT INTO ".TABLE_PREFIX."rl_experiment_sets (remote_lab_id, name, set_code) VALUES ('$_POST[remote_lab_id]', '$_POST[name]', '$_POST[set_code]')";
		mysql_query($sql, $db);		
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;	
		
	}
}

if (isset($_POST['save_experiment_set']) && isset($_POST['submit_save'])) {	
	$_POST['experiment_set_id'] = intval($_POST['experiment_set_id']);
	$_POST['name'] 			= trim($_POST['name']);
	$_POST['set_code'] 	= trim($_POST['set_code']);
	$_POST['remote_lab_id'] = intval($_POST['remote_lab_id']);
	
	$missing_fields = array();

	if (!$_POST['set_code']) {
		$missing_fields[] = _AT('set_code');
	}
	
	if (!$_POST['remote_lab_id']) {
		$missing_fields[] = _AT('remote_lab_id');
	}
	
	if (!$_POST['name']) {
		$missing_fields[] = _AT('experiment_set_name');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_save'])) {
		
		$_POST['experiment_set_id']  = intval($_POST['experiment_set_id']);
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['set_code'] = $addslashes($_POST['set_code']);			
		$_POST['remote_lab_id']  = intval($_POST['remote_lab_id']);

		//The following checks if name length exceed 100, defined by DB structure
		$_POST['name'] = validate_length($_POST['name'], 100);

		$sql	= "UPDATE ".TABLE_PREFIX."rl_experiment_sets SET remote_lab_id='$_POST[remote_lab_id]', name='$_POST[name]', set_code='$_POST[set_code]' WHERE experiment_set_id='$_POST[experiment_set_id]'";
		mysql_query($sql, $db);		
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;
		
	}
}

if(isset($_POST['edit']) && isset($_POST['id'])){
	$set_id = $_POST['id'][0];
	$sql	= "SELECT *  FROM ".TABLE_PREFIX."rl_experiment_sets WHERE experiment_set_id=$set_id";
	$result	= mysql_query($sql, $db);	
	$row_edit = mysql_fetch_assoc($result);	
}

if (isset($_POST['submit_yes'])) {
	if (isset($_POST['listofsets']))  {
		$list_of_sets = explode(',', $_POST['listofsets']);
		$list_of_sets_in = implode('\',\'', $list_of_sets);
		$list_of_sets_in = "'" . $list_of_sets_in . "'";
		$sql = "DELETE FROM ".TABLE_PREFIX."rl_experiment_sets WHERE experiment_set_id IN ($list_of_sets_in)";
		$result	= mysql_query($sql, $db);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;		
	}
}

if(isset($_POST['delete']) && isset($_POST['id'])){	
	if (isset($_POST['id'])) {
		$list_of_sets = implode(',', $_POST['id']);
		$list_of_sets_in = implode('\',\'', $_POST['id']);
		$list_of_sets_in = "'" . $list_of_sets_in . "'";		
		$hidden_vars['listofsets'] = $list_of_sets;
		
        $sql =  "SELECT   
                    *
                FROM
                    ".TABLE_PREFIX."rl_experiments_es
                WHERE
                    experiment_set_id IN ($list_of_sets_in)";
        
        $result = mysql_query($sql, $db);
        $result_num = mysql_num_rows($result);
        
        if($result_num == 0){
            $sql = "SELECT name FROM ".TABLE_PREFIX."rl_experiment_sets WHERE experiment_set_id IN ($list_of_sets_in)";

            $result	= mysql_query($sql, $db);

            while($set=mysql_fetch_assoc($result)) {
                $set_list_to_print .= '<li>'.$set['name'].'</li>';
            }

            $msg->addConfirm(array('SET_DELETE', $set_list_to_print), $hidden_vars);

            require (AT_INCLUDE_PATH.'header.inc.php');

            $msg->printConfirm();

            require(AT_INCLUDE_PATH.'footer.inc.php');
            exit;
        }else{
            $msg->addError('EXPERIMENT_SETS_USING');
            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

/* get a list of all the experiment sets we have, and links to  edit, delete */

$sql	=   "SELECT
                * ,
                es.name AS ename,
                rl.name AS rname
            FROM
                ".TABLE_PREFIX."rl_experiment_sets AS es,
                ".TABLE_PREFIX."rl_remote_labs AS rl
            WHERE
                rl.course_id=$_SESSION[course_id]
            AND
                rl.remote_lab_id=es.remote_lab_id
            ORDER BY
                rl.remote_lab_id ASC,
                es.name ASC";
$result	= mysql_query($sql, $db);
$num_experiment_sets = mysql_num_rows($result);

$sql	= "SELECT *  FROM ".TABLE_PREFIX."rl_remote_labs  WHERE course_id=$_SESSION[course_id] ORDER BY remote_lab_id DESC";
$result_remote_labs	= mysql_query($sql, $db);
$num_remote_labs = mysql_num_rows($result_remote_labs);

while($lab = mysql_fetch_assoc($result_remote_labs)){
	$labs[] = $lab;
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form0">
<input type="hidden" name="add_experiment_set" value="true" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><a href="mods/remotelab/experiment_sets.php" onclick="javascript:toggleform('new'); return false;" style="font-family: Helevetica, Arial, sans-serif;" onmouseover="this.style.cursor='pointer'"><?php echo _AT('experiment_set_new'); ?></a></h3>
	</div>
    <div id="new" style="display:none;">
        <div class="row">
            <label for="name"><?php echo _AT('experiment_set_name'); ?></label><br />
            <input type="text" name="name" id="name" size="70" />
        </div>
        <div class="row">
            <label for="set_code"><?php echo _AT('set_code'); ?></label><br />
            <input type="text" name="set_code" id="set_code" size="70"  />
        </div>
        <div class="row">
            <label for="remote_laboratory"><?php echo _AT('laboratory'); ?></label><br />
            <select name="remote_lab_id" id="remote_lab_id">
            <?php foreach($labs as $laboratory): ?>
            	<option value="<?php echo $laboratory['remote_lab_id']; ?>">
				<?php echo $laboratory['name']; ?> (<?php if($laboratory['type']==1){ echo _AT("remote_laboratory"); }else{ echo _AT("virtual_laboratory"); }  ?>)
                </option>
            <?php endforeach; ?>                
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
<input type="hidden" name="save_experiment_set" value="true" />
<input type="hidden" name="experiment_set_id" value="<?php echo $row_edit['experiment_set_id']; ?>" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><?php echo _AT('experiment_set_update'); ?></h3>
	</div>
    <div id="update">
        <div class="row">
            <label for="name"><?php echo _AT('experiment_set_name'); ?></label><br />
            <input type="text" name="name" id="name" size="70" value="<?php echo $row_edit['name']; ?>"/>
        </div>
        <div class="row">
            <label for="set_code"><?php echo _AT('set_code'); ?></label><br />
            <input type="text" name="set_code" id="set_code" size="70" value="<?php echo $row_edit['set_code']; ?>" />
        </div>
        <div class="row">
            <label for="remote_laboratory"><?php echo _AT('laboratory'); ?></label><br />
            <select name="remote_lab_id" id="remote_lab_id">           	
            <?php foreach($labs as $laboratory): ?>
            	<option value="<?php echo $laboratory['remote_lab_id']; ?>" <?php if($laboratory['remote_lab_id']==$row_edit['remote_lab_id']) echo "selected='selected'"; ?> >
				<?php echo $laboratory['name']; ?> (<?php if($laboratory['type']==1){ echo _AT("remote_laboratory"); }else{ echo _AT("virtual_laboratory"); }  ?>)
                </option>
            <?php endforeach; ?>                              
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
	<th scope="col"><?php echo _AT('experiment_set'); ?></th>
	<th scope="col"><?php echo _AT('laboratory'); ?></th>
    <th scope="col"><?php echo _AT('set_code'); ?></th>
</tr>
</thead>

<?php if ($num_experiment_sets): ?>
	<tfoot>
	<tr>	
		<td colspan="4" style="padding-left:38px;">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />			
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			
		</td>
	</tr>
	</tfoot>
	<tbody>

	<?php while ($row = mysql_fetch_assoc($result)) : ?>
		<tr onmousedown="document.form.es<?php echo $row['experiment_set_id']; ?>.checked = !document.form.es<?php echo $row['experiment_set_id']; ?>.checked; togglerowhighlight(this, 'es<?php echo $row['experiment_set_id']; ?>');">
			<td width="10"><input type="checkbox" name="id[]" value="<?php echo $row['experiment_set_id']; ?>" id="es<?php echo $row['experiment_set_id']; ?>" onmouseup="this.checked=!this.checked"/></td>
			<td><?php echo $row['ename']; ?></td>
			<td><?php echo $row['rname']; ?> (<?php if($row['type']==1){ echo _AT("remote_laboratory"); }else{ echo _AT("virtual_laboratory"); }  ?>)</td>
            <td><?php echo $row['set_code']; ?></td>		
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
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