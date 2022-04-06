<?php
$sql_size = "SELECT * FROM avery_rfid.save_size WHERE id_save_item='$id'";
$result_size = MiQuery($sql_size,$dbMi_138);
$arr_size = [];	
if(!empty($result_size)){
    foreach ($result_size as $key => $size){
        $arr_size[$key]['SIZE'] 	            = $size['SIZE'];
        $arr_size[$key]['LABEL_ITEM'] 	        = $size['LABEL_ITEM'];
        $arr_size[$key]['BASE_ROLL'] 	        = $size['BASE_ROLL']; 
        $arr_size[$key]['QTY'] 	                = $size['QTY']; 
    }  
}
?>