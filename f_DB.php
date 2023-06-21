<?php
/***************************************************************************
function dbconect()


引数			なし

戻り値	$con	mysql接続済みobjectT
***************************************************************************/

function dbconect(){

    //iniファイル読み取り準備
    $db_ini_array = parse_ini_file('./ini/DB.ini',true);

    //iniファイル内情報取得処理
    $host = $db_ini_array['database']['host'];
    $user = $db_ini_array['database']['user'];
    $password = $db_ini_array['database']['userpass'];
    $database = $db_ini_array['database']['database'];

    //DBアクセス処理
    $con = new mysqli($host,$user,$password, $database, '3306') or die('1'.$con->error);
	
    //utf8を使用する
    $con->set_charset('utf8') or die('2'.$con->error);
    
    return ($con);
}

/************************************************************************************************************
function setting_array_get()


引数1        なし

戻り値	$setting_array
************************************************************************************************************/
	
function setting_array_get(){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true); 
    require_once('f_DB.php');
    
    //変数
    $setting_array = array();
    $setting_form_num = explode(',', $form_ini_array['SETTING']['setting_form_num']);
    $sql = "";
    
    //処理
    $sql = "SELECT *FROM setting_table WHERE user_id = '".$_SESSION['loginuser']['user_id']."';";
    $con = dbconect();
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 0; $i < count($setting_form_num); $i++)
        {
            $setting_array[$form_ini_array[$setting_form_num[$i]]['column_name']] = $result_row[$form_ini_array[$setting_form_num[$i]]['column_name']];
        }
    }
    return $setting_array;
}

/***************************************************************************
function mitsumori_dbconect()


引数			なし

戻り値	$con	mysql接続済みobjectT
***************************************************************************/

function mitsumori_dbconect(){
	
    //iniファイル読み取り準備
    $db_ini_array = parse_ini_file('./ini/DB.ini',true);

    //iniファイル内情報取得処理
    $host = $db_ini_array['mitsumori_database']['host'];
    $user = $db_ini_array['mitsumori_database']['user'];
    $password = $db_ini_array['mitsumori_database']['userpass'];
    $database = $db_ini_array['mitsumori_database']['database'];

    //DBアクセス処理
    $con = new mysqli($host,$user,$password, $database, '3306') or die('1'.$con->error);
	
    //utf8を使用する
    $con->set_charset('utf8') or die('2'.$con->error);
    
    return ($con);
}

/************************************************************************************************************
function login($login_id,$login_password)


引数1	$login_id                 ログインID
引数2	$login_password               ログインパスワード

戻り値	$log_result					ログイン結果(true:正常、false:エラー)
************************************************************************************************************/
	
function login($login_id,$login_password){

    //初期設定
    require_once('f_DB.php');

    //定数
    $Loginsql = "select * from `user_table` where `login_id` = '".$login_id."' AND `login_password` = '".$login_password."' AND `delete_flag` = '0';";

    //変数
    $log_result = false;
    $rownums = 0;

    //ログイン検索処理
    $con = dbconect();
    $result = $con->query($Loginsql);
    $rownums = $result->num_rows;
    $result_row = $result->fetch_array(MYSQLI_ASSOC);
    
    //ログイン判断処理
    if ($rownums == 1)
    {
        //ログイン社員情報をセッション変数に格納する
        $_SESSION['loginuser']['user_id'] = $result_row['user_id'];
        $_SESSION['loginuser']['user_name'] = $result_row['user_name'];
        $_SESSION['loginuser']['user_status'] = $result_row['user_status'];
        $_SESSION['loginuser']['manager'] = $result_row['manager'];
        $log_result = true;
    }
    return ($log_result);	
}

/************************************************************************************************************
function hyozi_user_list($user_id)


引数1   $user_id                   ユーザーID

戻り値	$hyozi_user_list		表示対象社員リスト
 * [0] 自分のユーザーID
 * [1] 自分のユーザーID + 隣接する部下のユーザーID
 * [2] 自分のユーザーID + 配下全員のユーザーID
 * [3] 全社員のユーザーID
************************************************************************************************************/
	
function hyozi_user_list($user_id){
    
    //初期設定
    require_once('f_DB.php');	
    
    //変数
    $hyozi_user_list = array();
    $sql = "";
    
    //処理
    $con = dbconect();
    
    //自分のユーザー
    $hyozi_user_list[0][0] = $user_id;
    
    //隣接する部下を求める処理
    $sql = "SELECT `user_id` FROM `user_table` where `user_id` = '".$user_id."' OR `manager` = '".$user_id."';";
    $result = $con->query($sql);
    $i = 0;    
    $hyozi_user_list[1] = array();
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $hyozi_user_list[1][$i] = $result_row['user_id'];
        $i++;
    }
    
    //配下全員を求める処理
    $sql = "SELECT t1.user_id FROM user_table t1 inner join user_treepath_table t2 on t1.user_id = t2.descendant ";
    $sql .= "WHERE t2.ancestor = ".$user_id.";";
    $result = $con->query($sql);
    $i = 0;    
    $hyozi_user_list[2] = array();
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $hyozi_user_list[2][$i] = $result_row['user_id'];
        $i++;
    }
    
    //全社員のデータを求める
    $sql = "SELECT `user_id` FROM `user_table`;";
    $result = $con->query($sql);
    $i = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $hyozi_user_list[3][$i] = $result_row['user_id'];
        $i++;
    }
    return $hyozi_user_list;
}

/************************************************************************************************************
function update_loginuser()


引数1         なし

戻り値	なし
************************************************************************************************************/
	
function update_loginuser($user_id){
    
    //初期設定
    require_once('f_DB.php');	
    
    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    
    //ログインユーザー情報更新
    $sql = "SELECT *FROM `user_table` where `user_id` = '".$user_id."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $_SESSION['loginuser']['user_id'] = $result_row['user_id'];
        $_SESSION['loginuser']['user_name'] = $result_row['user_name'];
        $_SESSION['loginuser']['user_status'] = $result_row['user_status'];
        $_SESSION['loginuser']['manager'] = $result_row['manager'];
    }
}
/************************************************************************************************************
function makeList_item($sql,$post)


引数1	$sql                          SQL
引数2	$post                         前の画面から受け取った情報

戻り値	$list_html                    一覧表作成HTML
************************************************************************************************************/
	
function makeList_item($sql,$post){
    
    //初期設定
    require_once("f_DB.php");	
    $form_ini_array = parse_ini_file('./ini/form.ini',true);
    
    //定数
    $pagename = $_SESSION['pagename'];
    $list_item_num = explode(',', $form_ini_array[$pagename]['list_item_num']);
    $list_item_name = explode(',', $form_ini_array[$pagename]['list_item_name']);
    $page_limit_num = 10;
    
    //変数
    $setting_array = setting_array_get();
    $list_html = '';
    $list_class='';
    $page_num = 0;
    $start_page_num = 0;
    $max_page_num = 0;
    $disabled = '';
    $class = 'table_button';
    
    //ページ判定処理
    if(isset($_SESSION['paging'][$pagename.'_paging']))
    {
        $page_num = $_SESSION['paging'][$pagename.'_paging'];
    }

    //SQLに表示範囲を追記する
    $start_page_num = $page_num * $page_limit_num;
    $sql[1] = $sql[1]." LIMIT ".$start_page_num.", ".$page_limit_num.";";
    $sql[0] = $sql[0].";";
    
    //一覧表ヘッダー作成処理    
    $list_html .= '<div class="list_scroll">';
    $list_html .= '<table><tr>';
    $list_html .= '<th>No</th>';
    for($i = 0; $i < count($list_item_name); $i++)
    {
        $list_html .= '<th>'.$list_item_name[$i].'</th>';
    }
    
    if($pagename != "REPORTLIST")
    {
        $list_html .= '<th>編集</th>';    
    }
    if($pagename == "REPORTLIST")
    {
        $list_html .= '<th>閲覧</th>';
    }
    $list_html .= '</tr>';
    
    //一覧表内容作成処理
    $con = dbconect();
    $result = $con->query($sql[1]) or ($judge = true);
    $counter = 0;
    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        //偶数行の場合は背景を灰色にする
        if(($counter % 2) == 1)
        {
            $list_class = ' class="list_stripe"';
        }        
        $list_html .= '<tr'.$list_class.'>';
        $list_html .= '<th>'.($start_page_num + $counter + 1).'</th>';
        for($i = 0; $i < count($list_item_num); $i++)
        {
            $columns = $form_ini_array[$list_item_num[$i]]['column_name'];
            if($result_row[$columns] != "")
            {
                $list_html .= '<td>'.$result_row[$columns].'</td>';
            }
            else
            {
                $list_html .= '<td>-</td>';
            }
        }
        
        if($pagename != "REPORTLIST")
        {           
            $table_num = $form_ini_array[$pagename]['table_num'];
            $edit_id = $result_row[$form_ini_array[$table_num.'id']['column_name']];
            if(isset($setting_array[$pagename]))
            {
                $hyozi_flag = $setting_array[$pagename];
                if(in_array($result_row['user_id'],$_SESSION["hyozi_user_list"][$hyozi_flag]))
                {
                    $disabled = '';   
                    $class = 'table_button';
                }
                else
                {
                    $disabled = 'disabled';
                    $class = 'table_disabled_button';
                }
            }
            $list_html .= '<td><input type="button" value="編集" onclick="open_edit_modal('.$edit_id.');" class="'.$class.'" '.$disabled.'></td>';
        }
        if($pagename == "REPORTLIST")
        {
            $list_html .= '<td><button type="submit" value="'.$result_row['schedule_id'].'" name="REPORTCREATE_read" class="table_button">閲覧</button></td>';
        }
        $list_html .= '</tr>';
        
        $list_class = '';
        $counter++;
    }    
    $list_html .= '</table>';
    $list_html .= '</div>';
    //画面下部分作成
    $result = $con->query($sql[0]) or ($judge = true);
    while ($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $list_count = $result_row['COUNT(*)'];
        $max_page_num = floor($list_count / $page_limit_num);          
        if(($list_count % $page_limit_num) == 0 && $list_count > 0)
        {
            $max_page_num = $max_page_num - 1;
        }        
    }
    $list_html .= '<div class="paging">';    
    
    //表示件数作成
    $list_html .= '<a class="list_count">'.($start_page_num + 1).' - '.(($page_num * $page_limit_num) + $counter).'件 / '.$list_count.'件</a>';
    
    //ページング部分作成    
    if($page_num == 0)
    {
        $list_html .= '<a class="paging_num" style="background: #ccc;">最初へ</a>';
        $list_html .= '<a class="paging_num" style="background: #ccc;">前へ</a>';                
    }
    else
    {
        $list_html .= '<button type="submit" value="0" class="paging_num" name="'.$pagename.'_paging">最初へ</button>';
        $list_html .= '<button type="submit" value="'.($page_num - 1).'" class="paging_num" name="'.$pagename.'_paging">前へ</button>';        
    }
    
    //前5ページを表示する
    $min = $page_num -5;
    for($i = 0; $i < 5; $i++)
    {
        if(($min + $i) >= 0)
        {
            $list_html .= '<button type="submit" value="'.($i + $min).'" class="paging_num" name="'.$pagename.'_paging">'.($i + $min + 1).'</button>';   
        }
    }
    
    //現在のページを表示する
    $list_html .= '<a class="paging_num" style="color: white; background: #0078D4;">'.($page_num + 1).'</a>'; 

    //後ろ5ページ分を表示する
    for($i = 1; $i <= 5; $i++)
    {
        if(($page_num + $i) <= $max_page_num)
        {
            $list_html .= '<button type="submit" value="'.($i + $page_num).'" class="paging_num" name="'.$pagename.'_paging">'.($i + $page_num + 1).'</button>';   
        }
    }
