<?

mysql_connect("gfow.db.7014275.hostedresource.com","gfow","Joebob98");
@mysql_select_db("gfow") or die( "Unable to select database");

$query="SELECT * FROM customers";
$result=mysql_query($query);

mysql_close();

$num=mysql_numrows($result);

$i=0;
while ($i < $num) {

  $nIndex=mysql_result($result,$i,"nIndex");
  $vcEmail=mysql_result($result,$i,"vcEmail");
  $vcPassword=mysql_result($result,$i,"vcPassword");
  $vcName=mysql_result($result,$i,"vcName");

  echo "$nIndex,$vcEmail,$vcPassword,$vcName<br>";

$i++;
}
?>