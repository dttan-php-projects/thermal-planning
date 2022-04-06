<?php 
require_once("../Database.php");

$conn = _conn();

$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
$urlRoot = str_replace('data/','',$urlRoot);
header("Content-type:text/xml");//set content type and xml tag
echo "<?xml version=\"1.0\"?>";
// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
$FORM_TYPE = '';
if(!empty($_COOKIE["print_type_thermal"])){
	$FORM_TYPE = $_COOKIE["print_type_thermal"];
}
$sql = "SELECT distinct SO_LINE,ITEM,CREATED_TIME FROM oracle_download";  
// echo $sql;die;
$rowsResult = MiQuery($sql, $conn);
if ($conn ) mysqli_close($conn);
// check role
$arrayRole = ['minh.vo','phung.le','hang.nguyenthu','tri.pham','vien.trinh','yen.thai','thitram.nguyen'];
$user = '';
if(!empty($_COOKIE["VNRISIntranet"])){
	$user = $_COOKIE["VNRISIntranet"];
	if(in_array($user,$arrayRole)){
		$deleteNO = 1;
	}else{
		$deleteNO = 0;
	}
}    
if(count($rowsResult)>0){ 
	$header = '<head>
					<column width="85" type="ed" align="left" sort="str">SO LINE</column>
					<column width="140" type="ed" align="left" sort="str">ITEM</column>
					<column width="140" type="ed" align="left" sort="str">CREATED TIME</column>
				</head>';
	echo("<rows>");	
	echo $header;
	if(!empty($rowsResult)){ 
		$cellStart = "<cell><![CDATA[";
        $cellEnd = "]]></cell>";
		$ID = 0;
		foreach ($rowsResult as $row){
			$ID++;
			$SO_LINE = $row['SO_LINE']; 
			$ITEM = $row['ITEM'];
			$CREATED_TIME = $row['CREATED_TIME'];		
			echo("<row id='".$ID."'>");
			echo( $cellStart);  // LENGTH
				echo($SO_LINE);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($ITEM);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($CREATED_TIME);  //value for product name                 
			echo( $cellEnd);			
			echo("</row>");
		}
	}
	echo("</rows>");
}else{
	echo("<rows></rows>");
}
?>