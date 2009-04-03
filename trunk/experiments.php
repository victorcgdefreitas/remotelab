<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['add_experiment']) && isset($_POST['submit_new'])) {	
	$package_data                   = explode(',', $_POST['package_item_id']);
    $_POST['title'] 				= trim($_POST['title']);
    $package_id                     = intval($package_data[0]);
	$package_item_id                = intval($package_data[1]);
    $package_org_id                 = intval($package_data[2]); 
    $_POST['experiment_code'] 		= trim($_POST['experiment_code']);
//	$_POST['maxallowedtimes'] 		= intval($_POST['maxallowedtimes']);
	$_POST['visible'] 				= intval($_POST['visible']);
	$_POST['reservation_duration'] 	= intval($_POST['reservation_duration']);
	$_POST['start'] 				= trim($_POST['start']);
	$_POST['end'] 					= trim($_POST['end']);
	
	$missing_fields = array();

	if (!$_POST['title'] ) {
		$missing_fields[] = _AT('title');
	}

    if (!$_POST['experiment_code']) {
		$missing_fields[] = _AT('experiment_code');
	}

	if (!$_POST['package_item_id']) {
		$missing_fields[] = _AT('package_item_id');
	}

    if (!$_POST['sets']) {
		$missing_fields[] = _AT('experiment_sets');
	}

	if (!$_POST['maxallowedtimes']) {
		$missing_fields[] = _AT('max_allowed_times');
	}
		
	if (!$_POST['visible']) {
		$missing_fields[] = _AT('visible');
	}
	
	if (!$_POST['reservation_duration']) {
		$missing_fields[] = _AT('reservation_duration');
	}
	
	if (!$_POST['start']) {
		$missing_fields[] = _AT('start');
	}	
	
	if (!$_POST['end']) {
		$missing_fields[] = _AT('end');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_new'])) {
		
		$_POST['title'] 				= $addslashes($_POST['title']);
		$_POST['start'] 				= $addslashes($_POST['start']);
		$_POST['end'] 					= $addslashes($_POST['end']);

		//The following checks if title length exceed 100, defined by DB structure
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql	=   "INSERT INTO
                        ".TABLE_PREFIX."rl_experiments(
                        title,
                        code,
                        package_id,
                        package_item_id,
                        package_org_id,
                        maxallowedtimes,
                        visible,
                        reservation_duration,
                        start,
                        end,
                        course_id)
                    VALUES (
                        '$_POST[title]',
                        '$_POST[experiment_code]',
                        '$package_id',
                        '$package_item_id',
                        '$package_org_id',
                        '$_POST[maxallowedtimes]',
                        '$_POST[visible]',
                        '$_POST[reservation_duration]',
                        '$_POST[start]',
                        '$_POST[end]',
                        '$_SESSION[course_id]')";

		$result_insert = mysql_query($sql, $db);
        if($result_insert){
            $exp_id = mysql_insert_id($db);
            $expsets = $_POST['sets'];
            foreach($expsets as $set){
                $sql	=   "INSERT INTO
                            ".TABLE_PREFIX."rl_experiments_es(
                            experiment_set_id,
                            experiment_id)
                        VALUES (
                            '$set',
                            '$exp_id')";

                mysql_query($sql, $db);
            }
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }else{
            $msg->addError('ACTION_FAILED');
            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }
	}
}

