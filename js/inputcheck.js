function schedule_check()
{
    //スケジュール登録時に顧客チェックをする(アポイントの時は行わない)
    if(document.getElementById("appointment_flag").value == "")
    {
        var datalist = document.getElementById("SCHcustomername_datalist").options;    
        var customername = document.getElementById("SCHcustomername_text").value;
        var flag = 0;

        //登録済み顧客名が入力されており、担当者が異なる場合新規登録
        if(document.getElementById("SCHcustomername_hidden").value != "")
        {
            for(var i = 0; i < datalist.length; i++)
            {                
                if(datalist[i].dataset.id == document.getElementById("SCHcustomername_hidden").value)
                {
                    if(document.getElementById("SCHusername").value != datalist[i].dataset.userid)
                    {
                        document.getElementById("SCHcustomername_hidden").value = "";
                        textbox_disabled("SCHcustomerabbreviation",'');                        
                    }
                }
            }            
        }   
    }
}

function mitsumori_kyaku_check()
{
    var datalist = document.getElementById("ANKkyaku_datalist").options;    
    var customername = document.getElementById("ANKkyaku").value; 
    document.getElementById("kyaku_flag").value = "";
    
    for(var i = 0; i < datalist.length; i++)
    {
        if(datalist[i].value == customername)
        {
            document.getElementById("kyaku_flag").value = datalist[i].dataset.id;
        }
    }
}

function check(form_num,flag)
{
    var judge = true;

    //入力チェックを行う
    if(flag != "2")
    {
        for(var i = 0; i < form_num.length; i++)
        {
            if(form_num[i]["form_num"] == "USEloginpassword")
            {            
                if(document.getElementById('password_input_flg').value === "1")
                {
                    if(pass_check() === false)
                    {
                        judge = false;
                    }
                    if(flag === 1)
                    {
                        if(document.getElementById('password').value !== document.getElementById('nowpass').value)
                        {
                            input_style('nowpass',false);
                            document.getElementById("nowpass_errormsg").textContent = "現在のパスワードと一致しません";
                            judge = false;
                        }
                        else
                        {
                            input_style('nowpass',true);
                            document.getElementById("nowpass_errormsg").textContent = "";
                        }
                    }
                }
            }
            else if(form_num[i]["form_num"] == "SCHtime")
            {
                if(time_check() === false)
                {
                    judge = false;
                }
            }
            else
            {
                if(form_num[i]["form_num"] != "USEstatus" && form_num[i]["form_num"] != "check_status")
                {
                    if(input_check(form_num[i]["form_num"],form_num[i]["max_length"],form_num[i]["form_format"],form_num[i]["isnotnull"]) === false)
                    { 
                        judge = false;
                    }
                }
            }
        }
    }
    
    //データチェック
    if(judge)
    {
        if(data_check(form_num,flag,pagename) === false)
        {
            judge = false;
        }
    }
    
    if(judge)
    {
        if(pagename=="MATTER" && mitsumori_flag == "1" && flag == 0)
        {
            if(window.confirm("続けて見積・請求システムへ案件登録しますか？"))
            {
                document.getElementById("mitsumori_insert").value = "0";
            }
        }
    }
    
    if(judge)
    {
        if(flag == '4')
        {
            var title = document.getElementById("SCHcustomername_text").value + '様';
            var date = document.getElementById("SCHdate").value;
            var starttime = date + 'T' + document.getElementById("SCHtime_starthour").value + ':' + document.getElementById("SCHtime_startmin").value + ':00';
            var endtime = date + 'T' + document.getElementById("SCHtime_endhour").value + ':' + document.getElementById("SCHtime_endmin").value + ':00';
            var url = 'https://outlook.office.com/calendar/action/compose?' + 'subject=' + title  + '&startdt=' + starttime + '&enddt=' + endtime;
            window.open(encodeURI(url));
        }
    }
    return judge;
}

