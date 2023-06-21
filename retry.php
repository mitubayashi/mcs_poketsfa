<?php
    
?>
<html>
<head>
<link rel="icon" type="image/png" href="./img/favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script language="JavaScript">
    history.forward();
    function countdown(){
    location.href = "./login.php";
    }
    
</script>
</head>
<body>
    <CENTER>
    <?php
        echo "その様なページ移動は禁止されています。<br>5秒以内にページが移動しない場合は下記のボタンをクリックしてください。";
        echo "<form action='./login.php' method='post'>";
        echo "<input type='submit' class = 'button' name ='logout__button' value = 'ログイン画面に戻る' style = 'WIDTH : 140px; HEIGHT : 30px;' >";
        echo "</form>";
    ?>
    </CENTER>
    <script type="text/javascript">
        setInterval( "countdown()", 5000 );
    </script>
</body>
</html>
