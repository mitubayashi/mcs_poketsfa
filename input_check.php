<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:');
    header('Pragma:');
    header('Content-type: text/html; charset=utf8'); 
?>
<?php
    //初期設定
    require_once("f_DB.php");
    $form_ini_array = parse_ini_file("./ini/form.ini",true); 
    
    //変数
    $judge = true;
    $error_form_num = array();
    $counter = 0;
    $form_num = $_POST['form_num'];
    $post_value = $_POST['value'];
    $value = array();
    $pagename = $_POST['pagename'];
    $flag = $_POST['flag'];
    $edit_id = $_POST['edit_id'];
    $before_updatetime = $_POST['updatetime'];
    $after_updatetime = "";
    
    //データを整理する
    for($i = 0; $i < count($post_value); $i++)
    {
        $value[$form_num[$i]['form_num']] = $post_value[$i];
    }
    
    //特殊文字入力チェック
    if($judge == true && $flag != "2")
    {
        for($i = 0; $i < count($form_num); $i++)
        {
            if($form_num[$i]['max_length'] != 0 && $form_num[$i]['max_length'] != "")
            {
                if($form_num[$i]['form_num'] == "comment")
                {
                    $count = mb_strlen($value[$form_num[$i]['form_num']]) + substr_count($value[$form_num[$i]['form_num']],"\n");
                }
                elseif($form_ini_array[$form_num[$i]['form_num']]['field_type'] == '2')
                {
                    $count = mb_strlen($value[$form_num[$i]['form_num']]) + substr_count($value[$form_num[$i]['form_num']],"\n");
                }
                else
                {
                    $count = mb_strlen($value[$form_num[$i]['form_num']]);
                }
                if($count > $form_num[$i]['max_length'])
                {
                    $judge = false;
                    $error_form_num[$counter]['form_num'] = $form_num[$i]['form_num'];
                    $error_form_num[$counter]['error_msg'] = "登録できるデータ量を超えています";
                    $counter++;
                }
            }
        }
    }
    
    //既登録チェック
    $con = dbconect();
    if($judge)
    {
        if($pagename == "CUSTOMER")
        {
            $sql = "SELECT *FROM customer_table ";
            $sql .= "WHERE customer_id != '".$edit_id."' AND customer_name = '".$value['CUSname']."' AND user_id = '".$value['CUSuserid']."';";
            $result = $con->query($sql);
            if($result->num_rows > 0)
            {
                $judge = false;
                $error_form_num[$counter]['form_num'] = "CUSname";
                $error_form_num[$counter]['error_msg'] = "この顧客は登録されています";
            }            
        }
        elseif($pagename == "USER")
        {
            $sql = "SELECT *FROM user_table ";
            $sql .= "WHERE user_id != '".$edit_id."' AND login_id = '".$value['USEloginid']."';";
            $result = $con->query($sql);
            if($result->num_rows > 0)
            {
                $judge = false;
                $error_form_num[$counter]['form_num'] = "USEloginid";
                $error_form_num[$counter]['error_msg'] = "このログインIDは登録済みです";
            }
        }
        elseif($flag == "4" && $edit_id == "")
        {//スケジュール登録時の顧客チェック
            $sql = "SELECT *FROM customer_table ";
            $sql .= "WHERE customer_name = '".$value['SCHcustomername_text']."' AND user_id = '".$value['SCHusername']."';";
            $result = $con->query($sql);
            if($result->num_rows > 0)
            {
                $judge = false;
                $error_form_num[$counter]['form_num'] = "SCHcustomername_text";
                $error_form_num[$counter]['error_msg'] = "同じ顧客名、担当者のデータが登録済みです";
            }  
        }
    }
    
    //他端末からの更新チェック
    if($judge == true && $before_updatetime != "" && $pagename != "REPORTCREATE")
    {
        if($flag == 5)
        {
            $table_name = "schedule_table";
            $id_column_name = "schedule_id";
        }
        else
        {
            $table_num = $form_ini_array[$pagename]['table_num'];
            $table_name = $form_ini_array[$table_num]['table_name'];
            $id_column_name = $form_ini_array[$table_num.'id']['column_name'];
        }
        $sql = "SELECT update_time FROM `".$table_name."` WHERE ".$id_column_name." = '".$edit_id."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $after_updatetime = $result_row['update_time'];
        }
        if($before_updatetime != $after_updatetime)
        {
        $error_form_num[$counter]['form_num'] = "updatetime";
            $judge = false;
        }
    }
    
    //ユーザー更新、削除時のチェック
    if($judge == true && $pagename == "USER")
    {
        if(($value['USEstatus'] == '0' && $flag == '1') || $flag == "2")
        {
            //自分以外の配下の情報を取得する
            $sql = "SELECT t1.user_id FROM user_table t1 inner join user_treepath_table t2 on t1.user_id = t2.descendant ";
            $sql .= "WHERE t2.ancestor = ".$edit_id." AND t1.user_id != ".$edit_id.";";
            $result = $con->query($sql);
            if($result->num_rows > 0)
            {
                $error_form_num[$counter]['form_num'] = "user_status_error";
                $judge = false;
            }
            if($judge)
            {
                $sql = "SELECT *FROM user_table WHERE delete_flag = 0 AND user_status = 1 AND user_id != ".$edit_id.";";
                $result = $con->query($sql);
                if($result->num_rows == 0)
                {
                    $judge = false;
                    $error_form_num[$counter]['form_num'] = "delete_user_error";
                }    
            }
        }
    }
    
    //面談報告書新規登録時のチェック
    if($pagename == "REPORTCREATE" && $flag == "0")
    {
        $sql = "SELECT *FROM interview_report_table WHERE schedule_id = '".$edit_id."';";
        $result = $con->query($sql);
        if($result->num_rows > 0)
        {
            //他の端末から登録されていた場合
            $sql = "SELECT user_id FROM customer_table WHERE customer_id = '".$value['REPORTCREATEcustomername']."';";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $user_id = $result_row['user_id'];
            }
            if($user_id == $_SESSION["loginuser"]["user_id"])
            {
                $error_form_num[$counter]['form_num'] = "report_create";
            }            
            else
            {
                $error_form_num[$counter]['form_num'] = "report_create_error";
            }
        }
    }
    
    //面談報告書編集時のチェック
    if($pagename == "REPORTCREATE" && $flag == "1")
    {
        $sql = "SELECT update_time,check_status FROM interview_report_table WHERE schedule_id = '".$edit_id."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $after_updatetime = $result_row['update_time'];
            $check_status = $result_row['check_status'];
        }
        if($before_updatetime != $after_updatetime)
        {
            if($check_status != "2" && ($value['check_status'] != $check_status))
            {
                $error_form_num[$counter]['form_num'] = "check_status_error";
                $judge = false;                   
            }
            else
            {
                //担当者を求める
                $sql = "SELECT user_id FROM customer_table WHERE customer_id = '".$value['REPORTCREATEcustomername']."';";
                $result = $con->query($sql);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $user_id = $result_row['user_id'];
                }
                if($user_id != $_SESSION["loginuser"]["user_id"])
                {
                    $error_form_num[$counter]['form_num'] = "report_update_error";
                    $judge = false;      
                }
                else
                {
                    $error_form_num[$counter]['form_num'] = "updatetime";
                    $judge = false;      
                }
            }
        }
    }
    echo json_encode($error_form_num);
?>


