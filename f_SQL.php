<?php
/***************************************************************************
function List_itemSQL()


引数1   $post                   前の画面から受け取った情報      

戻り値	$sql                    SQL
 * [0] 表示件数を表示するSQL
 * [1] 一覧表を表示するSQL
***************************************************************************/

function List_itemSQL($post,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini', true);
    $SQL_ini_array = parse_ini_file('./ini/SQL.ini', true);  
    
    //定数    
    $sech_form_num = explode(',', $form_ini_array[$pagename]["sech_form_num"]);
    $sech_form_type = explode(',', $form_ini_array[$pagename]["sech_form_type"]);
    
    //変数
    $setting_array = setting_array_get();
    $sql[0] = "";
    $sql[1] = "";
    $where = "";
    
    //SQL作成処理
    $sql[0] .= $SQL_ini_array[$pagename]["count_sql"];
    $sql[1] .= $SQL_ini_array[$pagename]["select_sql"];
    
    //検索条件追加    
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        $column_name = $form_ini_array[$sech_form_num[$i]]["column_name"];
        //検索条件追記
        if($sech_form_type[$i] == "0" || $sech_form_type[$i] == "1")
        {
            if(isset($post[$sech_form_num[$i]]) && $post[$sech_form_num[$i]] != "")
            {
                if($pagename == 'REPORTLIST' && $column_name == 'user_id')
                {
                    $where .= " AND t2.user_id = '".$post[$sech_form_num[$i]]."' ";                    
                }
                elseif($sech_form_num[$i] == 'MATcustomername')
                {
                    $where .= " AND t2.customer_id = '".$post[$sech_form_num[$i]]."' ";
                }
                elseif($sech_form_num[$i] == 'MATusername')
                {
                    $where .= " AND t2.user_id = '".$post[$sech_form_num[$i]]."' ";
                }
                else
                {
                    if($sech_form_type[$i] == "0")
                    {
                        //完全一致
                        $where .= " AND t1.".$column_name." = '".$post[$sech_form_num[$i]]."' ";
                    }
                    elseif($sech_form_type[$i] == "1")
                    {
                        //部分一致
                        $where .= " AND t1.".$column_name." LIKE '%".$post[$sech_form_num[$i]]."%' ";              
                    }    
                }  
            }
            else 
            {
                
                if($pagename == 'REPORTLIST' && $column_name == 'user_id' || $pagename == 'ORDERMGR' && $sech_form_num[$i] == 'MATusername')
                {
                    if(!isset($post['REPORTLIST_list']) || $post['REPORTLISTcustomerid'] == "")
                    {
                        $hyozi_flag = $setting_array[$pagename];
                        $where .= " AND t2.user_id IN(".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";    
                    }
                }         
            }
        }
        elseif($sech_form_type[$i] == "2")
        {
            //日付検索
            if(isset($post[$sech_form_num[$i].'_0']) && isset($post[$sech_form_num[$i].'_1']))
            {
                if($column_name == "order_month")
                {
                    if($post[$sech_form_num[$i].'_0'] != "" && $post[$sech_form_num[$i].'_1'] != "")
                    {
                        $where .= " AND t1.".$column_name." BETWEEN '".$post[$sech_form_num[$i].'_0']."-01' AND '".$post[$sech_form_num[$i].'_1']."-01' ";                     
                    }
                    elseif($post[$sech_form_num[$i].'_0'] != "" && $post[$sech_form_num[$i].'_1'] == "")
                    {
                        $where .= " AND t1.".$column_name." >= '".$post[$sech_form_num[$i].'_0']."-01' ";
                    }
                    elseif($post[$sech_form_num[$i].'_0'] == "" && $post[$sech_form_num[$i].'_1'] != "")
                    {
                        $where .= " AND t1.".$column_name." <= '".$post[$sech_form_num[$i].'_1']."-01' ";                  
                    }  
                }
                else
                {
                    if($post[$sech_form_num[$i].'_0'] != "" && $post[$sech_form_num[$i].'_1'] != "")
                    {
                        $where .= " AND t1.".$column_name." BETWEEN '".$post[$sech_form_num[$i].'_0']."' AND '".$post[$sech_form_num[$i].'_1']."' ";                     
                    }
                    elseif($post[$sech_form_num[$i].'_0'] != "" && $post[$sech_form_num[$i].'_1'] == "")
                    {
                        $where .= " AND t1.".$column_name." >= '".$post[$sech_form_num[$i].'_0']."' ";
                    }
                    elseif($post[$sech_form_num[$i].'_0'] == "" && $post[$sech_form_num[$i].'_1'] != "")
                    {
                        $where .= " AND t1.".$column_name." <= '".$post[$sech_form_num[$i].'_1']."' ";                  
                    }                    
                }
            }                 
        }
    }
    $sql[0] .= $where;
    $sql[1] .= $where;
    
    return $sql;
}

/***************************************************************************
function updateSQL()


引数1   $update                   更新情報      
引数2   $pagename                 画面名

戻り値	$sql                      UPDATEのSQL
***************************************************************************/

