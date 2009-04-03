<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM ".TABLE_PREFIX."cmi";
$result = mysql_query($sql, $db);	
while($row = mysql_fetch_assoc($result)){
	echo $row["item_id"] . " = " . $row["lvalue"] . " = " . $row["rvalue"] . "<br><br>";
}

?>

<div id="helloworld">
	
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
