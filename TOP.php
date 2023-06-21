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
    
    //スケジュール表示
    $week_schedule_html = makeSchedule_week();
    
    //案件管理表示
    $matter_list = makeList_matter();
    
    //アポイント予定一覧作成
    $appointment_list = makeList_appointment();
    
    //スケジュール登録モーダル
    $schedule_modal_html = makeModalHtml('schedule_insert_form_num');
    
    //スケジュール編集モーダル中身作成
    $scheduleedit_modal_html = makeModalHtml('schedule_edit_form_num');
    
    //受注額予実管理データ取得
    $top_yozitu_data = top_yozitu_data();
    
    //お知らせデータ取得
    $notice_data_html = makeNotice_html();
    
    //入力チェック情報取得      
    $schedule_insert_form_num = get_input_data("schedule_insert_form_num", $pagename);
    $schedule_edit_form_num = get_input_data("schedule_edit_form_num",$pagename);
    
    //データ削除
    unset($_SESSION['pre_post']);
?>
<html>
<head>
    <title>TOP</title>
    <link rel="icon" type="image/png" href="./img/favicon.ico">
    <link rel="stylesheet" href="./css/list_css.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script>
        var pagename = '<?php echo $pagename; ?>';
        var schedule_modal_html = '<?php echo $schedule_modal_html; ?>';
        var scheduleedit_modal_html = '<?php echo $scheduleedit_modal_html; ?>';
        var schedule_insert_form_num = JSON.parse('<?php echo json_encode($schedule_insert_form_num); ?>');
        var schedule_edit_form_num = JSON.parse('<?php echo json_encode($schedule_edit_form_num); ?>');       
        window.onload = function() {            
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
                    //スケジュール編集削除ボタンを非表示にする
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
        //ブラウザバック対策
        window.onpageshow = function(event) {
            if (event.persisted || window.performance && window.performance.navigation.type == 2) 
            {
                window.location.href = 'retry.php';
            }
        };  
    </script>
<style type="text/css">
    .container {
      width: 70%;   
      margin: auto;
    }
  </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <script src='./js/graph.js'></script>
    <script src='./js/open_content.js'></script>
    <script src='./js/inputset.js'></script>
    <script src='./js/inputcheck.js'></script>
    <script src='./jquery/jquery.min.js'></script>
</head>
<body>
    <?php echo $header_html; ?>
    <form action="./pageJump.php" method="post">
    <div style="width:100%; max-width: 100%;height: calc(100% - 43px);max-height:calc(100% - 43px); display: flex;">
        <div style="width: 55%;">
            <div style="height: 50%; background-color: white; margin: 2px;">
                <div class="top_content_title">スケジュール</div>
                <?php echo $week_schedule_html; ?>
            </div>
            <div style="height: 50%; background-color: white; margin: 2px;">
                <div class="top_content_title">案件管理</div>
                <div class='top_content_subtitle'>案件一覧<input type = "submit" name="MATTER_button" value="案件一覧" class="table_button"></div>
                <?php echo $matter_list; ?>
            </div>
        </div>
        <div style="width: 25%;">
            <div style="height: 100%; background-color: white; margin: 2px;">
                <div class="top_content_title">アポイント促進</div>
                <div class='top_content_subtitle'>アポイント予定<input type = "button" value="スケジュール登録" onclick="open_schedule_modal();" class="table_button"></div>                    
                <?php echo $appointment_list; ?>
            </div>
        </div>
        <div style="width: 20%;">
            <div style="height: 70%; background-color: white; margin: 2px;">
                <div class="top_content_title">受注額予実管理<input type="submit" value="詳細" name="YOZITU_button" class="table_button"></div>                       
                    <div class="container">
                        <canvas id="doughnut_chart1"></canvas>
                        <script>
                            var yotei = '<?php echo $top_yozitu_data['target_now']; ?>';
                            var ziseki = '<?php echo $top_yozitu_data['amount_now']; ?>';
                            var name = '<?php echo $top_yozitu_data['user_name']; ?>';
                            var id = "doughnut_chart1";
                            var title = name + "(現時点)";
                            top_graph(yotei,ziseki,id,title);    //円グラフ表示            
                        </script>
                    </div>                  
                    <div class="container">
                        <canvas id="doughnut_chart2"></canvas>
                        <script>
                            var yotei = '<?php echo $top_yozitu_data['target_year']; ?>';
                            var ziseki = '<?php echo $top_yozitu_data['amount_year']; ?>';
                            var name = '<?php echo $top_yozitu_data['user_name']; ?>';
                            var id = "doughnut_chart2";
                            var title = name + "(期全体)";
                            top_graph(yotei,ziseki,id,title);    //円グラフ表示            
                        </script>
                    </div>
            </div>
            <div style="height: 30%; background-color: white; margin: 2px;">
                <div class="top_content_title">お知らせ</div>
                <?php echo $notice_data_html; ?>
            </div>
        </div>
    </div>
    </form>  
    <dialog id="dialog" class="modal_body">
    </dialog>
    <!--スケジュール編集削除メニューボタン作成-->
    <form action="./pageJump.php" method="post">
        <div id="schedule_menu_box" class="schedule_menu_box">        
            <button type="button" value="" class="schedule_menu_button" id="schedule_edit_button" onclick="open_scheduleedit_modal(this.value);">編集</button>
            <button type="submit" value="" class="schedule_menu_button" id="schedule_delete_button" name="TOP_scheduledelete">削除</button>        
        </div>
    </form>
</body>
</html>

