<?php
	
	date_default_timezone_set('Asia/Ho_Chi_Minh'); 
	ini_set('max_execution_time',3000);  // set time 5 minutes
	ini_set('memory_limit', '512M');
	require_once ("../Database.php");

	function getMasterItem($internal_item) 
	{
		$internal_item = trim($internal_item);
		$results = array();
		$conn = _conn();

		$sql = "SELECT * FROM master_bom WHERE `INTERNAL_ITEM` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';

		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) return '';

		if ($conn ) mysqli_close($conn);

		return $results;
	}

	function getNHOM($internal_item) 
	{
		$internal_item = trim($internal_item);
		$results = array();
		$conn = _conn();

		$sql = "SELECT `NHOM` FROM master_bom WHERE `internal_item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';

		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) return '';

		if ($conn ) mysqli_close($conn);

		return trim(strtoupper($results['NHOM']));
	}

	function getKitDetail($internal_item) 
	{
		$internal_item = trim($internal_item);
		$results = array();
		$conn = _conn();

		$sql = "SELECT `CHI_TIET_KIT` FROM master_bom WHERE `internal_item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';

		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) return '';

		if ($conn ) mysqli_close($conn);

		return trim($results['CHI_TIET_KIT']);
	}

	function formatDate($value){
		return date('d-M-y',strtotime($value));
	}

	function cellColor($objPHPExcel, $cells, $color){
    
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                 'rgb' => $color
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '7094db')
                )
            )
        ));
	}
	
	// load packing and attachment
	function remarkPackingInstr($ORDER_NUMBER, $LINE_NUMBER) 
	{
		$results = array();
		$conn = _conn("au_avery");
		$conn2 = _conn();

		$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';
		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) {
			$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso_total WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
			$query = mysqli_query($conn, $sql);
			if (!$query ) return '';
			$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
			if (empty($results) ) {
				// get BOM data
				$sql = "SELECT REMARK_3_PACKING AS PACKING_INSTRUCTIONS  FROM oracle_download WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
				$query = mysqli_query($conn2, $sql);
				if (!$query ) return '';
				$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
			}
		}

		if ($conn ) mysqli_close($conn);
		if ($conn2 ) mysqli_close($conn2);

		return (!empty($results) ) ? trim($results['PACKING_INSTRUCTIONS']) : '';

	}

	function remnarkTrimCard($packingInstr) 
	{	
		$packingInstr = !empty($packingInstr) ? strtolower(trim($packingInstr) ) : '';
		
		$remark = '';
		
		$array = array(
			'trim card',
			'trimcard',
			'trim-card',
			'trim/card'
		);
		
		foreach ($array as $value ) {
			if (strpos($packingInstr, $value) !== false) {
				$remark = 'TRIM CARD';
				break;
			}
		}

		return $remark;
		
	}

	function getSecurityCol($masterItem) 
	{
		return !empty($masterItem) ? trim($masterItem['SECURITY'] ) : '';	
	}

	/** =========START ================================================================================ */
	
	require_once ($_SERVER["DOCUMENT_ROOT"]."/Module/PHPExcel.php");

	$FORM_TYPE = isset($_COOKIE['print_type_thermal'])?$_COOKIE['print_type_thermal']:'';
	$FROM_DATE = $_GET['from_date_value'];
	$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
	$TO_DATE = $_GET['to_date_value'];
	$TO_DATE = date('Y-m-d',strtotime($TO_DATE));

	$conn = _conn();

	$fields = 'FORM_TYPE,s_m.SO_LINE,s_m.CREATE_DATE,NUMBER_NO,REQ,PD,s_m.INTERNAL_ITEM,s_i.RBO,s_m.ITEM,ITEM_DES,s_m.QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,MATERIAL_QTY,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,SO_UPS,CREATED_BY,DATA_RECEIVED,SO_LAN,SHIP_TO,CS';
	$query = "SELECT $fields FROM save_material as s_m JOIN save_item as s_i ON s_i.NUMBER_NO = s_m.ID_SAVE_ITEM where (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') AND FORM_TYPE='$FORM_TYPE' order by s_m.ID asc";
	//echo $query;      die;  
	$queryAll = mysqli_query($conn, $query);
	$rowsResult = mysqli_fetch_all($queryAll, MYSQLI_ASSOC);

	if ($conn ) mysqli_close($conn);
	  
	// COUNT 
	$count = count($rowsResult);

	// Add new sheet
	$objPHPExcel = new PHPExcel();

	/** =========REPORT NO - FORM TYPE ================================================================================ */
		// // Add new sheet
		// 	$objPHPExcel->createSheet();

		// Add some data
			$objPHPExcel->setActiveSheetIndex(0);
		
		// active and set title
			$objPHPExcel->getActiveSheet()->setTitle('Report');

		// set Header, width
			$array_az = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W');
		// set format
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
			foreach($array_az as $key_c => $column) {
				for ($rowId = 1; $rowId <= $count+1; $rowId++ ) {
					if ($rowId == 1 ) {
						cellColor($objPHPExcel, $column . '1', '80ccff');	
					} else {
						cellColor($objPHPExcel, $column . $rowId, 'e6e6ff');
					}
					
				}
				
				if ($key_c == 0) {continue;}
				$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
			}
			
		// font-weigth
			$objPHPExcel->getActiveSheet()->getStyle("A1:W1")->getFont()->setBold( true );

		// color cell
			cellColor($objPHPExcel, 'A1', '80ccff');
		// set header value

			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'NGAY LAM DON');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'STT PLANNING');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'SO#');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'CUST REQ DATE');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'PROMISE DATE');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'MA HANG - ITEM CODE');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'RBO');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'NHAN - ORDER ITEM');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'SO LUONG CON NHAN - QTY');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'MA VAT TU - ORACLE MATERIAL');

			$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'DESCRIPTION - MATERIAL');
			$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'SO LUONG VAT TU CAN - QTY-EA');
			$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'SO LUONG VAT TU CAN - QTY-YD');
			$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'SO LUONG VAT TU CAN - QTY-MT');
			$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'KICH THUOC - CHIEU DAI');
			$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'KICH THUOC - CHIEU RONG');
			$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'MA MUC - ORACLE');
			$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'DESCRIPTION - MUC');
			$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'SO LUONG MUC CAN - QTY-MT');
			$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'SO UP');

			$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'CREATED BY');
			$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'SO KIT');
			$objPHPExcel->getActiveSheet()->SetCellValue('W1', 'NOTE 1');
			$objPHPExcel->getActiveSheet()->SetCellValue('X1', 'NOTE 2');

		
		$rowCount = 1;
		if ($count > 0 ) {
			foreach ($rowsResult as $row ) {
			
				$rowCount++;
	
				$NUMBER_NO = $row['NUMBER_NO'];			
				$SAVE_DATE = formatDate($row['CREATE_DATE']);
				$SO_LINE = $row['SO_LINE'];	
				
				$SO_LINE_ARR = explode('-', $SO_LINE);

				$packingInstr = remarkPackingInstr($SO_LINE_ARR[0], $SO_LINE_ARR[1]);
				$TRIMCARD = remnarkTrimCard($packingInstr);
				
				
				$PD = formatDate($row['PD']);
				$REQ = formatDate($row['REQ']);
				$INTERNAL_ITEM = $row['INTERNAL_ITEM'];
	
				// @tandoan: 20200709 - lấy số nhóm PAXAR, IPPS, ... để remark nếu là nhóm IPPS
				$masterItem = getMasterItem($INTERNAL_ITEM);
				$security = getSecurityCol($masterItem);
				$NHOM = getNHOM($INTERNAL_ITEM);
				
				$NOTE_NHOM = '';
				if (strpos($NHOM,'FG') !== false ) {
					$NOTE_NHOM = 'IPPS'; // chi @Yen yêu cầu 
				} else if (strpos($NHOM,'IPPS') !== false ) {
					$NOTE_NHOM = $NHOM;
				}

				if (strpos($NHOM,'IPPS') !==false || strpos($NHOM,'FG') !== false ) {
					if (strpos(strtoupper($security),'HÀNG ĐẶC BIỆT') !==false || strpos(strtoupper($security),'HANG DAC BIET') !==false ) {
						$NOTE_NHOM .= " - HÀNG ĐẶC BIỆT ";
					}
				}
				
				// $chi_tiet_kit = ($NHOM == 'IPPS' ) ? getKitDetail($INTERNAL_ITEM) : '';
	
				$RBO = $row['RBO'];
				if($FORM_TYPE=='paxar'){ $ITEM_DES = $row['ITEM_DES']; }else{ $ITEM_DES = $row['ITEM']; }			
				
				$QTY = !empty($row['QTY'])?$row['QTY']:'-';
				if($QTY!=='-'){ $QTY = (int)$QTY; }
	
				$MATERIAL_CODE = $row['MATERIAL_CODE'];
				$MATERIAL_DES = $row['MATERIAL_DES'];
				$EA_SHT = !empty($row['EA_SHT'])?$row['EA_SHT']:'-';
				if($EA_SHT!=='-'){	$EA_SHT = (int)$EA_SHT; }
				$YD = !empty($row['YD'])?$row['YD']:'-';
				if($YD!=='-'){ $YD = (int)$YD; }
				$MT = !empty($row['MT'])?$row['MT']:'-';
				if($MT!=='-'){ $MT = (int)$MT; }
				$MATERIAL_QTY = !empty($row['MATERIAL_QTY'])?$row['MATERIAL_QTY']:'-';
				if($MATERIAL_QTY!=='-'){ $MATERIAL_QTY = (int)$MATERIAL_QTY; }
				if($FORM_TYPE=='sips'||$FORM_TYPE=='trim'){ if($EA_SHT=='-'){ $EA_SHT = $MATERIAL_QTY; } }
				$LENGTH = $row['LENGTH'];
				$WIDTH = $row['WIDTH'];
				$INK_CODE = $row['INK_CODE'];
				$INK_DES = $row['INK_DES'];
				$INK_QTY = !empty($row['INK_QTY'])?$row['INK_QTY']:'-';
				if($INK_QTY!=='-'){ $INK_QTY = (int)$INK_QTY; }
				$SO_UPS = $row['SO_UPS'];
				$CREATED_BY = $row['CREATED_BY'];
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $SAVE_DATE);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $NUMBER_NO);
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $SO_LINE);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $REQ);
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $PD);
	
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $INTERNAL_ITEM );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $RBO);
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $ITEM_DES);
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $QTY);
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $MATERIAL_CODE);
	
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $MATERIAL_DES);
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $EA_SHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $YD);
				$objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $MT);
				$objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $LENGTH);
	
				$objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $WIDTH);
				$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $INK_CODE);
				$objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $INK_DES);
				$objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $INK_QTY);
				$objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $SO_UPS);
	
				$objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $CREATED_BY);
				$objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $QTY);
				$objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, $NOTE_NHOM);
				$objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $TRIMCARD);
	
	
			} // for

		}
		
		// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
			// ở đây mình lưu file dưới dạng excel2007
			header('Content-type: application/vnd.ms-excel');
			$filename = $FORM_TYPE."_".date("d_m_Y__H_i_s");
			header('Content-type: application/vnd.ms-excel;charset=utf-8');	
			header('Content-Encoding: UTF-8');
			header("Cache-Control: no-store, no-cache");
			header("Content-Disposition: attachment; filename=$filename.xlsx");

			PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007')->save('php://output');


	/** =============END =========================================================================================================== */