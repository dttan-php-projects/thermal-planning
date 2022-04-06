<?php
$countSo = count($arr_so);
if($countSo>0&&$countSo<=5){
	// REPARE PARAM
?>
<!-- CODE FOR SO<=5 -->
<table style="width:100%;height:100%;border-collapse:collapse;"  cellpadding="0" cellspacing="0">
	<tr style="height:25px;" class="Cter Gray">
		<td class="none_border_top none_border_left" style="width:30px;"><b>STT</b></td>
		<td class="none_border_top" style="width:5%"><b>Size</b></td>
		<td class="none_border_top" style="width:5%"><b>Tổng số lượng</b></td>
		<?php 
			for($i=1;$i<=5;$i++){				
				if($countSo>=$i){
					if(!empty($arr_so[$i-1]['so_line'])){
						$soline = $arr_so[$i-1]['so_line'];
						echo "<td style='width:8%' class='none_border_top'><b>$soline</b></td>";
					}else{
						echo "<td class='none_border_top'><b>&nbsp;</b></td>";
					}
				}else{
					if($i==$countSo+1){
						$colspan = 5-$countSo;
						echo "<td colspan='$colspan' class='none_border_top'><b>&nbsp;</b></td>";
					}
				}				
			}
		?>
		<td class="none_border_top" style="width:5%"><b>Target</b></td>
		<td class="none_border_top" style="width:5%"><b>Tổng số cái/Dây</b></td>		
		<?php 
			for($i=1;$i<=5;$i++){				
				if($countSo>=$i){
					if(!empty($arr_so[$i-1]['so_line'])){
						$soline = $arr_so[$i-1]['so_line'];
						echo "<td style='width:8%' class='none_border_top'><b>$soline</b></td>";
					}else{
						echo "<td class='none_border_top'><b>&nbsp;</b></td>";
					}
				}else{
					if($i==$countSo+1){
						$colspan = 5-$countSo;
						echo "<td colspan='$colspan' class='none_border_top'><b>&nbsp;</b></td>";
					}
				}				
			}
		?>
		<td class="none_border_top " style="width:5%"><b>%Scrap</b></td>
	</tr> 
	<?php	
		$limit = 5;
		$countSoTmp = $countSo;
		if($countSoTmp<=$limit){
			$countSoTmp = $limit;
		}
		$total_target = 0;
		$countSize = count($arr_size);
		$countSizeTmp = $countSize;
		if($countSize<$limit){
			$countSize = $limit;
		}
		for($i=1;$i<=$countSize;$i++){
			if($countSizeTmp>=$i){
			$l =$i-1;
			$target = (!empty($arr_size[$l]))?(($arr_size[$l]['qty_row'])*$NUM_WIRE):0;
			$total_target+=$target;
	?>
	<tr>
		<td class="none_border_left"><?php echo $countSizeTmp>=$i?$i:'&nbsp;';?></td>
		<td><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['size']:'&nbsp;';?></b></td>
		<td><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['qty']:'&nbsp;';?></b></td>
		<?php			
			for($k=1;$k<=$limit;$k++){
				if($countSo>=$k){
					$qty_so = 'qty_so_'.$k;			
					echo "<td><b>".((!empty($arr_size[$l][$qty_so]))?$arr_size[$l][$qty_so]:'&nbsp;')."</b></td>";
				}else{
					if($k==$countSo+1){
						$colspan = 5-$countSo;
						echo "<td colspan='$colspan' class='none_border_bottom'><b>&nbsp;</b></td>";
					}
				}				
			}
		?>
		<td><b><?php echo $target?number_format($target):'&nbsp;';?></b></td>
		<td><b><?php echo (!empty($arr_size[$l]))?number_format($arr_size[$l]['qty_row']):'&nbsp;';?></b></td>
		<?php
			for($m=1;$m<=$limit;$m++){				
				if($countSo>=$m){
					$row_so = 'row_so_'.$m;
					echo "<td><b>".((!empty($arr_size[$l][$row_so]))?$arr_size[$l][$row_so]:'&nbsp;')."</b></td>";
				}else{
					if($m==$countSo+1){
						$colspan = 5-$countSo;
						echo "<td colspan='$colspan' class=''><b>&nbsp;</b></td>";
					}
				}
			}
		?>
		<td style="font-size:10px;"><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['scrap_percent']:'&nbsp;';?></b></td>
	</tr>
	<?php }else{ ?>
		<tr><td class="none_border_top_bottom none_border_left" colspan="100">&nbsp;</td></tr>
	<?php }} ?>
	<tr>
	<td class="none_border_left <?php echo count($arr_size)>=26?'none_border_bottom':'';?>">&nbsp;</td>
	<td class="<?php echo count($arr_size)>=26?'none_border_bottom':'';?>"><b>Tổng</b></td>
	<td class="<?php echo count($arr_size)>=26?'none_border_bottom':'';?>"><b><?php echo (!empty($total_qty_size))?number_format($total_qty_size):'';?></b></td>
	<?php
		for($i=1;$i<=5;$i++){				
			if($countSo>=$i){
				if(!empty($arr_so[$i-1]['so_line'])){
					$soline = $arr_so[$i-1]['so_line'];
					echo "<td style='width:8%' class='none_border_bottom'><b>".number_format(${'qty_so_total_'.$i})."</b></td>";
				}else{
					echo "<td class='none_border_bottom'><b>&nbsp;</b></td>";
				}
			}else{
				if($i==$countSo+1){
					$colspan = 5-$countSo;
					echo "<td colspan='$colspan' class='none_border_bottom'><b>&nbsp;</b></td>";
				}
			}				
		}
	?>
		<td class="none_border_top <?php echo count($arr_size)>=26?'none_border_bottom':'';?>" style="width:5%"><b><?php echo $total_target?number_format($total_target):'';?></b></td>
		<td class="none_border_top <?php echo count($arr_size)>=26?'none_border_bottom':'';?>" style="width:5%"><b><?php echo (!empty($total_row_size))?number_format($total_row_size):'';?></b></td>
		<?php 
			for($i=1;$i<=5;$i++){				
				if($countSo>=$i){
					if(!empty($arr_so[$i-1]['so_line'])){
						echo "<td style='width:8%' class='none_border_top none_border_bottom'><b>".number_format(${'qty_row_total_'.$i})."</b></td>";
					}else{
						echo "<td class='none_border_top none_border_bottom'><b>&nbsp;</b></td>";
					}
				}else{
					if($i==$countSo+1){
						$colspan = 5-$countSo;
						echo "<td colspan='$colspan' class='none_border_top none_border_bottom'><b>&nbsp;</b></td>";
					}
				}				
			}
		?>
		<td style="font-size:10px;" class="none_border_top none_border_bottom"><b><?php echo !empty($total_scrap)?$total_scrap.'%':'';?></b></td>
	</tr>            
</table>
<!-- END SO<=5 -->
<?php }else{ ?>
	<table style="width:100%;height:100%;border-collapse:collapse;"  cellpadding="0" cellspacing="0">
	<tr style="height:25px;" class="Cter Gray">
		<td class="none_border_top none_border_left" style="width:30px;"><b>STT</b></td>
		<td class="none_border_top" style="width:5%"><b>Size</b></td>
		<td class="none_border_top" style="width:5%"><b>Tổng số lượng</b></td>
		<?php 
			$width = floor(76/($countSo*2));
			for($i=1;$i<=$countSo;$i++){	
				if(!empty($arr_so[$i-1]['so_line'])){
					$soline = $arr_so[$i-1]['so_line'];
					echo "<td style='width:".$width ."%' class='none_border_top'><b>$soline</b></td>";
				}else{
					echo "<td style='width:".$width ."%' class='none_border_top'><b>&nbsp;</b></td>";
				}		
			}
		?>
		<td class="none_border_top" style="width:5%"><b>Target</b></td>
		<td class="none_border_top" style="width:5%"><b>Tổng số cái/Dây</b></td>		
		<?php 
			for($i=1;$i<=$countSo;$i++){	
				if(!empty($arr_so[$i-1]['so_line'])){
					$soline = $arr_so[$i-1]['so_line'];
					echo "<td style='width:".$width ."%' class='none_border_top'><b>$soline</b></td>";
				}else{
					echo "<td style='width:".$width ."%' class='none_border_top'><b>&nbsp;</b></td>";
				}		
			}
		?>
		<td class="none_border_top " style="width:5%"><b>%Scrap</b></td>
	</tr> 
	<?php			
		$total_target = 0;
		for($i=1;$i<=count($arr_size);$i++){
			$l =$i-1;
			$target = (!empty($arr_size[$l]))?(($arr_size[$l]['qty_row'])*$NUM_WIRE):0;
			$total_target+=$target;
	?>
	<tr>
		<td class="none_border_left"><?php echo $i;?></td>
		<td><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['size']:'';?></b></td>
		<td><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['qty']:'';?></b></td>
		<?php			
			for($k=1;$k<=$countSo;$k++){				
				$qty_so = 'qty_so_'.$k;			
				echo "<td><b>".((!empty($arr_size[$l]))?$arr_size[$l][$qty_so]:'')."</b></td>";
			}
		?>
		<td><b><?php echo $target?number_format($target):'';?></b></td>
		<td><b><?php echo (!empty($arr_size[$l]))?number_format($arr_size[$l]['qty_row']):'';?></b></td>
		<?php
			for($m=1;$m<=$countSo;$m++){
				$row_so = 'row_so_'.$m;
				echo "<td><b>".((!empty($arr_size[$l]))?$arr_size[$l][$row_so]:'')."</b></td>";
			}
		?>
		<td style="font-size:10px;"><b><?php echo (!empty($arr_size[$l]))?$arr_size[$l]['scrap_percent']:'';?></b></td>
	</tr>
	<?php } ?>	
	<tr>
	<td class="none_border_left">&nbsp;</td>
	<td><b>Tổng</b></td>
	<td><b><?php echo (!empty($total_qty_size))?number_format($total_qty_size):'';?></b></td>
	<?php
		for($i=1;$i<=$countSo;$i++){
			if(!empty($arr_so[$i-1]['so_line'])){				
				$soline = $arr_so[$i-1]['so_line'];
				echo "<td style='width:".$width ."%' class='none_border_bottom'><b>".number_format(${'qty_so_total_'.$i})."</b></td>";
			}else{
				echo "<td style='width:".$width ."%' class='none_border_bottom'><b>&nbsp;</b></td>";
			}		
		}
	?>
		<td class="none_border_top" style="width:5%"><b><?php echo $total_target?number_format($total_target):'';?></b></td>
		<td class="none_border_top" style="width:5%"><b><?php echo (!empty($total_row_size))?number_format($total_row_size):'';?></b></td>
		<?php 
			for($i=1;$i<=$countSo;$i++){
				if(!empty($arr_so[$i-1]['so_line'])){
					echo "<td style='width:".$width ."%' class='none_border_top'><b>".number_format(${'qty_row_total_'.$i})."</b></td>";
				}else{
					echo "<td class='none_border_top'><b>&nbsp;</b></td>";
				}			
			}
		?>
		<td style="font-size:10px;" class="none_border_top"><b><?php echo !empty($total_scrap)?$total_scrap.'%':'';?></b></td>
	</tr>            
</table>
<!-- CODE FOR SO>=6 -->
<?php }?>