//    for($i = 0; $i <= $max_page_num; $i++)
//    {
//        if($i == $page_num)
//        {
//            $list_html .= '<a class="paging_num" style="color: white; background: #0078D4;">'.($i + 1).'</a>'; 
//        }
//        else
//        {
//            $list_html .= '<button type="submit" value="'.$i.'" class="paging_num" name="'.$pagename.'_paging">'.($i + 1).'</button>';   
//        }
//    }
    if($page_num == $max_page_num)
    {
        $list_html .= '<a class="paging_num" style="background: #ccc;">次へ</a>';
        $list_html .= '<a class="paging_num" style="background: #ccc;">最後へ</a>';
    }
    else
    {    
        $list_html .= '<button type="submit" value="'.($page_num + 1).'" class="paging_num" name="'.$pagename.'_paging">次へ</button>';
        $list_html .= '<button type="submit" value="'.$max_page_num.'" class="paging_num" name="'.$pagename.'_paging">最後へ</button>';
    }
    
    $list_html .= '</div>';
    return $list_html;
}

/************************************************************************************************************
function insert($insert,$pagename)


引数1   $insert                            登録情報
引数2   $pagename                     画面名

戻り値	なし
************************************************************************************************************/
	
function insert($insert,$pagename){
    
    //初期設定
    require_once('f_DB.php');
    require_once('f_SQL.php');
    
    //変数
    $judge = false;
    $sql = '';
    
    //処理
    $con = dbconect();
    $sql = insertSQL($insert,$pagename);
    $con->query($sql);
    
    //ユーザー組織構造登録と表示設定登録
    if($pagename == 'USER')
    {
        insert_user_tree($insert);
        insert_user_setting();
    }
}

/************************************************************************************************************
function update($update,$pagename)


引数1	$update                       更新情報
引数2   $pagename                     画面名

戻り値	なし
************************************************************************************************************/
	
function update($update,$pagename){
    
    //初期設定
    require_once('f_DB.php');
    require_once('f_SQL.php');
    
    //変数
    $judge = false;
    $sql = '';    
    
    //処理
    $con = dbconect();
    $sql = updateSQL($update,$pagename);
    $con->query($sql);
    
    //ユーザー組織構造登録,表示設定更新
    if($pagename == 'USER')
    {
        update_user_tree($update);
    }
}

/************************************************************************************************************
function delete($delete,$pagename)


引数1	$delete                       削除情報
引数2   $pagename                     画面名

戻り値	なし
************************************************************************************************************/
	
function delete($delete,$pagename){
    
    //初期設定
    require_once('f_DB.php');
    require_once('f_SQL.php');
    
    //変数
    $judge = false;
    $sql = '';    
    
    //処理
    $sql = deleteSQL($delete,$pagename);
    $con = dbconect();
    $con->query($sql);
            
//    //ユーザー組織構造登録
//    if($pagename == 'USER')
//    {
//        delete_user_tree($delete);
//    }
}

/************************************************************************************************************
function insert_user_tree($insert)


引数    なし
	
戻り値	なし
************************************************************************************************************/
	
function insert_user_tree($insert){
    
    //初期設定
    require_once('f_DB.php');

    //変数
    $sql = '';
    
    //処理
    $con = dbconect();
    $descendant = $insert['USEmanager'];           //子のユーザーID
    $ancestor = "";             //親のユーザーID    
    $sql = "SELECT MAX(user_id) FROM user_table;";
    $result = $con->query($sql) or ($judge = true);
    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $ancestor = $result_row['MAX(user_id)'];
    }
    
    //ユーザー組織構造登録SQL
    if($descendant == "")
    {//上位者が「指定なし」の場合
        $sql = "INSERT INTO `user_treepath_table`(ancestor,descendant) VALUES (".$ancestor.",".$ancestor.");";
    }
    else
    {
        $sql = "INSERT INTO user_treepath_table(ancestor,descendant) ";
        $sql .= "SELECT t1.ancestor, ".$ancestor." FROM user_treepath_table as t1 ";
        $sql .= "WHERE t1.descendant = ".$descendant;
        $sql .= " UNION ALL SELECT ".$ancestor.", ".$ancestor.";";   
    }
    $con->query($sql);
}

/************************************************************************************************************
function insert_user_setting()


引数    なし
	
戻り値	なし
************************************************************************************************************/
	
function insert_user_setting(){
    
    //初期設定
    require_once('f_DB.php');
    $form_ini_array = parse_ini_file("./ini/form.ini",true);
    
    //変数
    $sql = "";
    $setting_form_num = explode(',', $form_ini_array['SETTING']['setting_form_num']);
    
    //ユーザーID取得処理
    $con = dbconect();
    $sql = "SELECT MAX(user_id) FROM user_table;";
    $result = $con->query($sql) or ($judge = true);    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_id = $result_row['MAX(user_id)'];
    }
    
    $sql = "INSERT INTO setting_table(";
    $sql .= "user_id";
    for($i = 0; $i < count($setting_form_num); $i++)
    {
        $sql .= ','.$form_ini_array[$setting_form_num[$i]]['column_name'];
    }
    $sql .= ') VALUE(';
    $sql .= $user_id;
    for($i = 0; $i < count($setting_form_num); $i++)
    {
        if($setting_form_num[$i] == 'SETtopyozitu')
        {
            $sql .= ",'".$user_id.",0'";
        }
        else
        {
            $sql .= ",'0'";
        }
    }
    $sql .= ");";
    
    $con->query($sql);
}
/************************************************************************************************************
function delete_user_tree($delete)


引数    $delete     削除情報
	
戻り値	なし
************************************************************************************************************/
	
function delete_user_tree($delete){
    
    //初期設定
    require_once('f_DB.php');

    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    
    //ユーザー組織構造登録SQL
    $sql = "DELETE FROM user_treepath_table WHERE ancestor = ".$delete['edit_id']." OR descendant = ".$delete['edit_id'].";";    
    $con->query($sql);       
}

/************************************************************************************************************
function update_user_tree($update)


引数    $update     更新情報
	
戻り値	なし
************************************************************************************************************/
	
