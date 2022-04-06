<?php
	if(strpos($SHIP_TO,"WORLDON")!==FALSE){
		if($FORM_TYPE=='paxar'){
			echo '<h2 style="position:absolute;top:-370%;left:16%" class="ship_worldon">WORLDON</h2>';
		}elseif($FORM_TYPE=='trim'){
			echo '<h2 style="position:absolute;top:-370%;left:30%" class="ship_worldon">WORLDON</h2>';
		}elseif($FORM_TYPE=='sips'){
			echo '<h2 style="position:absolute;top:-370%;left:30%" class="ship_worldon">WORLDON</h2>';
		}	
	}
	
?>