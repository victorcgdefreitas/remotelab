<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_GET['expid'])){
    $expid = $_GET['expid'];
}

if(isset($_POST['expid'])){
    $expid = $_POST['expid'];    
}

if (isset($_POST['save_interactions']) && isset($_POST['submit_save']) && isset($_POST['id'])) {
	$rlcmi      = $_POST['id'];
    $label      = $_POST['label'];
    $varname    = $_POST['varname'];
    $type       = $_POST['type'];
    
	$missing_fields = array();

    if (!$_POST['id'] ) {
		$missing_fields[] = _AT('id');
	}

    foreach($rlcmi as $i => $value){

        if (!$label[$i]) {
            $missing_fields[] = _AT('label');
        }
        if (!$varname[$i]) {
            $missing_fields[] = _AT('varname');
        }
        if (!$type[$i]) {
            $missing_fields[] = _AT('type');
        }

    }	

    if (!$_GET['expid'] ) {
		$missing_fields[] = _AT('expid');
	}

    if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

    $sql    =   "SELECT
                        experiment_id
                    FROM
                        ".TABLE_PREFIX."rl_experiments
                    WHERE
                        experiment_id=$expid
                    AND
                        course_id=$_SESSION[course_id]";

    $result_exps = mysql_query($sql, $db);

    while(mysql_num_rows($result_exps) == 0){
        $msg->addError(_AT('OTHERS_EXPERIMENT'));
    }  

	if (!$msg->containsErrors() && isset($_POST['submit_save'])) {

        $sql    =   "DELETE FROM
                        ".TABLE_PREFIX."rl_cmi                       
                    WHERE
                        experiment_id=$expid";
        
        mysql_query($sql, $db);       
               
        foreach($rlcmi as $i => $value){
            
            $lvalue     = $_POST['lvalue'];
            $rvalue     = $_POST['rvalue'];
            $label      = $_POST['label'];
            $varname    = $_POST['varname'];
            $type       = $_POST['type'];

            $sql	=   "INSERT INTO
                        ".TABLE_PREFIX."rl_cmi(
                        experiment_id,
                        lvalue,
                        rvalue,
                        label,
                        varname,
                        type)
                    VALUES (
                        '$expid',
                        '$lvalue[$i]',
                        '$rvalue[$i]',
                        '$label[$i]',
                        '$varname[$i]',
                        '$type[$i]')";
            mysql_query($sql, $db);
        }        
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        //header("Location:" . $_SERVER['HTTP_REFERER'] . "?" . $_SERVER['QUERY_STRING'] );
        header("Location: experiments.php");
        exit;
    }else{
        $msg->addError('ACTION_FAILED');
        header("Location:" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] );
        exit;
    }
}

require (AT_INCLUDE_PATH.'header.inc.php');

/* get a list of all the experiment's interactions we have, and links to  update */

$sql =  "SELECT
            cm.rvalue
        FROM
            ".TABLE_PREFIX."rl_experiments AS ex,
            ".TABLE_PREFIX."cmi AS cm
        WHERE
            ex.package_item_id=cm.item_id
        AND
            ex.experiment_id=$expid
        AND
            cm.lvalue = 'cmi.interactions._count'
        GROUP BY
            cm.lvalue";

$result_cmi_count = mysql_query($sql, $db);
$interactions_count = mysql_fetch_assoc($result_cmi_count);

for($i=0;$i<$interactions_count[rvalue];$i++){
    $interactions[] = "cmi.interactions." . $i . ".id";
}

$interactions_in = "'" . implode("','",$interactions) ."'";

$sql =  "SELECT
            cm.rvalue,
            cm.lvalue,
            cm.cmi_id
        FROM
            ".TABLE_PREFIX."rl_experiments AS ex,
            ".TABLE_PREFIX."cmi AS cm
        WHERE
            cm.lvalue
        IN
            ($interactions_in)
        AND
            ex.package_item_id=cm.item_id
        AND
            ex.experiment_id=$expid            
        GROUP BY
            cm.lvalue";

