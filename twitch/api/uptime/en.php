<?php
header("Content-Type:text/plain");

if(isset($_GET["units"])){
	$unitCount=$_GET["units"];
}
else $unitCount=4;

if(isset($_GET["username"])){
	$username=rawurlencode($_GET["username"]);
	$idCheckUrl=file_get_contents("https://api.twitch.tv/kraken/users?login=".$username."&client_id=0gh9o9fest4sn1ohihvdpf6djdojdn&api_version=5");
	$data1=json_decode($idCheckUrl,true);
	if($data1["_total"]==0){
		$output="Cannot find the channel named ".$username.".";
	}
	else{
		$id=$data1["users"][0]["_id"];
		$url=file_get_contents("https://api.twitch.tv/kraken/streams/".$id."?client_id=0gh9o9fest4sn1ohihvdpf6djdojdn&api_version=5");
		$data2=json_decode($url,true);
		if($data2["stream"]===null){
			if(isset($_GET["offline"])) $output=rawurldecode(rawurlencode($_GET["offline"]));
			else $output="Channel is currently not live.";
		}
		else{
			$nowTime=new DateTime();
			$startTimeData=$data2["stream"]["created_at"];
			$startTime=new DateTime($startTimeData);
			$timeGap=date_diff($startTime,$nowTime);
			$output="";
			if($timeGap->d>0 and $unitCount>=1){
				if($timeGap->d==1) $output.=" ".$timeGap->d." day";
				else $output.=" ".$timeGap->d." days";
			}
			if($timeGap->h>0 and $unitCount>=2){
				if($timeGap->h==1) $output.=" ".$timeGap->h." hour";
				else $output.=" ".$timeGap->h." hours";
			}
			if($timeGap->i>0 and $unitCount>=3){
				if($timeGap->i==1) $output.=" ".$timeGap->i." minute";
				else $output.=" ".$timeGap->i." minutes";
			}
			if($timeGap->s>0 and $unitCount>=4){
				if($timeGap->s==1) $output.=" ".$timeGap->s." second";
				else $output.=" ".$timeGap->s." seconds";
			}
			$output="Currently streaming for ".trim($output).".";
		}
	}
}
else $output="Usage: ~/uptime.php?username={CHANNEL}";

echo(trim($output));
?>