<?php
date_default_timezone_set("Europe/Stockholm");

# settings
$settings['buffertime'] = '00:05:00';
$settings['audiothreshold'] = '-44dB';
$settings['silenceduration'] = '1.0';
$settings['workdirbase'] = 'data';

while(1){
	echo "\nmaking a new recording...\n";
	saveStream($settings);
}

function saveStream($settings){
	# create a workdir
	$workdir = $settings['workdirbase'].date('YmdHis');
	mkdir($workdir);
	chmod($workdir,0777); 
	
	# download and log silence
	$cmd = 'ffmpeg -i https://live-cdn.sr.se/pool2/p2musik/p2musik.isml/p2musik-audio=192000.m3u8 -t '.$settings['buffertime'].' -af silencedetect=noise='.$settings['audiothreshold'].':d='.$settings['silenceduration'].' '.$workdir.'/tracks.m3u8 2> '.$workdir.'/vol.txt';
	shell_exec($cmd);
	processDir($workdir, $settings);

}


function processDir($workdir, $settings){
	# make an audiofile
	foreach (glob($workdir."/*.ts") as $filename){
		$filenames[] = $filename;
	}
	if($filenames){

		sort($filenames, SORT_NATURAL);
		$tsfiles = implode(' ', $filenames);
		$cmd = 'cat '.$tsfiles.' > '.$workdir.'/complete.ts';
		shell_exec($cmd);
		
		# convert to mp3
		$cmd = 'ffmpeg -i '.$workdir.'/complete.ts '.$workdir.'/complete.mp3';
		shell_exec($cmd);
		
		# parse silence log
		$log = file_get_contents($workdir.'/vol.txt');
		preg_match_all('/silence_start: (.*)/', $log, $matches['starts']);
		preg_match_all('/silence_end: (.*?) /', $log, $matches['ends']);
		preg_match_all('/silence_duration: (.*)/', $log, $matches['durations']);
		$cutlist = array(
			'starts' => $matches['starts'][1],
			'ends' => $matches['ends'][1],
			'durations' => $matches['durations'][1]
		);
	
		# make a lot of tiny cuts
		for($i=0; $i<count($cutlist['durations']); $i++){
			if(floatval($cutlist['starts'][$i]) > 0){
				$cmd = 'ffmpeg -i '.$workdir.'/complete.mp3 -ss '.secsToFFMPEG($cutlist['starts'][$i]-0.1).' -to '.secsToFFMPEG($cutlist['ends'	][$i]+0.1).' done/'.$workdir.'-'.$i.$settings['audiothreshold'].'.mp3';
				echo "\nmade 1 clip (".$cutlist['durations'][$i].")\n";
				shell_exec($cmd);
			}
		}
	
		# delete workdir TODO
		shell_exec('rm -rf '.$workdir);
		shell_exec('rsync -a done/ earthpeople@miscbox.earthpeople.se:/var/www/p2tystnad.se/htdocs/done/');
	}
}

function secsToFFMPEG($sec){
	$secsplit = explode(".", $sec);
	$base = date("H:i:s", mktime(0, 0, $secsplit[0], 0, 0, 0));
	return $base.'.'.$secsplit[1];
}