function update_user_tree($update){
    
    //初期設定
    require_once('f_DB.php');

    //変数
    $sql = "";
    $descendant_array = array();
    $ancestor_array = array();
    
    //処理
    $con = dbconect();
    
    //ユーザー組織構造削除
    $sql = "SELECT descendant FROM `user_treepath_table` WHERE ancestor = '".$update['edit_id']."';";
    $result = $con->query($sql);
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $descendant_array[$counter] = $result_row['descendant'];
        $counter++;
    }
    
    $sql = "SELECT ancestor FROM `user_treepath_table` WHERE descendant = '".$update['edit_id']."' AND ancestor != descendant;";
    $result = $con->query($sql);
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $ancestor_array[$counter] = $result_row['ancestor'];
        $counter++;
    }
    
    $sql = "DELETE FROM `user_treepath_table` WHERE descendant IN (".implode(",",$descendant_array).") ";
    $sql .= "AND ancestor IN (".implode(",",$ancestor_array).") AND ancestor != descendant;";
    $con->query($sql);
    
    //ユーザー情報登録
    if($update['USEmanager'] != '')
    {
        $sql ="INSERT INTO `user_treepath_table` (ancestor,descendant) ";
        $sql .= " SELECT supertree.ancestor,subtree.descendant ";
        $sql .= " FROM `user_treepath_table` AS supertree ";
        $sql .= " CROSS JOIN `user_treepath_table` AS subtree ";
        $sql .= " WHERE supertree.descendant = ".$update['USEmanager']." ";
        $sql .= " AND subtree.ancestor = ".$update['edit_id'].";";
        $con->query($sql);
    }
    
    //重複データを削除する
    $con->query('CREATE TABLE tmp as SELECT * FROM user_treepath_table GROUP BY ancestor,descendant;');
    $con->query('DROP TABLE user_treepath_table;');
    $con->query('ALTER TABLE tmp RENAME TO user_treepath_table;');
}

/************************************************************************************************************
function schedule_insert($insert,$pagename); 


引数1    $insert     登録情報
引数2    $pagename   画面名
	
戻り値	なし
************************************************************************************************************/
	
function schedule_insert($insert,$pagename){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $judge = false;
    $sql = "";    
    $start_time = $insert["SCHtime_starthour"].":".$insert["SCHtime_startmin"];
    $end_time = $insert["SCHtime_endhour"].":".$insert["SCHtime_endmin"];
    $customer_id = $insert["SCHcustomername_hidden"];
    
    //処理
    $con = dbconect();
    
    //顧客を新規登録するのか判定する
    if($insert["SCHcustomername_hidden"] == "")
    {
        $sql = "INSERT INTO `customer_table` (customer_name,customer_abbreviation,user_id,delete_flag,create_userid) VALUES ('".$insert["SCHcustomername_text"]."','".$insert["SCHcustomerabbreviation"]."','".$insert["SCHusername"]."','0','".$insert["SCHusername"]."');";
        $con->query($sql); 
        $sql = "SELECT MAX(customer_id) FROM customer_table;";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $customer_id = $result_row["MAX(customer_id)"];
        }
    }

    //スケジュール登録
    $sql = "INSERT INTO `schedule_table` (customer_id,authoer,schedule_date,start_time,end_time,create_userid) VALUES ('".$customer_id."','".$insert["SCHauthoer"]."','".$insert["SCHdate"]."','".$start_time."','".$end_time."','".$insert["SCHusername"]."');";
    $con->query($sql);   
    
    //アポイント情報削除判定
    if($insert['appointment_flag'] != "")
    {
        $sql = "DELETE FROM `appointment_table` WHERE appointment_id = '".$insert['appointment_flag']."';";
        $con->query($sql);   
    }
}

/************************************************************************************************************
function schedule_edit($update,$pagename); 


引数1    $update     登録情報
引数2    $pagename   画面名
	
戻り値	なし
************************************************************************************************************/
	
function schedule_edit($update,$pagename){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $judge = false;
    $sql = "";    
    $start_time = $update["SCHtime_starthour"].":".$update["SCHtime_startmin"];
    $end_time = $update["SCHtime_endhour"].":".$update["SCHtime_endmin"];
    
    //処理
    $con = dbconect();
    
    //スケジュール登録
    $sql = "UPDATE `schedule_table` SET authoer = '".$update['SCHauthoer']."',schedule_date = '".$update['SCHdate']."',start_time  = '".$start_time."',end_time = '".$end_time."' WHERE schedule_id = '".$update['edit_id']."';";
    $con->query($sql);   
    
}

/************************************************************************************************************
function schedule_delete($delete,$pagename); 


引数1    $delete     登録情報
引数2    $pagename   画面名
	
戻り値	なし
************************************************************************************************************/
	
function schedule_delete($delete,$pagename){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
   
    //変数
    $sql = "";
    $delete_id = "";
    if(isset($delete['TOP_scheduledelete']))
    {
        $delete_id = $delete['TOP_scheduledelete'];
    }
    elseif(isset ($delete['MONTHSCHEDULE_scheduledelete']))
    {
        $delete_id = $delete['MONTHSCHEDULE_scheduledelete'];
    }
    
    //スケジュール削除処理
    if($delete_id != "")
    {
        $con = dbconect();
        $sql = "DELETE FROM `schedule_table` WHERE schedule_id = '".$delete_id."';";
        $con->query($sql);  
    }
}

/************************************************************************************************************
function appointment_delete($delete,$pagename); 


引数1    $delete     登録情報
引数2    $pagename   画面名
	
戻り値	なし
************************************************************************************************************/
	
function appointment_delete($delete,$pagename){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
   
    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    $sql = "DELETE FROM `appointment_table` WHERE appointment_id = '".$delete['TOP_appointmentdelete']."';";
    $con->query($sql);
}
/************************************************************************************************************
function makeList_appointment($sql,$post)


引数	なし

戻り値	$list_html                    一覧表作成HTML
************************************************************************************************************/
	
function makeList_appointment(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $setting_array = setting_array_get();
    $list_html = "";
    $sql = "";
    
    //処理
    $con = dbconect();
    $hyozi_flag = $setting_array['topappointment'];
    //$sql = "SELECT t1.appointment_id,t1.authoer,DATE_FORMAT(t1.appointment_date, '%m/%d') as appointment_date,t2.customer_name,t2.customer_abbreviation,t2.user_id FROM `appointment_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id WHERE t2.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
    $sql = "SELECT t1.appointment_id,t1.authoer,t1.appointment_date,t2.customer_id,t2.customer_name,t2.customer_abbreviation,t2.user_id FROM `appointment_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id WHERE t2.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
    $sql .= "ORDER BY t1.appointment_date ASC;";
    
    //ヘッダー部分作成
    $list_html .= '<div class="top_list_scroll" style=" max-height:calc(100% - 85px); max-width: 95%;">';
    $list_html .= "<table>";
    $list_html .= "<tr style='background:#89daf8;'>";
    $list_html .= "<th>日</th>";
    $list_html .= "<th>顧客名</th>";
    $list_html .= "<th>削除</th>";
    $list_html .= "</tr>";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $comment = '顧客名：'.$result_row['customer_name'].'&#13;&#10;日付：'.$result_row['appointment_date'].'&#13;&#10;作成者：'.$result_row['authoer'];
        $onclick_parameter = "'".$result_row['customer_id']."','".$result_row['customer_name']."','".$result_row['customer_abbreviation']."','".$result_row['user_id']."','".$result_row['appointment_id']."','".$result_row['appointment_date']."'";
        $list_html .= "<tr>";
        $list_html .= "<td>".date('m/d', strtotime($result_row['appointment_date']))."</td>";
        if($result_row['user_id'] == $_SESSION['loginuser']['user_id'])
        {
            $list_html .= '<td><a style="color: blue;text-decoration: underline;" title="'.$comment.'" onclick="open_appointment('.$onclick_parameter.');">'.$result_row["customer_abbreviation"].'</a></td>';
            $list_html .= '<td><button value="'.$result_row['appointment_id'].'" name="TOP_appointmentdelete"  class="table_button">削除</button></td>';
        }
        else
        {
            $list_html .= '<td><a title="'.$comment.'" onclick="">'.$result_row["customer_abbreviation"].'</a></td>';
            $list_html .= '<td><button value="'.$result_row['appointment_id'].'" name="TOP_appointmentdelete"  class="table_disabled_button" disabled>削除</button></td>';
        }
        $list_html .= "</tr>";
    }
    
    $list_html .=  "</table>";
    $list_html .= "</div>";
    return $list_html;
}

/************************************************************************************************************
function makeList_matter()


引数	なし

戻り値	$list_html                    一覧表作成HTML
************************************************************************************************************/
	
function makeList_matter(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $setting_array = setting_array_get();
    $list_html = "";
    $sql = "";
    
    //処理
    $con = dbconect();
    $hyozi_flag = $setting_array["topmatter"];
    $sql = "SELECT t1.matter_name,t2.customer_name,t2.customer_abbreviation,t1.matter_start_date,t1.matter_tender_date,t1.estimated_order_amount,CASE WHEN t1.order_status = '0' THEN '受注' WHEN t1.order_status = '1' THEN '未受注A' WHEN t1.order_status = '2' THEN '未受注B' WHEN t1.order_status = '3' THEN '未受注C' WHEN t1.order_status = '4' THEN '失注' END AS order_status FROM matter_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
    $sql .= "WHERE order_status != 0 AND order_status != 4 ";
    $sql .= "AND t2.user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
    $sql .= "ORDER BY t1.matter_start_date ASC;";
    
    //ヘッダー部分作成
    $list_html .= '<div class="top_list_scroll" style=" max-height:calc(100% - 85px); max-width: 98%;">';
    $list_html .= "<table>";
    $list_html .= "<tr style='background:#89daf8;'>";
    $list_html .= "<th>案件名</th>";
    $list_html .= "<th>顧客名</th>";
    $list_html .= "<th>受付日付</th>";
    $list_html .= "<th>開始日付</th>";
    $list_html .= "<th>ステータス</th>";
    $list_html .= "<th>予定受注額</th>";
    $list_html .= "</tr>";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $list_html .= "<tr>";
        $list_html .= "<td>".$result_row["matter_name"]."</td>";
        $list_html .= "<td>".$result_row["customer_abbreviation"]."</td>";
        $list_html .= "<td>".$result_row["matter_tender_date"]."</td>";
        $list_html .= "<td>".$result_row["matter_start_date"]."</td>";
        $list_html .= "<td>".$result_row["order_status"]."</td>";
        $list_html .= "<td>".$result_row["estimated_order_amount"]."</td>";
        $list_html .= "</tr>";
    }
    
    $list_html .=  "</table>";
    $list_html .= "</div>";
    return $list_html;
}
/************************************************************************************************************
function makeSchedule_week()


引数	なし

戻り値	$schedule_html                    一覧表作成HTML
************************************************************************************************************/
	
