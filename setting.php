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
    $post = $_SESSION['list'];
    $pagename = $_SESSION['pagename'];
    
    //画面上部作成処理
    $header_html = makeBoxHeader();
    
    //設定情報取得
    $setting_data = get_setting_data();
    
    //表示設定入力欄作成
    $setting_form_html = make_setting_Form();
    
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>表示設定</title>
        <link rel="stylesheet" href="./css/list_css.css" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script>
            var pagename = '<?php echo $pagename; ?>';
            //値セット
            window.onload = function() {
                var setting_data = JSON.parse('<?php echo json_encode($setting_data); ?>');
                for(var i = 0; i < setting_data.length; i++)
                {
                    document.getElementById(setting_data[i]["form_num"]).value = setting_data[i]["value"];
                }
            }
            
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
    </head>
    <body>
        <?php echo $header_html; ?>
        <div class="list_content_area" style="min-height: calc(100% - 87px);">
            <form action="./pageJump.php" method="post">
                <?php echo $setting_form_html; ?>
            </form>
        </div>
    </body>
</html>