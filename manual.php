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
    require_once("f_Form.php");
    require_once("f_Header.php");
       
    //表示対象社員更新
    $_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
    update_loginuser($_SESSION["loginuser"]["user_id"]);
    
    //定数
    $pagename = $_SESSION["pagename"];    
    $post = $_SESSION['list'];
    
    //画面上部作成処理
    $header_html = makeBoxHeader();
?>
<html>
<head>
    <title>取扱説明書</title>
    <link rel="icon" type="image/png" href="./img/favicon.ico">
    <link rel="stylesheet" href="./css/list_css.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script>
        //javascriptとCSSを更新する
//        window.onload = function() {
//            if (window.name != "test")
//            {
//                location.reload();
//                window.name = "test";
//            }             
//        }
        //ブラウザバック対策
        window.onpageshow = function(event) {
            if (event.persisted || window.performance && window.performance.navigation.type == 2) 
            {
                window.location.href = 'retry.php';
            }
        };  
    </script>
    <script src='./js/graph.js'></script>
    <script src='./js/open_content.js'></script>
    <script src='./js/inputset.js'></script>
    <script src='./js/inputcheck.js'></script>
    <script src='./jquery/jquery.min.js'></script>
</head>
<body>
<?php echo $header_html; ?>
<iframe src="./pdf/manual.pdf" width="100%" height="100%"></iframe>
</body>
</html>




