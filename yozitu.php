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

//表示対象社員更新
$_SESSION['hyozi_user_list'] = hyozi_user_list($_SESSION["loginuser"]["user_id"]);
update_loginuser($_SESSION["loginuser"]["user_id"]);

//定数
$post = $_SESSION['list'];
$pagename = $_SESSION['pagename'];

//画面上部作成処理
$header_html = makeBoxHeader();

//受注額予実管理データ取得
$yozitu_data = yozitu_data();

//円グラフ表示エリア作成
$now_graph_html = "";
$year_graph_html = "";
$counter1 = 0;
$counter2 = 0;
$now_yozitu_data = array();
$year_yozitu_data = array();
for($i = 0; $i < count($yozitu_data); $i++)
{
    if($yozitu_data[$i]['target_now'] != 0)
    {//目標金額が0円以外の円グラフを表示する
        $now_graph_html .= '<div  style="display: flex;">';
        $now_graph_html .= '<div class="container">';
        $now_graph_html .= '<canvas id="douhnut_chart_now'.$counter1.'"></canvas>';           
        $now_graph_html .= '</div>';
        $now_graph_html .= '<div style="padding-top:40px; padding-bottom:30px; line-height:40px; font-size: 18px;" id="now_syosai'.$counter1.'"></div>';
        $now_graph_html .= '</div>';
        $now_yozitu_data[$counter1] = $yozitu_data[$i];
        $counter1++;
    }
    if($yozitu_data[$i]['target_year'] != 0)
    {//目標金額が0円以外の円グラフを表示する
        $year_graph_html .= '<div  style="display: flex;">';
        $year_graph_html .= '<div class="container">';
        $year_graph_html .= '<canvas id="douhnut_chart_year'.$counter2.'"></canvas>';       
        $year_graph_html .= '</div>';
        $year_graph_html .= '<div style="padding-top:40px; padding-bottom:30px; line-height:40px; font-size: 18px;" id="year_syosai'.$counter2.'"></div>';
        $year_graph_html .= '</div>';
        $year_yozitu_data[$counter2] = $yozitu_data[$i];
        $counter2++;
    }
    //データ削除
    unset($_SESSION['pre_post']);
}
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>受注額予実管理</title>
        <link rel="stylesheet" href="./css/list_css.css" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script>
            var now_yozitu_data = JSON.parse('<?php echo json_encode($now_yozitu_data); ?>');
            var year_yozitu_data = JSON.parse('<?php echo json_encode($year_yozitu_data); ?>');
            var pagename = '<?php echo $pagename; ?>';
            //ブラウザバック対策
            window.onpageshow = function(event) {
                if (event.persisted || window.performance && window.performance.navigation.type == 2) 
                {
                    window.location.href = 'retry.php';
                }
            }; 
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
        <script src='./js/graph.js'></script>
        <script src='./js/open_content.js'></script>
        <script src='./js/inputset.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script src='./jquery/jquery.min.js'></script>
        <style type="text/css">
        .container {       
          width: 250px;
        }
        </style>
    </head>
    <body>
        <?php echo $header_html; ?>
        <form action="./pageJump.php" method="post">
            <table style="width: 100%; min-height: calc(100% - 87px); table-layout: fixed;">
                <tr>
                    <td style="background: white; padding-top: 0;" valign="top">
                        <div class='top_content_subtitle'>現時点</div>
                        <?php echo $now_graph_html;  ?>
                        <script>
                            for(var i = 0; i < now_yozitu_data.length; i++)
                            {
                                var yotei = now_yozitu_data[i]['target_now'];
                                var ziseki = now_yozitu_data[i]['amount_now'];
                                var id = "douhnut_chart_now" + i;
                                
                                var syosai_id = "now_syosai" + i;
                                var title = now_yozitu_data[i]['user_name'];
                                yozitu_graph(yotei,ziseki,id,title,syosai_id);        
                            }
                        </script>
                    </td>
                    <td style="background: white; padding-top: 0;" valign="top">
                        <div class='top_content_subtitle'>期全体</div>  
                        <?php echo $year_graph_html;  ?>
                        <script>
                            for(var i = 0; i < year_yozitu_data.length; i++)
                            {
                                var yotei = year_yozitu_data[i]['target_year'];
                                var ziseki = year_yozitu_data[i]['amount_year'];
                                var id = "douhnut_chart_year" + i;
                                var title = year_yozitu_data[i]['user_name'];
                                var syosai_id = "year_syosai" + i;
                                yozitu_graph(yotei,ziseki,id,title,syosai_id);                        
                            }
                        </script>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>