function updateSQL($update,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);
    
    //変数
    $sql = "";
    $table_num = $form_ini_array[$pagename]["table_num"];
    $table_name = $form_ini_array[$table_num]['table_name'];
    $edit_form_num = explode(',', $form_ini_array[$pagename]["edit_form_num"]);
    $edit_id_column = $form_ini_array[$table_num."id"]["column_name"]; 
    
    //処理
    $sql .= "UPDATE ".$table_name." SET ";
    
    for($i = 0; $i < count($edit_form_num); $i++)
    {
        if($edit_form_num[$i] == "USEloginpassword" && $update["password_input_flg"] == "0" && $pagename == "USER")
        {
            continue;
        }
        $column = $form_ini_array[$edit_form_num[$i]]["column_name"];
        if($update[$edit_form_num[$i]] == "")
        {
            $sql .= $column." = null,";
        }
        else
        {
            if($column == "order_month")
            {
                $sql .= $column." = '".$update[$edit_form_num[$i]]."-01',";
            }
            else
            {
                $sql .= $column." = '".$update[$edit_form_num[$i]]."',";
            }
        }
    }
    
    //案件のステータスが「受注」となった場合、受注者の更新を行う
    if($pagename == 'MATTER' && isset($update['MATstatus']))
    {
        if($update['MATstatus'] == '0')
        {
            $user_get_sql = "SELECT user_id FROM customer_table WHERE customer_id = '".$update['MATcustomerid']."';";
            $con = dbconect();
            $result = $con->query($user_get_sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $user_id = $result_row['user_id'];
            }
            $sql .= "order_user = '".$user_id."',";
        }
    }
    $sql = rtrim($sql,',');    
    $sql .= " WHERE ".$edit_id_column." = '".$update["edit_id"]."';";   
    
    return $sql;
}


/***************************************************************************
function insertSQL()


引数1   $insert                   新規登録情報      
引数2   $pagename                 画面名

戻り値	$sql                      INSERTのSQL
***************************************************************************/

function insertSQL($insert,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);
    
    //変数
    $sql = "";
    $table_num = $form_ini_array[$pagename]["table_num"];
    $table_name = $form_ini_array[$table_num]['table_name'];
    $insert_form_num = explode(',', $form_ini_array[$pagename]["insert_form_num"]);
    
    //処理
    $sql .= "INSERT INTO ".$table_name." (";
    
    //項目名
    for($i = 0; $i < count($insert_form_num); $i++)
    {
        $column = $form_ini_array[$insert_form_num[$i]]["column_name"];
        $sql .= $column.",";
    }
    if($pagename == "USER")
    {
        $sql .= "delete_flag)";
    }    
    elseif($pagename == "CUSTOMER")
    {
        $sql .= "delete_flag,create_userid)";
    }
    elseif($pagename == "MATTER")
    {
        $sql .= "create_userid)";
    }
    else
    {
        $sql = rtrim($sql,',');
        $sql .= ')';
    }
    //値
    $sql .= ' VALUES (';
    for($i = 0; $i < count($insert_form_num); $i++)
    {
        if($insert[$insert_form_num[$i]] == "")
        {
            $sql .= "null,";
        }
        else
        {
            if($insert_form_num[$i] == "MATmonth")
            {
                $value = $insert[$insert_form_num[$i]].'-01';
            }
            else
            {
                $value = $insert[$insert_form_num[$i]];
            }
            $sql .= "'".$value."',";
        }
    }
    if($pagename == "USER")
    {
        $sql .= "'0');";
    }    
    elseif($pagename == "CUSTOMER")
    {
        $sql .= "'0','".$insert['CUSuserid']."');";
    }
    elseif($pagename == "MATTER")
    {        
        $con = dbconect();
        $user_sql = "SELECT user_id FROM customer_table WHERE customer_id = '".$insert['MATcustomerid']."';";
        $result = $con->query($user_sql);    
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $user_id = $result_row['user_id'];
        }
        
        $sql .= "'".$user_id."');";
    }
    else
    {
        $sql = rtrim($sql,',');
        $sql .= ');';
    }
    
    return $sql;
}

/***************************************************************************
function deleteSQL($delete,$pagename)


引数1   $delete                   削除情報      
引数2   $pagename                 画面名

戻り値	$sql                      UPDATEのSQL
***************************************************************************/

function deleteSQL($delete,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);
    
    //変数
    $sql = "";
    $table_num = $form_ini_array[$pagename]["table_num"];
    $table_name = $form_ini_array[$table_num]['table_name'];
    $delete_list_id_column = $form_ini_array[$table_num."id"]["column_name"];
    
    $sql = "UPDATE ".$table_name." SET delete_flag = 1 WHERE ".$delete_list_id_column." = ".$delete["edit_id"].";";
    
    return $sql;
}

/***************************************************************************
function setOrderbySQL($delete,$pagename)


引数1   $sql                             SQL      
引数2   $pagename                  画面名

戻り値	$sql                        SQL
***************************************************************************/

function setOrderbySQL($sql,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);

    //定数
    $default_sort_column = explode(',',$form_ini_array[$pagename]['default_sort_column']);
    $default_sort_type = explode(',',$form_ini_array[$pagename]['default_sort_type']);
    $orderby_type[0] = ' DESC ';
    $orderby_type[1] = ' ASC ';
    
    //変数
    $orderby = "";
    
    //処理
    if($form_ini_array[$pagename]['default_sort_column'] != "")
    {
        $orderby = " ORDER BY ";
        for($i = 0; $i < count($default_sort_column); $i++)
        {
            if($default_sort_column[$i] != "")
            {
                $column = $form_ini_array[$default_sort_column[$i]]['column_name'];
                $orderby .= " t1.".$column." ".$orderby_type[$default_sort_type[$i]].",";
            }
        }
    }
    
    //末尾の「,」を削除する
    $orderby = substr($orderby, 0, -1);
    $sql[1] .= $orderby;
    
    return $sql;
}
?>