if (isset($_POST['save_experiment']) && isset($_POST['submit_save'])) {	
	$package_data                   = explode(',', $_POST['package_item_id']);
    $_POST['experiment_id'] 		= intval($_POST['experiment_id']);
	$_POST['title'] 				= trim($_POST['title']);
    $_POST['experiment_code'] 		= trim($_POST['experiment_code']);
    $package_id                     = intval($package_data[0]);
	$package_item_id                = intval($package_data[1]);
    $package_org_id                 = intval($package_data[2]);
	$_POST['maxallowedtimes'] 		= intval($_POST['maxallowedtimes']);
	$_POST['visible'] 				= intval($_POST['visible']);
	$_POST['reservation_duration'] 	= intval($_POST['reservation_duration']);
	$_POST['start'] 				= trim($_POST['start']);
	$_POST['end'] 					= trim($_POST['end']);
	
	$missing_fields = array();

	if (!$_POST['title'] ) {
		$missing_fields[] = _AT('title');
	}

    if (!$_POST['experiment_code']) {
		$missing_fields[] = _AT('experiment_code');
	}
    
	if (!$_POST['package_item_id']) {
		$missing_fields[] = _AT('package_item_id');
	}

    if (!$_POST['sets']) {
		$missing_fields[] = _AT('experiment_sets');
	}
    
	if (!$_POST['maxallowedtimes']) {
		$missing_fields[] = _AT('max_allowed_times');
	}
		
	if (!$_POST['visible']) {
		$missing_fields[] = _AT('visible');
	}
	
	if (!$_POST['reservation_duration']) {
		$missing_fields[] = _AT('reservation_duration');
	}
	
	if (!$_POST['start']) {
		$missing_fields[] = _AT('start');
	}	
	
	if (!$_POST['end']) {
		$missing_fields[] = _AT('end');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit_save'])) {

		$_POST['title'] 				= $addslashes($_POST['title']);
		$_POST['start'] 				= $addslashes($_POST['start']);
		$_POST['end'] 					= $addslashes($_POST['end']);

		//The following checks if name length exceed 100, defined by DB structure
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql	=   "UPDATE
                        ".TABLE_PREFIX."rl_experiments
                    SET
                        title='$_POST[title]',
                        code='$_POST[experiment_code]',
                        package_id='$package_id',
                        package_item_id='$package_item_id',
                        package_org_id='$package_org_id',
                        maxallowedtimes='$_POST[maxallowedtimes]',
                        visible='$_POST[visible]',
                        reservation_duration='$_POST[reservation_duration]',
                        start='$_POST[start]', end='$_POST[end]'
                    WHERE
                        experiment_id='$_POST[experiment_id]'";

		mysql_query($sql, $db);

        $exp_id = $_POST[experiment_id];

        $sql    =   "DELETE FROM
                        ".TABLE_PREFIX."rl_experiments_es
                    WHERE
                        experiment_id=$exp_id";
        mysql_query($sql, $db);

        $expsets = $_POST['sets'];

        foreach($expsets as $set){
            $sql	=   "INSERT INTO
                        ".TABLE_PREFIX."rl_experiments_es(
                        experiment_set_id,
                        experiment_id)
                    VALUES (
                        '$set',
                        '$exp_id')";

            mysql_query($sql, $db);
        }
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;
		
	}
}

if(isset($_POST['edit']) && isset($_POST['id'])){
	$exp_id = $_POST['id'][0];
	$sql	=   "SELECT
                    *
                FROM
                    ".TABLE_PREFIX."rl_experiments
                WHERE
                    experiment_id=$exp_id";
	$result	= mysql_query($sql, $db);	
	$row_edit = mysql_fetch_assoc($result);

    $sql =  "SELECT
                experiment_set_id
            FROM
                ".TABLE_PREFIX."rl_experiments_es
            WHERE
                experiment_id=$exp_id";
                
    $result_exp_sets	= mysql_query($sql, $db);
    while($row = mysql_fetch_assoc($result_exp_sets)){
        $expsets[] = $row['experiment_set_id'];        
    }
}

if (isset($_POST['submit_yes'])) {
	if (isset($_POST['listofexps']))  {
		$list_of_exps = explode(',', $_POST['listofexps']);
		$list_of_exps_in = implode('\',\'', $list_of_exps);
		$list_of_exps_in = "'" . $list_of_exps_in . "'";

		$sql =  "DELETE FROM
                    ".TABLE_PREFIX."rl_experiments
                WHERE
                    experiment_id IN ($list_of_exps_in)";

		mysql_query($sql, $db);

        $sql =  "DELETE FROM
                    ".TABLE_PREFIX."rl_experiments_es
                WHERE
                    experiment_id IN ($list_of_exps_in)";

        mysql_query($sql, $db);

        $sql =  "DELETE FROM
                    ".TABLE_PREFIX."rl_cmi
                WHERE
                    experiment_id IN ($list_of_exps_in)";

        mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');	
		header("Location:" . $_SERVER['PHP_SELF']);
		exit;		
	}
}

