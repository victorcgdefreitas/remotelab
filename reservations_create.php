<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

function addSecondsToTimestamp ($_timestamp, $_amount) {
    list($year, $month, $day, $hours, $minutes, $seconds) = preg_split('/[- :]/', $_timestamp);
    return date('Y-m-d H:i:s', mktime($hours, $minutes, $seconds + $_amount, $month, $day, $year));
}

if(isset($_POST['submit_reserve'])){
    $reserve_start  = $_POST['id'];
    $reserve_set    = $_POST['expset'];
    $expid          = $_POST['expid'];

    $missing_fields = array();

	if (!$_POST['id'] ) {
		$missing_fields[] = _AT('id');
	}

    if (!$_POST['expset'] ) {
		$missing_fields[] = _AT('expset');
	}

    if (!$_POST['expid'] ) {
		$missing_fields[] = _AT('expid');
	}

    if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));        
        header("Location: experiments_student.php");
        exit;
	}

    $sql =  "SELECT
                exes.experiments_es_id AS expes_id,
                ex.maxallowedtimes AS max
            FROM
                ".TABLE_PREFIX."rl_experiments_es AS exes,
                ".TABLE_PREFIX."rl_experiments AS ex
            WHERE
                exes.experiment_set_id=$reserve_set
            AND
                exes.experiment_id=$expid
            AND
                exes.experiment_id=ex.experiment_id";

    $result_exp_es  = mysql_query($sql, $db);
    $expes          = mysql_fetch_assoc($result_exp_es);    
    $expesid        = $expes['expes_id'];
    $max            = $expes['max'];

    $sql =  "SELECT
                *
            FROM
                ".TABLE_PREFIX."rl_reservations AS rs,
                ".TABLE_PREFIX."rl_experiments AS ex,
                ".TABLE_PREFIX."rl_experiments_es AS exes
            WHERE
                exes.experiment_id=$expid
            AND
                ex.experiment_id=exes.experiment_id
            AND
                exes.experiments_es_id=rs.experiments_es_id
            AND
                rs.member_id=$_SESSION[member_id]";

    $result_exp_es = mysql_query($sql, $db);
    if($result_exp_es){
        $num_control    = mysql_num_rows($result_exp_es);
        if($max == $num_control){
            $msg->addError('MAX_RESERVATION');
            header("Location: experiments_student.php");
            exit;
        }
    }

    $sql =  "INSERT INTO
                ".TABLE_PREFIX."rl_reservations (
                experiments_es_id,
                member_id,
                start_time)
            VALUES (
                '$expesid',
                '$_SESSION[member_id]',
                '$reserve_start')";
    mysql_query($sql, $db);
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    header("Location: reservations_student.php");
    exit;
}


if(isset($_POST['id'])){
    $expid = intval($_POST['id']);
    $sql =  "SELECT
                *
            FROM
                ".TABLE_PREFIX."rl_experiments
            WHERE
                experiment_id=$expid
            AND
                course_id=$_SESSION[course_id]";
    
    $result_experiment  = mysql_query($sql, $db);
    $experiment         = mysql_fetch_assoc($result_experiment);

    $expstart   = $experiment['start'];
    $expend     = $experiment['end'];
    $duration   = $experiment['reservation_duration'];

    $sql =  "SELECT
                exes.experiment_set_id,
                exs.name
            FROM
                ".TABLE_PREFIX."rl_experiments_es AS exes,
                ".TABLE_PREFIX."rl_experiment_sets AS exs
            WHERE
                exes.experiment_id=$expid
            AND
                exes.experiment_set_id=exs.experiment_set_id
            ORDER BY exs.name ASC";
    
    $result_experiment_sets = mysql_query($sql, $db);
    
    while($row = mysql_fetch_assoc($result_experiment_sets)){
            $exp_sets[] = array($row['experiment_set_id'],$row['name']);
    }
}
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<form name="form" method="post" action="mods/remotelab/reservations_create.php" name="form">
<input type="hidden" value="<?php echo $expid; ?>" name="expid"  />
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('select'); ?></th>
	<th scope="col"><?php echo _AT('reservation_start'); ?></th>
    <th scope="col"><?php echo _AT('experiment_set'); ?></th>   
</tr>
</thead>
	<tfoot>
	<tr>
		<td colspan="4" style="padding-left:38px;">
			<input type="submit" name="submit_reserve" value="<?php echo _AT('reserve'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>
    <?php $i=1; ?>
	<?php while ($expstart < $expend) : ?>    
		<tr>
			<td width="10">
                <input type="radio" name="id" value="<?php echo $expstart; ?>" id="expstart<?php echo $i; ?>" onclick="disableOthers('<?php echo $i; ?>');" />
            </td>
			<td><?php echo date("Y-m-d H:i",strtotime($expstart)); ?></td>
            <td><select name="expset" id="expset<?php echo $i; ?>" disabled>
            <?php   foreach($exp_sets as $expset){                        
                        $expsetid   = $expset[0];
                        $expsetname = $expset[1];
                        $sql =  "SELECT
                                    exs.name,
                                    exs.experiment_set_id
                                FROM
                                    ".TABLE_PREFIX."rl_experiments_es AS exes,
                                    ".TABLE_PREFIX."rl_experiment_sets AS exs,
                                    ".TABLE_PREFIX."rl_reservations AS rs
                                WHERE
                                    rs.start_time='" . date("Y-m-d H:i:s",strtotime($expstart)) ."'
                                AND
                                    exs.experiment_set_id=$expsetid
                                AND
                                    exes.experiments_es_id=rs.experiments_es_id
                                AND
                                    exs.experiment_set_id=exes.experiment_set_id";                        
                        $result_exp_set_reservation = mysql_query($sql, $db);
                        if($result_exp_set_reservation){                                                        
                            $num_reservation = mysql_num_rows($result_exp_set_reservation);                            
                            if($num_reservation == 0){
                                echo "<option value=\"" . $expsetid ."\">" . $expsetname . "</option>";
                            }
                        }
                    }
            ?>
            </select>
            </td>            
		</tr>
        <?php $i++; ?>
        <?php $expstart = addSecondsToTimestamp ($expstart, $duration*60);  ?>
	<?php endwhile; ?>
</tbody>
</table>
</form>
<script type="text/javascript">
    function disableOthers (objid){
        var elementId = 'expset' + objid;
        document.getElementById(elementId).disabled = false;
        for (var i=0;i<document.form.elements.length;i++)	{
            var e = document.form.elements[i];            
            if ((e.id != elementId) && (e.type=='select-one')) {
                document.getElementById(e.id).disabled = "disabled";

            }
        }
    }

</script>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
