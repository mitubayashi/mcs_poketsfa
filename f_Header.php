<?php
/***************************************************************************
function makeBoxHeader()


引数			なし

戻り値	$header_html	画面上部HTML
***************************************************************************/

function makeBoxHeader(){
    
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);    
    require_once("f_Header.php");
    
    //変数
    $header_html = "";    
        
    //左上ボタン作成処理
    $header_html .=  '<div class="header">';
    $header_html .= "<div class='header_left_content'>";
    $header_html .= '<form action="./pageJump.php" method="post">';
    $header_html .= make_button($_SESSION["pagename"],"set_top_button");    
    $header_html .= '</form>';
    $header_html .= "</div>";
    
    //社員名表示部分作成
    $header_html .= "<div class='header_right_content'>";
    $header_html .= $_SESSION["loginuser"]["user_name"];
    $header_html .= '<input type="button" value="ログアウト" class="table_button" onclick="location.href=\'login.php\'">';
    $header_html .= "</div>";    
    $header_html .= "</div>";
    
    //メインタイトル作成
    $main_title = $form_ini_array[$_SESSION["pagename"]]["page_maintitle"];
    if($main_title != "")
    {
        $header_html .= "<div class='main_title'>";
        $header_html .= '<form action="./pageJump.php" method="post">';
        $header_html .= $main_title;
        $header_html .= make_button($_SESSION["pagename"],"set_maintitle_button"); 
        $header_html .= '</form>';
        $header_html .= "</div>";
    }
    
    //サブタイトル作成
    $sub_title = $form_ini_array[$_SESSION["pagename"]]["page_subtitle"];
    if($sub_title != "")
    {
        $header_html .= "<div class='sub_title'>";
        $header_html .= '<form action="./pageJump.php" method="post">';
        $header_html .= $sub_title;
        $header_html .= make_button($_SESSION["pagename"],"set_subtitle_button"); 
        $header_html .= '</form>';
        $header_html .= "</div>";
    }
    return $header_html;
}

/***************************************************************************
function make_button()


引数1   $pagename               ページ名
引数2   $set_type               ボタンの表示位置      

戻り値	$button_html	画面上部HTML
***************************************************************************/

function make_button($pagename,$set_type){
    
    //初期設定
    $button_ini_array = parse_ini_file("./ini/button.ini",true);    
    $button_array = explode(",",$button_ini_array[$pagename][$set_type]);
    
    //変数
    $button_html = "";
    
    for($i = 0; $i < count($button_array); $i++)
    {
        if($button_array[$i] != "")
        {
            //ログイン社員が一般である場合はユーザー管理ボタンを非表示
            if($button_array[$i] == "USER_button" && $_SESSION['loginuser']['user_status'] == "0")
            {
                continue;
            }
            $button_style = $button_ini_array[$button_array[$i]]['button_style'];
            $button_type = $button_ini_array[$button_array[$i]]['button_type'];
            $button_value = $button_ini_array[$button_array[$i]]['button_value'];
            $button_onclick = $button_ini_array[$button_array[$i]]['button_onclick'];

            switch ($button_style) {
                case '1':
                    $button_class = 'header_button';
                    break;
                case '2':
                    $button_class = 'table_button';
                    break;
                case '3':
                    $button_class = 'modal_button';
                    break;
            }
            switch ($button_type) {
                case '0':
                    $type = "submit";
                    break;
                case '1':
                    $type = "button";
                    break;
            }
            if($button_array[$i] == "CSV_button") 
            {
                $button_onclick = "location.href='csv_download.php?pagename=".$pagename."'";
            }
            $button_html .= '<input type="'.$type.'" value="'.$button_value.'" class="'.$button_class.'" name="'.$button_array[$i].'" onclick="'.$button_onclick.'">';   
        }
    }
    if(isset($_SESSION['pre_create']['REPORTCREATE_read']) && $set_type == 'set_top_button')
    {
        $button_html .= '<input type="submit" value="一覧に戻る" class="header_button" name="REPORTLIST_back">';            
    }
    if(isset($_SESSION['list']['REPORTLIST_list']) && $set_type == 'set_top_button')
    {
        $button_html .= '<button type="submit" class="header_button" name="REPORTCREATE_button" value="'.$_SESSION['list']['REPORTLIST_list'].'">'; 
        $button_html .= '登録に戻る';
        $button_html .= '</button>';
    }
    return $button_html;
}
?>