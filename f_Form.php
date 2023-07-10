<?php
/***************************************************************************
function makeModalHtml()


引数			なし

戻り値	$modal_html     モーダルHTML
***************************************************************************/

function makeModalHtml($form_type){
    
    //初期設定
    require_once('f_Form.php');
    
    //定数
    $pagename = $_SESSION['pagename'];
    
    //変数
    $modal_html = '';
    $modal_button = '';
    
    switch ($form_type){
        case 'sech_form_num':
            $title = '検索条件';     
            $modal_button .= '<input type="submit" value="検索" class="modal_button" name="'.$pagename.'_button">';
            $modal_button .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
            break;
        case 'insert_form_num':        
            $title = '新規登録';     
            $modal_button .= '<input type="hidden" value="" id="mitsumori_insert" name="mitsumori_insert">';
            $modal_button .= '<input type="submit" value="登録" class="modal_button" name="'.$pagename.'_insert" onclick="return check(insert_form_num,0);">';
            $modal_button .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
            break;
        case 'edit_form_num':
            $title = '編集';
            $modal_button .= '<input type="hidden" value="" id="edit_id" name="edit_id">';
            $modal_button .= '<input type="hidden" value="" id="updatetime" name="updatetime">';
            $modal_button .= '<input type="submit" value="更新" class="modal_button" name="'.$pagename.'_edit" onclick="return check(edit_form_num,1);">';
            $modal_button .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
            if($pagename == "USER" || $pagename == "CUSTOMER") 
            {
                $modal_button .= '<input type="submit" value="削除" class="modal_button" name="'.$pagename.'_delete" onclick="return check(edit_form_num,2);">';
            }            
            break;
        case 'schedule_insert_form_num':
            $title = 'スケジュール登録';
            $modal_button .= '<input type="hidden" value="" id="appointment_flag" name="appointment_flag">';            
            $modal_button .= '<input type="submit" value="登録" class="modal_button" name="'.$pagename.'_scheduleinsert" onclick="schedule_check(); return check(schedule_insert_form_num,4);">';
            $modal_button .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
            break;
        case 'schedule_edit_form_num':
            $title = 'スケジュール編集';
            $modal_button .= '<input type="hidden" value="" id="appointment_flag" name="appointment_flag">';            
            $modal_button .= '<input type="hidden" value="" id="edit_id" name="edit_id">';
            $modal_button .= '<input type="hidden" value="" id="updatetime" name="updatetime">';
            $modal_button .= '<input type="submit" value="更新" class="modal_button" name="'.$pagename.'_scheduleedit" onclick="return check(schedule_edit_form_num,5);">';
            $modal_button .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
            break;
    }
    
    $modal_html .= '<div class="modal_title">'.$title.'</div>';
    $modal_html .= '<form action="pageJump.php" method="post">';
    //$modal_html .= '<form action="pageJump.php" method="post" accept-charset="Shift_JIS">';
    $modal_html .= makeForm($form_type);
    $modal_html .= '<div class="modal_button_area">';
    $modal_html .= $modal_button;
    $modal_html .= '</div>';
    $modal_html .= '</form>';    
        
    return $modal_html;
}

/***************************************************************************
function makeForm()


引数    $form_type  フォームタイプ(検索条件、新規登録、編集)

戻り値	$form_html	入力フォームHTML
***************************************************************************/

function makeForm($form_type){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);    
    require_once("f_Form.php");
    
    //定数
    $pagename = $_SESSION["pagename"];
    
    //変数
    $form_html = ""; 
    $form_colum = $form_ini_array[$pagename][$form_type];
    $form_num = explode(',', $form_colum);

    //入力フォーム作成
    if($form_colum != "")
    {
        $form_html .= '<table class="form_table">';
        for($i = 0; $i < count($form_num); $i++)
        {
            $item_name = $form_ini_array[$form_num[$i]]["item_name"];
            $field_type = $form_ini_array[$form_num[$i]]["field_type"];
            $form_size = $form_ini_array[$form_num[$i]]["form_size"];        
            $isnotnull = $form_ini_array[$form_num[$i]]["isnotnull"];
            $max_length = $form_ini_array[$form_num[$i]]["max_length"];
            $form_format = $form_ini_array[$form_num[$i]]["form_format"];
            $onchange = "";
            
            //入力チェック作成
            if($form_type != "sech_form_num")
            {
                $onchange = 'input_check(this.id,'.$max_length.','.$form_format.','.$isnotnull.');';
            }
            
            //入力欄作成
            switch ($field_type){
                case '1':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    if($form_num[$i] == "SCHauthoer")
                    {
                        $form_html .= '<input type="text" value="'.$_SESSION["loginuser"]["user_name"].'" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';
                    }
                    else
                    {
                        $form_html .= '<input type="text" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';                    
                    }
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';                
                    break;
                case '2':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    if($form_type == "sech_form_num")
                    {
                        $form_html .= '<input type="text" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'" placeholder="スペース(半角空白)で区切ってください">';
                        $form_html .= '<label><input type="radio" value="OR" name="'.$form_num[$i].'_radio" id="'.$form_num[$i].'_radio">OR</label>　';
                        $form_html .= '<label><input type="radio" value="AND" name="'.$form_num[$i].'_radio" id="'.$form_num[$i].'_radio">AND</label>';
                    }
                    else
                    {
                        $form_html .= '<textarea name="'.$form_num[$i].'" id="'.$form_num[$i].'" rows="7" cols="60" class="form_textarea" onchange="'.$onchange.'"></textarea>';
                    }
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>'; 
                    break;
                case '3':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    $form_html .= pulldown_set($form_num[$i],$form_type,$onchange); 
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';                
                    break;
                case '4':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    $form_html .= multiple_pulldown_set($form_num[$i]); 
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';
                    break;
                case '5':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    if($form_type == "sech_form_num")
                    {
                        $form_html .= '<input type="date" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'_0" id="'.$form_num[$i].'_0">';           
                        $form_html .= '～';
                        $form_html .= '<input type="date" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'_1" id="'.$form_num[$i].'_1">';
                    }
                    elseif($form_num[$i] == "SCHdate")
                    {
                        $form_html .= '<input type="date" value="'.date("Y-m-d").'" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';
                    }
                    else
                    {
                        $form_html .= '<input type="date" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';
                    }
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';                
                    break;
                case '6':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    $form_html .= time_input_set($form_num[$i]);
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';   
                    break;
                case '7':
                    $form_html .= password_input_set($form_num[$i],$form_type,$form_size);
                    break;
                case '8':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    $form_html .= radio_set($form_num[$i],$form_type);
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>';               
                    break;
                case '9':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    $form_html .= input_pulldown_set($form_num[$i]);
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_text_errormsg"></a>';
                    $form_html .= '</td></tr>';                
                    break;
                case '10':
                    $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
                    $form_html .= '<td>';
                    if($form_type == "sech_form_num")
                    {
                        $form_html .= '<input type="month" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'_0" id="'.$form_num[$i].'_0">';           
                        $form_html .= '～';
                        $form_html .= '<input type="month" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'_1" id="'.$form_num[$i].'_1">';
                    }
                    else
                    {
                        $form_html .= '<input type="month" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';
                    }
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>'; 
                    break;
            }    
        }
        $form_html .= '</table>';
    }    
    return $form_html;
}

