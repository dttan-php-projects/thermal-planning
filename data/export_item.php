<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('max_execution_time',300);  // set time 5 minutes
require_once ("../Database.php");
$conn = _conn();

require_once "./Module/PHPExcel/IOFactory.php";
$fields = '*';
// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
$sql = "SELECT $fields FROM master_item where ACTIVE=1";
$rowsResult = MiQuery($sql, $conn);
if ($conn ) mysqli_close($conn);

if(!empty($rowsResult)){ 
	foreach ($rowsResult as $row){
		$ITEM_CODE = $row['ITEM_CODE'];
		$ORDER_ITEM = $row['ORDER_ITEM'];
		$ORACLE_MATERIAL = $row['ORACLE_MATERIAL'];
		$DESCRIPTION_MATERIAL = $row['DESCRIPTION_MATERIAL'];
		$WIDTH = $row['WIDTH'];
		$HEIGHT = $row['HEIGHT'];
		$INK_CODE = $row['INK_CODE'];
		$INK_DESCRIPTION = $row['INK_DESCRIPTION'];
		$UP = $row['UP'];
		$MATERIAL_UNIT = $row['MATERIAL_UNIT'];
		$SET = $row['SET'];
		$VAT_TU_CHIA_3 = $row['VAT_TU_CHIA_3'];
		$LAYOUT = $row['LAYOUT'];
		$DANG_ROLL = $row['DANG_ROLL'];
		$VAI = $row['VAI'];
		$SIPS_VT_X2 = $row['SIPS_VT_X2'];
		$data[] = [$ITEM_CODE,$ORDER_ITEM,$ORACLE_MATERIAL,$DESCRIPTION_MATERIAL,$WIDTH,$HEIGHT,$INK_CODE,$INK_DESCRIPTION,$UP,$MATERIAL_UNIT,$SET,$VAT_TU_CHIA_3,$LAYOUT,$DANG_ROLL,$VAI,$SIPS_VT_X2];
	}
}
//Khởi tạo đối tượng
$excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
$excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
//$excel->getActiveSheet()->setTitle('demo ghi dữ liệu');

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
$excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$excel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
$excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$excel->getActiveSheet()->getColumnDimension('D')->setWidth(95);
$excel->getActiveSheet()->getColumnDimension('E')->setWidth(33);
$excel->getActiveSheet()->getColumnDimension('F')->setWidth(36);
$excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$excel->getActiveSheet()->getColumnDimension('H')->setWidth(90);
$excel->getActiveSheet()->getColumnDimension('I')->setWidth(11);
$excel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
$excel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$excel->getActiveSheet()->getColumnDimension('L')->setWidth(17);
$excel->getActiveSheet()->getColumnDimension('M')->setWidth(40);
$excel->getActiveSheet()->getColumnDimension('N')->setWidth(19);
$excel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$excel->getActiveSheet()->getColumnDimension('P')->setWidth(11);
//Xét in đậm cho khoảng cột
$excel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
$excel->getActiveSheet()->getStyle('A1:P'.(count($rowsResult)+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
/**
 * |A1|B1|C1|..|n1|
 * |A2|B2|C2|..|n1|
 * |..|..|..|..|..|
 * |An|Bn|Cn|..|nn|
 */
$excel->getActiveSheet()->setCellValue('A1', 'MA HANG - ITEM CODE');
$excel->getActiveSheet()->setCellValue('B1', 'NHAN - ORDER ITEM');
$excel->getActiveSheet()->setCellValue('C1', 'MA VAT TU - ORACLE MATERIAL');
$excel->getActiveSheet()->setCellValue('D1', 'Description MATERIAL');
$excel->getActiveSheet()->setCellValue('E1', 'Kich thuoc nhan chieu dai (DVT:mm)');
$excel->getActiveSheet()->setCellValue('F1', 'Kich thuoc nhan chieu rong (DVT:mm)');
$excel->getActiveSheet()->setCellValue('G1', 'MA MỰC ORACLE MATERIAL');
$excel->getActiveSheet()->setCellValue('H1', 'Description  MỰC');
$excel->getActiveSheet()->setCellValue('I1', 'SỐ UPS');
$excel->getActiveSheet()->setCellValue('J1', 'DON VI VAT TU');
$excel->getActiveSheet()->setCellValue('K1', 'PAXAR SET');
$excel->getActiveSheet()->setCellValue('L1', 'PAXAR VT CHIA 3');
$excel->getActiveSheet()->setCellValue('M1', 'PAXAR LAYOUT');
$excel->getActiveSheet()->setCellValue('N1', 'PAXAR DANG_ROLL');
$excel->getActiveSheet()->setCellValue('O1', 'PAXAR VAI');
$excel->getActiveSheet()->setCellValue('P1', 'SIPS VT X2');
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
$numRow = 2;
foreach($data as $row){
	$excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
	$excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
	$excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
	$excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
	$excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
	$excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
	$excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
	$excel->getActiveSheet()->setCellValue('H'.$numRow, $row[7]);
	$excel->getActiveSheet()->setCellValue('I'.$numRow, $row[8]);
	$excel->getActiveSheet()->setCellValue('J'.$numRow, $row[9]);
	$excel->getActiveSheet()->setCellValue('K'.$numRow, $row[10]);
	$excel->getActiveSheet()->setCellValue('L'.$numRow, $row[11]);
	$excel->getActiveSheet()->setCellValue('M'.$numRow, $row[12]);
	$excel->getActiveSheet()->setCellValue('N'.$numRow, $row[13]);
	$excel->getActiveSheet()->setCellValue('O'.$numRow, $row[14]);
	$excel->getActiveSheet()->setCellValue('P'.$numRow, $row[15]);
	$numRow++;
}
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
header('Content-type: application/vnd.ms-excel');
$filename = date("d_m_Y__H_i_s").".xls";
header("Content-Disposition: attachment; filename='$filename'");
PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');