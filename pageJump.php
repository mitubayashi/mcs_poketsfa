<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:');
    header('Pragma:');
    header('Content-type: text/html; charset=utf8'); 
?>
<?php
    //初期設定
    $form_ini_array = parse_ini_file('./ini/form.ini',true);
    require_once('f_DB.php');
    require_once('f_Construct.php');
    
    //ブラウザバック対策
    startJump($_POST);
    
    //変数
    $url = 'retry';
    $page_type = '';
    
    //ページ遷移処理
    $_SESSION['pre_post'] = $_POST;
    $keyarray = array_keys($_POST);
    foreach ($keyarray as $key)
    {
        //画面遷移、検索処理
        if (strstr($key, '_button') != false || strstr($key, '_list') != false || strstr($key, '_read') != false)
	{
            if($key == 'REPORTCREATE_read')
            {
                $_SESSION['list']['REPORTCREATE_button'] = null;
                $_SESSION['pre_create'] = $_POST;
            }
            elseif($key == 'REPORTCREATE_button')
            {
                $_SESSION['list']['REPORTCREATE_button'] = null;
                $_SESSION['list']['REPORTLIST_list'] = null;
                $_SESSION['pre_create'] = $_POST;                
            }
            else
            {
                if(isset($_POST['REPORTLIST_list']))
                {
                    $post = $_POST['REPORTLIST_list'];
                    $_SESSION['list'] = $_POST;
                    $_SESSION['list']['REPORTLIST_list'] = $post;
                    
                    //デフォルト検索条件
                    $con = dbconect();
                    $sql = "SELECT customer_id FROM schedule_table WHERE schedule_id = ".$post.";";
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $_SESSION['list']['REPORTLISTcustomerid'] = $result_row['customer_id'];
                    }
                }
                elseif($key == "TOP_button")
                {
                    $_SESSION['list'] = $_POST;
                    $_SESSION['pre_create']['REPORTCREATE_read'] = null;
                }
                elseif(isset($_SESSION['list']['REPORTLIST_list']))
                {
                    $post = $_SESSION['list']['REPORTLIST_list'];
                    $_SESSION['list'] = $_POST;
                    $_SESSION['list']['REPORTLIST_list'] = $post;
                }
                elseif($key == "REPORTLIST_button")
                {
                    $_SESSION['list'] = $_POST;
                }
                else
                {
                    $_SESSION['list'] = $_POST;
                }
            }
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];                
            $_SESSION['pagename'] = $pre_url[0];

            //情報初期化
            $_SESSION['paging'] = null;
            $_SESSION['topschedule'] = null;
            $_SESSION['monthschedule'] = null;
        }
        if(strstr($key, '_back') != false)
        {
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];

            //情報初期化
            $_SESSION['pre_create'] = null;
            $_SESSION['paging'] = null;
            $_SESSION['topschedule'] = null;
            $_SESSION['monthschedule'] = null;            
        }
        //一覧表ページ移動
        if(strstr($key, '_paging') != false)
        {
            $_SESSION['paging'] = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];            
        }
        
        //TOPスケジュール切り替え
        if(strstr($key, '_topschedule') != false)
        {
            $_SESSION['topschedule'] = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];                
        }        
        
        //月別スケジュール切り替え
        if(strstr($key, '_monthschedule') != false)
        {
            $_SESSION['monthschedule'] = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];              
        }
        
        //新規登録処理
        if(strstr($key, '_insert') != false)
        {
            $insert = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];            
            insert($insert,$_SESSION['pagename']);         
            if($_POST['mitsumori_insert'] == "0")
            {
                $page_type = '6';       //見積・請求システム案件登録画面
                $_SESSION['pagename'] = 'MITSUMORI';
                $_SESSION['insert'] = $insert;
            }
        }
        
        //編集処理
        if(strstr($key, '_edit') != false)
        {
            $update = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            update($update,$_SESSION['pagename']);
        }
        
        //削除処理
        if(strstr($key, '_delete') != false)
        {
            $delete = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            delete($delete,$_SESSION['pagename']);            
        }
        
        //見積システム案件登録
        if($key == "MITSUMORIINSERT")
        {
            $insert = $_POST;
            $page_type = "2";
            $_SESSION['pagename'] = "MATTER";
            mitsumori_insert($insert,"MITSUMORI");
        }
        
        //スケジュール登録処理
        if(strstr($key, '_scheduleinsert') != false)
        {
            $insert = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            schedule_insert($insert,$_SESSION['pagename']);             
        }
        
        //スケジュール編集
         if(strstr($key, '_scheduleedit') != false)
        {
            $update = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            schedule_edit($update,$_SESSION['pagename']);             
        }
        
        //スケジュール削除
        if(strstr($key, '_scheduledelete') != false)
        {
            $delete = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            schedule_delete($delete,$_SESSION['pagename']);   
        }
        
        //面談報告書作成処理
        if(strstr($key, '_reportinsert') != false)
        {
            $insert = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            report_insert($insert,$_SESSION['pagename']);            
        }
        
        //面談報告書編集処理
        if(strstr($key, '_reportedit') != false)
        {
            $update = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            report_update($update,$_SESSION['pagename']);            
        }
        
        //表示設定更新
        if($key == 'SETTINGupdate')
        {
            $update = $_POST;
            setting_update($update);
            $page_type = '1';
            $_SESSION['pagename'] = 'TOP';
        }
        //アポイント予定削除
        if($key == "TOP_appointmentdelete")
        {
            $delete = $_POST;
            $pre_url = explode('_', $key);
            $page_type = $form_ini_array[$pre_url[0]]['page_type'];
            $_SESSION['pagename'] = $pre_url[0];
            appointment_delete($delete,$_SESSION['pagename']);               
        }
        if($key == 'TARGETAMOUNTupdate')
        {
            $update = $_POST;
            amount_update($update);
            $page_type = '1';
            $_SESSION['pagename'] = 'TOP';
        }
    }
    
    //ページ判定
    switch ($page_type){
        case '1':
            $url = 'TOP';
            break;
        case '2':
            $url = 'list';
            break;
        case '3':
            $url = 'month_schedule';
            break;
        case '4':
            $url = 'yozitu';
            break;
        case '5':
            $url = 'report';
            break;
        case '6':
            $url = 'mitsumori_insert';
            break;
        case '7':
            $url = 'order_management';
            break;
        case '8':
            $url = 'setting';
            break;
        case '9':
            $url = 'target_amount';
            break;
        case '10':
            $url = 'manual';
            break;
    }
    
	header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
			.$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/".$url.".php");
?>
