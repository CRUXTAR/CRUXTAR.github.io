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
		$output=$username."이라는 채널을 찾을 수 없습니다.";
	}
	else{
		$id=$data1["users"][0]["_id"];
		$url=file_get_contents("https://api.twitch.tv/kraken/streams/".$id."?client_id=0gh9o9fest4sn1ohihvdpf6djdojdn&api_version=5");
		$data2=json_decode($url,true);
		if($data2["stream"]===null){
			if(isset($_GET["offline"])) $output=rawurldecode(rawurlencode($_GET["offline"]));
			else $output="지금은 방송 중이지 않습니다.";
		}
		else{
			$nowTime=new DateTime();
			$startTimeData=$data2["stream"]["created_at"];
			$startTime=new DateTime($startTimeData);
			$timeGap=date_diff($startTime,$nowTime);
			$output="";
			if($timeGap->d>0 and $unitCount>=1){
				$output.=" ".$timeGap->d."일";
			}
			if($timeGap->h>0 and $unitCount>=2){
				$output.=" ".$timeGap->h."시간";
			}
			if($timeGap->i>0 and $unitCount>=3){
				$output.=" ".$timeGap->i."분";
			}
			if($timeGap->s>0 and $unitCount>=4){
				$output.=" ".$timeGap->s."초";
			}
			$output="현재 ".trim($output)."째 방송 중입니다.";
			}
		}
	}
else $output="사용법: ~/uptime.php?username={채널 ID}";

echo(trim($output));
?>