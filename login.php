<?php
	session_start();
	header('Expires:-1'); 
	header('Cache-Control:'); 
	header('Pragma:'); 
	header('Content-type: text/html; charset=utf-8'); 
?>
<?php
    //セッション変数初期化
    $_SESSION = array();
    
    //初期設定
    require_once('f_DB.php');
    $system_ini_array = parse_ini_file("./ini/system.ini",true);
    
    //変数
    $login_id = '';
    $login_password = '';
    $error_msg = '';
    $input_text_style = '';
    $massage = "";
    
    //使用期限判定
    $result = limit_date();
    if($result[0] != 0)
    {
        if($result[0] == 2)
        {
            $massage = "<a class = 'error'>あと、".$result[1]."日で有効期限が切れます。</a>";
        }
        if(isset($_POST['login_id']))
        {
            $login_id = $_POST['login_id'];
            $login_password = $_POST['login_password'];

            //ログイン判定
            $judge = login($login_id, $login_password);
            if($judge)
            {
                $_SESSION['pagename'] = 'TOP';
                $_SESSION['pre_post']['TOP_button'] = 'TOP';
                $_SESSION['list']['TOP_button'] = 'TOP';
                
                //TOP画面へ遷移
                echo '<script type="text/javascript">';
                echo "<!--\n";
                echo 'location.href = "./TOP.php";';
                echo '// -->';
                echo '</script>';            
            }
            else
            {
                $error_msg = '<div class="error_msg">ログインIDまたはパスワードが違います</div>';
                $input_text_style = ' style="background-color:#ffd6d6; border:#ff0000 1px solid;" ';
            }        
        }
    }
    else
    {
        $massage = "<a class = 'error'>有効期限が切れてます。</a>";
    }
?>
<html>
<head>
    <title>ログイン</title>
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
    <center>
        <div class="campany_logo">
            <img src="<?php echo $system_ini_array['SYSTEM_SETTING']['company_logo']; ?>">
        </div>
        <div style="font-size: 20px;">
            ポケットSFA
        </div>        
        <?php echo $massage;  ?>
        <form action="login.php" method="post">
            <div class="login_form">
                <div class="login_title">Login</div>
                <?php echo $error_msg; ?>
                <table style="width:100%;">
                    <tr><td><a class="login_form_itemname">ログインID</a></td></tr>
                    <tr><td><input type="text" value="<?php echo $login_id;?>" class="form-text" name="login_id" <?php echo $input_text_style; ?> size="50"></td></tr>
                    <tr><td><a class="login_form_itemname">パスワード</a></td></tr>
                    <tr><td><input type="password" value="" class="form-text" name="login_password" <?php echo $input_text_style; ?> size="50"></td></tr>
                </table>
                <input type="submit" value="ログイン" name="login_button" class="login_button">
            </div>
        </form>
    </center>
</body>
</html>