/***************************************************************************
function pulldown_set()


引数    $form_num

戻り値	$pulldown_html  	プルダウンHTML
***************************************************************************/

function pulldown_set($form_num,$form_type,$onchange){
    
    //初期設定
    require_once("f_Form.php");
    require_once("f_DB.php");
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    $form_ini_array = parse_ini_file("./ini/form.ini",true);  
    
    //変数
    $setting_array = setting_array_get();
    $pulldown_html = "";
    $id_list = array();
    $name_list = array();    
    $delete_flag_list = array();
    
    //定数
    $pagename = $_SESSION['pagename'];
    $isnotnull = $form_ini_array[$form_num]["isnotnull"];
            
    //処理
    if($form_num == 'MATstatus')
    {
        $id_list = explode(',', $selection_form_ini_array[$form_num]['selection_value']);
        $name_list = explode(',', $selection_form_ini_array[$form_num]['selection_name']);
        $delete_flag_list = explode(',', $selection_form_ini_array[$form_num]['delete_flag']);
    }
    else
    {
        $con = dbconect();
        if($pagename == 'CUSTOMER' || $pagename == 'TOP' || $form_num == 'REPORTLISTuserid' || $pagename == "MONTHSCHEDULE" || $form_num == 'MATusername') 
        {
            $sql = "SELECT user_id,user_name,delete_flag FROM user_table WHERE user_status != 2 ";
            if($form_type != 'sech_form_num' || $form_num == 'REPORTLISTuserid')
            {
                if($form_num == 'SCHusername' && $form_type == "schedule_insert_form_num")
                {
                    $hyozi_flag = $setting_array["topappointment"];                    
                }
                elseif($form_num == 'SCHusername' && $form_type == "schedule_edit_form_num")
                {
                    $hyozi_flag = 3;
                }
                else
                {
                    $hyozi_flag = $setting_array[$pagename];
                }
                $sql .= " AND user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";       
            }
            if($pagename == "ORDERMGR")
            {
                $hyozi_flag = $setting_array[$pagename];
                $sql .= " AND user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";       
            }
            $sql .= ' ;';
        }
        elseif($form_num == "REPORTLISTcustomerid" || $form_num == "MATcustomerid")
        {
            $sql = "SELECT t1.customer_id,t1.customer_name,t2.user_name,t1.delete_flag FROM customer_table as t1 LEFT JOIN user_table as t2 ON t1.user_id = t2.user_id ";
            $hyozi_flag = $setting_array[$pagename];
            $sql .= " WHERE t1.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";  
            if(isset($_SESSION['list']['REPORTLIST_list']))
            {
                $sql1 = "SELECT customer_id FROM schedule_table WHERE schedule_id = '".$_SESSION['list']['REPORTLIST_list']."';";
                $result = $con->query($sql1);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $sql .= " OR t1.customer_id = '".$result_row['customer_id']."'";
                }                
            }
            $sql .= ' ;';
        }
        elseif($form_num == "MATcustomername")
        {            
            $hyozi_flag = $setting_array[$pagename];
            if($pagename == "ORDERMGR" )
            {
                $sql = "SELECT t1.customer_id,t1.customer_name,t2.user_name,t1.delete_flag FROM customer_table as t1 LEFT JOIN user_table as t2 ON t1.user_id = t2.user_id WHERE t1.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).");";
            }
            else
            {
                $sql = "SELECT t1.customer_id,t1.customer_name,t2.user_name,t1.delete_flag FROM customer_table as t1 LEFT JOIN user_table as t2 ON t1.user_id = t2.user_id ;";
            }
        }
        else
        {
            $sql = "SELECT user_id,user_name,delete_flag FROM user_table WHERE user_status = '1';";
        }
        $result = $con->query($sql) or ($judge = true);
        $counter = 0;

        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            if($form_num == "REPORTLISTcustomerid" || $form_num == "MATcustomername" || $form_num == "MATcustomerid")
            {
                $id_list[$counter] = $result_row["customer_id"];
                $name_list[$counter] = $result_row["customer_name"].'　('.$result_row['user_name'].')';
                $delete_flag_list[$counter] = $result_row['delete_flag'];
                $counter++;  
            }
            else
            {
                $id_list[$counter] = $result_row["user_id"];
                $name_list[$counter] = $result_row["user_name"]; 
                $delete_flag_list[$counter] = $result_row['delete_flag'];
                $counter++;     
            }
               
        }
    }
    
    $pulldown_html .= '<select name="'.$form_num.'" id="'.$form_num.'" class="pulldown" onchange="'.$onchange.'">';
    $pulldown_html .= '<option value="" data-deleteflag="0">指定なし</option>';
    for($i = 0; $i < count($id_list); $i++)
    {
        $pulldown_html .= '<option value="'.$id_list[$i].'" data-deleteflag="'.$delete_flag_list[$i].'">';
        $pulldown_html .= $name_list[$i];
        $pulldown_html .= '</option>';
    }
    $pulldown_html .= '</select>';
    
    return $pulldown_html;
}

/***************************************************************************
function multiple_pulldown_set()


引数    $form_num

戻り値	$pulldown_html  	プルダウンHTML
***************************************************************************/

function multiple_pulldown_set($form_num){
    
    //初期設定
    require_once("f_Form.php");
    require_once("f_DB.php");
    
    //変数
    $pulldown_html = "";
    $userid_list = array();
    $username_list = array();
    $deleteflag_list = array();
    
    //処理
    
    $con = dbconect();
    $sql = "SELECT user_id,user_name,delete_flag FROM user_table WHERE user_status = '1';";
    $result = $con->query($sql) or ($judge = true);
    $counter = 0;
    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $userid_list[$counter] = $result_row["user_id"];
        $username_list[$counter] = $result_row["user_name"];
        $deleteflag_list[$counter] = $result_row["delete_flag"];
        $counter++;        
    }
    
    //id,name指定
    $multiple_pulldown_box_id = "multiple_pulldown_".$form_num;
    $multiple_pulldown_textbox_id = "textbox_".$form_num;
    $multiple_pulldown_checkbox_name = 'multiple_pulldown_check_'.$form_num;
    
    $pulldown_html .= '<input type="button" class="multiple_pulldown_nav" value="選択する" onclick="open_multiple_pulldown(this,'.$multiple_pulldown_box_id.');">';
    $pulldown_html .= '<input type="text" class="form-text disabled" id="'.$multiple_pulldown_textbox_id.'" size="50" value="" tabindex="-1">';
    $pulldown_html .= '<input type="hidden" size="50" id="'.$form_num.'" name="'.$form_num.'" value="">';
    $pulldown_html .= '<div id="'.$multiple_pulldown_box_id.'" class="multiple_pulldown_box" onmouseleave="close_multiple_pulldown('.$multiple_pulldown_box_id.');">';
    
    for($i = 0; $i < count($userid_list); $i++)
    {
        $pulldown_html .= '<div id="'.$form_num.'_checkbox_data_'.$userid_list[$i].'">';
        $pulldown_html .= '<input type="checkbox" value="'.$userid_list[$i].'" data-name="'.$username_list[$i].'" data-deleteflag="'.$deleteflag_list[$i].'" name="'.$multiple_pulldown_checkbox_name.'" onclick="multiple_pulldown_value_set('.$multiple_pulldown_checkbox_name.','.$form_num.','.$multiple_pulldown_textbox_id.');">'.$username_list[$i];
        $pulldown_html .= '</div>';
    }
    
    $pulldown_html .= '</div>';
    
    return $pulldown_html;
}

