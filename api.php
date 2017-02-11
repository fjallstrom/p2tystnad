<?php
function makeWaveform($filename){
	if(!file_exists($filename.'.png')){
		$cmd = 'sox '.$filename.' -c 1 -t wav - gain 15 | wav2png -o '.$filename.'.png  --foreground-color=2e4562ff --background-color=00000000 -w 800 /dev/stdin';
		shell_exec($cmd);
	}
	return $filename.'.png';
}
function createdAt($filename){
	if($filename){
		preg_match_all('/data(.*)\-(\d)/', $filename, $matches);
		if(isset($matches[0]) && isset($matches[1])){
			return date('Y-m-d', strtotime(substr($matches[1][0],0,8))).' '.substr($matches[1][0],8,2).':'.substr($matches[1][0],10,2);
		}
	}
}

$playlist = array();
$filelist = glob("done/*.mp3");
rsort($filelist);
foreach ($filelist as $filename) {
	if(file_exists($filename.'.png')){
		$title = createdAt($filename);
		if(strtotime($title) > strtotime('-7 day')) {
			$item = new StdClass();
			$item->mp3 = $filename;
			$item->title = $title;
			$item->poster = makeWaveform($filename);
			$playlist[] = $item;
			unset($item);
		}
	}
}
echo json_encode($playlist);