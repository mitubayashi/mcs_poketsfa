function resize_schedule(event){
    //カレンダーサイズ調整(縦幅)
    const windowHeight = document.getElementById('month_schedule_area').clientHeight;
    var elements = document.getElementsByClassName('month_calendar_td');
    for(var i = 0; i < elements.length; i++){
        if(windowHeight <= 425)
        {
            elements[i].style.height = '85px';
        }
        else
        {
            elements[i].style.height = ((windowHeight - 50) / 5) + 'px';
        }
    }
}

function open_all_schedule(date,obj)
{    
    //すべてのスケジュールを表示する
    $.ajax({
        url:"open_all_schedule.php",
        type:"POST",
        dataType:"json",
        data:{ open_date: date}
    })
    .done((data) => {
        //成功した場合の処理
        //日付を変更する
        document.getElementById('monthschedule_right_title').innerHTML = data[0];
        
        //スケジュールを表示する
        document.getElementById('monthschedule_right_content').innerHTML = data[1];
        
        //すべてのセルの色を初期化する
        var td = document.getElementById('monthschedule_calendar_table').querySelectorAll("td");
        for(var i = 0; i < td.length; i++)
        {
            td[i].style.backgroundColor = "white";
        }        
        //選択した日付の背景色を変更する
        obj.style.backgroundColor = "#e0efff";
        //社員の表示非表示処理
        hidden_schedule();
    })
    .fail((data) => {
      //失敗した場合の処理
      alert("通信に失敗しました");
    })   
}

function hidden_schedule()
{
    const checkbox = document.getElementsByName('userlist_checkbox');
    
    //チェックボックスが複数の場合
    for(var i = 0; i < checkbox.length; i++)
    {
        if(checkbox[i].checked)
        {
            var Row = document.getElementsByClassName('user_' + checkbox[i].value);    //表示にする要素取得
            for(var j = 0; j < Row.length; j++)
            {
                Row[j].style.display = "";      
            }
        }
        else
        {            
            var Row = document.getElementsByClassName('user_' + checkbox[i].value);    //非表示にする要素取得
            for(var j = 0; j < Row.length; j++)
            {
                Row[j].style.display = "none";      
            }            
        }
    }       
}