/***************************************************************************
function radio_set()


引数1    $form_num
引数2    $form_type

戻り値	$radio_html  	ラジオボタンHTML
***************************************************************************/

function radio_set($form_num,$form_type){
    
    //初期設定
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    require_once("f_Form.php");
    require_once("f_DB.php");
    
    //変数
    $radio_html = "";
    
    //処理
    $selection_name = explode(',', $selection_form_ini_array[$form_num]['selection_name']);
    $selection_value = explode(',', $selection_form_ini_array[$form_num]['selection_value']);

    //指定なしを追加
    if($form_type == "sech_form_num")
    {
        $radio_html .= '<input type="radio" value="" name="'.$form_num.'">指定なし';
    }
    
    for($j = 0; $j < count($selection_name); $j++)
    {
        if($j == 0 && $form_type != "sech_form_num")
        {
            $checked = "checked";
        }
        else
        {
            $checked = "";
        }
        $radio_html .= '<input type="radio" value="'.$selection_value[$j].'" name="'.$form_num.'" '.$checked.'>'.$selection_name[$j];        
    }
    
    return $radio_html;
}

/***************************************************************************
function password_input_set($form_num,$form_type,$form_size)


引数1    $form_num
引数2    $form_type
引数3    $form_size

戻り値	$password_html  	ラジオボタンHTML
***************************************************************************/

function password_input_set($form_num,$form_type,$form_size){
    
    //初期設定
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    require_once("f_Form.php");
    require_once("f_DB.php");
    
    //変数
    $password_html = "";
    
    //処理
    if($form_type == "insert_form_num")
    {
        $password_html .= '<tr><td class="form_item_name">パスワード<input type="hidden" value="1" name="password_input_flg" id="password_input_flg"></td>';
        $password_html .= '<td><input type="password" value="" class="form-text" size="'.$form_size.'" name="'.$form_num.'" id="'.$form_num.'" onchange="pass_check();">';
        $password_html .= '<br><a class="error" id="'.$form_num.'_errormsg"></a>';
        $password_html .= '</td></tr>';
        $password_html .= '<tr><td class="form_item_name">確認用パスワード</td>';
        $password_html .= '<td><input type="password" value="" class="form-text" size="'.$form_size.'" id="checkpass" onchange="pass_check();">';      
        $password_html .= '<br><a class="error" id="checkpass_errormsg"></a>';
        $password_html .= '</td></tr>';
    }

    if($form_type == "edit_form_num")
    {
        $password_html .= '<tr><td><input type="button" value="パスワードを変更する" class="table_button" onclick="password_input_open();"></td>';
        $password_html .= '<td><input type="hidden" value="0" name="password_input_flg" id="password_input_flg"></td></tr>';
        $password_html .= '<tr class="password_input"><td class="form_item_name">現在パスワード</td>';
        $password_html .= '<td><input type="password" value="" class="form-text" size="'.$form_size.'" id="nowpass">';
        $password_html .= '<br><a class="error" id="nowpass_errormsg"></a>';
        $password_html .= '<input type="hidden" value="" id="password"></td></tr>';
        $password_html .= '<tr class="password_input"><td class="form_item_name">変更後パスワード</td>';
        $password_html .= '<td><input type="password"  name="'.$form_num.'"　value="" class="form-text" size="'.$form_size.'" id="'.$form_num.'" onchange="pass_check();">';   
        $password_html .= '<br><a class="error" id="'.$form_num.'_errormsg"></a>';
        $password_html .= '</td></tr>';
        $password_html .= '<tr class="password_input"><td class="form_item_name">確認用パスワード</td>';
        $password_html .= '<td><input type="password" value="" class="form-text" size="'.$form_size.'" id="checkpass" onchange="pass_check();">';
        $password_html .= '<br><a class="error" id="checkpass_errormsg"></a>';
        $password_html .= '</td></tr>';
    }
        
    return $password_html;
}

/***************************************************************************
function time_input_set($form_num)


引数1    $form_num


戻り値	$time_html  	時刻入力HTML
***************************************************************************/

