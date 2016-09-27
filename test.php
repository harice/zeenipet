<?php 
$con = mysql_connect("localhost","zeenicom_zeeni","BlursGottaTangoGashes81");
mysql_select_db("zeenicom_zeeni",$con);
print $sql = "select * from aitoc_reserve_catalog_product_option_title WHERE title like '%Choose Outline Color Number%'";
$rs = mysql_query($sql);
$i =1;
while($row = mysql_fetch_array($rs))
{
	print $i.'> '.$row['option_id']." = ".$row['title']."<br>";
	$i++;
	$sql2 = "update aitoc_reserve_catalog_product_option set is_require = 0 WHERE option_id='".$row['option_id']."'";
    $rs2 = mysql_query($sql2);
}
?>
