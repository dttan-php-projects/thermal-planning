<!DOCTYPE html>
<html>
<head>
<title>PRINT THERMAL</title>
<link rel="stylesheet" href="<?php echo $urlRoot.'print/css/style.css';?>">
</head>
<script type="text/javascript">
    window.onload = function() { 
        window.print(); 		  
		setTimeout(function () { window.close(); }, 100);
	  }
 </script>
<body >
	<div style="height:100%; width:100%;"> <!-- fix page break -->
        <table style="width:99%;height:99%;border-collapse:collapse;margin-left:0%;"  cellpadding="0" cellspacing="0">
            <tr>
				<td colspan="" class="none_border">
					<table class="" style="width:100%;height:100%;border-collapse:collapse;"  cellpadding="0" cellspacing="0"> <!-- table 1-->
						<tr class="bold pt13 padding">
							<td colspan="5" class="aleft none_border"><?php echo $THERMAL_TEXT;?></td>
							<td colspan="12" class="aleft padding none_border">LỆNH SẢN XUẤT/PRODUCTION ORDER</td>
						</tr>
						<tr style="height:5%" class="barcode bold pt9 padding">
							<td colspan="3" class="none_border">
							<?php
								echo $BARCODE;
							?>
							</td>
							<td colspan="14" class="aleft pt6 none_border"></td>
						</tr>
						<tr style="height:9%">
							<td colspan="17" class="none_border none_border">
								<table class="" style="width:100%;height:100%;border-collapse:collapse;" cellpadding="0" cellspacing="0">
									<tr class="bold pt9 padding">
										<td style="width:25%" class="aleft pt11 xanh_1 none_border" colspan="3">Thermal</td>
										<td style="width:12%" class="aleft xanh_2 none_border" colspan="1">Order date:</td>
										<td class="aleft xanh_2 none_border" colspan="3"><?php echo $ORDERED;?></td>
										<td class="aleft hong_1 none_border" colspan="1">Ship to:</td>
										<td style="width:30%" class="none_border aleft hong_1" colspan="7"><?php echo $SHIP_TO;?></td>
										<td class="aleft xanh_3 none_border" colspan="2">SO MAY</td>
									</tr>	
									<tr class="bold pt9 padding none_border">
										<td class="none_border aleft xanh_1 pt12" colspan="3"><?php echo $NUMBER_NO;?></td>
										<td class="none_border aleft xanh_2" colspan="1">Request date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $REQ;?></td>
										<td class="none_border aleft hong_1" colspan="1">RBO:</td>
										<td class="none_border aleft hong_1" colspan="7"><?php echo $RBO;?></td>
										<td class="none_border aleft xanh_3" colspan="2">SO CPU</td>
									</tr>
									<tr class="bold pt9 padding">
										<td class="none_border aleft xanh_1 pt11" colspan="3">Ngay lam don<span style="float:right;padding-right:5px" class="create_date"><?php echo $SAVE_DATE;?></td>
										<td class="none_border aleft xanh_2" colspan="1">Promise date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $PD;?></td>
										<td class="none_border aleft hong_1" colspan="1">CS name:</td>
										<td class="none_border aleft hong_1" colspan="7"><?php echo $CS;?></td>
										<td class="none_border aleft xanh_3" colspan="2"></td>
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
											foreach($arr_supply as $material){
												$STT++;
												$SO_LINE = $material['SO_LINE'];
												$ITEM = $material['ITEM'];
												$INTERNAL_ITEM = $material['INTERNAL_ITEM'];
												$QTY = $material['QTY'];
												$TOTAL_QTY+=$QTY;
												$MATERIAL_CODE = $material['MATERIAL_CODE'];												
												$MATERIAL_QTY = $material['MATERIAL_QTY'];
												$LENGTH = $material['LENGTH'];
												$WIDTH = $material['WIDTH'];
												$INK_CODE = $material['INK_CODE'];
												$INK_QTY = $material['INK_QTY'];
												$MULTIPLE = $material['MULTIPLE'];
												$SAMPLE = $material['SAMPLE'];
												$SO_UPS = $material['SO_UPS'];
									?>
									<tr class="pt6">
										<td colspan="">&nbsp;<br/><?php echo $STT;?><br/>&nbsp;<br/></td>
										<td class="pt7" style="width:8%" colspan=""><?php echo $SO_LINE;?></td>
										<td class="pt7" style="width:10%" colspan=""><?php echo $ITEM;?></td>
										<td style="width:10%" colspan="3"><?php echo $INTERNAL_ITEM;?></td>
										<td class="pt7" style="width:4%" colspan=""><?php echo $QTY>0?number_format($QTY):'-';?></td>
										<td style="width:8%" colspan="2"><?php echo $MATERIAL_CODE;?></td>
										<td class="pt7" style="width:15%" colspan="3"><?php echo $MATERIAL_QTY>0?number_format($MATERIAL_QTY):'-';?></td>
										<td style="width:4%" colspan=""><?php echo $LENGTH;?></td>
										<td style="width:4%" colspan=""><?php echo $WIDTH;?></td>
										<td class="pt7" style="width:10%" colspan="2"><?php echo $INK_CODE;?></td>
										<td class="pt7" style="width:3%" colspan=""><?php echo $INK_QTY>0?number_format($INK_QTY):'-';?></td>
									</tr>
									<?php }} ?>
									<!-- end material -->
									<!-- check to add material temp-->	
									<?php
									if($STT<5){
										$limit = 5-$STT;
										for($j=1;$j<=$limit;$j++){
									?>
									<tr class="pt6">
										<td colspan="">&nbsp;</td>
										<td class="pt7" style="width:8%" colspan="">&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
										<td class="pt7" style="width:5%" colspan="">&nbsp;</td>
										<td style="width:10%" colspan="3">&nbsp;</td>
										<td class="pt7" style="width:4%" colspan="">&nbsp;</td>
										<td style="width:8%" colspan="2">&nbsp;</td>
										<td class="pt7" style="width:15%" colspan="3">&nbsp;</td>
										<td style="width:4%" colspan="">&nbsp;</td>
										<td style="width:4%" colspan="">&nbsp;</td>
										<td class="pt7" style="width:10%" colspan="2">&nbsp;</td>
										<td class="pt7" style="width:3%" colspan="">&nbsp;</td>
									</tr>
									<?php }} ?>
								</table>
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border">&nbsp;</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="3" class="aleft none_border">
								QUI CÁCH ĐÓNG GÓI /PACKAGING
							</td>
							<td colspan="14" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold padding">
							<td colspan="3" class="pt12 vang">
								<?php echo number_format($TOTAL_QTY);?>
							</td>
							<td colspan="14" class="none_border aleft pt6">
								PCS
							</td>
						</tr>
						<tr class="blank">
							<td colspan="3" class="none_border">&nbsp;</td>
							<td colspan="14" class="none_border">&nbsp;</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="7" class="aleft none_border">
								CHI TIẾT IN/PRINTING DETAIL
							</td>
							<td colspan="10" class="aleft none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6">
							<td colspan="1">
								&nbsp;
							</td>
							<td colspan="2">
								V
							</td>
							<td colspan="4">
								Bao ngoài /Packing
							</td>
							<td colspan="7" class="none_border">
								&nbsp;
							</td>
							<td colspan="1" class="none_border hong_2 aleft padding">
								Printing by:
							</td>
							<td colspan="2" class="none_border">
							&nbsp;
							</td>
						</tr>
						<tr class="bold pt6">
							<td style="width:5%" colspan="1" class="aleft padding">
								Q'ty
							</td>
							<td style="width:15%" colspan="2">
								&nbsp;
							</td>
							<td style="width:15%" colspan="4">
								&nbsp;
							</td>
							<td colspan="7" class="none_border">
								&nbsp;
							</td>
							<td style="width:15%" colspan="1" class="hong_2 aleft padding none_border">
								Quality checked by:
							</td>
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6">
							<td colspan="1" class="aleft padding">
								Date
							</td>
							<td colspan="2" class="">
								&nbsp;
							</td>
							<td colspan="4" class="">
								&nbsp;
							</td>
							<td colspan="7" class="none_border">
								&nbsp;
							</td>
							<td colspan="1" class="hong_2 aleft padding none_border">
								Packed by:
							</td>
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="blank">
							<td colspan="7" class="none_border">&nbsp;</td>
							<td colspan="11" class="none_border">&nbsp;</td>
						</tr>
						<tr class="bold pt6">
							<td style="width:15%" colspan="2" class="aleft padding none_border" >
								SAMPLING
							</td>
							<td style="width:15%" colspan="2" class="none_border">
								P
							</td>
							<td colspan="3" class="none_border">
								..............pcs
							</td>
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
							<td colspan="3" class="none_border">
								CS
							</td>							
							<td colspan="5" class="none_border">
								..............pcs
							</td>
						</tr>
						<tr class="bold pt6">
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
							<td colspan="3" class="none_border">
								..............pcs
							</td>
							<td colspan="2" class="none_border">
								&nbsp;
							</td>
							<td colspan="3" class="none_border">
								PD
							</td>							
							<td colspan="5" class="none_border">
								..............pcs
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border">&nbsp;</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="aleft none_border">
								Remark:
							</td>
							<td colspan="10" class="aleft xanh_2 none_border">
								<?php echo $REMARK_1;?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="aleft xanh_2 none_border">
								<?php echo $REMARK_2;?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="aleft cam_1 none_border">
								<?php echo $REMARK_3;?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="aleft cam_1 none_border">
								<?php echo $REMARK_4;?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="aleft cam_1 none_border">
								<?php echo $REMARK_5;?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt6 padding">
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="aleft cam_1 none_border">
								Sử dụng đúng tem mực;
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border">&nbsp;</td>
						</tr>
						<!-- Trace Ability-->
						<tr class="bold pt6">
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
						<tr class="pt6">
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
						<tr class="pt6">
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
						<tr class="pt6">
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
				</td>
            </tr>
        </table>
    </div>
</body>
</html>