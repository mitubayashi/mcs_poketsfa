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
    $schedule_data = array();
    $schedule_html = "";
    $setting_array = setting_array_get();
    $hyozi_flag = $setting_array['monthschedule'];
    $create_flag = $setting_array['REPORTCREATE'];
    
    //処理
    $con = dbconect();
    $sql = "SELECT t1.schedule_id,t1.authoer,t1.schedule_date,DATE_FORMAT(t1.start_time,'%H:%i') AS start_time,DATE_FORMAT(t1.end_time,'%H:%i') AS end_time,t2.customer_name,t3.user_name,t3.user_id,t4.interview_report_id,t1.create_userid,t2.user_id FROM `schedule_table` AS t1 LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id ";                
    $sql .= "LEFT JOIN `interview_report_table` AS t4 ON t1.schedule_id = t4.schedule_id ";
    $sql .= "WHERE t1.schedule_date = '".$_POST['open_date']."' ";
    $sql .= "AND t1.create_userid IN (".implode(",",$_SESSION["hyozi_user_list"][$hyozi_flag]).") ";    
    $sql .= "ORDER BY start_time ASC;";
    $result = $con->query($sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if($_POST['open_date'] <= date('Ymd'))
        {
            if($result_row['interview_report_id'] == "")
            {
                if(in_array($result_row['user_id'], $_SESSION['hyozi_user_list'][$create_flag]))
                {
                    $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];
                    $schedule_html .= '<button class="all_schedule user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: red;" value="'.$result_row['schedule_id'].'" data-flag="0">';
                    $schedule_html .= '時間：'.$result_row['start_time'].'～'.$result_row['end_time'].'<br>';
                    $schedule_html .= '顧客名：'.$result_row['customer_name'].'<br>';
                    $schedule_html .= '担当者：'.$result_row['user_name'].'<br>';
                    $schedule_html .= '</button>';                   
                }
                else
                {
                    $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];
                    $schedule_html .= '<button class="all_schedule user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: red;" value="'.$result_row['schedule_id'].'" data-flag="0" disabled>';
                    $schedule_html .= '時間：'.$result_row['start_time'].'～'.$result_row['end_time'].'<br>';
                    $schedule_html .= '顧客名：'.$result_row['customer_name'].'<br>';
                    $schedule_html .= '担当者：'.$result_row['user_name'].'<br>';
                    $schedule_html .= '</button>';                      
                }
            }
            elseif($result_row['interview_report_id'] != "")
            {
                $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];
                $schedule_html .= '<button class="all_schedule user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: #FA9996;" value="'.$result_row['schedule_id'].'" data-flag="1">';
                $schedule_html .= '時間：'.$result_row['start_time'].'～'.$result_row['end_time'].'<br>';
                $schedule_html .= '顧客名：'.$result_row['customer_name'].'<br>';
                $schedule_html .= '担当者：'.$result_row['user_name'].'<br>';
                $schedule_html .= '</button>';                   
            }
        }
        else 
        {            
            $comment = '時間：'.$result_row['start_time'].' から '.$result_row['end_time'].'&#13;&#10;顧客名：'.$result_row['customer_name'].'&#13;&#10;担当者：'.$result_row['user_name'];
            $schedule_html .= '<button class="all_schedule user_'.$result_row['create_userid'].'" name="REPORTCREATE_button" title="'.$comment.'" style="color: blue;"  value="'.$result_row['schedule_id'].'" data-flag="0" disabled>';
            $schedule_html .= '時間：'.$result_row['start_time'].'～'.$result_row['end_time'].'<br>';
            $schedule_html .= '顧客名：'.$result_row['customer_name'].'<br>';
            $schedule_html .= '担当者：'.$result_row['user_name'].'<br>';
            $schedule_html .= '</button>';            
        }
    }    
    
    //値をセットする
    $week_array = array('日', '月', '火', '水', '木', '金', '土');
    $datetime = new DateTime($_POST['open_date']);
    $week = $datetime->format('w');
    $schedule_data[0] = date('n月j日', strtotime($_POST['open_date']))."(".$week_array[$week].")";    
    $schedule_data[1] = $schedule_html;
    
    echo json_encode($schedule_data);
?>