function time_input_set($form_num){
    
    //初期設定
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    require_once("f_Form.php");
    require_once("f_DB.php");
    
    //変数
    $start_hour_html = "";
    $start_min_html = "";
    $end_hour_html = "";
    $end_min_html = "";
    $time_html = "";
    
    //処理
    //時入力欄作成
    $selection_name = explode(',', $selection_form_ini_array[$form_num]['selection_name1']);
    $selection_value = explode(',', $selection_form_ini_array[$form_num]['selection_value1']);
   
    $start_hour_html .= '<input type="text" size="20" value="00" list="'.$form_num.'_starthour_datalist" name="'.$form_num.'_starthour" id="'.$form_num.'_starthour" class="form-text" onchange="time_input(this.id,starthour_datalist);" style="width: 37px;" autocomplete="off" onclick="open_multiple_pulldown(this,starthour_datalist);" onblur="close_multiple_pulldown(starthour_datalist,event.relatedTarget);">';
    $end_hour_html .= '<input type="text" size="20" value="00" list="'.$form_num.'_endhour_datalist" name="'.$form_num.'_endhour" id="'.$form_num.'_endhour"class="form-text" onchange="time_input(this.id,endhour_datalist);" style="width: 37px;" autocomplete="off"  onclick="open_multiple_pulldown(this,endhour_datalist);" onblur="close_multiple_pulldown(endhour_datalist,event.relatedTarget);">';
    $start_hour_html .= '<div id="starthour_datalist" class="multiple_pulldown_box">';
    $end_hour_html .= '<div id="endhour_datalist" class="multiple_pulldown_box">';

    for($i = 0; $i < count($selection_name); $i++)
    {
        $start_hour_html .= '<div>';
        $start_hour_html .= '<input type="button" value="'.$selection_value[$i].'" id="starthour_datalist_nav" style="background-color: rgba(0,0,0,0); border: none;" onclick="time_set(this.value,'.$form_num.'_starthour,starthour_datalist);">';
        $start_hour_html .= '</div>';
        $end_hour_html .= '<div>';
        $end_hour_html .= '<input type="button" value="'.$selection_value[$i].'" id="endhour_datalist_nav" style="background-color: rgba(0,0,0,0); border: none;" onclick="time_set(this.value,'.$form_num.'_endhour,endhour_datalist);">';
        $end_hour_html .= '</div>';
    }
    
    $start_hour_html .= '</div>';
    $end_hour_html .= '</div>';
    
    //分入力欄作成
    $selection_name = explode(',', $selection_form_ini_array[$form_num]['selection_name2']);
    $selection_value = explode(',', $selection_form_ini_array[$form_num]['selection_value2']);
    
    $start_min_html .= '<input type="text" size="20" value="00" list="'.$form_num.'_startmin_datalist" name="'.$form_num.'_startmin" id="'.$form_num.'_startmin" class="form-text" onchange="time_input(this.id,startmin_datalist);" style="width: 37px;" autocomplete="off" onclick="open_multiple_pulldown(this,startmin_datalist);" onblur="close_multiple_pulldown(startmin_datalist,event.relatedTarget);">';
    $end_min_html .= '<input type="text" size="20" value="00" list="'.$form_num.'_endmin_datalist" name="'.$form_num.'_endmin" id="'.$form_num.'_endmin"class="form-text" onchange="time_input(this.id,endmin_datalist);" style="width: 37px;" autocomplete="off"  onclick="open_multiple_pulldown(this,endmin_datalist);" onblur="close_multiple_pulldown(endmin_datalist,event.relatedTarget);">';
    $start_min_html .= '<div id="startmin_datalist" class="multiple_pulldown_box">';
    $end_min_html .= '<div id="endmin_datalist" class="multiple_pulldown_box">';
    for($i = 0; $i < count($selection_name); $i++)
    {
        $start_min_html .= '<div>';
        $start_min_html .= '<input type="button" value="'.$selection_value[$i].'" id="startmin_datalist_nav" style="background-color: rgba(0,0,0,0); border: none;" onclick="time_set(this.value,'.$form_num.'_startmin,startmin_datalist);">';
        $start_min_html .= '</div>';
        $end_min_html .= '<div>';
        $end_min_html .= '<input type="button" value="'.$selection_value[$i].'" id="endmin_datalist_nav" style="background-color: rgba(0,0,0,0); border: none;" onclick="time_set(this.value,'.$form_num.'_endmin,endmin_datalist);">';
        $end_min_html .= '</div>';
    }  
    $start_min_html .= '</div>';
    $end_min_html .= '</div>';    
    
    $time_html = $start_hour_html."時".$start_min_html."分　～ ".$end_hour_html."時".$end_min_html."分";
    $time_html .= '<input type="hidden" value="0" id="time_input_flag">';
    return $time_html;    
}

/***************************************************************************
function input_pulldown_set($form_num)


引数1    $form_num


戻り値	$input_pulldown_html  	時刻入力HTML
***************************************************************************/

function input_pulldown_set($form_num){
    
    //初期設定
    require_once("f_Form.php");
    require_once("f_DB.php");   
    
    //変数
    $setting_array = setting_array_get();
    $input_pulldown_html = "";
    
    //処理
    $input_pulldown_html .= '<input type="text" name="'.$form_num.'_text" list="'.$form_num.'_datalist" size="60" class="form-text" id = "'.$form_num.'_text" autocomplete="off" onchange="input_pulldown_value_set(this); input_check(this.id,60,5,1);">';
    $input_pulldown_html .= '<datalist id="'.$form_num.'_datalist">';
    
    if($_SESSION["pagename"] == "MONTHSCHEDULE")
    {
        $hyozi_flag = 3;
    }
    else
    {
        $hyozi_flag = $setting_array["topappointment"];
    }
    $sql = "SELECT t1.customer_id,t1.customer_name,t1.customer_abbreviation,t1.user_id,t2.user_name,t1.delete_flag FROM customer_table AS t1 ";
    $sql .= "LEFT JOIN user_table AS t2 ON t1.user_id = t2.user_id ";
    $sql .= " WHERE t1.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";    
    $con = dbconect();
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $input_pulldown_html .= '<option value="'.$result_row["customer_name"].'　('.$result_row['user_name'].')" data-id="'.$result_row["customer_id"].'" data-userid="'.$result_row["user_id"].'" data-customername="'.$result_row['customer_name'].'" data-abbreviation="'.$result_row["customer_abbreviation"].'" data-deleteflag="'.$result_row['delete_flag'].'"></option>';
    }   
    $input_pulldown_html .= '</datalist>';
    $input_pulldown_html .= '<input type="hidden" name="'.$form_num.'_hidden" id="'.$form_num.'_hidden">';   
    return $input_pulldown_html;
}

/************************************************************************************************************
function make_report_Form()


引数1	$post                       前の画面からの情報

戻り値	$report_input_html          面談報告書入力欄HTML
************************************************************************************************************/
	
