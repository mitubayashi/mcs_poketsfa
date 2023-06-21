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
    $pagename = $_SESSION["pagename"];   
    
    //画面上部作成処理
    $header_html = makeBoxHeader();
    
    //月別スケジュール作成処理
    $month_schedule_html = makeSchedule_month();
    
    //スケジュール編集モーダル中身作成
    $scheduleedit_modal_html = makeModalHtml('schedule_edit_form_num');
    
    //入力チェック情報取得      
    $schedule_edit_form_num = get_input_data("schedule_edit_form_num",$pagename);
    
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
<head>
    <title>月別スケジュール</title>
    <link rel="icon" type="image/png" href="./img/favicon.ico">
    <link rel="stylesheet" href="./css/list_css.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script src='./js/open_content.js'></script>
    <script src='./js/inputset.js'></script>
    <script src='./js/schedule.js'></script>
    <script src='./js/inputcheck.js'></script>
    <script src='./jquery/jquery.min.js'></script>
    <script>
        var pagename = '<?php echo $pagename; ?>';
        var scheduleedit_modal_html = '<?php echo $scheduleedit_modal_html; ?>';
        var schedule_edit_form_num = JSON.parse('<?php echo json_encode($schedule_edit_form_num); ?>');    
        window.onload = function() {
            //すべてのチェックボックスにチェックを入れる
            let checkbox = document.querySelectorAll(`input[type='checkbox']`);
            for(const i of checkbox)
            {
                i.checked = true;
            }
            //カレンダーサイズ調整
            resize_schedule(event);
            
            //スケジュール右クリックイベント
            document.oncontextmenu = function(event){            
                var obj = event.target.className.split(' ');
                if(obj[0] == "schedule_content" || obj[0] == "all_schedule")
                {
                    //alert(event.target.value);
                    //複数選択可能プルダウンをすべて閉じる
                    var element = document.getElementsByClassName("schedule_menu_box");    
                    for(var i = 0; i < element.length; i++)
                    {
                        element[i].style.display = "none";
                    }
                    
                    if(event.target.dataset.flag == "0")
                    {
                        var box = event.target;
                        var id = document.getElementById("schedule_menu_box");
                        var bounds = box.getBoundingClientRect(); 
                        var style = id.currentStyle || document.defaultView.getComputedStyle(id, '');
                        if (style.display == 'none')
                        { //消えてれば表示させる
                            id.style.display = 'block';
                            id.style.top = bounds.top + event.target.offsetHeight +'px';
                            id.style.left = bounds.left + 'px';

                            //ボタンにIDをセットする
                            document.getElementById("schedule_delete_button").value = event.target.value;
                            document.getElementById("schedule_edit_button").value = event.target.value;
                        }
                    }
                }
                else
                {
                    id = document.getElementById("schedule_menu_box");
                    //複数選択可能プルダウンの選択肢を非表示にする
                    if(event.target == null)
                    {
                        id.style.display = "none";
                    }
                    else
                    {
                        if((id.id + '_nav') != event.target.id)
                        {
                            id.style.display = "none";
                        }
                    }
                }
                return false;
            }            
        }

        //ウィンドウサイズが変わった時、カレンダーサイズを調整
        window.addEventListener('resize', resize_schedule);
        
        //ブラウザバック対策
        window.onpageshow = function(event) {
            if (event.persisted || window.performance && window.performance.navigation.type == 2) 
            {
                window.location.href = 'retry.php';
            }
        }; 
    </script>
</head>
<body>
    <?php echo $header_html; ?>
    <?php echo $month_schedule_html; ?>
    <!--モーダル画面外枠作成-->
    <dialog id="dialog" class="modal_body">        
    </dialog>
    <!--スケジュール編集削除メニューボタン作成-->
    <form action="./pageJump.php" method="post">
        <div id="schedule_menu_box" class="schedule_menu_box">        
            <button type="button" value="" class="schedule_menu_button" id="schedule_edit_button" onclick="open_scheduleedit_modal(this.value);">編集</button>
            <button type="submit" value="" class="schedule_menu_button" id="schedule_delete_button" name="MONTHSCHEDULE_scheduledelete">削除</button>        
        </div>
    </form>
</body>
</html>

