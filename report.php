<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:');
    header('Pragma:');
    header('Content-type: text/html; charset=utf8'); 
?>
<?php
    //リロード対策
    require_once('f_Construct.php');
    start();
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    require_once("f_Header.php");
    require_once ("f_SQL.php");
    $form_ini_array = parse_ini_file("./ini/form.ini",true);  
    
    //表示対象社員更新
    $_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
    update_loginuser($_SESSION["loginuser"]["user_id"]);
    
    //定数
    $post = $_SESSION['pre_create'];
    $pagename = $_SESSION["pagename"];   
    $setting_array = setting_array_get();
    $hyozi_flag = $setting_array['REPORTCREATE'];
    
    //画面上部作成処理
    $con = dbconect();
    $header_html = makeBoxHeader();
    $header_html .= "<div class='main_title'>";
    $header_html .= '<form action="./pageJump.php" method="post">';  
    if(isset($post["REPORTCREATE_button"]))
    {
        $sql = "SELECT COUNT(*) FROM interview_report_table WHERE schedule_id = '".$post["REPORTCREATE_button"]."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            if($result_row['COUNT(*)'] == '0')
            {
                $header_html .= "面談報告書作成";    
            }
            else
            {
                $header_html .= '面談報告書編集';
            }
        }        
        $header_html .= '<button type="submit" name="REPORTLIST_list" value="'.$post["REPORTCREATE_button"].'"  class="table_button">';
        $header_html .= '過去の面談報告書を見る';
        $header_html .= '</button>';
    }
    elseif(isset($post["REPORTCREATE_read"]))
    {
        //前ページの情報と後ろのページの情報を取得する
        $next = "";
        $prev = "";
        $counter = 0;
        $sql = List_itemSQL($_SESSION['list'],"REPORTLIST");
        $sql = setOrderbySQL($sql,"REPORTLIST");
        $result = $con->query($sql[1]);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            if($result_row['schedule_id'] == $post['REPORTCREATE_read']) 
            {
                break;
            }
            $counter++;
        }
        if(($counter - 1) < 0)
        {
            $prev = "";
        }
        else
        {
            $prev_cnt = 0;
            $result = $con->query($sql[1]);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                if(($counter - 1) == $prev_cnt)
                {
                    $prev = $result_row['schedule_id'];
                    break;
                }
                $prev_cnt++;
            }
        }
         if(($counter + 1) >= $result->num_rows)
        {
            $next = "";
        }
        else
        {
            $next_cnt = 0;
            $result = $con->query($sql[1]);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                if(($counter + 1) == $next_cnt)
                {
                    $next = $result_row['schedule_id'];
                    break;
                }
                $next_cnt++;
            }
        }
        $header_html .= "面談報告書閲覧";
        if($prev == "")
        {
            $header_html .= '<button type="submit" value="'.$prev.'" name="REPORTCREATE_read" class="table_disabled_button" disabled>＜</button>';
        }
        else
        {
            $header_html .= '<button type="submit" value="'.$prev.'" name="REPORTCREATE_read" class="table_button">＜</button>';
        }
        if($next == "")
        {
            $header_html .= '<button type="submit" value="'.$next.'" name="REPORTCREATE_read" class="table_disabled_button" disabled>＞</button>';
        }
        else
        {
            $header_html .= '<button type="submit" value="'.$next.'" name="REPORTCREATE_read" class="table_button">＞</button>';
        }
        $sql = 'SELECT t1.schedule_id,t1.customer_id,t1.authoer,t1.interview_date,
        t1.interview_partner_name,t1.interview_content,t1.next_appointment_date,t1.check_status,
        t1.comment,t2.customer_name,t3.user_id,t3.checker
        FROM interview_report_table AS t1 
        LEFT JOIN `customer_table` AS t2 ON t1.customer_id = t2.customer_id
        LEFT JOIN `user_table` AS t3 ON t2.user_id = t3.user_id';
        $sql .= ' WHERE schedule_id = '.$post['REPORTCREATE_read'].';';   
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $user_id = $result_row['user_id'];
        }
        if(in_array($user_id, $_SESSION['hyozi_user_list'][$hyozi_flag]))
        {
            $header_html .= '<button type="submit" value="'.$post["REPORTCREATE_read"].'" name="REPORTCREATE_button" class="table_button">編集する</button>';   
        }
        else
        {
        $header_html .= '<button type="submit" value="'.$post["REPORTCREATE_read"].'" name="REPORTCREATE_button" class="table_disabled_button" disabled>編集する</button>';       
        }
    }
    $header_html .= '</form>';
    $header_html .= "</div>";
    
    //面談報告書入力欄作成処理
    if(isset($post['REPORTCREATE_button']))
    {
        $report_input_html = make_report_Form($post);
        $schedule_id = $post['REPORTCREATE_button'];
    }
    elseif(isset($post['REPORTCREATE_read']))
    {
        $report_input_html = report_read_Form($post);
        $schedule_id = $post['REPORTCREATE_read'];
    }
    
    //お知らせ削除処理
    delete_notice($_SESSION["loginuser"]["user_id"],$schedule_id);
    
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
    <head>
        <title>面談報告書作成</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" href="./css/list_css.css" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script>
            var form_data = JSON.parse('<?php echo json_encode($_SESSION['form_data']); ?>');
            var pagename = '<?php echo $pagename; ?>';
            
            //ブラウザバック対策
            window.onpageshow = function(event) {
                if (event.persisted || window.performance && window.performance.navigation.type == 2) 
                {
                    window.location.href = 'retry.php';
                }
            };   
        </script>
        <script src='./js/open_content.js'></script>
        <script src='./js/inputset.js'></script>
        <script src='./jquery/jquery.min.js'></script>
        <script src='./js/inputcheck.js'></script>
    </head>
    <body>
        <?php echo $header_html; ?>
        <div class="list_content_area" style="min-height: calc(100% - 87px);">
            <form action="./pageJump.php" method="post">
                <?php echo $report_input_html; ?>
            </form>
        </div>
    </body>
</html>