function makeSchedule_week(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //定数
    $week_array = array('日', '月', '火', '水', '木', '金', '土');
            
    //変数
    $setting_array = setting_array_get();
    $hyozi_flag = $setting_array["topschedule"];
    $create_flag = $setting_array["REPORTCREATE"];
    $schedule_html = '';
    
    //処理    
    $con = dbconect();
    
    if(isset($_SESSION['topschedule']))
    {
        $start_date = $_SESSION['topschedule']['TOP_topschedule'];
    }
    else
    {
        $start_date = date("Y-m-d", strtotime("-1 day"));
    }
    
    $start_date_hyozi = new DateTime($start_date);
    $start_date_hyozi = $start_date_hyozi->format('Y年m月d日');
    
    $schedule_html .= '<div class="top_content_subtitle">';    
    $schedule_html .= $start_date_hyozi.'～';                    

    $back_date = new DateTime($start_date.'-7 day');
    $back_date = $back_date->format('Y-m-d');
    $next_date = new DateTime($start_date.'+7 day');
    $next_date = $next_date->format('Y-m-d');
                
    $schedule_html .= '<button type="submit" name="TOP_topschedule" value="'.$back_date.'" class="table_button">＜</button>';
    $schedule_html .= '<button type="submit" name="TOP_topschedule" value="'.$next_date.'" class="table_button">＞</button>';
    $schedule_html .= '<input type="submit" value="月表示" name="MONTHSCHEDULE_button" class="table_button">';
    $schedule_html .= '<input type="submit" value="面談報告書一覧" name="REPORTLIST_button" class="table_button">';    
    $schedule_html .= '</div>';    
    $schedule_html .= '<div class="week_schedule_table">';
    $schedule_html .= '<table><tr>';
    //ヘッダー部分作成
    for($i = 0; $i < 7; $i++)
    {        
        $datetime = new DateTime($start_date.' +'.$i.' day');
        $week = $datetime->format('w');
        $date = $datetime->format('m/d');
        switch ($week)
        {
            case '0':
                $class_name = 'sunday';
                break;
            case '6':
                $class_name = 'saturday';
                break;
            default :
                $class_name = '';
                break;
        }
        $schedule_html .= '<th class="'.$class_name.'">'.$date.'('.$week_array[$week].')</th>';
    }
    $schedule_html .= '</tr>';
    
    //内容作成処理
    for($i = 0; $i < 7; $i++)
    {
        $datetime = new DateTime($start_date.' +'.$i.' day');
        $date = $datetime->format('Y-m-d');
        $sql = "SELECT t1.schedule_id,t1.authoer,t1.schedule_date,DATE_FORMAT(t1.start_time,'%H:%i') AS start_time,DATE_FORMAT(t1.end_time,'%H:%i') AS end_time,t2.customer_name,t2.customer_abbreviation,user_name,t4.interview_report_id,t2.user_id FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id ";                
        $sql .= "LEFT JOIN `interview_report_table` AS t4 ON t1.schedule_id = t4.schedule_id ";
        $sql .= "WHERE t1.schedule_date = '".$date."' ";
        $sql .= "AND t1.create_userid IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
        $sql .= "ORDER BY start_time ASC;";
        $result = $con->query($sql);
        $schedule_html .= '<td>';
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];                             
            if($date <= date('Y-m-d') && $result_row['interview_report_id'] == '')
            {//過去のスケジュールかつ面談報告書未登録
                if(in_array($result_row['user_id'], $_SESSION['hyozi_user_list'][$create_flag]))
                {
                    $disabled = '';
                }
                else
                {
                    $disabled = 'disabled';
                }
                $data_flag = '0';
                $style = 'red';
            }
            elseif($date <= date('Y-m-d') && $result_row['interview_report_id'] != '')
            {//過去のスケジュールかつ面談報告書登録済み
                $data_flag = '1';
                $style = '#FA9996';
                $disabled = '';
            }
            else
            {//未来のスケジュール
                $data_flag = '0';
                $style = 'blue';
                $disabled = 'disabled';
            }            
            //スケジュール作成
            $schedule_html .= '<button class="schedule_content" name="REPORTCREATE_button" title="'.$comment.'" style="color: '.$style.';" value="'.$result_row['schedule_id'].'" data-flag="'.$data_flag.'" '.$disabled.'>';
            $schedule_html .= $result_row['start_time'].'～'.$result_row['end_time'].'<br>';
            $schedule_html .= $result_row['customer_abbreviation'].'<br>';
            $schedule_html .= '</button>';
        }
        $schedule_html .= '</td>';
    }    
    $schedule_html .= '</table>';    
    $schedule_html .= '</div>';
    return $schedule_html;
}

/************************************************************************************************************
function makeSchedule_month()


引数	なし

戻り値	$schedule_html                    一覧表作成HTML
************************************************************************************************************/
	
