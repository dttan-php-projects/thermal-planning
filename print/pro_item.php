<?php
$SAVE_DATE = $result_item['CREATED_TIME']?$result_item['CREATED_TIME']:"";	
$SAVE_DATE = date('d-M-y H:i',strtotime($SAVE_DATE));
$NUMBER_NO = $result_item['NUMBER_NO']?$result_item['NUMBER_NO']:"";
$PD 	   = $result_item['PD']?$result_item['PD']:"";
// Trường hợp định dạng dấu chấm: PD = ngày làm lệnh thực tế + 5 (nếu chủ nhật + 1)
if (strpos($PD, '.') !== false ) {
	// $PD = date("d.m.Y",strtotime($PD));
} else {
	$PD = date("d-M-y",strtotime($PD));
}

// $PD = date('d-M-y',strtotime($PD));
$REQ 	   = $result_item['REQ']?$result_item['REQ']:"";
$REQ = date('d-M-y',strtotime($REQ));
$ORDERED 	   = $result_item['ORDER']?$result_item['ORDER']:"";
$ORDERED = date('d-M-y',strtotime($ORDERED));
$SHIP_TO = $result_item['SHIP_TO']?$result_item['SHIP_TO']:"";
$RBO = $result_item['RBO']?$result_item['RBO']:"";
$CS = $result_item['CS']?$result_item['CS']:'';
$QTY = $result_item['QTY']?$result_item['QTY']:0;
$REMARK_1 = !empty($result_item['REMARK_1'])?$result_item['REMARK_1']:"";
$REMARK_2 = !empty($result_item['REMARK_2'])?$result_item['REMARK_2']:"";
$REMARK_3 = !empty($result_item['REMARK_3'])?$result_item['REMARK_3']:"";
$REMARK_4 = !empty($result_item['REMARK_4'])?$result_item['REMARK_4']:"";
$REMARK_5 = !empty($result_item['REMARK_5'])?$result_item['REMARK_5']:"";
$REMARK_6 = !empty($result_item['REMARK_6'])?$result_item['REMARK_6']:"";
if($QTY){
    $QTY = number_format($QTY);
}
$THERMAL_TEXT = '';
$COLOR_BY_SIZE = !empty($result_item['COLOR_BY_SIZE'])?$result_item['COLOR_BY_SIZE']:0;
$DATA_RECEIVED = '';
$SO_LAN = '';
if($FORM_TYPE=='paxar'){
	$THERMAL_TEXT = 'THERMAL PAXAR';
}elseif($FORM_TYPE=='trim'){
	$THERMAL_TEXT = 'THERMAL TRIM';
	$DATA_RECEIVED = !empty($result_item['DATA_RECEIVED'])?$result_item['DATA_RECEIVED']:"&nbsp;";
	$SO_LAN = !empty($result_item['SO_LAN'])?$result_item['SO_LAN']:"";
	if(!empty($SO_LAN)){
		$SO_LAN = "file-".$SO_LAN;
	}
}
elseif($FORM_TYPE=='sips'){
	$THERMAL_TEXT = 'THERMAL SIPS';
	$DATA_RECEIVED = !empty($result_item['DATA_RECEIVED'])?$result_item['DATA_RECEIVED']:"&nbsp;";
	$SO_LAN = !empty($result_item['SO_LAN'])?$result_item['SO_LAN']:"";
	if(!empty($SO_LAN)){
		$SO_LAN = "file-".$SO_LAN;
	}
}
?>