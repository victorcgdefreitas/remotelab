<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$sql =  "SELECT
            *
        FROM
            ".TABLE_PREFIX."rl_experiments
        WHERE
            course_id=$_SESSION[course_id]
        AND
            visible=1";
$result_experiments = mysql_query($sql, $db);
$num_exp = mysql_num_rows($result_experiments);


?>
<form method="post" action="<?php echo $_base_href; ?>mods/remotelab/reservations_create.php" name="form">
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('select'); ?></th>
	<th scope="col"><?php echo _AT('experiment_title'); ?></th>
    <th scope="col"><?php echo _AT('experiment_date'); ?></th>
    <th scope="col"><?php echo _AT('experiment_duration'); ?></th>    
</tr>
</thead>

<?php if ($num_exp>0): ?>
	<tfoot>
	<tr>
		<td colspan="5" style="padding-left:38px;">
			<input type="submit" name="submit_reservation" value="<?php echo _AT('create_reservation'); ?>" />            
		</td>
	</tr>
	</tfoot>
	<tbody>
    <?php $i=1; ?>
	<?php while ($row = mysql_fetch_assoc($result_experiments)) : ?>
    <?php
            $sql =  "SELECT
                        *
                    FROM
                        ".TABLE_PREFIX."rl_experiments_es AS exes,
                        ".TABLE_PREFIX."rl_reservations AS rs
                    WHERE
                        rs.member_id=$_SESSION[member_id]
                    AND
                        exes.experiment_id=$row[experiment_id]
                    AND
                        rs.experiments_es_id=exes.experiments_es_id";
    
            $result_reservations = mysql_query($sql, $db);
            if($result_experiments){
                $exp_reservation = mysql_fetch_assoc($result_reservations);
            }

    ?>
		<tr>
			<td onmousedown="document.form.ex<?php echo $i; ?>.checked = !document.form.excmi<?php echo $i; ?>.checked; togglerowhighlight(this, 'ex<?php echo $i; ?>'); " width="10">
                <input type="radio" name="id" value="<?php echo $row['experiment_id']; ?>" id="ex<?php echo $i; ?>" onmouseup="this.checked=!this.checked"  />
            </td>
			<td><?php echo $row['title']; ?></td>
            <td><?php echo $row['start']; ?> / <?php echo $row['end']; ?></td>
            <td><?php echo $row['reservation_duration']; ?> <?php echo _AT('in_minutes'); ?></td>            
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

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
