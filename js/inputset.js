function multiple_pulldown_value_set(checkbox,hidden,textbox)
{
    //変数
    var select_userid = "";
    var select_username = "";
    
    //選択肢が複数の場合
    for(var i = 0; i < checkbox.length; i++)
    {
        if(checkbox[i].checked)
        {
            select_userid = select_userid + checkbox[i].value + ",";
            select_username = select_username + checkbox[i].dataset.name + ",";
        }
    }
    
    //選択肢が1つの場合
    if(checkbox.checked)
    {
        select_userid = checkbox.value + ",";
        select_username = checkbox.dataset.name + ",";
    }
    
    //末尾の「,」を削除した値をセットする
    hidden.value = select_userid.slice(0, -1);
    textbox.value = select_username.slice(0, -1);
}

function edit_set(edit_id)
{    
    //入力欄フォームに値をセットする
    $.ajax({
        url:"edit_value_set.php",
        type:"POST",
        dataType:"json",
        async: false,
        data:{ edit_id: edit_id}
    })
    .done((data) => {
        //成功した場合の処理
        document.getElementById('edit_id').value = edit_id;
        modal_value_set(data);
    })
    .fail((data) => {
      //失敗した場合の処理
      alert("通信に失敗しました");
    })    
}

function schedule_edit_set(edit_id)
{
      //入力欄フォームに値をセットする
    $.ajax({
        url:"edit_schedule_set.php",
        type:"POST",
        dataType:"json",
        data:{ edit_id: edit_id}
    })
    .done((data) => {
        //成功した場合の処理
        document.getElementById('edit_id').value = edit_id;
        modal_value_set(data);
    })
    .fail((data) => {
      //失敗した場合の処理
      alert("通信に失敗しました");
    })      
}

function modal_value_set(set_data)
{
    for(var i = 0; i < set_data.length; i++)
    {
        switch(set_data[i]["field_type"])
        {
            case '1':
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"];
                break;
            case '2':
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"];
                break;
            case '3':
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"];
                if(set_data[i]["value"] == null)
                {
                    document.getElementById(set_data[i]["form_num"]).value = "";
                }
                break;
            case '4':                           
                const checkbox = document.getElementsByName("multiple_pulldown_check_" + set_data[i]["form_num"]);
                const select = set_data[i]["value"].split(",");
                var select_username = "";
                for(var j = 0; j < checkbox.length; j++)
                {
                    for(var k = 0; k < select.length; k++)
                    {
                        if(checkbox[j].value == select[k])
                        {
                            checkbox[j].checked = true;
                            select_username = select_username + checkbox[j].dataset.name + ",";
                        }
                    }
                }
                //値をセットする
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"]; 
                document.getElementById("textbox_" + set_data[i]["form_num"]).value = select_username.slice(0, -1);
                break;
            case '5':
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"];
                break;
            case '6':
                break;
            case '7':
                document.getElementById("password").value = set_data[i]["value"];
                break;
            case '8':
                let radio = document.getElementsByName(set_data[i]["form_num"]);
                for(var j = 0; j < radio.length; j++)
                {
                    if(radio[j].value == set_data[i]["value"])
                    {
                        radio[j].checked = true;
                    }
                }
                break;      
            case '10':
                document.getElementById(set_data[i]["form_num"]).value = set_data[i]["value"];
                break;
            case '9999':
                //更新時間データをセットする
                document.getElementById('updatetime').value = set_data[i]["value"];
                break;
        }
    }
}

function input_pulldown_value_set(obj)
{    
    var datalist = document.getElementById("SCHcustomername_datalist").options;
    document.getElementById("SCHcustomername_hidden").value = "";
    var flag = 0;
    for(var i = 0; i < datalist.length; i++)
    {
        if(obj.value == datalist[i].value)
        {
            document.getElementById("SCHcustomername_text").value = datalist[i].dataset.customername;
            document.getElementById("SCHcustomername_hidden").value = datalist[i].dataset.id;
            document.getElementById("SCHcustomerabbreviation").value = datalist[i].dataset.abbreviation;
            textbox_disabled("SCHcustomerabbreviation",-1);
            document.getElementById("SCHusername").value = datalist[i].dataset.userid;
            flag = 1;
            break;
        }        
        else if(obj.value == datalist[i].dataset.customername)
        {
            document.getElementById("SCHcustomername_text").value = datalist[i].dataset.customername;
            document.getElementById("SCHcustomername_hidden").value = datalist[i].dataset.id;
            document.getElementById("SCHcustomerabbreviation").value = datalist[i].dataset.abbreviation;
            textbox_disabled("SCHcustomerabbreviation",-1);
            document.getElementById("SCHusername").value = datalist[i].dataset.userid;
            flag = 1;
            break;
        }
    }
    if(flag == 0)
    {
        document.getElementById("SCHcustomerabbreviation").value = "";
        textbox_disabled("SCHcustomerabbreviation",'');
        document.getElementById("SCHusername").value = "";
    }
    input_check('SCHcustomerabbreviation',60,5,1);
    input_check('SCHusername','','',1);
}

function time_input(id,obj)
{
    if(time_check())
    {
        //開始時刻を入力
        if(id == "SCHtime_starthour" || id == "SCHtime_startmin")
        {
            if(document.getElementById("time_input_flag").value == "0")
            {
                //終了時刻を自動入力する
                var endhour = Number(document.getElementById("SCHtime_starthour").value);
                var endmin = Number(document.getElementById("SCHtime_startmin").value) + 30;

                //桁調整
                if(endmin >= 60)
                {
                    endhour += 1;
                    endmin = endmin - 60;
                }
                if(endhour <= 9)
                {
                    endhour = "0" + endhour;
                }
                if(endmin == 0)
                {
                    endmin = "00";
                }
                if(endhour >= 24)
                {
                    endhour = endhour - 24;
                }
                if(endhour == 0)
                {
                    endhour = "00";
                }
                //値をセットする
                document.getElementById("SCHtime_endhour").value = endhour;
                document.getElementById("SCHtime_endmin").value = endmin;
            }
        }
    }
    //終了時刻を入力
    if(id == "SCHtime_endhour" || id == "SCHtime_endmin")
    {
        document.getElementById("time_input_flag").value = "1";        
    }
    
    obj.style.display = "none";
}

function time_set(value,text_box,obj)
{
    text_box.value = value;
    time_input(text_box.id,obj);
}