function makeSchedule_month(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $setting_array = setting_array_get();
    $hyozi_flag = $setting_array['monthschedule'];
    $create_flag = $setting_array['REPORTCREATE'];
    $schedule_html = '';
    $right_content_html = '';
    $default_date_style = '';
    
    //処理
    $con = dbconect();
    if(isset($_SESSION['monthschedule']))
    {
        $date = $_SESSION['monthschedule']['MONTHSCHEDULE_monthschedule'];        
    }
    else
    {
        $date = date('Ym01');
    }
    
    if(date('Ym') == date('Ym', strtotime($date)))
    {
        $default_date = date('Ymd');
    }
    else 
    {
        $default_date = $_SESSION['monthschedule']['MONTHSCHEDULE_monthschedule'];
    }
    
    $month = date('m', strtotime($date));
    $year = date('Y', strtotime($date));
    $back_month = new DateTime($date.'-1 month');
    $back_month = $back_month->format('Ymd');
    $next_month = new DateTime($date.'+1 month');
    $next_month = $next_month->format('Ymd');
    
    $schedule_html .= '<div class="sub_title">';
    $schedule_html .= '<form action="./pageJump.php" method="post">';
    $schedule_html .= '<button type="submit" name="MONTHSCHEDULE_monthschedule" value="'.$back_month.'" class="table_button">＜</button>';
    $schedule_html .= $year.'年'.$month.'月';
    $schedule_html .= '<button type="submit" name="MONTHSCHEDULE_monthschedule" value="'.$next_month.'" class="table_button">＞</button>';
    $schedule_html .= '</form>';
    $schedule_html .= '</div>';    
    $schedule_html .= '<form action="./pageJump.php" method="post">';
    $schedule_html .= '<table id="month_schedule_area"><tr>';
    
    $schedule_html .= '<td style="width: 200px;">';
    $schedule_html .= '<div  class="monthschedule_side_area">';
    $schedule_html .= '<div class="monthschedule_side_title">社員リスト</div>';
    $schedule_html .= '<div class="monthschedule_side_content">';
    $schedule_html .= '<div  id="monthschedule_left_content">';
    $schedule_html .= '<table class="schedule_usercheckbox_table">';
    $sql = "SELECT *FROM user_table where user_id IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).");";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $schedule_html .= '<tr><td><label for="checkbox'.$result_row['user_id'].'" style="display: block; width: 100%; height: 100%;">';
        $schedule_html .= '<input type="checkbox" value="'.$result_row['user_id'].'" name="userlist_checkbox" onclick="hidden_schedule();" id="checkbox'.$result_row['user_id'].'">';
        $schedule_html .= $result_row["user_name"].'<br>';
        $schedule_html .= '</label></td></tr>';
    }
    $schedule_html .= '</table>';
    $schedule_html .= '</div>';
    $schedule_html .= '</div>';
    $schedule_html .= '</div>';
    $schedule_html .= '</td>';
    $schedule_html .= '<td>';
    $schedule_html .= '<div class="monthschedule_scroll">';
    $schedule_html .= '<table class="monthschedule_calendar_table" id="monthschedule_calendar_table"><tr><th class="sunday">日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th class="saturday">土</th></tr>';
    $calendar = makeCalendar($month,$year);
    $td_cnt = 0;
    
    //カレンダー部分表示処理
    for($i = 0; $i < count($calendar); $i++)
    {
        switch ($td_cnt)
        {
            case '0':
                $schedule_html .= '<tr>';
                $date_color = 'red';
                break;
            case '6':
                $date_color = 'blue';
                break;
            default :
                $date_color = 'black';
                break;
        }
        
        if($calendar[$i]['day'] != "")
        {
            $opendate = new DateTime($date.'+'.($calendar[$i]['day'] - 1).' day');
            $opendate = $opendate->format('Ymd');            
            if($default_date == $opendate)
            {
                $default_date_style = 'background-color: #e0efff;';
            }
            else
            {
                $default_date_style = '';
            }
            
            $schedule_html .= '<td onclick="open_all_schedule('.$opendate.',this);" style="'.$default_date_style.'">';
            $schedule_html .= '<div class="month_calendar_td">';
            $schedule_html .= '<a style="font-size: 12px; color: '.$date_color.';">';
            $schedule_html .= $calendar[$i]['day'].'<br>';
            $schedule_html .= '</a>';
            $sql = "SELECT t1.schedule_id,t1.authoer,t1.schedule_date,DATE_FORMAT(t1.start_time,'%H:%i') AS start_time,DATE_FORMAT(t1.end_time,'%H:%i') AS end_time,t2.customer_name,t2.customer_abbreviation,t3.user_name,t3.user_id,t4.interview_report_id,t1.create_userid FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id ";                
            $sql .= "LEFT JOIN `interview_report_table` AS t4 ON t1.schedule_id = t4.schedule_id ";
            $sql .= "WHERE t1.schedule_date = '".$opendate."' ";
            $sql .= "AND t1.create_userid IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
            $sql .= "ORDER BY start_time ASC;";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];                        
                if($opendate <= date('Ymd') && $result_row['interview_report_id'] == '')
                {//過去のスケジュールかつ面談報告書未登録
                    if(in_array($result_row['user_id'], $_SESSION['hyozi_user_list'][$create_flag]))
                    {
                        $disabled = '';
                    }
                    else
                    {
                        $disabled = 'disabled';
                    }
                    $data_flag = '0';
                    $style = 'red';
                }
                elseif($opendate <= date('Ymd') && $result_row['interview_report_id'] != "")
                {
                    $style = '#FA9996';
                    $data_flag = '1';
                    $disabled = '';
                }
                else
                {
                    $style = 'blue';
                    $data_flag = '0';
                    $disabled = 'disabled';
                }
                $schedule_html .= '<button class="schedule_content user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: '.$style.';" value="'.$result_row['schedule_id'].'" data-flag="'.$data_flag.'" '.$disabled.'>';
                $schedule_html .= $result_row['start_time'].'　'.$result_row['customer_abbreviation'].'<br>';
                $schedule_html .= '</button>';
            }         
        }
        else
        {
            $schedule_html .= '<td>';
            $schedule_html .= '<div class="month_calendar_td">';
            $schedule_html .= '<a style="font-size: 12px; color: '.$date_color.';">';
            $schedule_html .= $calendar[$i]['day'].'<br>';
            $schedule_html .= '</a>';            
        }
        $schedule_html .= '</div>';
        $schedule_html .= '</td>';
        if($td_cnt == 6)
        {
            $schedule_html .= '</tr>';
            $td_cnt = 0;
        }
        else
        {
            $td_cnt++;
        }
    }
    //右側に表示するスケジュール
    $sql = "SELECT t1.schedule_id,t1.authoer,t1.schedule_date,DATE_FORMAT(t1.start_time,'%H:%i') AS start_time,DATE_FORMAT(t1.end_time,'%H:%i') AS end_time,t2.customer_name,t3.user_name,t3.user_id,t4.interview_report_id,t1.create_userid FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id ";                
    $sql .= "LEFT JOIN `interview_report_table` AS t4 ON t1.schedule_id = t4.schedule_id ";
    $sql .= "WHERE t1.schedule_date = '".$default_date."' ";
    $sql .= "AND t1.create_userid IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";
    $sql .= "ORDER BY start_time ASC;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];                        
        if($default_date <= date('Ymd') && $result_row['interview_report_id'] == '')
        {//過去のスケジュールかつ面談報告書未登録
            if(in_array($result_row['user_id'], $_SESSION['hyozi_user_list'][$create_flag]))
            {
                $disabled = '';
            }
            else
            {
                $disabled = 'disabled';
            }
            $data_flag = '0';
            $style = 'red';
        }
        elseif($default_date <= date('Ymd') && $result_row['interview_report_id'] != "")
        {
            $style = '#FA9996';
            $data_flag = '1';
            $disabled = '';
        }
        else
        {
            $style = 'blue';
            $data_flag = '0';
            $disabled = 'disabled';
        }        
        $right_content_html .= '<button class="all_schedule user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: '.$style.';" value="'.$result_row['schedule_id'].'" data-flag="'.$data_flag.'" '.$disabled.'>';
        $right_content_html .= '時間：'.$result_row['start_time'].'～'.$result_row['end_time'].'<br>';
        $right_content_html .= '顧客名：'.$result_row['customer_name'].'<br>';
        $right_content_html .= '担当者：'.$result_row['user_name'].'<br>';
        $right_content_html .= '</button>';
    }    
    $schedule_html .= '</table>';
    $schedule_html .= '</div>';
    $schedule_html .= '</td>';
    $schedule_html .= '<td style="width: 20%;">';
    $schedule_html .= '<div  class="monthschedule_side_area">';
    $schedule_html .= '<div class="monthschedule_side_title" id="monthschedule_right_title">';
    $week_array = array('日', '月', '火', '水', '木', '金', '土');
    $datetime = new DateTime($default_date);
    $week = $datetime->format('w');
    $schedule_html .= date('n月j日', strtotime($default_date))."(".$week_array[$week].")";
    $schedule_html .= '</div>';
    $schedule_html .= '<div class="monthschedule_side_content">';
    $schedule_html .= '<div id="monthschedule_right_content">';
    $schedule_html .= $right_content_html;
    $schedule_html .= '</div>';
    $schedule_html .= '</div>';
    $schedule_html .= '</td>';    
    $schedule_html .= '</tr></table>';
    $schedule_html .= '</form>';    
    return $schedule_html;
}

/************************************************************************************************************
function makeCalendar()


引数1	$month                       月情報
引数2   $year                        年情報

戻り値	$calendar                    カレンダーデータ作成
************************************************************************************************************/
	
function makeCalendar($month,$year){    

    // 月末日を取得
    $last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));
    $calendar = array();
    $j = 0;

    // 月末日までループ
    for ($i = 1; $i < $last_day + 1; $i++) 
    {
        // 曜日を取得
        $week = date('w', mktime(0, 0, 0, $month, $i, $year));

        // 1日の場合
        if ($i == 1) 
        {
            // 1日目の曜日までをループ
            for ($s = 1; $s <= $week; $s++) 
            {
                // 前半に空文字をセット
                $calendar[$j]['day'] = '';
                $j++;
            }
        }

        // 配列に日付をセット
        $calendar[$j]['day'] = $i;
        $j++;

        // 月末日の場合
        if ($i == $last_day) 
        {
            // 月末日から残りをループ
            for ($e = 1; $e <= 6 - $week; $e++) 
            {
                // 後半に空文字をセット
                $calendar[$j]['day'] = '';
                $j++;
            }
        }
    }    
    return $calendar;
}

/************************************************************************************************************
function report_insert($insert,$pagename)


引数1	$insert                      登録情報
引数2   $pagename                    画面名

戻り値	なし
************************************************************************************************************/
	
function report_insert($insert,$pagename){  
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $judge = false;
    $sql = "";    
    
    //処理
    $con = dbconect();    

    //情報取得
    $sql = "SELECT t1.user_id,t2.report_create_notice,t2.report_check_notice FROM `customer_table` AS t1 LEFT JOIN `user_table` AS t2 ON t1.user_id = t2.user_id WHERE t1.customer_id = '".$insert['REPORTCREATEcustomername']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_id = $result_row['user_id'];
        $report_create_notice = explode(',', $result_row['report_create_notice']);
    }
    
    //面談報告書登録
    $sql = "INSERT INTO `interview_report_table` (schedule_id,customer_id,authoer,interview_date,interview_partner_name,interview_content,next_appointment_date,check_status,comment,create_userid) ";
    $sql .= "VALUES('".$insert['TOP_reportinsert']."','".$insert['REPORTCREATEcustomername']."','".$insert['REPORTCREATEauthoer']."','".$insert['REPORTCREATEinterviewdate']."','".$insert['REPORTCREATEpartnername']."','".$insert['REPORTCREATEcontent']."','".$insert['REPORTCREATEnextappointmentdate']."','2','','".$user_id."');";
    $con->query($sql);  
    
    //アポイント情報登録
    $sql = "INSERT INTO `appointment_table` (schedule_id,authoer,appointment_date,customer_id,create_userid) ";
    $sql .= "VALUES('".$insert['TOP_reportinsert']."','".$insert['REPORTCREATEauthoer']."','".$insert['REPORTCREATEnextappointmentdate']."','".$insert['REPORTCREATEcustomername']."','".$user_id."');";
    $con->query($sql);
    
        
    //同一のお知らせの削除を行う
    same_notice_delete($insert['TOP_reportinsert']);
    
    //お知らせ通知
    for($i = 0; $i < count($report_create_notice); $i++)
    {
        $sql = "INSERT INTO `notice_table` (notice_date,schedule_id,customer_id,notice_content,notice_receiver,notice_sender) ";
        $sql .= "VALUES('".date('Y-m-d')."','".$insert['TOP_reportinsert']."','".$insert['REPORTCREATEcustomername']."','面談報告書作成','".$report_create_notice[$i]."','".$_SESSION['loginuser']['user_id']."');";
        $con->query($sql);
    }
   
}

