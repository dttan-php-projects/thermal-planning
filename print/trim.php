<!DOCTYPE html>
<html>
<head>
<title>PRINT THERMAL</title>
<meta name="google" content="notranslate" />
<link rel="stylesheet" href="<?php echo $urlRoot.'print/css/style.css';?>">
<style>
	.ship-af {
		font-size:40px;
		font-weight:bold;
		color:red;
		text-shadow:rgba(0, 0, 0, .2) 3px 3px 3px;
		-webkit-text-stroke: 4px rgba(0, 0, 0, .6);
	}
</style>
<script src="./dhtmlx/js/jquery.min.js"></script>
</head>
<?php
$MarkNew = ""; 
$SO = explode("-",$arr_supply[0]['SO_LINE']);

// // @tandoan - 20200730: get packing instr all (trường hợp cũ chỉ lấy vnso)
// $packingInstr = remarkPackingInstr($SO[0], $SO[1]);

$UPC = MiQuery("SELECT REMARK_1_ITEM FROM master_bom WHERE INTERNAL_ITEM = '" . $arr_supply[0]['INTERNAL_ITEM'] . "' LIMIT 1",$dbMi_138);
$PACKING_INSTRUCTIONS = remarkPackingInstr($SO[0], $SO[1]);

// @tandoan - 20200730 - remark trim card
$remnarkTrimCard = remnarkTrimCard($PACKING_INSTRUCTIONS);

if (!empty($UPC) && !empty($PACKING_INSTRUCTIONS) ) {
	if (strpos($UPC, "UPC") !== false) {
		if (strpos($PACKING_INSTRUCTIONS, "HANGLE") !== false) {
			$REMARK_ITEM = "HANGLE";
		} else {
			$REMARK_ITEM = "COMBINE THERMAL & RFID";
		}
	}

	// $REMARK_4 = str_replace("Hàng Nike Thermal combine với RFID", "", $REMARK_4);
}
?>
<script type="text/javascript">	
    window.onload = function() { 
		var DCM1 = $("#DCM").outerHeight(true);
		var PageC = Math.ceil(DCM1/716);
		var Remain = PageC*716 - DCM1;
		if(Remain < 318) //338
		{
			$("#B").html('<p style="page-break-after:always;"></p>');
		}
		var totalH = 178;
		for(var i=1;i<1000;i++){
			console.log($("#row_"+i).outerHeight(true));
			var h = $("#row_"+i).outerHeight(true);
			if(h==null)
				break;
			totalH+=h;
			if(totalH>716){
				var r = 716 - totalH + h;
				//console.log("x " + r + " - " + h + " - " + totalH);
				$("#row_"+(i-1)).height(r+$("#row_"+(i-1)).outerHeight(true));
				totalH = 0;
			}
		}
        window.print(); 		  
		setTimeout(function () { window.close(); }, 100);
	  }
 </script>
