<?php

# 1. progress bar
# 2. on end, request older
# 3. api sort files natural
# 4. cripare vågform vid responsive
# 5. prenumerationstjänst via telefon

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>P2 Tystnad</title>

		<link href="//mincss.com/entireframework.min.css" rel="stylesheet" type="text/css">

		<style>
			.hero {
				background: #ff5a00;
				padding: 20px;
				border-radius: 10px;
				margin-top: 1em;
			}

			.description {
				background: #fff;
				border: 10px #ff5a00 solid;
				padding: 20px;
				border-radius: 10px;
				margin-top: 1em;
			}

			.hero h1 {
				margin-top: 0;
				margin-bottom: 0.3em;
			}

			#current-track {
				margin-top: -25px;
				background-color: #fff;
				padding: 10px;
				width: 127px;
				display: none;
			}

			#playpause {
				width: 50%;
				background-color: #c74600;
			}

			#playpause:hover {
				background-color: #E62143;
			}

			.c4 {
				padding: 10px;
				box-sizing: border-box;
			}

			.c4 h3 {
				margin-top: 0;
			}

			.c4 a {
				margin-top: 10px;
				display: inline-block;
			}
			#jp_container_1 {
				top: -141px;
				position: relative;
			}
			.fot {
				color: #aaa;
			}
			.fot a:link, .fot a:visited {
				color: #aaa;
			}

		</style>
	</head>
	<body>
		<div class="container">
			<h1>P2 Tystnad</h1>
			<div class="description">
				Det finaste ljudet som finns är tystnaden mellan alla andra ljud på Sveriges Radio P2. Prasslande kläder, inandningar och notblad som vänds. Här kan du lyssna på senaste veckans tystnad.
			</div>
			<div class="hero">
				
				<div id="silence_player" class="jp-player"></div>
				<div id="jp_container_1" class="jp_container_1">
					<div class="jp-progress" style="width: 100%; height: 1px">
						<div class="jp-seek-bar" style="width: 100%; height: 1px;">
							<div class="jp-play-bar" style="background-color: #fff; height: 1px;"></div>
						</div>
					</div>
				</div>
				<div id="current-track"></div>
				<center><a class="btn btn-b" id="playpause" href="#">Spela</a></center>
			</div>
			<br/>
			<br/>
			<p class="fot">
				Ett projekt av <a href="https://twitter.com/fjallstrom">Peder Fjällström</a> på <a href="https://www.stupidhackathon.se">Stupid Hackathon Sverige 2017</a>. All sopig kod finns <a href="https://github.com/fjallstrom/p2tystnad">här</a>.
			</p>
			<br/><br/>
		</div>
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/add-on/jplayer.playlist.min.js"></script>
		<script>
		var silencePlaylist;
		$(document).ready(function(){
		
			$("#silence_player").jPlayer({
				supplied: "mp3",
				size: {
					width: "100%",
					height: "280px"
				},
				cssSelectorAncestor: '#jp_container_1',
				smoothPlayBar: false,
				playBar: ".jp-play-bar",
			});
		
			silencePlaylist = new jPlayerPlaylist({
				jPlayer: "#silence_player"
			});
		
			$.getJSON("api.php", function(data){
				silencePlaylist.setPlaylist(data);
				silencePlaylist.select(0);
			});
		
			$('#playpause').click(function(){
				if($("#silence_player").data().jPlayer.status.paused == true){
					silencePlaylist.play()
					$('#playpause').text('Pausa');
					$('#current-track').fadeIn('300');
				}else{
					silencePlaylist.pause();
					$('#playpause').text('Spela');
				}
				return false;
			});


			$("#silence_player").bind($.jPlayer.event.play, function(event) {
				$('#current-track').empty();
				$('#current-track').append(silencePlaylist.playlist[silencePlaylist.current].title);
			});
		});
		</script>
	</body>
</html>