function make_report_Form($post){  
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once('f_Form.php');
    require_once('f_DB.php');
    
    //定数
    $pagename = $_SESSION['pagename'];
    $insert_form_num = explode(',', $form_ini_array[$pagename]['insert_form_num']);
    
    //変数
    $form_html = '';
    $report = array();
    $setting_array = setting_array_get();
    $form_data = array();
    $counter = 0;
    $updatetime = "";
    $create = "";
    $tabindex = "";
    
    //処理
    $con = dbconect();
    $sql = 'SELECT t1.schedule_id,t1.customer_id,t1.authoer,t1.interview_date,
            t1.interview_partner_name,t1.interview_content,t1.next_appointment_date,t1.check_status,
            t1.comment,t2.customer_name,t3.user_id,t3.checker,t1.update_time
            FROM interview_report_table AS t1 
            LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id
            LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id';
    $schedule_id = $post['REPORTCREATE_button'];
    $sql .= ' WHERE schedule_id = '.$schedule_id.';';    
    $result = $con->query($sql);
    if($result->num_rows == "0")
    {
        $flag = 0;      //新規登録
        for($i = 0; $i < count($insert_form_num); $i++)
        {
            if($insert_form_num[$i] == "REPORTCREATEauthoer")
            {
                $report[$insert_form_num[$i]] = $_SESSION["loginuser"]["user_name"];
            }
            elseif($insert_form_num[$i] == "REPORTCREATEinterviewdate")
            {
                $sql = "SELECT schedule_date FROM schedule_table WHERE schedule_id = '".$schedule_id."';";
                $result = $con->query($sql);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $report[$insert_form_num[$i]] = $result_row['schedule_date'];
                }
            }
            else
            {
                $report[$insert_form_num[$i]] = "";
            }
        }
    }
    else
    {
        $flag = 1;      //編集
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($insert_form_num); $i++)
            {
                $report[$insert_form_num[$i]] = $result_row[$form_ini_array[$insert_form_num[$i]]['column_name']];
            }
            $report["check_status"] = $result_row['check_status']; 
            $report["comment"] = $result_row['comment'];
            $report["checker"] = $result_row['checker'];
            $user_id = $result_row['user_id'];
            $updatetime = $result_row['update_time'];
        }
        $setting_array = setting_array_get();
        $create_flag = $setting_array["REPORTCREATE"];
        if(in_array($user_id, $_SESSION['hyozi_user_list'][$create_flag]))
        {
            $create = "";
            $tabindex = "0";
        }
        else
        {
            $create = "disabled";
            $tabindex = "-1";
        }            
    }
    
    //入力フォーム作成
    $form_colum = $form_ini_array[$pagename]["insert_form_num"];
    $form_num = explode(',', $form_colum);
    $form_html .= '<table class="form_table">';

    for($i = 0; $i < count($form_num); $i++)
    {
        $item_name = $form_ini_array[$form_num[$i]]["item_name"];
        $field_type = $form_ini_array[$form_num[$i]]["field_type"];
        $form_size = $form_ini_array[$form_num[$i]]["form_size"];        
        $isnotnull = $form_ini_array[$form_num[$i]]["isnotnull"];
        $max_length = $form_ini_array[$form_num[$i]]["max_length"];
        $form_format = $form_ini_array[$form_num[$i]]["form_format"];
        $onchange = 'input_check(this.id,'.$max_length.','.$form_format.','.$isnotnull.');';
                
        $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
        $form_html .= '<td>';

        //入力欄作成
        switch ($field_type)
        {
            case '1':   
                if($form_num[$i] == "REPORTCREATEcustomername")
                {
                    $sql = "SELECT t1.customer_id,t2.customer_name FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id WHERE t1.schedule_id = '".$schedule_id."';";
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $form_html .= '<input type="text" value="'.$result_row['customer_name'].'" class="form-text disabled" size="'.$form_size.'" tabindex="-1">';
                        $form_html .= '<input type="hidden" value="'.$result_row['customer_id'].'" name="'.$form_num[$i].'" id="'.$form_num[$i].'">';
                        $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                        $form_html .= '</td></tr>';  
                    }
                }
                elseif($form_num[$i] == "REPORTCREATEpartnername")
                {
                    $form_html .= '<input type="text" value="'.$report[$form_num[$i]].'" class="form-text '.$create.'" tabindex="'.$tabindex.'" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'" list="'.$form_num[$i].'_datalist" autocomplete="off">';
                    $sql = "SELECT customer_id FROM `schedule_table` WHERE schedule_id = '".$schedule_id."';";
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $customer_id = $result_row['customer_id'];
                    }
                    $sql = "SELECT DISTINCT interview_partner_name FROM interview_report_table WHERE customer_id = '".$customer_id."';";
                    $result = $con->query($sql);
                    $form_html .= '<datalist id="'.$form_num[$i].'_datalist">';
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $form_html .= '<option value="'.$result_row['interview_partner_name'].'"></option>';
                    }
                    $form_html .= '</datalist>';
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>'; 
                }
                else
                {
                    $form_html .= '<input type="text" value="'.$report[$form_num[$i]].'" class="form-text '.$create.'" tabindex="'.$tabindex.'" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';
                    $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                    $form_html .= '</td></tr>'; 
                }
                break;
            case '2':
                $form_html .= '<textarea name="'.$form_num[$i].'" id="'.$form_num[$i].'" rows="7" cols="60" class="form_textarea '.$create.'" tabindex="'.$tabindex.'" onchange="'.$onchange.'">'.$report[$form_num[$i]].'</textarea>';
                $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                $form_html .= '</td></tr>'; 
                break;
            case '5':
                $form_html .= '<input type="date" value="'.$report[$form_num[$i]].'" class="form-text '.$create.'" tabindex="'.$tabindex.'" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'"  onchange="'.$onchange.'">';
                $form_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
                $form_html .= '</td></tr>';      
                break;
        }
        
        //入力チェックデータ格納
        $form_data[$counter]['form_num'] = $form_num[$i];
        $form_data[$counter]['max_length'] = $max_length;
        $form_data[$counter]['form_format'] = $form_format;
        $form_data[$counter]['isnotnull'] = $isnotnull;
        $counter++;
    }
    
    //編集画面である場合、承認ステータスとコメントを作成する
    if($flag == 1)
    {
        if($_SESSION['loginuser']['user_id'] != $report['checker'] && $report['check_status'] == 2)
        {//承認ステータスが未確認かつログインユーザーが面談報告書承認者でない場合、承認ステータスとコメントは非表示とする
            $form_html .= '<input type="hidden" value="'.$report['check_status'].'" name="check_status" id="check_status">';
            $form_html .= '<a class="error" id="check_status_errormsg"></a>';
            $form_html .= '<input type="hidden" value="'.$report['comment'].'" name="comment" id="comment">';
            $form_html .= '<a class="error" id="comment_errormsg"></a>';
            $form_html .= '<input type="hidden" value="1" name="notice_type">';
        }
        else
        {
            if($_SESSION['loginuser']['user_id'] == $report['checker'])
            {//ログインユーザーが面談報告書承認者である 
                $disabled = '';
                $tabindex = '0';
                $notice_type = '0';
            }
            elseif($_SESSION['loginuser']['user_id'] != $report['checker'] && $report['check_status'] != 2)
            {//ログインユーザーが面談報告書承認者でないかつ承認ステータスが未確認以外である
                $form_html .= '<input type="hidden" value="2" name="check_status" id="check_status">';                
                $disabled = 'disabled';
                $tabindex = '-1';
                $notice_type = '1';
            }
            if($report['check_status'] == 0 || $report['check_status'] == 2)
            {
                $checked1 = 'checked';
                $checked2 = '';
            }
            elseif($report['check_status'] == 1)
            {
                $checked1 = '';
                $checked2 = 'checked';          
            }
            $form_html .= '<tr><td class="form_item_name">承認ステータス</td>';
            $form_html .= '<td>';
            $form_html .= '<input type="radio" value="0" name="check_status" '.$checked1.' '.$disabled.'>承認';
            $form_html .= '<input type="radio" value="1" name="check_status" '.$checked2.' '.$disabled.'>未承認';
            $form_html .= '<br><a class="error" id="check_status_errormsg"></a>';
            $form_html .= '</td></tr>';
            $form_html .= '<tr><td class="form_item_name">コメント</td>';
            $form_html .= '<td>';
            $form_html .= '<textarea name="comment" id="comment" rows="7" cols="60" class="form_textarea '.$disabled.'" tabindex="'.$tabindex.'" onchange="input_check(this.id,100,5,0);">'.$report['comment'].'</textarea>';
            $form_html .= '<br><a class="error" id="comment_errormsg"></a>';
            $form_html .= '<input type="hidden" value="'.$notice_type.'" name="notice_type">';         //面談報告書承認
            $form_html .= '</td></tr>';      
        }
        //入力チェックデータ格納
        $form_data[$counter]['form_num'] = "comment";
        $form_data[$counter]['max_length'] = "100";
        $form_data[$counter]['form_format'] = "5";
        $form_data[$counter]['isnotnull'] = 0;
        $counter++;
        $form_data[$counter]['form_num'] = "check_status";
        $form_data[$counter]['max_length'] = "0";
        $form_data[$counter]['form_format'] = "6";
        $form_data[$counter]['isnotnull'] = 0;
        $counter++;
    }
    $form_html .= '</table>';    
    $_SESSION['form_data'] = $form_data;
    
    //下のボタン作成
    if($flag == 1)
    {
        if($create == "" || $_SESSION['loginuser']['user_id'] == $report['checker'])
        {
            $form_html .= '<button type="submit" value="'.$schedule_id.'" class="modal_button" name="TOP_reportedit" id="report_button" onclick="return check(form_data,1);">更新</button>';
        }
    }
    else
    {
        $form_html .= '<button type="submit" value="'.$schedule_id.'" class="modal_button" name="TOP_reportinsert" id="report_button" onclick="return check(form_data,0);">登録</button>';
    }
    $form_html .= '<input type="hidden" id="updatetime" name="updatetime" value="'.$updatetime.'">';
    $form_html .= '<input type="hidden" id="schedule_id" name="schedule_id" value='.$schedule_id.'>';
    return $form_html;
}