if(isset($_POST['delete']) && isset($_POST['id'])){	
	if (isset($_POST['id'])) {
		$list_of_exps = implode(',', $_POST['id']);
		$list_of_exps_in = implode('\',\'', $_POST['id']);
		$list_of_exps_in = "'" . $list_of_exps_in . "'";		
		$hidden_vars['listofexps'] = $list_of_exps;
		
		$sql =  "SELECT
                    title
                FROM
                    ".TABLE_PREFIX."rl_experiments
                WHERE
                    experiment_id IN ($list_of_exps_in)";
		
		$result	= mysql_query($sql, $db);
		
		while($exp=mysql_fetch_assoc($result)) {
			$exp_list_to_print .= '<li>'.$exp['title'].'</li>';
		}
		
		$msg->addConfirm(array('SET_DELETE', $exp_list_to_print), $hidden_vars);
		
		require (AT_INCLUDE_PATH.'header.inc.php');
		
		$msg->printConfirm();
	
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

/* get a list of all the experiments we have, and links to  edit, delete */

$sql	=   "SELECT
                * 
            FROM
                ".TABLE_PREFIX."rl_experiments                
            WHERE
                course_id=$_SESSION[course_id]";

$result_experiments	= mysql_query($sql, $db);
$num_experiments = mysql_num_rows($result_experiments);

$sql	=   "SELECT
                pk.package_id AS package_id,
                si.org_id AS org_id,
                si.item_id AS item_id,
                si.title AS title
            FROM
                ".TABLE_PREFIX."packages AS pk,
                ".TABLE_PREFIX."scorm_1_2_item AS si,
                ".TABLE_PREFIX."scorm_1_2_org AS so
            WHERE
                pk.course_id=$_SESSION[course_id]
            AND
                pk.package_id=so.package_id
            AND
                so.org_id=si.org_id
            AND
                si.scormtype='sco'
            ORDER BY
                pk.package_id DESC";

$result_packages	= mysql_query($sql, $db);
$num_packages = mysql_num_rows($result_packages);

while($row = mysql_fetch_assoc($result_packages)){
	$packages[] = $row;
}

$sql    = "SELECT
                rl.name AS rlname,
                es.name AS esname,
                es.experiment_set_id AS esid
            FROM
                ".TABLE_PREFIX."rl_experiment_sets AS es,
                ".TABLE_PREFIX."rl_remote_labs AS rl
            WHERE
                course_id=$_SESSION[course_id]
            AND
                es.remote_lab_id=rl.remote_lab_id
            ORDER BY
                rl.remote_lab_id ASC,
                es.name ASC";

$result_experiment_sets	= mysql_query($sql, $db);

while($row = mysql_fetch_assoc($result_experiment_sets)){
	$sets[] = $row;
}

$sql =  "SELECT
            ex.experiment_id
        FROM
            ".TABLE_PREFIX."rl_experiments AS ex,
            ".TABLE_PREFIX."cmi AS cm        
        WHERE
            ex.package_item_id=cm.item_id
        GROUP BY
            ex.experiment_id";

$result_cmi = mysql_query($sql, $db);

while($row = mysql_fetch_assoc($result_cmi)){
	$cmi[] = $row['experiment_id'];
}

$sql =  "SELECT
            ex.experiment_id
        FROM
            ".TABLE_PREFIX."rl_experiments AS ex,
            ".TABLE_PREFIX."rl_cmi AS cm
        WHERE
            ex.experiment_id=cm.experiment_id
        GROUP BY
            ex.experiment_id";

$result_rlcmi = mysql_query($sql, $db);
if($result_rlcmi){
    while($row = mysql_fetch_assoc($result_rlcmi)){
        $rlcmi[] = $row['experiment_id'];
    }
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form0">
<input type="hidden" name="add_experiment" value="true" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><a href="mods/remotelab/experiments.php" onclick="javascript:toggleform('new'); return false;" style="font-family: Helevetica, Arial, sans-serif;" onmouseover="this.style.cursor='pointer'"><?php echo _AT('experiment_new'); ?></a></h3>
	</div>
    <div id="new" style="display:none;">
        <div class="row">
            <label for="title"><?php echo _AT('experiment_name'); ?></label><br />
            <input type="text" name="title" id="title" size="70" />
        </div>
        <div class="row">
            <label for="experiment_code"><?php echo _AT('experiment_code'); ?></label><br />
            <input type="text" name="experiment_code" id="experiment_code" size="70"  />
        </div>
        <div class="row">
            <label for="package_item_id"><?php echo _AT('package_item_id'); ?></label><br />
            <select name="package_item_id">
            <?php foreach($packages as $package): ?>
            	<option value="<?php echo $package['package_id']; ?>,<?php echo $package['item_id']; ?>,<?php echo $package['org_id']; ?>"><?php echo $package['title']; ?></option><br />
            <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <label for="experiment_sets"><?php echo _AT('experiment_sets'); ?></label><br />            
            <?php foreach($sets as $set): ?>                
            	<input  type="checkbox" name="sets[]" value="<?php echo $set['esid']; ?>"/><?php echo $set['esname']; ?> (<?php echo $set['rlname']; ?>)<br />                
            <?php endforeach; ?>          
        </div>
        <div class="row">
            <label for="maxallowedtimes"><?php echo _AT('maxallowedtimes'); ?></label><br />
            <input type="text" name="maxallowedtimes" id="maxallowedtimes" size="70" />
        </div>
        <div class="row">
            <label for="visible"><?php echo _AT('visible'); ?></label><br />
            <select name="visible" >
                <option value="1"><?php echo _AT('visible'); ?></option>
                <option value="2"><?php echo _AT('invisible'); ?></option>
            </select>
        </div>
         <div class="row">
            <label for="reservation_duration"><?php echo _AT('reservation_duration'); ?></label><br />
            <input type="text" name="reservation_duration" id="reservation_duration" size="70" />
        </div>
        <div class="row">
            <label for="start_date"><?php echo _AT('start_date'); ?></label><br />
            <input type="text" name="start" id="start_date" size="70" />
        </div>
        <div class="row">
            <label for="end_date"><?php echo _AT('end_date'); ?></label><br />
            <input type="text" name="end" id="end_date" size="70" />
        </div>
    
        <div class="row buttons">
            <input type="submit" name="submit_new" value="<?php echo _AT('add'); ?>" />
        </div>
    </div>	
</div>
</form>

<?php if(isset($_POST['edit']) && isset($_POST['id'])): ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form2">
<input type="hidden" name="save_experiment" value="true" />
<input type="hidden" name="experiment_id" value="<?php echo $row_edit['experiment_id']; ?>" />
<div class="input-form" style="width: 90%">
	<div class="row">
	<h3><?php echo _AT('experiment_update'); ?></h3>
	</div>
    <div id="update">
        <div class="row">
            <label for="title"><?php echo _AT('experiment_name'); ?></label><br />
            <input type="text" name="title" id="title" size="70" value="<?php echo $row_edit['title']; ?>" />
        </div>
        <div class="row">
            <label for="experiment_code"><?php echo _AT('experiment_code'); ?></label><br />
            <input type="text" name="experiment_code" id="experiment_code" size="70" value="<?php echo $row_edit['code']; ?>"  />
        </div>
        <div class="row">
            <label for="package_item_id"><?php echo _AT('package_item_id'); ?></label><br />
            <select name="package_item_id">
            <?php foreach($packages as $package): ?>
            	<option value="<?php echo $package['package_id']; ?>,<?php echo $package['item_id']; ?>,<?php echo $package['org_id']; ?>" <?php if($row_edit['package_item_id']==$package['item_id']) echo "selected='selected'"; ?>  ><?php echo $package['title']; ?></option><br />
            <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <label for="experiment_sets"><?php echo _AT('experiment_sets'); ?></label><br />
            <?php foreach($sets as $set): ?>
            <input  type="checkbox" name="sets[]" value="<?php echo $set['esid']; ?>" <?php if(is_array($expsets)): if(in_array($set['esid'], $expsets)) echo "checked='ckecked'"; endif; ?>/><?php echo $set['esname']; ?> (<?php echo $set['rlname']; ?>)<br />
            <?php endforeach; ?>
        </div>
        <div class="row">
            <label for="maxallowedtimes"><?php echo _AT('maxallowedtimes'); ?></label><br />
            <input type="text" name="maxallowedtimes" id="maxallowedtimes" size="70" value="<?php echo $row_edit['maxallowedtimes']; ?>" />
        </div>
        <div class="row">
            <label for="visible"><?php echo _AT('visible'); ?></label><br />
            <select name="visible" >
                <option value="1" <?php if($row_edit['visible']=='1') echo "selected='selected'"; ?>><?php echo _AT('visible'); ?></option>
                <option value="2" <?php if($row_edit['visible']=='2') echo "selected='selected'"; ?>><?php echo _AT('invisible'); ?></option>
            </select>
        </div>
         <div class="row">
            <label for="reservation_duration"><?php echo _AT('reservation_duration'); ?></label><br />
            <input type="text" name="reservation_duration" id="reservation_duration" size="70" value="<?php echo $row_edit['reservation_duration']; ?>" />
        </div>
        <div class="row">
            <label for="start_date"><?php echo _AT('start_date'); ?></label><br />
            <input type="text" name="start" id="start_date" size="70" value="<?php echo $row_edit['start']; ?>" />
        </div>
        <div class="row">
            <label for="end_date"><?php echo _AT('end_date'); ?></label><br />
            <input type="text" name="end" id="end_date" size="70" value="<?php echo $row_edit['end']; ?>" />
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
	<th scope="col"><?php echo _AT('experiment_name'); ?></th>
    <th scope="col"><?php echo _AT('experiment_code'); ?></th>
    <th scope="col"><?php echo _AT('experiment_sco_status'); ?></th>
    <th scope="col"><?php echo _AT('experiment_sco_variables'); ?></th>
</tr>
</thead>

<?php if ($num_experiments): ?>
	<tfoot>
	<tr>	
		<td colspan="5" style="padding-left:38px;">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />			
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />			
		</td>
	</tr>
	</tfoot>
	<tbody>

	<?php while ($row = mysql_fetch_assoc($result_experiments)) : ?>
		<tr onmousedown="document.form.ex<?php echo $row['experiment_id']; ?>.checked = !document.form.ex<?php echo $row['experiment_id']; ?>.checked; togglerowhighlight(this, 'ex<?php echo $row['experiment_id']; ?>');">
			<td width="10"><input type="checkbox" name="id[]" value="<?php echo $row['experiment_id']; ?>" id="ex<?php echo $row['experiment_id']; ?>" onmouseup="this.checked=!this.checked"/></td>
			<td><?php echo $row['title']; ?></td>
            <td><?php echo $row['code']; ?></td>
            <td><?php if(is_array($cmi)): ?>
                <?php if(in_array($row['experiment_id'], $cmi)): ?>
                <?php echo _AT('OK'); ?>
                <?php else: ?>
                <a href="tools/packages/scorm-1.2/view.php?org_id=<?php echo $row['package_org_id']; ?>"><?php echo _AT('experiment_sco_run_first_click'); ?></a>
                <?php endif; ?>
                <?php else: ?>
                <a href="tools/packages/scorm-1.2/view.php?org_id=<?php echo $row['package_org_id']; ?>"><?php echo _AT('experiment_sco_run_first_click'); ?></a>
                <?php endif; ?>
            </td>
            <td><?php if(is_array($rlcmi)): ?>
                    <?php if(in_array($row['experiment_id'], $rlcmi)): ?>
                    <a href="mods/remotelab/experiments_cmi.php?expid=<?php echo $row['experiment_id']; ?>"><?php echo _AT('edit'); ?></a>
                    <?php else: ?>
                    <a href="mods/remotelab/experiments_cmi.php?expid=<?php echo $row['experiment_id']; ?>"><?php echo _AT('experiment_sco_variable_register_click'); ?></a>

                    <?php endif; ?>
                <?php else: ?>
                <a href="mods/remotelab/experiments_cmi.php?expid=<?php echo $row['experiment_id']; ?>"><?php echo _AT('experiment_sco_variable_register_click'); ?></a>
               
                <?php endif; ?>
            </td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
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
		
			document.form0.title.focus();
		}

	} else {
		//hide
		document.getElementById(id).style.display='none';
	}
}

//-->
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>