/************************************************************************************************************
function report_update($update,$pagename)


引数1	$update                      登録情報
引数2   $pagename                    画面名

戻り値	なし
************************************************************************************************************/
	
function report_update($update,$pagename){  
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $judge = false;
    $sql = "";    
    
    //処理
    $con = dbconect();    

    //面談報告書更新
    $sql = "UPDATE `interview_report_table` SET authoer = '".$update['REPORTCREATEauthoer']."',interview_partner_name = '".$update['REPORTCREATEpartnername']."',interview_content = '".$update['REPORTCREATEcontent']."',next_appointment_date = '".$update['REPORTCREATEnextappointmentdate']."',check_status = '".$update['check_status']."',comment = '".$update['comment']."' ";
    $sql .= "WHERE schedule_id = '".$update['TOP_reportedit']."';";
    $con->query($sql);  
    
    //アポイント情報更新    
    $sql = "UPDATE `appointment_table` SET appointment_date = '".$update['REPORTCREATEnextappointmentdate']."' ";
    $sql .= "WHERE schedule_id = '".$update['TOP_reportedit']."';";
    $con->query($sql);
    
        
    //同一のお知らせの削除を行う
    same_notice_delete($update['TOP_reportedit']);
    
    //お知らせ通知
    $sql = "SELECT t1.user_id,t2.report_create_notice,t2.report_check_notice FROM `customer_table` AS t1 LEFT JOIN `user_table` AS t2 ON t1.user_id = t2.user_id WHERE t1.customer_id = '".$update['REPORTCREATEcustomername']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_id = $result_row['user_id'];
        if($update['notice_type'] == '0' && $update['check_status'] == '0')
        {
            $notice_content = '面談報告書承認';
            $notice_user = explode(',', $result_row['report_check_notice']);
        }
        elseif($update['notice_type'] == '0' && $update['check_status'] == '1')
        {
            $notice_content = '面談報告書未承認';
            $notice_user = explode(',', $result_row['report_check_notice']);
        }
        elseif($update['notice_type'] == '1')
        {
            $notice_content = '面談報告書編集';
            $notice_user = explode(',', $result_row['report_create_notice']);
        }
    }

    //お知らせ通知
    for($i = 0; $i < count($notice_user); $i++)
    {
        $sql = "INSERT INTO `notice_table` (notice_date,schedule_id,customer_id,notice_content,notice_receiver,notice_sender) ";
        $sql .= "VALUES('".date('Y-m-d')."','".$update['TOP_reportedit']."','".$update['REPORTCREATEcustomername']."','".$notice_content."','".$notice_user[$i]."','".$_SESSION['loginuser']['user_id']."');";
        $con->query($sql);
    }
    //承認、未承認時は担当者に通知を送る
    if(($update['check_status'] == '0' || $update['check_status'] == '1') && (in_array($user_id, $notice_user) == false))
    {
        $sql = "INSERT INTO `notice_table` (notice_date,schedule_id,customer_id,notice_content,notice_receiver,notice_sender) ";
        $sql .= "VALUES('".date('Y-m-d')."','".$update['TOP_reportedit']."','".$update['REPORTCREATEcustomername']."','".$notice_content."','".$user_id."','".$_SESSION['loginuser']['user_id']."');";
        $con->query($sql);
    }
    
}

/************************************************************************************************************
function same_notice_delete()


引数1   $schedule_id


戻り値	なし
************************************************************************************************************/
function same_notice_delete($schedule_id)
{
    //初期設定
    require_once("f_DB.php");
    
    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    
    //お知らせの削除を行う
    $sql = "DELETE FROM notice_table WHERE schedule_id = '".$schedule_id."';";
    $con->query($sql);
    
}

/************************************************************************************************************
function mitsumori_insert($insert,$pagename)


引数1   $insert                      登録情報
引数2   $pagename                    画面名

戻り値	なし
************************************************************************************************************/
	
function mitsumori_insert($insert,$pagename){  
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $judge = false;
    $sql = "";    
    
    //処理
    $con = mitsumori_dbconect();
    
    //案件コード作成処理
    $sql = "SELECT ANUKEY FROM jisyamaster;";
    $result =  $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $anukey = $result_row['ANUKEY'];
    }
   
    $sql = "SELECT PSUKEY FROM loginuserinfo WHERE USRID = '".$insert['ANKuser']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pukey = $result_row['PSUKEY'];
    }
   $skey = $anukey.$pukey;
   
   $svalue = "";
   $sql = "SELECT lpad(SVALUE + 1,5,'0') AS SVALUE FROM saibanmaster WHERE SKEY = '".$skey."';";
   $result = $con->query($sql);
   while($result_row = $result->fetch_array(MYSQLI_ASSOC))
   {
       $svalue = $result_row['SVALUE'];
   }
   
   //SKEYが未登録である場合,採番マスタに新規で登録する
   if($svalue == "")
   {
       $sql = "INSERT INTO saibanmaster(SKEY,SVALUE) VALUES('".$skey."',0);";
       $con->query($sql);
       $svalue = '00001';
   }
   
   $ankucode = $skey.$svalue;
   
   //顧客ID取得
   $sql = "SELECT KYAID FROM kokyakumaster WHERE KOKYAKUMEI = '".$insert['ANKkyaku']."';";
   $result = $con->query($sql);
   if($result->num_rows > 0)
   {
       while($result_row = $result->fetch_array(MYSQLI_ASSOC))
       {
           $kyaid = $result_row['KYAID'];
       }
   }
   else
   {
        $sql = "INSERT INTO kokyakumaster(KOKYAKUMEI,DELETEFLAG,UPDATEUSER) VALUES('".$insert['ANKkyaku']."',0,1);";
        $con->query($sql);
        $sql = "SELECT MAX(KYAID) FROM kokyakumaster;";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $kyaid = $result_row['MAX(KYAID)'];
        }
   }
   
   if($insert['ANKdate'] == "")
   {
       $insert['ANKdate'] = 'null';
   }
   else
   {
       $insert['ANKdate'] = "'".$insert['ANKdate']."'";
   }
   
   //案件登録
   $sql = "INSERT INTO ankeninfo(ANKUCODE,ANKENMEI,RYAKUMEI,USRID,KYAID,TANTOMEI,STATUS,GAIYO,JISSIBI,UPDATEUSER,UPDATETIME) VALUES('".$ankucode."','".$insert['ANKname']."','".$insert['ANKryaku']."','".$insert['ANKuser']."','".$kyaid."','".$insert['ANKkyakuuser']."','".$insert['ANKstatus']."','".$insert['ANKnaiyou']."',".$insert['ANKdate'].",'1','".date("Y/m/d H:i:s")."');";
   $con->query($sql);
   
   //採番マスタ更新
   $sql = "UPDATE saibanmaster SET SVALUE = SVALUE + 1 WHERE SKEY = '".$skey."';";
   $con->query($sql);
}
   
/************************************************************************************************************
function order_management($sql,$post)


引数1   $sql                              登録情報
引数2   $post                            前の画面の情報

戻り値	なし
************************************************************************************************************/
	
function order_management($sql,$post){  
    
    //初期設定
    require_once("f_DB.php");
    $form_ini_array = parse_ini_file('./ini/form.ini',true);
    
    //定数
    $pagename = $_SESSION['pagename'];
    $list_item_num = explode(',', $form_ini_array[$pagename]['list_item_num']);
    $list_item_name = explode(',', $form_ini_array[$pagename]['list_item_name']);
    
    //変数
    $list_html = '';
    
    //一覧表ヘッダー作成処理
    $list_html .= '<div class="list_scroll">';
    $list_html .= '<table><tr>';
    for($i = 0; $i < count($list_item_name); $i++)
    {
        $list_html .= '<th>'.$list_item_name[$i].'</th>';
    }
    $list_html .= '</tr>';
    
    //一覧表内容作成処理
    $con = dbconect();
    
    //未受注A
    $list_html .= '<tr style="background: #e0e0e0;"><td colspan="6">未受注A</td></tr>';
    $sqlA = $sql[1]." AND t1.order_status = 1 ;";
    $result = $con->query($sqlA) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $list_html .= '<tr>';
        for($i = 0; $i < count($list_item_num); $i++)
        {
            $columns = $form_ini_array[$list_item_num[$i]]['column_name'];
            $list_html .= '<td>'.$result_row[$columns].'</td>';
        }
        $list_html .= '</tr>';
    }
    
    //未受注B
    $list_html .= '<tr style="background: #e0e0e0;"><td colspan="6">未受注B</td></tr>';
    $sqlB = $sql[1]." AND t1.order_status = 2 ;";
    $result = $con->query($sqlB) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $list_html .= '<tr>';
        for($i = 0; $i < count($list_item_num); $i++)
        {
            $columns = $form_ini_array[$list_item_num[$i]]['column_name'];
            $list_html .= '<td>'.$result_row[$columns].'</td>';
        }
        $list_html .= '</tr>';
    }
    
    //未受注C
    $list_html .= '<tr style="background: #e0e0e0;"><td colspan="6">未受注C</td></tr>';
    $sqlC = $sql[1]." AND t1.order_status = 3 ;";
    $result = $con->query($sqlC) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $list_html .= '<tr>';
        for($i = 0; $i < count($list_item_num); $i++)
        {
            $columns = $form_ini_array[$list_item_num[$i]]['column_name'];
            $list_html .= '<td>'.$result_row[$columns].'</td>';
        }
        $list_html .= '</tr>';
    }
    $list_html .= '</table>';
    $list_html .= '</div>';
    return $list_html;
}