/************************************************************************************************************
function report_read_Form()


引数1	$post                       前の画面からの情報

戻り値	$report_input_html          面談報告書入力欄HTML
************************************************************************************************************/
	
function report_read_Form($post){  
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once('f_Form.php');
    require_once('f_DB.php');
    
    //定数
    $pagename = $_SESSION['pagename'];
    $insert_form_num = explode(',', $form_ini_array[$pagename]['insert_form_num']);
    
    //変数
    $form_html = '';
    $report = array();
    
    //処理
    $con = dbconect();
    $sql = 'SELECT t1.schedule_id,t1.customer_id,t1.authoer,t1.interview_date,
            t1.interview_partner_name,t1.interview_content,t1.next_appointment_date,t1.check_status,
            t1.comment,t2.customer_name,t3.user_id,t3.checker
            FROM interview_report_table AS t1 
            LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id
            LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id';
    $schedule_id = $post['REPORTCREATE_read'];
    $sql .= ' WHERE schedule_id = '.$schedule_id.';';   
    $result = $con->query($sql);
    if($result->num_rows == "0")
    {
        $flag = 0;      //新規登録
        for($i = 0; $i < count($insert_form_num); $i++)
        {
            $report[$insert_form_num[$i]] = "";
        }
    }
    else
    {
        $flag = 1;      //編集
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($insert_form_num); $i++)
            {
                $report[$insert_form_num[$i]] = $result_row[$form_ini_array[$insert_form_num[$i]]['column_name']];
            }
            $report["check_status"] = $result_row['check_status']; 
            $report["comment"] = $result_row['comment'];
            $report["checker"] = $result_row['checker'];
        }
    }   

    //入力フォーム作成
    $form_colum = $form_ini_array[$pagename]["insert_form_num"];
    $form_num = explode(',', $form_colum);
    $form_html .= '<table class="form_table">';   
    for($i = 0; $i < count($form_num); $i++)
    {
        $item_name = $form_ini_array[$form_num[$i]]["item_name"];
        $field_type = $form_ini_array[$form_num[$i]]["field_type"];
        $form_size = $form_ini_array[$form_num[$i]]["form_size"];        
        
        $form_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
        $form_html .= '<td>';
        //入力欄作成
        switch ($field_type)
        {
            case '1':   
                if($form_num[$i] == "REPORTCREATEcustomername")
                {
                    $sql = "SELECT t1.customer_id,t2.customer_name FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id WHERE t1.schedule_id = '".$schedule_id."';";
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $form_html .= '<div class="readonly_form">'.$result_row['customer_name'].'<div>';
                        $form_html .= '</td></tr>';
                    }
                }
                else
                {
                    $form_html .= '<div class="readonly_form">'.$report[$form_num[$i]].'<div>';
                    $form_html .= '</td></tr>';
                }
                break;
            case '2':
                $form_html .= '<div class="readonly_form">'.nl2br($report[$form_num[$i]]).'<div>';
                $form_html .= '</td></tr>';                    
                break;
            case '5':
                $form_html .= '<div class="readonly_form">'.$report[$form_num[$i]].'<div>';
                $form_html .= '</td></tr>';                       
                break;
        }    
    }    
    switch ($report['check_status'])
    {
        case '0':
            $check_status = '承認';
            break;
        case '1':
            $check_status = '未承認';
            break;
        case '2':
            $check_status = '未確認';
            break;
    }
    $form_html .= '<tr><td class="form_item_name">承認ステータス</td>';
    $form_html .= '<td>';
    $form_html .= '<div class="readonly_form">'.$check_status.'<div>';
    $form_html .= '</td></tr>';
    $form_html .= '<tr><td class="form_item_name">コメント</td>';
    $form_html .= '<td>';
    $form_html .= '<div class="readonly_form">'.nl2br($report['comment']).'<div>';
    $form_html .= '</td></tr>';
    $form_html .= '</table>';
    return $form_html;
}

/************************************************************************************************************
function make_mitsumori_Form()


引数1   	$insert                       前の画面からの情報

戻り値	$mitsumori_input_html          面談報告書入力欄HTML
************************************************************************************************************/
	
function make_mitsumori_Form($insert,$pagename){  
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini', true);
    require_once('f_DB.php');
    
    //変数
    $mitsumori_input_html = '';
    $form_type = "insert_form_num";
    $form_num = explode(',', $form_ini_array[$pagename][$form_type]);
    
    //入力欄作成
    $mitsumori_input_html .= '<table class="form_table">';
    for($i = 0; $i < count($form_num); $i++)
    {
        $item_name = $form_ini_array[$form_num[$i]]["item_name"];
        $field_type = $form_ini_array[$form_num[$i]]["field_type"];
        $form_size = $form_ini_array[$form_num[$i]]["form_size"];        
        $max_length = $form_ini_array[$form_num[$i]]["max_length"];
        $form_format = $form_ini_array[$form_num[$i]]["form_format"];
        $isnotnull = $form_ini_array[$form_num[$i]]["isnotnull"];
        $onchange = 'input_check(this.id,'.$max_length.','.$form_format.','.$isnotnull.');';
            
        
        $mitsumori_input_html .= '<tr><td class="form_item_name">'.$item_name.'</td>';
        $mitsumori_input_html .= '<td>';
        //入力欄作成
        switch ($field_type){
            case '1':
                if($form_num[$i] == "ANKcode")
                {
                    $mitsumori_input_html .= '<input type="text" value="(auto)" class="form-text disabled" tabindex="-1" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'">';     
                }
                elseif($form_num[$i] == "ANKname")
                {
                    $mitsumori_input_html .= '<input type="text" value="'.$insert['MATname'].'" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';          
                }
                else
                {
                    $mitsumori_input_html .= '<input type="text" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';          
                }
                break;
            case '2':
                $mitsumori_input_html .= '<textarea name="'.$form_num[$i].'" id="'.$form_num[$i].'" rows="7" cols="60" class="form_textarea" onchange="'.$onchange.'">'.$insert['MATcontent'].'</textarea>';
                break;
            case '3': 
                $mitsumori_input_html .= mitsumori_pulldown_set($form_num[$i],$form_type,$onchange);
                break;
            case '5':
                $mitsumori_input_html .= '<input type="date" value="" class="form-text" size="'.$form_size.'" name="'.$form_num[$i].'" id="'.$form_num[$i].'" onchange="'.$onchange.'">';      
                break;
            case '9':
                $mitsumori_input_html .= mitsumori_input_pulldown($form_num[$i]);
                break;
            default :
                $mitsumori_input_html .= '<div class="readonly_form">ー<div>';
                break;
        }    
        $mitsumori_input_html .= '<br><a class="error" id="'.$form_num[$i].'_errormsg"></a>';
        $mitsumori_input_html .= '</td></tr>';
    }
    $mitsumori_input_html .= '</table>';    
    return $mitsumori_input_html;    
}