<body >
	<div id="DCM" style="width:100%;"> <!-- fix page break -->
        <table style="width:99%;height:99%;border-collapse:collapse;margin-left:0%;"  cellpadding="0" cellspacing="0">
            <tr>
				<td colspan="" class="none_border">
					<table class="" style="width:100%;height:100%;border-collapse:collapse;"  cellpadding="0" cellspacing="0"> <!-- table 1-->
						<tr class="bold pt13 padding">
							<!-- @TanDoan:  add style="font-size:25px" -->
							<td colspan="3" class=" none_border" style="font-size:25px"><?php echo $THERMAL_TEXT;?></td>
							<!-- @TanDoan:  add style="font-size:25px" -->
							<td colspan="10" rowspan="2" class="aleft padding none_border" style="font-size:27px">LỆNH SẢN XUẤT/PRODUCTION ORDER</td>
							<td colspan="4" rowspan="2" class="none_border">
								<?php 
									require_once($pathERP."/print/thermal_AF.php"); 
									if (!empty($SECURITY_SHOW)) { 
										echo '<div style="text-shadow: 5px 2px 4px grey;font-weight:bold;font-size:24px;border-radius:8%;border:2px solid blue;padding:3px;background:yellow;">' . $SECURITY_SHOW . '</div>';
									}

									if (!empty($REMARK_FR)) {
										echo $REMARK_FR;
									}

									
								?>
							</td>
						</tr>
						<tr style="height:5%" class="barcode bold pt9 padding">
							<td colspan="3" class="none_border">
							<?php
								echo $BARCODE;
							?>
							</td>
							<!-- <td colspan="12" class="aleft pt6 none_border"></td> -->
						</tr>
						<tr style="height:9%">
							<td colspan="17" class="none_border none_border">
								<table class="" style="width:100%;height:100%;border-collapse:collapse;" cellpadding="0" cellspacing="0">
									<tr class="bold pt9 padding">
										<td style="width:30%" class="aleft pt11 xanh_1 none_border" colspan="3"><?php echo $NUMBER_NO;?></td>
										<td style="width:12%" class="aleft xanh_2 none_border" colspan="1">Order date:</td>
										<td class="aleft xanh_2 none_border" colspan="3"><?php echo $ORDERED;?></td>
										<td class="aleft hong_1 none_border" colspan="1">Ship to:</td>
										<td style="width:<?php echo !empty($REMARK_SHORT_LT) ? '30%' : '40%'; ?>" class="none_border aleft hong_1" colspan="9"><?php echo $SHIP_TO; ?></td>
										<?php 
											if (!empty($REMARK_SHORT_LT) ) {
												echo '<td rowspan="3" style="width:12%" class="none_border">'. $REMARK_SHORT_LT .'</td>';
											} 
										?>

										<!-- <td style="width:30%" class="none_border aleft hong_1" colspan="9"><?php // echo $SHIP_TO;?></td>
										<td rowspan="3" style="width:12%" class="none_border xanh_2"><?php // echo $REMARK_SHORT_LT;?></td> -->
									</tr>	
									<tr class="bold pt9 padding none_border">
										<td class="none_border aleft xanh_1 pt12" colspan="3">Ngay lam don<span style="float:right;padding-right:0" class="create_date"><?php echo $SAVE_DATE;?></span></td>
										<td class="none_border aleft xanh_2" colspan="1">Request date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $REQ;?></td>
										<td class="none_border aleft hong_1" colspan="1">RBO:</td>
										<td class="none_border aleft hong_1" colspan="9"><?php echo $RBO;?></td>
										
									</tr>
									<tr class="bold pt9 padding">
										<td class="none_border aleft xanh_1 pt11" colspan="3"><span style="float:left;" class="create_date">Data Received</span><span style="float:left;padding-left:30px" class="create_date"><?php echo $DATA_RECEIVED;?></span><span style="float:right;padding-right:0" class="create_date"><?php echo $SO_LAN;?></span></td>
										<td class="none_border aleft xanh_2" colspan="1">Promise date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $PD;?></td>
										<td class="none_border aleft hong_1" colspan="1">CS name:</td>
										<td class="none_border aleft hong_1" colspan="9"><?php echo $CS;?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border ">&nbsp;</td>
						</tr>
						<tr style="height:28%">
							<td colspan="17" class="none_border ">
								<table class="bold pt8 xanh_3" style="width:100%;height:100%;border-collapse:collapse;" cellpadding="0" cellspacing="0">
									<tr class="pt9" style="height:30px;">
										<td class="xanh_4" style="width:3%" rowspan="2">No</td>
										<td class="vang" colspan="6">Order infos</td>
										<td class="xanh_1" colspan="5">Paper infos</td>
										<td class="vang" colspan="2">Size lable</td>
										<td class="hong_1" colspan="3">Ink infos</td>
										<td class="xanh_4" style="width:3%" rowspan="2">Machine</td>
									</tr>
									<tr class="pt7 cam_1" style="height:30px;">
										<td colspan="">SO#</td>
										<td colspan="">Item</td>
										<td colspan="3">Internal item</td>
										<td colspan="">Q'ty</td>
										<td colspan="2">Material code</td>
										<td colspan="3">QTY</td>
										<td colspan="">Length</td>
										<td colspan="">Width</td>
										<td colspan="2">Ink code</td>
										<td colspan="">Q'ty</td>
									</tr>
									<!-- begin material -->
									<?php
										if(!empty($arr_supply)){
											$STT = 0;
											$TOTAL_QTY = 0;
											$COUNT_LOT = count($arr_supply);
											for($i=0;$i<$count_data;$i++){
												if(!empty($arr_supply[$i])){
													$STT++;
													$SO_LINE = $arr_supply[$i]['SO_LINE'];
													$ITEM = $arr_supply[$i]['ITEM'];
													$INTERNAL_ITEM = $arr_supply[$i]['INTERNAL_ITEM'];
													$QTY = $arr_supply[$i]['QTY'];
													$TOTAL_QTY+=$QTY;
													$MATERIAL_CODE = $arr_supply[$i]['MATERIAL_CODE'];												
													$MATERIAL_QTY = $arr_supply[$i]['MATERIAL_QTY'];
													$LENGTH = $arr_supply[$i]['LENGTH'];
													$WIDTH = $arr_supply[$i]['WIDTH'];
													$INK_CODE = $arr_supply[$i]['INK_CODE'];
													$INK_QTY = $arr_supply[$i]['INK_QTY'];
													$MULTIPLE = $arr_supply[$i]['MULTIPLE'];
													$SAMPLE = $arr_supply[$i]['SAMPLE'];
													$SO_UPS = $arr_supply[$i]['SO_UPS'];

													// @tandoan 20200921: Thêm vào Machine
													$bomData = getBOMData($INTERNAL_ITEM);
													$machineShow = !empty($bomData) ? trim($bomData['MACHINE']) : '';

									?>
									<tr <?php echo ($COUNT_LOT<6)?'style="height:35px;"':'';?> class="pt6" id="<?php echo 'row_'.$STT;?>">
										<td colspan="">&nbsp;<br/><?php echo $STT;?><br/>&nbsp;<br/></td>										
										<td class="pt12" style="width:12%" colspan=""><?php echo $SO_LINE;?></td>
										<td class="pt9" style="width:12%" colspan=""><?php echo $ITEM;?></td>
										<td class="pt8" style="width:12%" colspan="3"><?php echo $INTERNAL_ITEM;?></td>
										<td class="pt10" style="width:5%" colspan=""><?php echo $QTY>0?number_format($QTY):'-';?></td>
										<td class="pt7" colspan="2"><?php echo $MATERIAL_CODE;?></td>
										<td class="pt9" style="width:7%" colspan="3"><?php echo $MATERIAL_QTY>0?number_format($MATERIAL_QTY):'-';?></td>
										<td class="pt7" style="width:6%" colspan=""><?php echo $LENGTH;?></td>
										<td class="pt7" style="width:6%" colspan=""><?php echo $WIDTH;?></td>
										<td class="pt7" style="width:11%;" colspan="2"><?php echo $INK_CODE;?></td>
										<td class="pt9" style="width:5%" colspan=""><?php echo $INK_QTY>0?number_format($INK_QTY):'-';?></td>
										<td class="pt7" colspan=""><?php echo $machineShow; ?></td>
									</tr>
									<?php }}} ?>
									<!-- end material -->
								</table>
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border">							
							<?php
							$min_height = 'style="height:20px;"';
							?>
							&nbsp;
							</td>
						</tr>						
					</table>
				</td>
            </tr>
        </table>
    </div>
	<div id="B"></div>
	<div id="DCM2">
		<table style="width:99%;height:99%;border-collapse:collapse;margin-left:0%;"  cellpadding="0" cellspacing="0">
			<tr class="bold pt6 padding">
				<td colspan="2" class="aleft none_border" <?php echo $min_height;?>>
						QUI CÁCH ĐÓNG GÓI /PACKAGING
				</td>
				<td colspan="1" class="aleft none_border"></td>
				<td colspan="14" rowspan="6" class="none_border">
					<?php

						echo '<div class="remark">';
							echo '<div class="remark-left">';
								
								if (!empty($remnarkTrimCard)) {
									echo '<div class="remark-trimcard">';
										echo $remnarkTrimCard;
									echo '</div>';
								}

								if (!empty($remarkFRUIC)) {
									echo '<div class="remark-sample">';
										echo $remarkFRUIC;
									echo '</div>';
								}

								
							echo '</div>';


							echo '<div class="remark-right">';
								// // if (!empty($REMARK_ITEM) ) {
								// // 	echo '<div class="remark-right-detail ">';
								// // 		echo $REMARK_ITEM;
								// // 	echo '</div>';
								// // }

								
							echo '</div>';

						echo '</div>';
					?>
				</td>
			</tr>
			<tr class="bold padding" <?php echo $min_height;?>>
				<td colspan="2" class="pt20 vang">
					<?php echo number_format($TOTAL_QTY);?>
				</td>
				<td colspan="1" class="none_border aleft pt6">
					<?php echo getTRIMUnit($INTERNAL_ITEM); ?>
				</td>
			</tr>
			<tr class="blank">
				<td colspan="2" class="none_border">&nbsp;</td>
				
			</tr>
			<tr class="bold pt8" <?php echo $min_height;?>>
				<td colspan="3" class="none_border hong_2 aleft padding">
					Printing by:
				</td>
				

			</tr>
			<tr class="bold pt8" <?php echo $min_height;?>>							
				<td style="width:15%" colspan="3" class="hong_2 aleft padding none_border">
					Quality checked by:
				</td>
				

			</tr>
			<tr class="bold pt8" <?php echo $min_height;?>>							
				<td colspan="3" class="hong_2 aleft padding none_border">
					Packed by:
				</td>
				
				
			</tr>

			<tr class="blank">
				<td colspan="3" class="none_border">&nbsp;</td>
				<td colspan="14" class="none_border">&nbsp;</td>
			</tr>	
			<tr class="bold pt8 padding" <?php echo $min_height;?>>
				<td colspan="1" class="aleft none_border">
					Remark:
				</td>
				<td colspan="10" class="aleft xanh_2 none_border" style="position:relative">
							<?php echo $REMARK_1;?>
							<?php require_once($pathERP."/print/thermal_worldon.php");?>
						</td>
				<td colspan="6" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="bold pt10 padding" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="10" class="pt14 aleft xanh_2 none_border">
					<?php echo $REMARK_2;?>
				</td>
				<td colspan="6" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="bold pt10 padding" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="10" class="pt14 aleft cam_1 none_border">
					<?php echo $REMARK_3;?>
				</td>
				<td colspan="6" class="none_border">
					&nbsp; <?php // echo $REMARK_SHORT_LT; ?>
				</td>
			</tr>
			<tr class="bold pt10 padding" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="10" class="pt14 aleft cam_1 none_border">
					<?php echo $REMARK_4;?>
				</td>
				<td colspan="6" class="none_border">
					&nbsp; 
				</td>
			</tr>
			<tr class="bold pt10 padding" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="10" class="pt14 aleft cam_1 none_border">
					<?php echo $REMARK_5;?>
				</td>
				<td colspan="6" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="bold pt10 padding" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="10" class="pt14 aleft cam_1 none_border">
					<?php echo !empty($REMARK_6)?$REMARK_6:'Sử dụng đúng tem mực'; ?>
				</td>
				<td colspan="6" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="blank">
				<td colspan="17" class="none_border">&nbsp;</td>
			</tr>
			<!-- Trace Ability-->
			<tr class="bold pt6" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="3" class="none_border_bottom">
					&nbsp;
				</td>
				<td colspan="3">
					Màu
				</td>
				<td colspan="2">
					PO#
				</td>
				<td colspan="3">
					Lot#
				</td>
				<td colspan="3">
					Ghi chú
				</td>
				<td colspan="2" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="pt6" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td rowspan="3" class="none_border_top">
					Trace <br/>
					Ability
				</td>
				<td colspan="2">
					Vải
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="pt6" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="2">
					Giấy
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2" class="none_border">
					&nbsp;
				</td>
			</tr>
			<tr class="pt6" <?php echo $min_height;?>>
				<td colspan="1" class="none_border">
					&nbsp;
				</td>
				<td colspan="2">
					Mực
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="3">
					&nbsp;
				</td>
				<td colspan="2" class="none_border">
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
</body>
</html>