function time_check()
{
    var time_array = new Array();
    time_array[0] = 'SCHtime_starthour';
    time_array[1] = 'SCHtime_endhour';
    time_array[2] = 'SCHtime_startmin';   
    time_array[3] = 'SCHtime_endmin';
    var judge = true;
    
    //入力文字チェック
    for(var i = 0; i < time_array.length; i++)
    {
        var value = document.getElementById(time_array[i]).value;
        if(value.match(/[^0-9]+/) || 2 !== strlen(value)) 
        {
            input_style(time_array[i],false);
            judge=false;
        }
        else
        {
            input_style(time_array[i],true);
        }
    }    
    if(judge == false)
    {
        document.getElementById("SCHtime_errormsg").textContent = "半角数字2桁で入力してください";
    }
    else
    {
        document.getElementById("SCHtime_errormsg").textContent = "";
    }
    
    //正しい時刻が入力されているか判定する
    if(judge)
    {
        for(var i = 0; i < time_array.length; i++)
        {
            var value = document.getElementById(time_array[i]).value;
            //時刻の最大値を求める
            if(i <= 1)
            {
                var max_value = 23;
            }
            else
            {
                var max_value = 59;
            }

            //時刻チェック
            if(value > max_value)
            {
                input_style(time_array[i],false);
                judge=false;
            }
            else
            {
                input_style(time_array[i],true);
            }
        }
         if(judge == false)
        {
            document.getElementById("SCHtime_errormsg").textContent = "正しい時刻を入力してください";
        }
        else
        {
            document.getElementById("SCHtime_errormsg").textContent = "";
        }
    }
    return judge;
}

