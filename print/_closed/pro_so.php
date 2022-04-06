<?php
$bold_so = true;
$sql_SO = "SELECT * FROM au_avery.wv_save_so_selected WHERE id_save_item='$id'";
$result_SO = MiQuery($sql_SO,$dbMi_138);
$arr_so = [];	
$total_quantity = 0;
if(!empty($result_SO)){    
    foreach ($result_SO as $key => $SO) {
        $total_quantity+=$SO['qty'];        
        $arr_so[$key]['so_line'] 	= $SO['so_line'];
        $arr_so[$key]['so_gio'] 	= $SO['SO_GIO'];
        if(empty($arr_so[$key]['so_line'])){
            $arr_so[$key]['so_line'] = $id;
        }
        $quantity = number_format($SO['qty']); 
        $arr_so[$key]['qty'] = $quantity; 
        $arr_so[$key]['SO_SIZE'] = $SO['SO_SIZE'];   
        $arr_so[$key]['RIBBON'] = $SO['RIBBON'];   
        $arr_so[$key]['WARP_YARN'] = $SO['WARP_YARN'];    
    }
    $total_quantity = number_format($total_quantity);
}
?>