/************************************************************************************************************
function get_setting_data();


引数1    なし
	
戻り値	なし
************************************************************************************************************/
	
function get_setting_data(){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once('f_Form.php');
    require_once('f_DB.php');
    
    //定数
    $setting_form_num = explode(',', $form_ini_array['SETTING']['setting_form_num']);
    $user_id = $_SESSION['loginuser']['user_id'];
    
    //変数
    $setting_data = array();
    
    //処理
    $con = dbconect();
    $sql = "SELECT *FROM `setting_table` WHERE user_id = '".$user_id."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 0; $i < count($setting_form_num); $i++)
        {
            $setting_data[$i]['form_num'] = $setting_form_num[$i];
            $setting_data[$i]['value'] = $result_row[$form_ini_array[$setting_form_num[$i]]['column_name']];            
        }
    }
    return $setting_data;
}

/************************************************************************************************************
function setting_update($update)


引数1        $update                      登録情報

戻り値	なし
************************************************************************************************************/
	
function setting_update($update){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $setting_form_num = explode(',', $form_ini_array['SETTING']['setting_form_num']);
    $judge = false;
    $sql = "";    
    
    //処理
    $con = dbconect();   
    $sql = "UPDATE setting_table SET ";
    for($i = 0; $i < count($setting_form_num); $i++)
    {
        $column = $form_ini_array[$setting_form_num[$i]]["column_name"];
        $sql .= $column." = '".$update[$setting_form_num[$i]]."',";
    }
    $sql = rtrim($sql,',');    
    $sql .= " WHERE user_id = '".$_SESSION['loginuser']['user_id']."';";
    $con->query($sql);
}

/************************************************************************************************************
function amount_update($update)


引数1        $update                      登録情報

戻り値	なし
************************************************************************************************************/
	
function amount_update($update){
    
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);    
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //変数
    $sql = "";    
    
    //処理
    $con = dbconect();   
    
    //POSTデータを配列に格納する
    $user_id = $update['user_id'];
    $update_data = array();
    
    $sql = "SELECT user_id FROM target_amount_table WHERE period = '".$update['period']."';";
    $result = $con->query($sql);
    $amount_data = array();
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $amount_data[$result_row['user_id']] = $result_row['user_id'];
    }
    
    for($i = 0; $i < count($user_id); $i++)
    {
        if(isset($amount_data[$user_id[$i]]))
        {
            $sql = "UPDATE target_amount_table SET ";
            for($k = 1; $k <= 12; $k++)
            {
                $sql .= "amount".$k." = ".$update[$user_id[$i].'_'.$k].",";
            }
            $sql = rtrim($sql,',');
            $sql .= " WHERE period = ".$update['period']." AND user_id = ".$user_id[$i].";";
        }
        else
        {
            $sql = "INSERT INTO target_amount_table (user_id,period";
            for($k = 1; $k <= 12; $k++)
            {
                $sql .= ",amount".$k;
            }
            $sql .= ") VALUES (".$user_id[$i].",".$update['period']."";
            for($k = 1; $k <= 12; $k++)
            {
                $sql .= ",".$update[$user_id[$i].'_'.$k];
            }
            $sql .= ");";
        }
        $con->query($sql);
    }
}

/************************************************************************************************************
function top_yozitu_data()


引数1        なし

戻り値	$yozitu_data             受注額予実管理データ
************************************************************************************************************/
	
function top_yozitu_data(){
    
    //初期設定
    require_once("f_DB.php");
    $system_ini_array = parse_ini_file("./ini/system.ini",true); 
    
    //変数
    $start_month = $system_ini_array['SYSTEM_SETTING']['start_month'];
    $start_date = date_format(date_create('NOW'), "Y").$start_month.'01';
    $start_date = date("Ymd",strtotime($start_date));
    $setting_array = setting_array_get();
    $hyozi_flag = explode(',', $setting_array['topyozitu']);
    
    //期を求める
    $start_year = $system_ini_array['SYSTEM_SETTING']['start_year'];
    $period = date_format(date_create('NOW'), "Y") - $start_year;
    if($start_month <=  date_format(date_create('NOW'), "n"))
    {
        $period = $period + 1;
    }
    
    //表示ユーザー情報取得
    $hyozi_user_list = hyozi_user_list($hyozi_flag[0]);
    
    //目標金額を求める
    $con = dbconect();
    $target_amount = array();
    $sql = "SELECT ";
    for($i = 1; $i <= 12; $i++)
    {
        $sql .= "SUM(amount".$i.") AS amount".$i.",";
    }
    $sql = rtrim($sql,',');
    $sql .= " FROM target_amount_table WHERE period = '".$period."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).") ;";
    $result = $con->query($sql);
    $target_year = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 1; $i <= 12; $i++)
        {
            $target_amount['amount'.$i] = $result_row['amount'.$i];
            $target_year = $target_year + $result_row['amount'.$i];
        }
    }
    
    //現在までの目標額を求める
    $target_now = 0;
    for($i = 0; $i < 12; $i++)
    {
        $month = $start_month + $i;
        if($month > 12)
        {
            $month = $month - 12;
        }
        $target_now = $target_now + $target_amount['amount'.$month];
        if($month == date_format(date_create('NOW'), "n"))
        {
            break;
        }
    }
    
    //現時点の達成額を求める   
    $end_date = date_format(date_create('NOW'), "Y").date_format(date_create('NOW'), "m").'01';
    $end_date = date("Ymd",strtotime($end_date));
    $sql = "SELECT SUM(order_amount) AS amount FROM matter_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
    $sql .= " WHERE order_status = 0 AND order_month BETWEEN '".$start_date."' AND '".$end_date."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).");";
    $result = $con->query($sql);
    $amount_now = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $amount_now = $amount_now + $result_row['amount'];
    }
    //期全体の達成額を求める
    $end_date = date("Ymd",strtotime($start_date . "+12 month -1 day"));
    $sql = "SELECT SUM(order_amount) AS amount FROM matter_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
    $sql .= " WHERE order_status = 0 AND order_month BETWEEN '".$start_date."' AND '".$end_date."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).");";
    $result = $con->query($sql);
    $amount_year = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $amount_year = $amount_year + $result_row['amount'];
    }
    
    //社員名を取得する
    $sql = "SELECT user_name,user_status FROM user_table WHERE user_id = '".$hyozi_flag[0]."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $user_name = $result_row['user_name'];
    }
    
    switch($hyozi_flag[1])
    {
        case '0':
            $user_name .= "個人";
            break;
        case '1':
            $user_name .= "チーム";
            break;
        case '2':
            $user_name .= "チーム";
            break;
        case '3':
            $user_name = "全社員";
            break;
    }
        
    //データを格納する
    $yozitu_data = array();
    $yozitu_data['user_name'] = $user_name;
    $yozitu_data['target_now'] = $target_now;
    $yozitu_data['target_year'] = $target_year;
    $yozitu_data['amount_now'] = $amount_now;
    $yozitu_data['amount_year'] = $amount_year;
    
    return $yozitu_data;
}

/************************************************************************************************************
function yozitu_data()


引数1        なし

戻り値	$yozitu_data             受注額予実管理データ
************************************************************************************************************/
	