/************************************************************************************************************
function mitsumori_input_pulldown()


引数    $form_num

戻り値	$pulldown_html  	プルダウンHTML
************************************************************************************************************/
	
function mitsumori_input_pulldown($form_num){  
    
    //初期設定
    require_once("f_Form.php");
    require_once("f_DB.php");
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    
    //変数
    $pulldown_html = "";
    $disabled = "";
    
    //処理
    $con = mitsumori_dbconect();
    $onchange = "input_check('".$form_num."',40,5,1);";
    $pulldown_html .= '<input type="text" class="form-text" name="'.$form_num.'" id="'.$form_num.'" onchange="'.$onchange.'" list="'.$form_num.'_datalist">';
    $pulldown_html .= '<datalist id="'.$form_num.'_datalist">';
    $sql = "SELECT KYAID,KOKYAKUMEI FROM kokyakumaster WHERE DELETEFLAG = '0';";
    $i = 0;
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pulldown_html .= '<option value="'.$result_row['KOKYAKUMEI'].'" data-id="'.$result_row['KYAID'].'"></option>';
    }        
    $pulldown_html .= '</datalist>';
    return $pulldown_html;
}

/************************************************************************************************************
function mitsumori_pulldown_set()


引数    $form_num

戻り値	$pulldown_html  	プルダウンHTML
************************************************************************************************************/
	
function mitsumori_pulldown_set($form_num,$form_type,$onchange){  
    
    //初期設定
    require_once("f_Form.php");
    require_once("f_DB.php");
    $selection_form_ini_array = parse_ini_file("./ini/selection_form.ini",true);
    
    //変数
    $pulldown_html = "";
    $disabled = "";
    $id_list = array();
    $name_list = array();    
    
    //処理
    $con = mitsumori_dbconect();
    if($form_num == "ANKuser")
    {
        $sql = "SELECT USRID,HYOJIMEI FROM loginuserinfo WHERE DELETEFLAG = '0';";
        $i = 0;
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $id_list[$i] = $result_row['USRID'];
            $name_list[$i] = $result_row['HYOJIMEI'];
            $i++;
        }
    }
    elseif($form_num == "ANKstatus")
    {
        $id_list = explode(',', $selection_form_ini_array[$form_num]['selection_value']);
        $name_list = explode(',', $selection_form_ini_array[$form_num]['selection_name']);
        $disabled = "";
    }
    $pulldown_html .= '<select name="'.$form_num.'" id="'.$form_num.'" class="pulldown '.$disabled.'" onchange="'.$onchange.'">';
    for($i = 0; $i < count($id_list); $i++)
    {
        $pulldown_html .= '<option value="'.$id_list[$i].'">';
        $pulldown_html .= $name_list[$i];
        $pulldown_html .= '</option>';
    }
    $pulldown_html .= '</select>';
    
    return $pulldown_html;
}

/***************************************************************************
function make_setting_Form()


引数1       なし    


戻り値	$setting_form_html     	表示設定入力欄HTML
***************************************************************************/

