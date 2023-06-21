<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:');
    header('Pragma:');
    header('Content-type: text/html; charset=utf8'); 
?>
<?php
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);    
    require_once("f_DB.php");
    
    //変数
    $edit_list_data = array();
    
    //処理
    $con = dbconect();
    $table_num = $form_ini_array[$_SESSION["pagename"]]['table_num'];
    $table_name = $form_ini_array[$table_num]['table_name'];
    $sql = "SELECT * FROM ".$table_name." WHERE ".$form_ini_array[$table_num."id"]['column_name']." = '".$_POST["edit_id"]."';";
    $result = $con->query($sql) or ($judge = true);
    $counter = 0;
    $edit_form_num = explode(',', $form_ini_array[$_SESSION["pagename"]]['edit_form_num']);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 0; $i < count($edit_form_num); $i++)
        {
            $column = $form_ini_array[$edit_form_num[$i]]["column_name"];
            $edit_list_data[$i]["form_num"] = $edit_form_num[$i];            
            $edit_list_data[$i]["field_type"] = $form_ini_array[$edit_form_num[$i]]["field_type"];
            if($column == "order_month")
            {
                $edit_list_data[$i]["value"] = date('Y-m',  strtotime($result_row[$column]));
            }
            else
            {
                $edit_list_data[$i]["value"] = $result_row[$column];
            }  
            $counter++;
        }
        //更新時間情報を付与する
        $edit_list_data[$counter]["form_num"] = "updatetime";
        $edit_list_data[$counter]["value"] = $result_row['update_time'];
        $edit_list_data[$counter]["field_type"] = "9999";
    }

    echo json_encode($edit_list_data);
?>

