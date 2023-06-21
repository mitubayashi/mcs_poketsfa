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
    require_once('f_DB.php');
    require_once("f_Form.php");
    require_once("f_Header.php");
    require_once ("f_SQL.php");
    
    //表示対象社員更新
    $_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
    update_loginuser($_SESSION["loginuser"]["user_id"]);
    
    //定数
    $pagename = $_SESSION["pagename"];
    $insert = $_SESSION["insert"];
    
    //画面上部作成
    $header_html = makeBoxHeader();
    
    //見積・請求システムDB接続
    $con = mitsumori_dbconect();
    
    //入力欄作成
    $mitsumori_input_form = make_mitsumori_Form($insert,$pagename);
    
    //入力チェックデータ格納
    $insert_form_num = get_input_data("insert_form_num", $pagename);
    
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>見積・請求システム案件登録</title>
        <title>面談報告書作成</title>
        <link rel="stylesheet" href="./css/list_css.css" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script>
            var pagename = '<?php echo $pagename; ?>';
            var insert_form_num = JSON.parse('<?php echo json_encode($insert_form_num); ?>');
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
        <!-- 画面上部作成 -->
        <?php echo $header_html; ?>
        <!-- 入力欄作成 -->
        <div class="list_content_area" style="min-height: calc(100% - 87px);">
            <form action="./pageJump.php" method="post">
                <?php echo $mitsumori_input_form; ?>       
                <input type="hidden" value="" id="kyaku_flag">
                <input type="submit" value="登録" class="modal_button" name="MITSUMORIINSERT" onclick="mitsumori_kyaku_check(); return check(insert_form_num,0);">
            </form>
        </div>
    </body> 
</html>