function data_check(form_num,flag,pagename)
{
    var judge = true;
    var value = new Array();
    var form_data_num = new Array();
    var counter = 0;
    
    for(var i = 0; i < form_num.length; i++)
    {
        if(form_num[i]['form_num'] == 'USEstatus' || form_num[i]['form_num'] == 'check_status')
        {
            let elements = document.getElementsByName(form_num[i]['form_num']);
            for (let j = 0; j < elements.length; j++){
                if (elements.item(j).checked){
                    value[counter] = elements.item(j).value;
                    form_data_num[counter] = form_num[i];
                    counter++;
                }
            }
            if(document.getElementById(form_num[i]['form_num']) !== null)
            {
                value[counter] = document.getElementById(form_num[i]['form_num']).value;
                form_data_num[counter] = form_num[i];
                counter++;
            }
        } 
        else if(form_num[i]['form_num'] !== 'SCHtime')
        {
            value[counter] = document.getElementById(form_num[i]['form_num']).value;
            form_data_num[counter] = form_num[i];
            counter++;
        }
    }
    if(document.getElementById('edit_id') == null)
    {
        var edit_id = "";
        var updatetime = "";
    }
    else
    {
        var edit_id = document.getElementById('edit_id').value;
        var updatetime = document.getElementById('updatetime').value;
    }
    
    if(document.getElementById('schedule_id') !== null)
    {
        var edit_id = document.getElementById('schedule_id').value;
        var updatetime = document.getElementById('updatetime').value;   
    }
    
    if(flag == "4")
    {
        var edit_id = document.getElementById('SCHcustomername_hidden').value;
        var updatetime = "";
    }


    //データチェックを行う
    $.ajax({
        url:"input_check.php",
        type:"POST",
        async: false,
        dataType:"json",
        data:{ 
            form_num : form_data_num,
            value : value,
            pagename : pagename,
            flag : flag,
            edit_id : edit_id,
            updatetime: updatetime 
        }
    })
    .done((data) => {
        //成功した場合の処理
        if(data.length !== 0)
        {
            switch(data[0]['form_num'])
            {
                case 'updatetime':
                    var msg = "他の端末からデータが操作されています。\n情報を更新しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                    if(window.confirm(msg) == false)
                    {
                        judge = false;
                    }
                    break;
                case 'user_status_error':
                    var msg = "このユーザーは上位者として設定されているため、\n権限の変更、ユーザーの削除ができません。\n先に配下の社員の上位者を変更してください。";         
                    alert(msg);
                    judge = false;
                    break;
                case 'delete_user_error':
                    var msg = "管理者が0人となってしまうため、\n権限の変更、ユーザーの削除ができません。";
                    alert(msg);
                    judge = false;
                    break;
                case 'report_create':
                    var msg = "他の端末から面談報告書が登録されました。\n情報を更新しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                    if(window.confirm(msg) == false)
                    {
                        judge = false;
                    }
                    else
                    {
                        var button = document.getElementById('report_button');
                        button.setAttribute('name', 'TOP_reportedit');
                    }
                    break;
                case 'report_create_error':
                    var msg = "担当者により面談報告書が登録されているため、\n登録できません。";
                    alert(msg);
                    judge = false;
                    break;
                case 'check_status_error':
                    var msg = "他の端末から面談報告書が承認または未承認されたため、\n更新できません。";
                    alert(msg);
                    judge = false;
                    break;
                case 'report_update_error':
                    var msg = "他の端末から面談報告書が更新されたため、\n更新できません。";
                    alert(msg);
                    judge = false;
                    break;
                default:
                    for(var i = 0; i < data.length; i++)
                    {
                        input_style(data[i]['form_num'],false);
                        document.getElementById(data[i]['form_num'] + "_errormsg").textContent = data[i]['error_msg'];
                        judge = false;
                    }
                    break;
            }
        }
        else
        {
            if(flag == "0")
            {
                if(pagename == "MITSUMORI")
                {
                    if(document.getElementById("kyaku_flag").value == "")
                    {
                        var msg = "顧客を新規登録し、情報登録しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                    }
                    else
                    {
                        var msg = "入力内容正常確認。\n情報登録しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";                
                    }
                }
                else
                {
                    var msg = "入力内容正常確認。\n情報登録しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                }
            }
            else if(flag == "4")
            {
                if(document.getElementById("SCHcustomername_hidden").value == "")
                {
                    var msg = "顧客を新規登録し、情報登録しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                }
                else
                {
                    var msg = "入力内容正常確認。\n情報登録しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
                }
            }
            else if(flag == "1" || flag == "5")
            {
                var msg = "入力内容正常確認。\n情報更新しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
            }
            else
            {
                var msg = "入力内容正常確認。\n情報削除しますがよろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";            
            }
            
            if(window.confirm(msg) == false)
            {
                judge = false;
            }        
        }
    })
    .fail((jqXHR, textStatus, errorThrown) => {
      //失敗した場合の処理
      alert("通信に失敗しました");
      judge = false;
    })   
    
    return judge;
}

// form_format 1:日付入力　2:月入力　3:半角英数のみ　4:半角数字のみ
//                    5:ALL OK　6:チェックしない　7:時刻入力欄　8:目標金額
//

function input_check(name,max_length,form_format,isnotnull)
{
    var judge = true;
    var str = document.getElementById(name).value;
    m = String.fromCharCode(event.keyCode);
    var len = 0;
    var str2 = escape(str);
    
    //エラーメッセージリセット
    if(String(form_format) != '8')
    {
        document.getElementById(name + "_errormsg").textContent = "";
    }
    
    //入力内容チェック
    switch(String(form_format))
    {
        case '1':
            //日付入力チェック
            if(document.getElementById(name).validity.valid == false)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "日付に誤りがあります";
            }
            break;
        case '3':
            //半角英数チェック
            if(str.match(/[^0-9A-Za-z]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "半角英数字で入力してください";
            }
            break;
        case '4':
            //半角数字チェック
            if(str.match(/[^0-9]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "半角数字で入力してください";
            }
            break;
        case '8':
            if(str.match(/[^0-9]+/) || 11 < strlen(str) || document.getElementById(name).value == '') 
            {
                judge = false;
            }
            break;
    }
    
    //入力文字数チェック
    if(judge)
    {
        if(max_length != "" && max_length != 0)
        {
            if(max_length < strlen(str))
            {
                document.getElementById(name + "_errormsg").textContent = max_length + "字以内で入力してください";
                judge = false;        
            }
        }
    }
    //未入力チェック
    if(judge)
    {
        //案件管理のステータスが「受注」の場合、受注額と受注月は必須入力とする
        if((name == "MATamount" && document.getElementById("MATstatus").value == "0") || (name == "MATmonth" && document.getElementById("MATstatus").value == "0"))
        {
            isnotnull = 1;
        }
        if(isnotnull == 1)
        {
            if(document.getElementById(name).value == '')
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "必須入力項目です";
            }
        }
    }
    //入力エラーだった場合、入力欄を赤色にする
    if(name == "USEreportcreatenotice" || name == "USEreportchecknotice")
    {
        name = "textbox_" + name;
    }
    input_style(name,judge);
    
    //案件管理のステータスが「受注」の場合の処理
    if(name == "MATstatus")
    {//ステータスが「受注」以外の場合は受注額、受注月を入力不可とする
        console.log(document.getElementById('MATstatus'));
        if(document.getElementById('MATstatus').value != "0")
        {
            textbox_disabled("MATamount",-1);
            textbox_disabled("MATmonth",-1);        
            document.getElementById("MATamount").value = "";
            document.getElementById("MATmonth").value = "";
            document.getElementById("MATamount_errormsg").textContent = "";
            document.getElementById("MATmonth_errormsg").textContent = "";
            input_style('MATmonth',true);
            input_style('MATamount',true);
        }
        else
        {
            textbox_disabled("MATamount",'');
            textbox_disabled("MATmonth",'');
        }
    }

    return judge;
}

function pass_check()
{
    var password = document.getElementById("USEloginpassword");
    var checkpass = document.getElementById("checkpass");
    var judge = true;
    
    //エラーメッセージをリセットする
    document.getElementById("checkpass_errormsg").textContent = "";
    document.getElementById("USEloginpassword_errormsg").textContent = "";
    
    //入力内容チェック
    var str = password.value;
    if(str.match(/[^0-9A-Za-z]+/)) 
    {
        judge = false;
        document.getElementById("checkpass_errormsg").textContent = "半角英数字で入力してください";
    }
    
    //確認用パスワードと内容が一致するかチェックする
    if(judge)
    {
        if(password.value === "" && checkpass.value === "")
        {
            judge = false;
            document.getElementById("checkpass_errormsg").textContent = "必須入力項目です";
            document.getElementById("USEloginpassword_errormsg").textContent = "必須入力項目です";
        }
        if((password.value !== checkpass.value))
        {
            judge = false;       
            document.getElementById("checkpass_errormsg").textContent = "パスワードが一致しません";
        }
    }
    input_style(password.id,judge);
    input_style(checkpass.id,judge);
    
    return judge;
}

function input_style(name,judge)
{
    if(judge)
    {
        document.getElementById(name).style.backgroundColor = '';
        document.getElementById(name).style.border = '';
    }
    else
    {
        document.getElementById(name).style.backgroundColor = '#ffd6d6';
        document.getElementById(name).style.border = '#ff0000 1px solid';
    }
}

function strlen(str) {
  var ret = 0;
  for (var i = 0; i < str.length; i++,ret++) {
    var upper = str.charCodeAt(i);
    var lower = str.length > (i + 1) ? str.charCodeAt(i + 1) : 0;
    if (isSurrogatePear(upper, lower)) {
      i++;
    }
  }
  return ret;
}

function isSurrogatePear(upper, lower) {
  return 0xD800 <= upper && upper <= 0xDBFF && 0xDC00 <= lower && lower <= 0xDFFF;
}