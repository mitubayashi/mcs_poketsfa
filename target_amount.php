<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:'); 
    header('Content-type: text/html; charset=utf-8'); 
?>
<?php
    //リロード対策
    require_once('f_Construct.php');
    start();
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Header.php");
    require_once("f_Form.php");
    $system_ini_array = parse_ini_file("./ini/system.ini",true); 
    
    //表示対象社員更新
    $_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
    update_loginuser($_SESSION["loginuser"]["user_id"]);

    //変数
    $post = $_SESSION['list'];
    $pagename = $_SESSION['pagename'];
    
    //期を求める
    $start_year = $system_ini_array['SYSTEM_SETTING']['start_year'];
    $start_month = $system_ini_array['SYSTEM_SETTING']['start_month'];    
    $period = date_format(date_create('NOW'), "Y") - $start_year;
    if($start_month <=  date_format(date_create('NOW'), "n"))
    {
        $period = $period + 1;
    }       
    
    $start_date = date_format(date_create('NOW'), "Y").$start_month.'01';
    
    if(date("Ymd",strtotime($start_date . "+6 month")) <= date_format(date_create('NOW'), "Ymd"))
    {
        if(isset($post['next_period']))
        {
            if($period == $post['next_period'])
            {
                $next_period = $period + 1;
            }
            else
            {
                $next_period = $period;
                $period = $post['next_period'];
            }
        }
        else
        {
            $next_period = $period + 1;
        }
    }
    else
    {
        $next_period = "";
    }
    //画面上部作成処理
    $header_html = makeBoxHeader();
    $header_html .= "<div class='main_title'>";
    $header_html .= '<form action="./pageJump.php" method="post">';  
    $header_html .= '目標金額設定（'.$period.'期）';
    if($next_period !="")
    {
        $header_html .= '<input type="submit" value="'.$next_period.'期の入力に切り替える" name="TARGETAMOUNT_button" class="table_button">';
        $header_html .= '<input type="hidden" value="'.$next_period.'" name="next_period">';
    }    
    $header_html .= '</form>';
    $header_html .= "</div>";
    
    //目標金額入力欄作成
    $amount_form_html = make_amount_Form($period,$start_month);
    
     //データ削除
    unset($_SESSION['pre_post']);   
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>目標金額設定</title>
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
                <?php echo $amount_form_html; ?>
            </form>
        </div>
    </body>
</html>