<!DOCTYPE html>
<html>
<head>
	<title>PRINT THERMAL</title>
	<meta name="google" content="notranslate" />
	<link rel="stylesheet" href="<?php echo $urlRoot . 'print/css/style.css'; ?>">
	<style>
		.ship-af {
			font-size: 40px;
			font-weight: bold;
			color: red;
			text-shadow: rgba(0, 0, 0, .2) 3px 3px 3px;
			-webkit-text-stroke: 4px rgba(0, 0, 0, .6);
		}
	</style>
</head>
<script type="text/javascript">
	window.onload = function() {
		window.print();
		setTimeout(function() {
			window.close();
		}, 100);
	}
</script>
<?php



$REMARK_ITEM = '';
$REMARK_RBO = '';
$REMARK_PACKING_INSTRUCTIONS = '';
$PACKING_INSTRUCTIONS_NEW = '';

$NHOM = getNHOM($arr_supply[0]['INTERNAL_ITEM']);
$UOM = getUOM($arr_supply[0]['INTERNAL_ITEM']);

if ($NHOM == 'IPPS' ) {
	$THERMAL_TEXT = 'THERMAL IPPS';
} else if ($NHOM == 'FG' ) {
	$THERMAL_TEXT = 'THERMAL FG';
}

function remarkIPPS($NHOM, $SECURITY_SHOW) {
	if (strpos($NHOM, 'IPPS') !== false ) {
		if ( (strpos(strtoupper($SECURITY_SHOW), 'HÀNG ĐẶC BIỆT') !== false || strpos(strtoupper($SECURITY_SHOW), 'HANG DAC BIET') !== false) ) {
			return 'ADIDAS <br /> IPPS BP';
		} else {
			return 'IPPS';
		}
	} else if (strpos($NHOM, 'FG') !== false ) {
		if ( (strpos(strtoupper($SECURITY_SHOW), 'HÀNG ĐẶC BIỆT') !== false || strpos(strtoupper($SECURITY_SHOW), 'HANG DAC BIET') !== false) ) {
			return 'ADIDAS <br /> FG BP';
		} else {
			return 'FG';
		}
		
	} else 
	{
		return '';
	}
	
	
}

$REMARK_IPPS = remarkIPPS($NHOM, $SECURITY_SHOW);

$SO = explode("-", $arr_supply[0]['SO_LINE']);

// // @tandoan - 20200730: get packing instr all (trường hợp cũ chỉ lấy vnso)
// $packingInstr = remarkPackingInstr($SO[0], $SO[1]);

$UPC = MiQuery("SELECT REMARK_1_ITEM FROM master_bom WHERE INTERNAL_ITEM = '" . $arr_supply[0]['INTERNAL_ITEM'] . "' LIMIT 1", $dbMi_138);
$REMARK_3_PACKING = MiQuery("SELECT REMARK_3_PACKING FROM master_bom WHERE INTERNAL_ITEM = '" . $arr_supply[0]['INTERNAL_ITEM'] . "' LIMIT 1", $dbMi_138);

$CHI_TIET_KIT = MiQuery("SELECT CHI_TIET_KIT FROM master_bom WHERE INTERNAL_ITEM = '" . $arr_supply[0]['INTERNAL_ITEM'] . "' LIMIT 1", $dbMi_138);

$OnlyPackingInstr = remarOnlykPackingInstr($SO[0], $SO[1]);
$PACKING_INSTRUCTIONS_NEW = remarkPackingInstr($SO[0], $SO[1]);

if (strpos(strtoupper($PACKING_INSTRUCTIONS_NEW), 'KEM PACKING LIST CHI TIET^' ) !==false ) {
	$PACKING_INSTRUCTIONS_NEW = str_replace('KEM PACKING LIST CHI TIET^', '', $PACKING_INSTRUCTIONS_NEW);
}

// @tandoan - 20200730 - remark trim card
$remnarkTrimCard = remnarkTrimCard($PACKING_INSTRUCTIONS_NEW);

