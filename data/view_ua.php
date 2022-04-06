<?php   

require_once("../Database.php");

$conn = _conn();

$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
$urlRoot = str_replace('data/','',$urlRoot);
header("Content-type:text/xml");//set content type and xml tag
echo "<?xml version=\"1.0\"?>";
$fields = '*';
    // to do process so kho if(type_worst_vertical = 100-SB1) 10,5
    $sql = "SELECT $fields FROM master_item where ACTIVE=1"
    $rowsResult = MiQuery($sql, $conn);
	if ($conn ) mysqli_close($conn);
if(count($rowsResult)>0){ 
	echo("<rows>");
	if(!empty($rowsResult)){ 
		$cellStart = "<cell><![CDATA[";
        $cellEnd = "]]></cell>";
		foreach ($rowsResult as $row){
			$ID = $row['ID'];
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
			$UPDATED_BY = $row['UPDATED_BY'];
			echo("<row id='".$ID."'>");
				echo( $cellStart);  // LENGTH
					echo(0);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ITEM_CODE);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ORDER_ITEM);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ORACLE_MATERIAL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($DESCRIPTION_MATERIAL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($WIDTH);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($HEIGHT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($INK_CODE);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($INK_DESCRIPTION);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($UP);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($MATERIAL_UNIT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($SET);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($VAT_TU_CHIA_3);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($LAYOUT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($DANG_ROLL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($VAI);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($SIPS_VT_X2);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($UPDATED_BY);  //value for product name                 
				echo( $cellEnd);
			/*
			if($deleteNO){
				echo( $cellStart);  // LENGTH
				echo $link;  //value for product name                 
				echo( $cellEnd);
			}			
			*/
			echo("</row>");
		}
		// add 10 
		for($i=1;$i<=10;$i++){
			$ID = 'new_id_'.$i;
			$ITEM_CODE = '';
			$ORDER_ITEM = '';
			$ORACLE_MATERIAL = '';
			$DESCRIPTION_MATERIAL = '';
			$WIDTH = '';
			$HEIGHT = '';
			$INK_CODE = '';
			$INK_DESCRIPTION = '';
			$UP = '';
			$MATERIAL_UNIT = '';
			$SET = '';
			$VAT_TU_CHIA_3 = '';
			$LAYOUT = '';
			$DANG_ROLL = '';
			$VAI = '';
			$SIPS_VT_X2 = '';
			$UPDATED_BY = '';		
			echo("<row id='".$ID."'>");
				echo( $cellStart);  // LENGTH
					echo(0);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ITEM_CODE);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ORDER_ITEM);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($ORACLE_MATERIAL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($DESCRIPTION_MATERIAL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($WIDTH);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($HEIGHT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($INK_CODE);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($INK_DESCRIPTION);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($UP);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($MATERIAL_UNIT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($SET);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($VAT_TU_CHIA_3);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($LAYOUT);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($DANG_ROLL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($VAI);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($SIPS_VT_X2);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($UPDATED_BY);  //value for product name                 
				echo( $cellEnd);			
			echo("</row>");
		}
	}
	echo("</rows>");
}else{
	echo("<rows></rows>");
}
?>