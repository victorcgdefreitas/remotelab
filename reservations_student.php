<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_POST['submit_delete'])){
    $resid      = intval($_POST['id']);

    $sql    =   "DELETE FROM
                    ".TABLE_PREFIX."rl_reservations
                WHERE
                    reservation_id=$resid
                AND
                    member_id=$_SESSION[member_id]";
    mysql_query($sql, $db);
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    header("Location:" . $_SERVER['PHP_SELF']);
    exit;

}

$sql =  "SELECT
            ex.title AS title,
            rs.start_time AS start_time,
            rs.reservation_id AS reservation_id
        FROM            
            ".TABLE_PREFIX."rl_reservations AS rs            
        RIGHT JOIN
            ".TABLE_PREFIX."rl_experiments_es AS exes
        ON
            rs.experiments_es_id=exes.experiments_es_id
        RIGHT JOIN
            ".TABLE_PREFIX."rl_experiments AS ex
        ON
            exes.experiment_id=ex.experiment_id
        WHERE
            rs.member_id=$_SESSION[member_id]";

$result_reservations = mysql_query($sql, $db);
echo mysql_error($db);
$num_res = mysql_num_rows($result_reservations);

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('select'); ?></th>
	<th scope="col"><?php echo _AT('experiment_title'); ?></th>    
    <th scope="col"><?php echo _AT('reservation_start_time'); ?></th>
</tr>
</thead>
<?php if ($num_res > 0): ?>
	<tfoot>
	<tr>
		<td colspan="3" style="padding-left:38px;">			
            <input type="submit" name="submit_delete" value="<?php echo _AT('delete_reservation'); ?>" />
            <input type="submit" name="submit_start" value="<?php echo _AT('start_experiment'); ?>" onclick="document.form.action='mods/remotelab/experiment_view.php'" />
		</td>
	</tr>
	</tfoot>
	<tbody>
    <?php $i=1; ?>
	<?php while ($row = mysql_fetch_assoc($result_reservations)) : ?>
    
		<tr>
			<td onmousedown="document.form.res<?php echo $i; ?>.checked = !document.form.res<?php echo $i; ?>.checked; togglerowhighlight(this, 'res<?php echo $i; ?>'); " width="10">
                <input type="radio" name="id" value="<?php echo $row['reservation_id']; ?>" id="res<?php echo $i; ?>" onmouseup="this.checked=!this.checked"  />
            </td>
			<td><?php echo $row['title']; ?></td>            
            <td><?php echo $row['start_time']; ?></td>
		</tr>
        <?php $i++; ?>
	<?php endwhile; ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
