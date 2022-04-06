<?php
?>
<div class="section">LỆNH SẢN XUẤT/PRODUCTION ORDER - <?php echo $print_type_text;?></div>
<div class="inner-wrap">
	<label>Số lệnh:<input type="text" id="frm_no" value="<?php echo $NUMBER_NO_OK;?>"/></label>		
	<label>Ngày làm đơn:<input type="text" id="frm_create_date" value="<?php echo $date_create;?>"/></label>
	<label>Order date:<input type="text" id="frm_order" value="<?php echo $order;?>"/></label>
	<label>Request date:<input type="text" id="frm_request" value="<?php echo $req;?>"/></label>
	<label>Promise date:<input type="text" id="frm_promise" value="<?php echo $pd;?>"/></label>
	<?php
	if($print_type=='sips' || $print_type=='trim'){
		if(!empty($_COOKIE['trim_sips_data_received'])){
			$data_received = $_COOKIE['trim_sips_data_received'];
		}else{
			$data_received = date('d-M-y');
		}
		if(!empty($_COOKIE['trim_sips_so_lan'])){
			$so_lan = $_COOKIE['trim_sips_so_lan'];
		}else{
			$so_lan = '1';
		}
	?>
	<label>Data received:<input type="text" id="frm_data_received" value="<?php echo $data_received;?>"/></label>
	<label>File:
	<select id="frm_file">
	  <option value="1" <?php echo ($so_lan=='1')?'selected':'';?>>1</option>
	  <option value="2&3" <?php echo ($so_lan=='2&3')?'selected':'';?>>2&amp;3</option>
	  <option value="in thêm" <?php echo ($so_lan=='in thêm')?'selected':'';?>>in thêm</option>
	  <option value="sample" <?php echo ($so_lan=='sample')?'selected':'';?>>sample</option>
	  <option value="fru" <?php echo ($so_lan=='fru')?'selected':'';?>>FRU</option>
	</select>
	</label>
	<?php } ?>
	<label>Ship to:<input type="text" id="frm_ship_to" value="<?php echo $ship_to;?>"/></label>
	<label>RBO:<input type="text" id="frm_rbo" value="<?php echo $rbo;?>"/></label>
	<label>CS name:<input type="text" id="frm_cs" value="<?php echo $cs;?>"/></label>	
	<label>Số lượng:<input type="text" id="frm_qty" value="<?php echo $qty;?>"/></label>
	<label>Remark 1:<input type="text" id="frm_remark_1" value="<?php echo $remark_1;?>"/></label>
	<label>Remark 2:<input type="text" id="frm_remark_2" value="<?php echo $remark_2;?>"/></label>
	<label>Remark 3:<input type="text" id="frm_remark_3" value="<?php echo $remark_3;?>"/></label>
	<label>Remark 4:<input type="text" id="frm_remark_4" value="<?php echo $remark_4;?>"/></label>
	<?php
	if($print_type=='paxar' || $print_type=='trim'){
	?>
	<label>Remark 5:<input type="text" id="frm_remark_5" value="<?php echo $remark_5;?>"/></label>
	<?php } ?>
	<label>Order type:
	<select id="frm_sample">
	  <option value="0">ĐƠN MẪU</option>
	  <option value="1">ĐƠN CÓ MẪU</option>
	  <option value="2">ĐƠN KHÔNG CÓ MẪU</option>
	</select>
	</label>
	<div style="clear:left"></div>
</div>
<script>
	$( function(){
		$( "#frm_data_received" ).datepicker({dateFormat:"dd-M-y"});
		$( "#frm_request" ).datepicker({dateFormat:"dd-M-y"});
		$( "#frm_promise" ).datepicker({dateFormat:"dd-M-y"});
	});
</script>