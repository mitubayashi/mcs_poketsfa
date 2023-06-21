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
    $sql = "SELECT t2.customer_name,t2.customer_abbreviation,t2.user_id,t1.authoer,t1.schedule_date,DATE_FORMAT(t1.start_time,'%H') AS starthour,DATE_FORMAT(t1.start_time,'%i') AS startmin,DATE_FORMAT(t1.end_time,'%H') AS endhour,DATE_FORMAT(t1.end_time,'%i') AS endmin,t1.update_time FROM schedule_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id WHERE schedule_id = '".$_POST["edit_id"]."';";
    $result = $con->query($sql) or ($judge = true);
    $edit_form_num = "SCHcustomername_text,SCHcustomerabbreviation,SCHusername,SCHauthoer,SCHdate,SCHtime_starthour,SCHtime_startmin,SCHtime_endhour,SCHtime_endmin";
    $field_type = "1,1,3,1,1,1,1,1,1";
    $edit_form_num = explode(',', $edit_form_num);
    $field_type = explode(',', $field_type);
    $column_num = "customer_name,customer_abbreviation,user_id,authoer,schedule_date,starthour,startmin,endhour,endmin";
    $column_array = explode(',', $column_num);
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 0; $i < count($edit_form_num); $i++)
        {
            $column = $column_array[$i];
            $edit_list_data[$i]["form_num"] = $edit_form_num[$i];
            $edit_list_data[$i]["value"] = $result_row[$column];
            $edit_list_data[$i]["field_type"] = $field_type[$i];
            $counter++;
        }
        //更新時間情報を付与する
        $edit_list_data[$counter]["form_num"] = "updatetime";
        $edit_list_data[$counter]["value"] = $result_row['update_time'];
        $edit_list_data[$counter]["field_type"] = "9999";
    }
    
    echo json_encode($edit_list_data);
?>