function make_setting_Form(){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once('f_Form.php');
    require_once('f_DB.php');
    
    //定数
    $setting_form_num = explode(',', $form_ini_array['SETTING']['setting_form_num']);
    
    //変数
    $setting_form_html = '';
    
    //入力フォーム作成
    $con = dbconect();
    $setting_form_html .= '<table class="form_table">';
    for($i = 0; $i < count($setting_form_num); $i++)
    {
        $item_name = $form_ini_array[$setting_form_num[$i]]['item_name'];
        $setting_form_html .= '<tr><td class="form_item_name" style="width: 200px;">'.$item_name.'</td>';
        $setting_form_html .= '<td>';
        $setting_form_html .= '<select name="'.$setting_form_num[$i].'" id="'.$setting_form_num[$i].'" class="pulldown">';
        if($setting_form_num[$i] == "SETtopyozitu")
        {
                //ユーザーリスト作成
                $sql = "SELECT user_id,user_name FROM user_table;";
                $result = $con->query($sql);
                $user_list = array();
                $hyozi_user = array();
        
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $user_list[$result_row['user_id']] = $result_row['user_name'];
                }
                //入力欄作成
                if($_SESSION['loginuser']['user_status'] == "0")
                {
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',0']['value'] = $_SESSION['loginuser']['user_id'].',0';
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',0']['user_name'] = $user_list[$_SESSION['loginuser']['user_id']];
                    if($_SESSION['loginuser']['manager'] != "")
                    {
                        $hyozi_user[$_SESSION['loginuser']['manager'].',2']['value'] = $_SESSION['loginuser']['manager'].',2';
                        $hyozi_user[$_SESSION['loginuser']['manager'].',2']['user_name'] = $user_list[$_SESSION['loginuser']['manager']].'チーム';
                    }
                }
                if($_SESSION['loginuser']['user_status'] == "1")
                {
                    $user_id = $_SESSION['loginuser']['user_id'];
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',0']['value'] = $_SESSION['loginuser']['user_id'].',0';
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',2']['value'] = $_SESSION['loginuser']['user_id'].',2';
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',0']['user_name'] = $user_list[$_SESSION['loginuser']['user_id']];
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',2']['user_name'] = $user_list[$_SESSION['loginuser']['user_id']].'のチーム';
                    $sql = "SELECT user_id,user_status FROM user_table WHERE manager = '".$user_id."' AND user_id != '".$user_id."' AND delete_flag = '0';";
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        if($result_row['user_status'] == 0)
                        { 
                            $hyozi_user[$result_row['user_id'].',0']['value'] = $result_row['user_id'].',0';
                            $hyozi_user[$result_row['user_id'].',0']['user_name'] = $user_list[$result_row['user_id']];
                        }                 
                        else
                        {                                           
                            $hyozi_user[$result_row['user_id'].',2']['value'] = $result_row['user_id'].',2';           
                            $hyozi_user[$result_row['user_id'].',2']['user_name'] = $user_list[$result_row['user_id']].'チーム';         
                        }
                    }
                    if($_SESSION['loginuser']['manager'] != "")
                    {
                        $hyozi_user[$_SESSION['loginuser']['manager'].',2']['value'] = $_SESSION['loginuser']['manager'].',2';           
                        $hyozi_user[$_SESSION['loginuser']['manager'].',2']['user_name'] = $user_list[$_SESSION['loginuser']['manager']].'チーム';   
                    }
                }
                if($_SESSION['loginuser']['user_status'] == "2")
                {
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',3']['value'] = $_SESSION['loginuser']['user_id'].',3';
                    $hyozi_user[$_SESSION['loginuser']['user_id'].',3']['user_name'] = $user_list[$_SESSION['loginuser']['user_id']];
                }
                
                //登録済みデータを選択肢に追加する
                $sql = "SELECT *FROM `setting_table` WHERE user_id = '".$_SESSION['loginuser']['user_id']."';";
                $result = $con->query($sql);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $hyozi_flag = explode(',', $result_row['topyozitu']);
                    $user_name = $user_list[$hyozi_flag[0]];
                    if($hyozi_flag[1] == "1" || $hyozi_flag[1] == "2")
                    {
                        $user_name .= 'チーム';
                    }
                    $hyozi_user[$result_row['topyozitu']]['value'] = $result_row['topyozitu'];
                    $hyozi_user[$result_row['topyozitu']]['user_name'] = $user_name;
                }
                    
                foreach($hyozi_user as $key => $value)
                {
                    $setting_form_html .= '<option value="'.$value['value'].'">'.$value['user_name'].'</option>';
                }                  
        }
        else
        {
            if($_SESSION['loginuser']['user_status'] == "2")
            {
                $setting_form_html .= '<option value="3">すべてのデータ</option>';
            }
            else
            {
                $setting_form_html .= '<option value="0">自分のデータのみ</option>';        
                $setting_form_html .= '<option value="1">自分＋隣接する部下のデータ</option>';
                $setting_form_html .= '<option value="2">自分＋配下の社員のデータ</option>';           
            }
        }
        $setting_form_html .= '</select>';   
        $setting_form_html .= '</td></tr>';        
    }
    $setting_form_html .= '</table>';
    $setting_form_html .= '<input type="submit" name="SETTINGupdate" value="登録" class="modal_button">';
    return $setting_form_html;
}

/***************************************************************************
function make_targetamount_Form()


引数1       なし    


戻り値	$targeramount_form_html     	目標金額入力欄HTML
***************************************************************************/

function make_amount_Form($period,$start_month){
    
    //初期設定
    require_once("f_DB.php");
    
    //変数
    $setting_array = setting_array_get();
    $hyozi_flag = $setting_array['TARGETAMOUNT'];
    $amount_form_html = '';
    $form_data = array();
    
    //ヘッダー部分作成
    $amount_form_html .= '<div class="list_scroll">';
    $amount_form_html .= '<table><tr>';
    $amount_form_html .= '<th>社員名</th>';
    
    //金額項目名(12ヶ月分)作成
    for($i = 0; $i < 12; $i++)
    {
        $month = $start_month + $i;        
        //年越し計算
        if($month > 12)
        {
            $month = $month - 12;
        }
        $amount_form_html .= '<th>'.$month.'月</th>';
    }
    $amount_form_html .= '</tr>';
    
    //入力対象社員取得
    $con = dbconect();
    
    //在籍社員取得
    $user_list1 = array();
    $sql = "SELECT user_id,user_name FROM user_table WHERE user_status != 2 AND delete_flag = 0 AND user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ;";
    $result = $con->query($sql);
    $i = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_list1[$i]['user_id'] = $result_row['user_id'];
        $user_list1[$i]['user_name'] = $result_row['user_name'];
        $i++;
    }
    
    //目標額が入力済みかつ削除済み社員を取得
    $user_list2 = array();
    $sql = "SELECT t1.user_id,t1.user_name FROM user_table AS t1 LEFT JOIN target_amount_table as t2 ON t1.user_id = t2.user_id ";
    $sql .= "WHERE period = '".$period."' AND delete_flag = 1 AND t1.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ;";
    $result = $con->query($sql);
    $i = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_list2[$i]['user_id'] = $result_row['user_id'];
        $user_list2[$i]['user_name'] = $result_row['user_name'];
        $i++;
    }
    
    //二つの社員情報を結合する
    $user_list = array_merge($user_list1,$user_list2);
    
    //目標金額データ取得
    $amount_data = array();
    $sql = "SELECT *FROM user_table AS t1 LEFT JOIN target_amount_table as t2 ON t1.user_id = t2.user_id ";
    $sql .= "WHERE period = '".$period."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 1; $i <= 12; $i++)
        {
            $amount_data[$result_row['user_id']]['amount'.$i] = $result_row['amount'.$i];
        }
    }
    
    $counter = 0;
    for($i = 0; $i < count($user_list); $i++)
    {
        $amount_form_html .= '<tr>';
        $amount_form_html .= '<th>'.$user_list[$i]['user_name'];
        $amount_form_html .= '<input type="hidden" name="user_id[]" value="'.$user_list[$i]['user_id'].'">';
        $amount_form_html .= '</th>';
        for($k = 0; $k < 12; $k++)
        {
            $month = $start_month + $k;        
            //年越し計算
            if($month > 12)
            {
                $month = $month - 12;
            }
            if(isset($amount_data[$user_list[$i]['user_id']]))
            {
                $value = $amount_data[$user_list[$i]['user_id']]['amount'.$month];
            }
            else
            {
                $value = 0;
            }
            $form_num = $user_list[$i]['user_id'].'_'.$month;
            $amount_form_html .= '<td><input type="text" name="'.$form_num.'" id="'.$form_num.'" class="amount_form" value="'.$value.'" onchange="input_check(this.id,0,8,0);"></td>';
        
            //入力チェックデータ格納
            $form_data[$counter]['form_num'] = $form_num;
            $form_data[$counter]['max_length'] = 0;
            $form_data[$counter]['form_format'] = 8;
            $form_data[$counter]['isnotnull'] = 0;
            $counter++;
        }
        $amount_form_html .= '</tr>';
    }
    
    $_SESSION['form_data'] = $form_data;
    $amount_form_html .= '</table>';
    $amount_form_html .= '</div>';
    $amount_form_html .= '<input type="hidden" name="period" value="'.$period.'">';
    $amount_form_html .= '<input type="submit" name="TARGETAMOUNTupdate" value="登録" class="modal_button" onclick="return check(form_data,3);">';
    return $amount_form_html;
}

?>

