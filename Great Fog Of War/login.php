<?

// setup: get incoming parameters and connect to database.
$emailid = preg_replace('[^A-Za-z0-9_-@.]', "", strtolower($_GET["emailid"]));
$password = preg_replace('[^A-Za-z0-9_-@.]', "", strtolower($_GET["password"]));

mysql_connect("gfow.db.7014275.hostedresource.com","gfow","Joebob98");
@mysql_select_db("gfow") or die( "Unable to select database");

$query="SELECT * FROM customers WHERE vcEmail='$emailid' and vcPassword='$password'";
// echo $query;
$result=mysql_query($query);
$loginnum=mysql_numrows($result);

if ($loginnum>0) {
  $nIndex=mysql_result($result,$i,"nIndex");
  $vcName=mysql_result($result,$i,"vcName");
  // next, look for the games
  // note: yes, this could have been done in one step. But, we should refrain
  //   for expansion purposes. One day these tables might be on different servers.
  $query="SELECT * FROM games WHERE nCustomerIndexChallenger=$nIndex OR nCustomerIndexOpponent=$nIndex";
  $result=mysql_query($query);
  $gamenum=mysql_numrows($result);
}

mysql_close();

// okay, write the results out
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n<response>\n";

if ($loginnum==0) {
  echo "<auth>No</auth>\n";
} else {
  echo "<auth>Yes</auth>\n";
  echo "<name>$vcName</name>\n";
  if ($gamenum==0) {
    echo "<gamecount>0</gamecount>\n";
  } else {
    echo "<gamecount>$gamenum</gamecount>\n";
    $i=0;
    while ($i < $gamenum) {
	  // to do: this is security hole, using the index number. Later, add a key unique to each
	  // player that is a randomly assigned. Knowing the common nIndex would allow for an guessing
	  // attack for cheating.
      $gid=mysql_result($result,$i,"nIndex");
      $nCustomerIndexChallenger=mysql_result($result,$i,"nCustomerIndexChallenger");
      $vcChallengerName=mysql_result($result,$i,"vcChallengerName");
      $vcOpponentName=mysql_result($result,$i,"vcOpponentName");
      $vcURL=mysql_result($result,$i,"vcURL");
      $dtDateLastPlayed=mysql_result($result,$i,"dtDateLastPlayed");
      $nTurn=mysql_result($result,$i,"nTurn");

	  echo '<game gid="'.$gid.'"';
	  if ($nCustomerIndexChallenger==$nIndex) {
	     // We are the challenger (the game starter)
		 echo ' opponent="'.$vcOpponentName.'"';
		 if ($nTurn==0) {echo ' invite="out"';} else {echo ' invite="na"';};
	  } else {
	     // We are the opponent receiver of the challenge
		 echo ' opponent="'.$vcChallengerName.'"';
		 if ($nTurn==0) {echo ' invite="in"';} else {echo ' invite="na"';};
	  };
	  echo ' url="'.$vcURL.'"';
	  echo ' lastplayed="'.$dtDateLastPlayed.'"';
	  echo ' turn="'.$nTurn.'" />'."\n";
      $i++;
    }
  }
}

echo "</response>";
?>