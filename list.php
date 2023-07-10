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
    $system_ini_array = parse_ini_file("./ini/system.ini",true); 
    
    //表示対象社員更新
    $_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
    update_loginuser($_SESSION["loginuser"]["user_id"]);
    
    //定数
    $post = $_SESSION['list'];
    $pagename = $_SESSION["pagename"];   
    
    //画面上部作成処理
    $header_html = makeBoxHeader();

    //SQL作成
    $sql = List_itemSQL($post,$pagename);
    $sql = setOrderbySQL($sql,$pagename);
    
    //一覧表作成処理
    if($pagename == 'ORDERMGR')
    {
        $list_html = order_management($sql,$post);
    }
    else
    {
        $list_html = makeList_item($sql,$post);       
    }
    //検索条件モーダル中身作成
    $sech_modal_html = makeModalHtml('sech_form_num');
    
    //新規登録モーダル中身作成
    $insert_modal_html = makeModalHtml('insert_form_num');
    
    //編集モーダル中身作成
    $edit_modal_html = makeModalHtml('edit_form_num');
    
    //入力チェック情報取得      
    $insert_form_num = get_input_data("insert_form_num", $pagename);
    $edit_form_num = get_input_data("edit_form_num",$pagename);
    
    //検索条件保持
    $sech_value = array();    
    $sech_form_num = explode(',', $form_ini_array[$pagename]['sech_form_num']);
    $sech_form_type = explode(',', $form_ini_array[$pagename]["sech_form_type"]);
    $count = 0;
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        if($sech_form_type[$i] == '2')
        {
            $sech_value[$count]["form_num"] = $sech_form_num[$i].'_0';
            $sech_value[$count]["field_type"] = $form_ini_array[$sech_form_num[$i]]["field_type"];
            if(isset($post[$sech_form_num[$i].'_0']))
            {
                $sech_value[$count]["value"] = $post[$sech_form_num[$i].'_0'];
            }
            else
            {
                $sech_value[$count]["value"] = "";           
            }
            $count++;
            $sech_value[$count]["form_num"] = $sech_form_num[$i].'_1';
            $sech_value[$count]["field_type"] = $form_ini_array[$sech_form_num[$i]]["field_type"];
            if(isset($post[$sech_form_num[$i].'_1']))
            {
                $sech_value[$count]["value"] = $post[$sech_form_num[$i].'_1'];
            }
            else
            {
                $sech_value[$count]["value"] = "";           
            }
            $count++;
        }
        elseif($sech_form_type[$i] == '3')
        {
            $sech_value[$count]["form_num"] = $sech_form_num[$i];
            $sech_value[$count]["field_type"] = $form_ini_array[$sech_form_num[$i]]["field_type"];
            $sech_value[$count+1]["form_num"] = $sech_form_num[$i]."_radio";
            $sech_value[$count+1]["field_type"] = "8";

            if(isset($post[$sech_form_num[$i]]))
            {
                $sech_value[$count]["value"] = $post[$sech_form_num[$i]];
                $sech_value[$count+1]["value"] = $post[$sech_form_num[$i]."_radio"];
            }
            else
            {
                $sech_value[$count]["value"] = "";         
                $sech_value[$count+1]["value"] = "OR";
            }    
            $count = $count + 2;
        }
        else
        {
            $sech_value[$count]["form_num"] = $sech_form_num[$i];
            $sech_value[$count]["field_type"] = $form_ini_array[$sech_form_num[$i]]["field_type"];

            if(isset($post[$sech_form_num[$i]]))
            {
                $sech_value[$count]["value"] = $post[$sech_form_num[$i]];
            }
            else
            {
                $sech_value[$count]["value"] = "";           
            }    
            $count++;
        }        
    }
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
<head>
    <title>管理画面</title>
    <link rel="icon" type="image/png" href="./img/favicon.ico">
    <link rel="stylesheet" href="./css/list_css.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script>
        var pagename = '<?php echo $pagename; ?>';
        var sech_modal_html = '<?php echo $sech_modal_html; ?>';
        var insert_modal_html = '<?php echo $insert_modal_html; ?>';
        var edit_modal_html = '<?php echo $edit_modal_html; ?>';
        var sech_form_num = JSON.parse('<?php echo json_encode($sech_form_num); ?>');
        var insert_form_num = JSON.parse('<?php echo json_encode($insert_form_num); ?>');
        var edit_form_num = JSON.parse('<?php echo json_encode($edit_form_num); ?>');
        var sech_value = JSON.parse('<?php echo json_encode($sech_value); ?>');
        var mitsumori_flag = '<?php echo $system_ini_array["SYSTEM_SETTING"]["mitsumori_flag"]; ?>';

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
    <script src='./js/inputcheck.js'></script>
    <script src='./jquery/jquery.min.js'></script>
</head>
<body>
    <?php echo $header_html; ?>
    <div class="list_content_area" style="min-height: calc(100% - 123px);">
    <form action="./pageJump.php" method="post">
        <?php echo $list_html; ?>
    </form>
    </div>   
    <dialog id="dialog" class="modal_body">        
    </dialog>
</body>
</html>