if (!empty($UPC) ) {
	if (strpos( strtoupper($UPC), "UPC") !== false) {
		
		if (strpos($PACKING_INSTRUCTIONS_NEW, "HANGLE") !== false ) {
			$REMARK_ITEM = "HANGLE";
		} else {
			$REMARK_ITEM = "COMBINE THERMAL & RFID";
		}

		// Đối với đơn hàng WORLDON thì remark không hiển thị
		if (strpos(strtoupper($SHIP_TO), 'WORLDON') !== false) {
			$REMARK_ITEM = '';	
		}
		
	}

	$REMARK_4 = str_replace("Hàng Nike Thermal combine với RFID", "", $REMARK_4);
}
?>

<body>
	<div style="height:100%; width:100%;">
		<!-- fix page break -->
		<table style="width:99%;height:99%;border-collapse:collapse;margin-left:0%;" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="" class="none_border">
					<table class="" style="width:100%;height:100%;border-collapse:collapse;" cellpadding="0" cellspacing="0">
						<!-- table 1-->
						<tr class="bold pt13 padding">
							<!-- @TanDoan:  add style="font-size:25px" -->
							<td colspan="3" class=" none_border" style="font-size:25px"><?php echo $THERMAL_TEXT; ?></td>
							<!-- @TanDoan:  add style="font-size:25px" -->
							<td colspan="5" rowspan="2" class="aleft padding none_border" style="font-size:30px;text-align:center;">LỆNH SẢN XUẤT</td>
							<td colspan="5" rowspan="2" class="aleft padding none_border" style="font-size:24px;text-align:center;">
								<?php 
									echo (stripos($REMARK_1, 'CHUYEN PREPRESS LAM LAYOUT' ) !== false) ? '<div style="text-shadow: 5px 2px 4px grey;width:80%;height:60%;font-weight:bold;font-size:18px;border-radius:8%;border:1px solid black;padding-top:10px;background:black;color:white;">CHUYEN PREPRESS LAM LAYOUT</div>' : ''; 
								?>
							</td>
							<td colspan="6" rowspan="2" class="none_border">
								<?php
									require_once($pathERP . "/print/thermal_AF.php");
									if (!empty($SECURITY_SHOW)) {
										echo '<div style="text-shadow: 5px 2px 4px grey;font-weight:bold;font-size:24px;border-radius:8%;border:2px solid blue;padding:3px;background:yellow;">' . $SECURITY_SHOW . '</div>';
									}
									if (!empty($REMARK_FR)) {
										echo $REMARK_FR;
									}
									
								?>
							</td>
						</tr>
						<tr style="height: 35px;" class="barcode bold pt9 padding">
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
										<td style="width:25%" class="aleft pt11 xanh_1 none_border" colspan="3">&nbsp;</td>
										<td style="width:12%" class="aleft xanh_2 none_border" colspan="1">Order date:</td>
										<td class="aleft xanh_2 none_border" colspan="3"><?php echo $ORDERED; ?></td>
										<td class="aleft hong_1 none_border" colspan="1">Ship to:</td>
										<td style="width:<?php echo !empty($REMARK_SHORT_LT) ? '30%' : '40%'; ?>" class="none_border aleft hong_1" colspan="9"><?php echo $SHIP_TO; ?></td>
										<?php 
											if (!empty($REMARK_SHORT_LT) ) {
												echo '<td rowspan="3" style="width:12%" class="none_border">'. $REMARK_SHORT_LT .'</td>';
											} 
										?>
									</tr>
									<tr class="bold pt9 padding none_border">
										<td class="none_border aleft xanh_1 pt12" colspan="3"><?php echo $NUMBER_NO; ?></td>
										<td class="none_border aleft xanh_2" colspan="1">Request date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $REQ; ?></td>
										<td class="none_border aleft hong_1" colspan="1">RBO:</td>
										<td class="none_border aleft hong_1" colspan="9"><?php echo $RBO; ?></td>
									</tr>
									<tr class="bold pt9 padding">
										<td class="none_border aleft xanh_1 pt11" colspan="3">Ngay lam don<span style="float:right;padding-right:5px" class="create_date"><?php echo $SAVE_DATE; ?></td>
										<td class="none_border aleft xanh_2" colspan="1">Promise date:</td>
										<td class="none_border aleft xanh_2" colspan="3"><?php echo $PD; ?></td>
										<td class="none_border aleft hong_1" colspan="1">CS name:</td>
										<td class="none_border aleft hong_1" colspan="9"><?php echo $CS; ?></td>
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
										<td class="xanh_1" colspan="2">Paper infos</td>
										<td class="cam_1" colspan="3">Need Q'ty</td>
										<td class="vang" colspan="2">Size lable</td>
										<td class="hong_1" colspan="3">Ink infos</td>
										<td class="xanh_4" style="width:3%" rowspan="2">Machine</td>
									</tr>
									<tr class="pt7 cam_1" style="height:30px;">
										<td colspan="">SO#</td>
										<td colspan="">Item</td>
										<td colspan="">Internal item</td>
										<td colspan="">RBO</td>
										<td colspan=""><?php echo (!empty($COLOR_BY_SIZE && strpos($RBO, "NIKE") !== FALSE)) ? 'Color' : 'Item'; ?></td>
										<td colspan="">Q'ty</td>
										<td colspan="">Material code</td>
										<td colspan="">Description</td>
										<td colspan="">EA</td>
										<td colspan="">YD</td>
										<td colspan="">MT</td>
										<td colspan="">Length</td>
										<td colspan="">Width</td>
										<td colspan="">Ink code</td>
										<td colspan="">Description</td>
										<td colspan="">Q'ty</td>
									</tr>
									<!-- begin material -->
									<?php
									if (!empty($arr_supply)) {
										$STT = 0;
										$TOTAL_QTY = 0;
										$COUNT_LOT = count($arr_supply);
										for ($i = 0; $i < $count_data; $i++) {
											if (!empty($arr_supply[$i])) {
												$STT++;
												$SO_LINE = $arr_supply[$i]['SO_LINE'];
												$ITEM = trim($arr_supply[$i]['ITEM']);
												$INTERNAL_ITEM = $arr_supply[$i]['INTERNAL_ITEM'];
												$ITEM_DES = $arr_supply[$i]['ITEM_DES'];
												$QTY = $arr_supply[$i]['QTY'];
												$TOTAL_QTY += $QTY;
												$MATERIAL_CODE = $arr_supply[$i]['MATERIAL_CODE'];
												$MATERIAL_DES = $arr_supply[$i]['MATERIAL_DES'];
												$EA_SHT = $arr_supply[$i]['EA_SHT'];
												$YD = $arr_supply[$i]['YD'];
												$MT = $arr_supply[$i]['MT'];
												$MATERIAL_QTY = $arr_supply[$i]['MATERIAL_QTY'];
												$LENGTH = $arr_supply[$i]['LENGTH'];
												$WIDTH = $arr_supply[$i]['WIDTH'];
												$INK_CODE = $arr_supply[$i]['INK_CODE'];
												$INK_DES = $arr_supply[$i]['INK_DES'];
												$INK_QTY = $arr_supply[$i]['INK_QTY'];
												$MULTIPLE = $arr_supply[$i]['MULTIPLE'];
												$SAMPLE = $arr_supply[$i]['SAMPLE'];
												$SO_UPS = $arr_supply[$i]['SO_UPS'];
												
												// @tandoan 20200921: Thêm vào Machine
												$bomData = getBOMData($INTERNAL_ITEM);
												$machineShow = !empty($bomData) ? trim($bomData['MACHINE']) : '';
												?>
												<tr class="pt6" <?php echo ($COUNT_LOT < 5) ? 'style="height:42px;"' : ''; ?>>
													<td colspan=""><?php echo $STT; ?></td>
													<td class="pt12" style="width:12%" colspan=""><?php echo $SO_LINE; ?></td>
													<td class="pt9" style="width:9%" colspan=""><?php echo $ITEM; ?></td>
													<td style="width:9%" colspan=""><?php echo $INTERNAL_ITEM; ?></td>
													<td style="width:6%" colspan=""><?php echo $RBO; ?></td>
													<td style="width:4%" colspan=""><?php echo (strlen($ITEM_DES) > 70) ? (substr($ITEM_DES, 0, 70) . '...') : ($ITEM_DES); ?></td>
													<td class="pt10" style="width:4%" colspan=""><?php echo $QTY > 0 ? number_format($QTY) : '-'; ?></td>
													<td style="width:8%" colspan=""><?php echo $MATERIAL_CODE; ?></td>
													<td style="width:8%" colspan=""><?php echo (strlen($MATERIAL_DES) > 30) ? (substr($MATERIAL_DES, 0, 30) . '...') : ($MATERIAL_DES); ?></td>
													<td class="pt7" style="width:3%" colspan=""><?php echo $EA_SHT > 0 ? number_format($EA_SHT) : '-'; ?></td>
													<td class="pt7" style="width:3%" colspan=""><?php echo $YD > 0 ? number_format($YD) : '-'; ?></td>
													<td class="pt7" style="width:3%" colspan=""><?php echo $MT > 0 ? number_format($MT) : '-'; ?></td>
													<td style="width:4%" colspan=""><?php echo $LENGTH; ?></td>
													<td style="width:4%" colspan=""><?php echo $WIDTH; ?></td>
													<td class="pt7" style="width:10%" colspan=""><?php echo $INK_CODE; ?></td>
													<td colspan="" style="width:10%"><?php echo (strlen($INK_DES) > 70) ? (substr($INK_DES, 0, 70) . '...') : ($INK_DES); ?></td>
													<td class="pt7" style="width:3%" colspan=""><?php echo $INK_QTY > 0 ? number_format($INK_QTY) : '-'; ?></td>
													<td class="pt7" style="width:3%" colspan=""><?php echo $machineShow; ?></td>
												</tr>
									<?php }
										}
									} ?>
									<!-- end material -->
									<!-- check to add material temp-->
								</table>
							</td>
						</tr>
						<tr class="blank">
							<td colspan="17" class="none_border">
								<?php
								$min_height = '';
								if ($STT >= 10) {
									$min_height = 'style="height:20px;"';
								}
								if ($STT >= 15 && $STT <= 15 || $STT >= 25 && $STT <= 34 || $STT >= 44 && $STT <= 53 || $STT >= 63 && $STT <= 72) {
									echo '<p style="page-break-after:always;"></p>';
								}
								?>
								&nbsp;
							</td>
						</tr>
						<!-- Các remark tại đây  -->
						<tr class="bold pt6 padding" <?php echo $min_height; ?>>
							<td colspan="2" class="aleft none_border">
								QUI CÁCH ĐÓNG GÓI /PACKAGING
							</td>
							<td colspan="" class="none_border"></td>
							<!-- Các remark tại đây  -->
							<td colspan="14" rowspan="6" class="none_border">
								<!-- @tandoan20191023: 
										internal item: 1-272128-000-00, 1-272831-000-00, 1-273932-000-00
										Hiển thị:  ITEM COMBINED OFFSET & THERMAL. Update:  ITEM
										Anh tri.pham yêu cầu 
									Anh Quang gửi mail (Item Combine với các production line). Thêm trường hợp
									CONBINE THERMAL & HANGTAG
								-->
								<!-- Thay đổi Rule: Lấy Combine theo Remark 3 (master bom) -->
								<?php
									//Thermal Combine với RFID
									if (!empty($REMARK_3_PACKING) ) {
										if (strpos(strtolower($REMARK_3_PACKING), 'nike') !==false ) {
											$REMARK_3_PACKING = '';
										} else {
											if (strpos($REMARK_ITEM, 'HANGLE') !==false ) {
												$REMARK_3_PACKING = '';
											}
										}
										
									}
									

									// /** 1.  ITEM: COMBINE OFFSET & THERMAL @tandoan */
									// $INTERNAL_ITEM_COMBINED_OFFSET_CHECK = [
									// 	'1-272128-000-00',
									// 	'1-272831-000-00',
									// 	'1-273932-000-00',
									// 	'1-273124-000-00'
									// ];

									// foreach ($INTERNAL_ITEM_COMBINED_OFFSET_CHECK as $key => $INTERNAL_ITEM_COMBINED_OFFSET) {
									// 	$INTERNAL_ITEM_COMBINED_OFFSET = trim($INTERNAL_ITEM_COMBINED_OFFSET);
									// 	if ($INTERNAL_ITEM == $INTERNAL_ITEM_COMBINED_OFFSET) {
									// 		$REMARK_ITEM = 'COMBINED OFFSET & THERMAL';
									// 		break;
									// 	}
									// }

									// /** 2.  ITEM: COMBINE RFID & HANGTAG @tandoan */
									// $INTERNAL_ITEM_COMBINED_RFID_HANGTAG_CHECK = [
									// 	'4-226640-238-00',
									// 	'1-610002-000-03',
									// 	'1-217817-000-00',
									// 	'4-412468-238-00'
									// ];

									// foreach ($INTERNAL_ITEM_COMBINED_RFID_HANGTAG_CHECK as $key => $INTERNAL_ITEM_COMBINED_RFID_HANGTAG) {
									// 	$INTERNAL_ITEM_COMBINED_RFID_HANGTAG = trim($INTERNAL_ITEM_COMBINED_RFID_HANGTAG);
									// 	if ($INTERNAL_ITEM == $INTERNAL_ITEM_COMBINED_RFID_HANGTAG) {
									// 		$REMARK_ITEM = 'COMBINE RFID & HANGTAG';
									// 		break;
									// 	}
									// }

									/** 3.  ITEM: ADICOM @tandoan */
									$INTERNAL_ITEM_ADICOM_CHECK = [
										'B248868',
										'B248789',
										'B31572',
										'B31570',
										'B87286',
										'P186880',
										'B86940',
										'B87296',
										'B282466',
										'B965027',
										'B35024',
										'B34663',
										'B94944',
										'B104391',
										'B361310',
										'B87402',
										'B262587',
										'B260480',
										'B268009',
										'B87399',
										'B87416',
										'B231822',
										'B267624',
										'B67779',
										'B53501',
										'B53502',
										'B35022',
										'B234300',
										'B250965',
										'B188136',
										'B276654',
										'B275531',
										'B289233',
										'B392876',
										'B268006',
										'B362425',
										'B401198',
										'1-098451-000-00',
										'1H014401-REV-00',
										'B87418',
										'1-138706-000-00',
										'1H013124-REV-00',
										'1-160168-000-00',
										'1-260943-000-00',
										'1H012350-REV-00-DS',
										'1H015532-000-00',
										'1-153568-000-00',
										'1H016693-000-00',
										'1-116903-000-00',
										'1-080362-000-00',
										'1H022509-000-00',
										'1-135596-000-00',
										'1-142922-000-00',
										'1-103744-000-00',
										'B38349',
										'B329444',
										'B41030',
										'B67776',
										'CB321379A',
										'CB524455A',
										'CB327910A',
										'B234343'
									];

									foreach ($INTERNAL_ITEM_ADICOM_CHECK as $key => $INTERNAL_ITEM_ADICOM) {
										$INTERNAL_ITEM_ADICOM = trim($INTERNAL_ITEM_ADICOM);
										if ($INTERNAL_ITEM == $INTERNAL_ITEM_ADICOM) {
											$REMARK_ITEM .= 'ADICOM';
											break;
										}
									}

									/** 4.  ITEM: show khách hàng CHUTEX  @tandoan*/
									$INTERNAL_ITEM_CHUTEX_CHECK = [
										'1-271410-005-00',
										'1-271410-001-00'
									];
									foreach ($INTERNAL_ITEM_CHUTEX_CHECK as $key => $INTERNAL_ITEM_CHUTEX) {
										if ($INTERNAL_ITEM == $INTERNAL_ITEM_CHUTEX) {
											if (strpos(strtoupper($SHIP_TO), 'CHUTEX') !== false) {
												if (!empty($PACKING_INSTRUCTIONS_NEW)) {
													$REMARK_ITEM .= $PACKING_INSTRUCTIONS_NEW;
												} else {
													$REMARK_ITEM .= ' - Tat ca cac line dong chung 1 thung hang';
												}
												break;
											}
										}
									}

									/** 5.  note UNIQLO dua vao RBO  @tandoan */
									if (strpos($RBO, 'UNIQLO') !== false) {
										$REMARK_RBO = 'UNIQLO';
									}

									/** 6.  KHONG KIM LOAI và remark 1 (CHUYEN PREPRESS LAM LAYOUT)  @tandoan*/
									if (!empty($PACKING_INSTRUCTIONS_NEW)) {
										if (strpos(strtoupper($PACKING_INSTRUCTIONS_NEW), "KHONG KIM LOAI") !== false) {
											if (!empty($REMARK_1)) {
												$REMARK_PACKING_INSTRUCTIONS = 'KHONG KIM LOAI. ' . $REMARK_1;
											} else {
												$REMARK_PACKING_INSTRUCTIONS = 'KHONG KIM LOAI';
											}
										} else {
											$REMARK_PACKING_INSTRUCTIONS = $REMARK_1;
										}
									} else {
										$REMARK_PACKING_INSTRUCTIONS = $REMARK_1;
									}

									// Hiển thị thông tin các remark @tandoan
									// $REMARK_RBO = "REMARK RBO";
									// $REMARK_SAMPLE = "SAMPLE";
									// $REMARK_ITEM = "REMARK ITEM";
									// $REMARK_PACKING_INSTRUCTIONS = "REMARK PACKING INSTRUCTION";
									// $remarkCCO = 'DÁN COO "MADE IN KOREA" TRÊN RIBBON';
									// $remarkSpecialItem = "HÀNG ĐẶC BIỆT";

									echo '<div class="remark">';
										echo '<div class="remark-left">';
											
											if (!empty($REMARK_RBO)) {
												echo '<div class="remark-sample">';
													echo $REMARK_RBO;
												echo '</div>';
											}
											if (!empty($REMARK_SAMPLE)) {
												echo '<div class="remark-sample">';
													echo $REMARK_SAMPLE;
												echo '</div>';
											}
											
											if (!empty($REMARK_IPPS)) {
												echo '<div class="remark-ipps">';
													echo $REMARK_IPPS;
												echo '</div>';
											}
											
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
											if (!empty($REMARK_ITEM) ) {
												echo '<div class="remark-right-detail ">';
													echo $REMARK_ITEM;
												echo '</div>';
											}

											if (!empty($REMARK_3_PACKING) ) {
												echo '<div class="remark-right-detail ">';
													echo $REMARK_3_PACKING;
												echo '</div>';
											}

											if (!empty($REMARK_PACKING_INSTRUCTIONS) ) {
												echo '<div class="remark-right-detail ">';
													echo $REMARK_PACKING_INSTRUCTIONS;
												echo '</div>';
											}

											if (!empty($remarkCCO) ) {
												echo '<div class="remark-right-detail ">';
													echo $remarkCCO;
												echo '</div>';
											}

											

											// // if (!empty($remarkSpecialItem) ) {
											// // 	echo '<div class="remark-right-detail ">';
											// // 		echo $remarkSpecialItem;
											// // 	echo '</div>';
											// // }

										echo '</div>';

									echo '</div>';

								?>
							</td>
						</tr>
						<tr class="bold padding" <?php echo $min_height; ?>>
							<td colspan="2" class="pt20 vang">
								<?php echo number_format($TOTAL_QTY); ?>
							</td>
							<td class="none_border aleft pt6">
								
								<?php 
								    $unit = ($NHOM == 'IPPS' || $NHOM == 'FG' ) ? $UOM : 'PCS';
									echo $unit;
								?>
							</td>
						</tr>
						<tr class="blank" <?php echo $min_height; ?>>
							<td colspan="2" class="none_border">&nbsp;</td>
							<td class="none_border">&nbsp;</td>
							
						</tr>
						<?php
						if ($STT >= 13 && $STT <= 13) {
							?>
							<tr class="blank">
								<td colspan="17" class="none_border">
									<p style="page-break-after:always;"></p>&nbsp;
								</td>
							</tr>
						<?php } ?>
						<tr class="bold pt8" <?php echo $min_height; ?>>
							<td colspan="2" class="none_border hong_2 aleft padding">
								Printing by:
							</td>
							<td colspan="14" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt8" <?php echo $min_height; ?>>
							<td style="width:15%" colspan="2" class="hong_2 aleft padding none_border">
								Quality checked by:
							</td>
							<td colspan="14" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt8" <?php echo $min_height; ?>>
							<td colspan="2" class="hong_2 aleft padding none_border">
								Packed by:
							</td>
							<td colspan="14" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="blank" <?php echo $min_height; ?>>
							<td colspan="7" class="none_border">&nbsp;</td>
							<td colspan="10" class="none_border">&nbsp;</td>
						</tr>
						<?php
						if ($STT >= 10 && $STT <= 11) {
							?>
							<tr class="blank">
								<td colspan="17" class="none_border">
									<p style="page-break-after:always;"></p>&nbsp;
								</td>
							</tr>
						<?php } ?>
						<tr class="bold pt8 padding" <?php echo $min_height; ?>>
							<td colspan="1" class="aleft none_border" style="width:2%">
								Remark:
							</td>
							<td colspan="10" class="pt14 aleft xanh_2 none_border" style="position:relative">
								<?php echo $REMARK_2; ?>
								<?php require_once($pathERP . "/print/thermal_worldon.php"); ?>
							</td>
							<td colspan="6" class="none_border" >
								&nbsp;  
							</td>
						</tr>
						<tr class="bold pt8 padding" <?php echo $min_height; ?>>
							<td colspan="1" class="none_border">
								&nbsp; 
							</td>
							<td colspan="10" class="pt14 aleft xanh_2 none_border">
								<?php echo $REMARK_3; ?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp; <?php // echo $REMARK_SHORT_LT; ?>
							</td>
						</tr>
						<tr class="bold pt8 padding" <?php echo $min_height; ?>>
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="pt14 aleft cam_1 none_border">
								<?php echo $REMARK_4; ?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp; 
							</td>
						</tr>
						<tr class="bold pt8 padding" <?php echo $min_height; ?>>
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="10" class="pt14 aleft cam_1 none_border">
								<?php echo $REMARK_5; ?>
							</td>
							<td colspan="6" class="none_border">
								&nbsp;
							</td>
						</tr>
						<tr class="bold pt8 padding" <?php echo $min_height; ?>>
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td colspan="8" class="pt14 aleft cam_1 none_border">
								<?php echo !empty($REMARK_6) ? $REMARK_6 : 'Sử dụng đúng tem mực'; ?>
							</td>
							<td colspan="2" class="pt14 aleft cam_1 none_border">
								<?php echo '<span style="text-transform:uppercase" >' . $REMARK_MAKALOT . '</span>'; ?>
							</td>
							<?php 
								if (!empty($CHI_TIET_KIT) ) {		
							?>
								<td colspan="2" class="pt14 aleft cam_1 none_border">
									<?php echo '<span style="font-weight:bold;" >' . $CHI_TIET_KIT . '</span>'; ?>
								</td>
							<?php
								}
							?>
							<td colspan="6" class="none_border">
								&nbsp; 
							</td>
						</tr>
						<tr class="blank" <?php echo $min_height; ?>>
							<td colspan="17" class="none_border">&nbsp;</td>
						</tr>
						<!-- Trace Ability-->
						<tr class="bold pt6" <?php echo $min_height; ?>>
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
						<tr class="pt6" <?php echo $min_height; ?>>
							<td colspan="1" class="none_border">
								&nbsp;
							</td>
							<td rowspan="3" class="none_border_top">
								Trace <br />
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
						<tr class="pt6" <?php echo $min_height; ?>>
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
						<tr class="pt6" <?php echo $min_height; ?>>
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