function yozitu_data(){
    
    //初期設定
    require_once("f_DB.php");
    $system_ini_array = parse_ini_file("./ini/system.ini",true); 
    
    //変数
    $user_id = $_SESSION['loginuser']['user_id'];
    $user_statu = $_SESSION['loginuser']['user_status'];
    $user_manager = $_SESSION['loginuser']['manager'];
    $start_month = $system_ini_array['SYSTEM_SETTING']['start_month'];
    $start_date = date_format(date_create('NOW'), "Y").$start_month.'01';
    $start_date = date("Ymd",strtotime($start_date));
    
    //期を求める
    $start_year = $system_ini_array['SYSTEM_SETTING']['start_year'];
    $period = date_format(date_create('NOW'), "Y") - $start_year;
    if($start_month <=  date_format(date_create('NOW'), "n"))
    {
        $period = $period + 1;
    }
    
    //表示対象社員、チームを求める
    $con = dbconect();
    $yozitu_user = array();
    switch($user_statu){
        case '0':
            $yozitu_user[0] = $user_id.',0';
            if($user_manager != "")
            {
                $yozitu_user[1] = $user_manager.',2';
            }
            break;
        case '1':
            $yozitu_user[0] = $user_id.',0';
            $yozitu_user[1] = $user_id.',2';
            $counter = 2;
            $sql = "SELECT user_id,user_status FROM user_table WHERE manager = '".$user_id."' AND user_id != '".$user_id."';";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                if($result_row['user_status'] == 0)
                {
                    $yozitu_user[$counter] = $result_row['user_id'].',0';
                }
                else
                {
                    $yozitu_user[$counter] = $result_row['user_id'].',2';
                }
                $counter++;
            }
            if($user_manager != "")
            {
                $yozitu_user[$counter] = $user_manager.',2';
            }
            break;
        case '2':
            $yozitu_user[0] = $user_id.',3';
            break;
    }
    
    //受注管理データ取得
    $yozitu_data = array();
    for($i = 0; $i < count($yozitu_user); $i++)
    {
        //表示ユーザー情報取得
        $hyozi_flag = explode(',', $yozitu_user[$i]);
        $hyozi_user_list = hyozi_user_list($hyozi_flag[0]);
        
        //目標金額を求める
        $target_amount = array();
        $sql = "SELECT ";
        for($k = 1; $k <= 12; $k++)
        {
            $sql .= "SUM(amount".$k.") AS amount".$k.",";
        }
        $sql = rtrim($sql,',');
        $sql .= " FROM target_amount_table WHERE period = '".$period."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).");";
        $result = $con->query($sql);
        $target_year = 0;
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($k = 1; $k <= 12; $k++)
            {
                $target_amount['amount'.$k] = $result_row['amount'.$k];
                $target_year = $target_year + $result_row['amount'.$k];
            }
        }
        
        //現在の月までの目標額を求める
        $target_now = 0;
        for($k = 0; $k < 12; $k++)
        {
            $month = $start_month + $k;
            if($month > 12)
            {
                $month = $month - 12;
            }
            $target_now = $target_now + $target_amount['amount'.$month];
            if($month == date_format(date_create('NOW'), 'n'))
            {//現在の月まで計算したらループを抜ける
                break;
            }
        }
        
        //現在の月までの達成額を求める
        $end_date = date_format(date_create('NOW'), "Y").date_format(date_create('NOW'), "m").'01';
        $end_date = date("Ymd",strtotime($end_date));
        $sql = "SELECT SUM(order_amount) AS amount FROM matter_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
        $sql .= " WHERE order_status = 0 AND order_month BETWEEN '".$start_date."' AND '".$end_date."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).");";
        $result = $con->query($sql);
        $amount_now = 0;
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $amount_now = $amount_now + $result_row['amount'];
        }
        
        //期全体の達成額を求める
        $end_date = date("Ymd",strtotime($start_date . "+12 month -1 day"));
        $sql = "SELECT SUM(order_amount) AS amount FROM matter_table AS t1 LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
        $sql .= " WHERE order_status = 0 AND order_month BETWEEN '".$start_date."' AND '".$end_date."' AND user_id IN (".implode(",",$hyozi_user_list[$hyozi_flag[1]]).");";
        $result = $con->query($sql);
        $amount_year = 0;
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $amount_year = $amount_year + $result_row['amount'];
        }
        
        //社員名を取得する
        $sql = "SELECT user_name FROM user_table WHERE user_id = '".$yozitu_user[$i]."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $user_name = $result_row['user_name'];
        }

        switch($hyozi_flag[1])
        {
            case '0':
                $user_name .= "(個人)";
                break;
            case '1':
                $user_name .= "(チーム)";
                break;
            case '2':
                $user_name .= "(チーム)";
                break;
            case '3':
                $user_name = "全社員";
                break;
        }
        
        //データを格納する
        $yozitu_data[$i]['user_name'] = $user_name;
        $yozitu_data[$i]['target_now'] = $target_now;
        $yozitu_data[$i]['target_year'] = $target_year;
        $yozitu_data[$i]['amount_now'] = $amount_now;
        $yozitu_data[$i]['amount_year'] = $amount_year;
    }
    return $yozitu_data;
}

/************************************************************************************************************
function makeNotice_html()


引数1        なし

戻り値	$notice_data_html             お知らせデータHTML
************************************************************************************************************/
	
function makeNotice_html(){
    
    //初期設定
    require_once("f_DB.php");
    
    //変数
    $notice_data_html = "";
    $user_id = $_SESSION["loginuser"]["user_id"];
    $sql = "";
    
    //処理
    $sql = "SELECT t1.notice_date,t1.schedule_id,t1.notice_content,t2.customer_name,t2.customer_abbreviation,t3.user_name AS receiver,t4.user_name AS sender ";
    $sql .= " FROM notice_table AS t1 ";
    $sql .= " LEFT JOIN customer_table AS t2 ON t1.customer_id = t2.customer_id ";
    $sql .= " LEFT JOIN user_table AS t3 ON t1.notice_receiver = t3.user_id ";
    $sql .= " LEFT JOIN user_table AS t4 ON t1.notice_sender = t4.user_id ";
    $sql .= " WHERE t1.notice_receiver = '".$user_id."';";
    
    $con = dbconect();
    $result = $con->query($sql);
    $notice_data_html .= '<div class="notice_scroll">';
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $comment = '顧客名：'.$result_row['customer_name'].'&#13;&#10;送信者：'.$result_row['sender'].'&#13;&#10;受信者：'.$result_row['receiver'].'&#13;&#10;内容：'.$result_row['notice_content'];
        if($result_row['notice_content'] == "面談報告書未承認")
        {
            $style = 'color: red;';
        }
        else
        {
            $style = 'color: black;';
        }
        $notice_data_html .= '<button title="'.$comment.'" class="notice" name="REPORTCREATE_button" value="'.$result_row['schedule_id'].'" style="'.$style.'">';       
        $notice_data_html .= $result_row['notice_date'].'　'.$result_row['customer_abbreviation'].'　'.$result_row['notice_content'];      
        $notice_data_html .= '</button>';        
    }
    $notice_data_html .= '</div>';
    return $notice_data_html;
}

/************************************************************************************************************
function delete_notice()


引数1        なし

戻り値	なし
************************************************************************************************************/
	
function delete_notice($user_id,$schedule_id){
    
    //初期設定
    require_once("f_DB.php");
    
    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    $sql = "DELETE FROM notice_table WHERE schedule_id = '".$schedule_id."' AND notice_receiver = '".$user_id."';";
    $con->query($sql);
}

/************************************************************************************************************
function get_input_data()


引数1        なし

戻り値	なし
************************************************************************************************************/
	
function get_input_data($form_type,$pagename){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true); 
    
    //変数
    $form_data = array();
    $counter = 0;
    
    //処理
    if($form_ini_array[$pagename][$form_type] != "")
    {
        if($pagename == "MITSUMORI")
        {
            $form_num = "ANKname,ANKryaku,ANKuser,ANKkyaku,ANKkyakuuser,ANKstatus,ANKnaiyou,ANKdate";
            $form_num = explode(',', $form_num);
        }
        else
        {
            $form_num = explode(',', $form_ini_array[$pagename][$form_type]);
        }
        for($i = 0; $i < count($form_num); $i++)
        {
            if($form_num[$i] == "SCHcustomername")
            {
                $form_data[$counter]['form_num'] = $form_num[$i].'_text';
            }
            else
            {
                $form_data[$counter]['form_num'] = $form_num[$i];
            }
            $form_data[$counter]['max_length'] = $form_ini_array[$form_num[$i]]['max_length'];
            $form_data[$counter]['form_format'] = $form_ini_array[$form_num[$i]]['form_format'];
            $form_data[$counter]['isnotnull'] = $form_ini_array[$form_num[$i]]['isnotnull'];
            $form_data[$counter]['field_type'] = $form_ini_array[$form_num[$i]]['field_type'];
            $counter++;
        }
    }
    return $form_data;
}

/************************************************************************************************************
function limit_date()


引数          なし					

戻り値	$result		有効期限結果
************************************************************************************************************/
	
function limit_date(){
    
    //初期設定
    require_once("f_DB.php");
    
    //定数
    $date = date_format(date_create("NOW"), "Y-m-d");
    $sql = "SELECT *FROM systeminfo;";
    
    //変数
    $limit_result = 0;																								// 有効期限判断
    $rownums = 0;																									// 検索結果件数
    $startdate = "";
    $enddate = "";
    $befor_month = "";
    $message = "";
    $result_limit = array();
    
    //ログイン検索処理
    $con = dbconect();																								// db接続関数実行
    $result = $con->query($sql) or die($con-> error);														// クエリ発行
    $rownums = $result->num_rows;																					// 検索結果件数取得
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $startdate = $result_row['STARTDATE'];
    }
    
    //ログイン判断処理
    $enddate = date_add(date_create($startdate), date_interval_create_from_date_string('1 year'));
    $enddate = date_sub($enddate, date_interval_create_from_date_string('1 days'));
    $enddate = date_format($enddate, 'Y-m-d');
    $befor_month = date_format(date_create($enddate), 'Y-m-01');
    $befor_month = date_sub(date_create($befor_month), date_interval_create_from_date_string('1 month'));
    $befor_month = date_format($befor_month, 'Y-m-d');
    if($enddate >= $date)
    {
        $limit_result = 1;
        if($befor_month <= $date)
        {
            $enddate2 = date_create($enddate);
            $date2 = date_create($date);
            $limit_result = 2;
            $interval = date_diff($date2, $enddate2);
            $message = $interval->format('%a');
        }
    }
    else
    {
        $limit_result = 0;
    }
    $result_limit[0] = $limit_result;
    $result_limit[1] = $message;
    return ($result_limit);
}
?>