$result_cmi = mysql_query($sql, $db);
$num_cmi    = mysql_num_rows($result_cmi);

?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>" name="form">
<input type="hidden" name="save_interactions" value="true" />
<input type="hidden" name="expid" value="<?php echo $expid; ?>" />
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col"><input type="checkbox" value="select/unselect all" id="all" title="select/unselect all" name="selectall" onclick="CheckAll();" /></th>
	<th scope="col"><?php echo _AT('experiment_sco_interactions'); ?></th>    
    <th scope="col"><?php echo _AT('experiment_label'); ?></th>
    <th scope="col"><?php echo _AT('experiment_varname'); ?></th>
    <th scope="col"><?php echo _AT('experiment_type'); ?></th>
</tr>
</thead>

<?php if ($num_cmi>0): ?>
	<tfoot>
	<tr>
		<td colspan="5" style="padding-left:38px;">
			<input type="submit" name="submit_save" value="<?php echo _AT('update'); ?>" />

		</td>
	</tr>
	</tfoot>
	<tbody>
    <?php $i=1; ?>
	<?php while ($row = mysql_fetch_assoc($result_cmi)) : ?>
    <?php $sql =    "SELECT
                        *
                    FROM
                        ".TABLE_PREFIX."rl_cmi
                    WHERE
                        lvalue='$row[lvalue]'
                    AND
                        experiment_id=$expid";
         
          $result_rlcmi = mysql_query($sql, $db);
          if($result_rlcmi){
            $row_rlcmi = mysql_fetch_assoc($result_rlcmi);
          }
    ?>
		<tr>
			<td onmousedown="document.form.excmi<?php echo $i; ?>.checked = !document.form.excmi<?php echo $i; ?>.checked; togglerowhighlight(this, 'excmi<?php echo $i; ?>'); setRow('<?php echo $i; ?>');" width="10">
                <input type="checkbox" name="id[<?php echo $i; ?>]" value="1" id="excmi<?php echo $i; ?>" onmouseup="this.checked=!this.checked" <?php if($row_rlcmi['lvalue']==$row['lvalue']) echo "checked=\"checked\""; ?> />
            </td>
			<td>
                <input type="hidden" name="lvalue[<?php echo $i; ?>]" value="<?php echo $row['lvalue']; ?>" />
                <input type="hidden" name="rvalue[<?php echo $i; ?>]" value="<?php echo $row['rvalue']; ?>" />
                <?php echo $row['rvalue']; ?>
                </td>
            <td><input name="label[<?php echo $i; ?>]" type="text" id="label<?php echo $i; ?>" size="30" value="<?php echo $row_rlcmi['label']; ?>" /></td>
            <td><input name="varname[<?php echo $i; ?>]" type="text" id="varname<?php echo $i; ?>"  size="30" value="<?php echo $row_rlcmi['varname']; ?>" /></td>
            <td>
                <input name="type[<?php echo $i; ?>]" type="radio" id="typea<?php echo $i; ?>"   value="1" <?php if($row_rlcmi['type']==1) echo "checked=\"checked\""; ?> /><?php echo _AT('in'); ?>
                <input name="type[<?php echo $i; ?>]" type="radio" id="typeb<?php echo $i; ?>"  value="2" <?php if($row_rlcmi['type']==2) echo "checked=\"checked\""; ?> /><?php echo _AT('out'); ?>
            </td>
		</tr>
    <?php $i++; ?>
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
		if ((e.type=='checkbox')) {
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

function setRow(i){
    if(document.getElementById('excmi' + i).checked != "true"){
        document.getElementById('label' + i).value = "";
        document.getElementById('varname' + i).value = "";
        document.getElementById('typea' + i).checked = "";
        document.getElementById('typeb' + i).checked = "";
    }
}

//-->
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>