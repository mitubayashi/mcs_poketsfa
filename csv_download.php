<?php
    session_start();
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);    
    require_once("f_DB.php");
    require_once("f_SQL.php");
    
    //定数
    $pagename = $_GET['pagename'];
    $post = $_SESSION['list'];
    
    //変数
    $csv = "";
    
    //項目名作成
    $csv_form_num = explode(',', $form_ini_array[$pagename]['csv_form_num']);
    for($i = 0; $i < count($csv_form_num); $i++)
    {
        $csv .= '"'.$form_ini_array[$csv_form_num[$i]]['item_name'].'",';
    }
    $csv .= "\r\n";
 
    //データ作成
    $con = dbconect();
    $sql = List_itemSQL($post,$pagename);
    $sql = setOrderbySQL($sql,$pagename);
    
    if($pagename == "ORDERMGR")
    {
        //未受注A
        $csv .= '"未受注(A)"';
        $csv .= "\r\n";
        $sqlA = $sql[1]." AND t1.order_status = 1 ;";
        $result = $con->query($sqlA) or ($judge = true);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($csv_form_num); $i++)
            {
                $column = $form_ini_array[$csv_form_num[$i]]['column_name'];
                $csv .= '"'.$result_row[$column].'",';
            }
            $csv .= "\r\n";
        }
         //未受注B
        $csv .= '"未受注(B)"';
        $csv .= "\r\n";
        $sqlB = $sql[1]." AND t1.order_status = 2 ;";
        $result = $con->query($sqlB) or ($judge = true);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($csv_form_num); $i++)
            {
                $column = $form_ini_array[$csv_form_num[$i]]['column_name'];
                $csv .= '"'.$result_row[$column].'",';
            }
            $csv .= "\r\n";
        }   
        //未受注C
        $csv .= '"未受注(C)"';
        $csv .= "\r\n";
        $sqlC = $sql[1]." AND t1.order_status = 3 ;";
        $result = $con->query($sqlC) or ($judge = true);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($csv_form_num); $i++)
            {
                $column = $form_ini_array[$csv_form_num[$i]]['column_name'];
                $csv .= '"'.$result_row[$column].'",';
            }
            $csv .= "\r\n";
        }
    }
    else
    {
        $result = $con->query($sql[1]);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($csv_form_num); $i++)
            {
                $column = $form_ini_array[$csv_form_num[$i]]['column_name'];
                $csv .= '"'.$result_row[$column].'",';
            }
            $csv .= "\r\n";
        }        
    }
    //文字コードを変更する
    $csv = mb_convert_encoding($csv, "SJIS");  

    // HTTPヘッダ設定
    $filepath = './temp/template.csv';
    $filename = 'CSV出力.csv';
    header('Content-Type: application/octet-stream');
    header('Content-Length: '.filesize($filepath));
    header('Content-Disposition: attachment; filename='.$filename.'');

    // ファイル出力
    echo $csv;
    readfile($filepath);
?>

