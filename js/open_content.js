function open_sech_modal()
{
    //検索条件画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = sech_modal_html;
    
    //入力内容をセットする、したくない場合はコメントアウトする
    modal_value_set(sech_value);
}

function open_insert_modal()
{
    //新規登録画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = insert_modal_html;    
    
    //案件管理の受注額、受注月を入力不可とする
    if(pagename == "MATTER")
    {
        textbox_disabled("MATamount",-1);
        textbox_disabled("MATmonth",-1);
    }
    
    //削除済みのユーザー、顧客をプルダウンの選択肢から除外する
    for(var i = 0; i < insert_form_num.length; i++)
    {
        switch(String(insert_form_num[i]["field_type"]))
        {
            case "3":
                var datalist = document.getElementById(insert_form_num[i]["form_num"]).options;
                for(var j = 0; j < datalist.length; j++)
                {
                    if(datalist[j].dataset.deleteflag != "0")
                    {
                        datalist[j].remove();
                        j--;
                    }
                }
                break;
            case "4":
                const checkbox = document.getElementsByName("multiple_pulldown_check_" + insert_form_num[i]["form_num"]);
                for(var j = 0; j < checkbox.length; j++)
                {
                    if(checkbox[j].checked == false && checkbox[j].dataset.deleteflag != "0")
                    {
                        document.getElementById(insert_form_num[i]["form_num"] + '_checkbox_data_' + checkbox[j].value).remove();
                    }
                }
                break;
        }
    }
}

function open_edit_modal(edit_id)
{
    //編集画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = edit_modal_html;
    
    //入力内容をセットする
    edit_set(edit_id);
    
    if(pagename == "MATTER")
    {//ステータスが「受注」以外の場合は受注額、受注月を入力不可とする
        if(document.getElementById('MATstatus').value != "0")
        {
            textbox_disabled("MATamount",-1);
            textbox_disabled("MATmonth",-1);  
            document.getElementById("MATamount").value = "";
            document.getElementById("MATmonth").value = "";
        }
    }
    
    //削除済みのユーザー、顧客をプルダウンの選択肢から除外する
    for(var i = 0; i < edit_form_num.length; i++)
    {
        switch(String(edit_form_num[i]["field_type"]))
        {
            case "3":
                var datalist = document.getElementById(edit_form_num[i]["form_num"]).options;
                for(var j = 0; j < datalist.length; j++)
                {
                    if(datalist[j].dataset.deleteflag != "0" && datalist[j].value != document.getElementById(edit_form_num[i]["form_num"]).value)
                    {
                        datalist[j].remove();
                        j--;
                    }
                }
                break;
            case "4":
                const checkbox = document.getElementsByName("multiple_pulldown_check_" + edit_form_num[i]["form_num"]);
                for(var j = 0; j < checkbox.length; j++)
                {
                    if(checkbox[j].checked == false && checkbox[j].dataset.deleteflag != "0")
                    {
                        document.getElementById(edit_form_num[i]["form_num"] + '_checkbox_data_' + checkbox[j].value).remove();
                    }
                }
                break;  
        }
    }
}

function open_schedule_modal()
{
    //スケジュール登録画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = schedule_modal_html;       
    document.getElementById("appointment_flag").value = "";
    
    //削除済みの顧客を選択肢から除外する
    var datalist = document.getElementById("SCHcustomername_datalist").options;
    for(var i = 0; i < datalist.length; i++)
    {
        if(datalist[i].dataset.deleteflag != "0")
        {
            datalist[i].remove();
            i--;
        }
    }
}

function open_scheduleedit_modal(edit_id)
{
    //スケジュール編集画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = scheduleedit_modal_html;       
    document.getElementById("appointment_flag").value = "";
    
    //メニューボタンを閉じる
    id = document.getElementById("schedule_menu_box");
    id.style.display = "none";

    //入力内容をセットする
    schedule_edit_set(edit_id);
    textbox_disabled("SCHcustomername_text",-1);
    textbox_disabled("SCHcustomerabbreviation",-1);
    textbox_disabled("SCHusername",-1);
}

function open_appointment(customer_id,customer_name,customer_abbreviation,user_id,appointment_id,start_date)
{
    var modal = document.getElementById('dialog');
    modal.showModal();
    modal.innerHTML = schedule_modal_html;
    
    //アポイントメントフラグ
    document.getElementById("appointment_flag").value = appointment_id;
    
    //顧客名
    document.getElementById("SCHcustomername_text").value = customer_name;
    var datalist = document.getElementById("SCHcustomername_datalist").options;    
    document.getElementById("SCHcustomername_hidden").value = customer_id;
    textbox_disabled("SCHcustomername_text",-1);

    //略称
    document.getElementById("SCHcustomerabbreviation").value = customer_abbreviation;
    textbox_disabled("SCHcustomerabbreviation",-1);

    //担当者
    document.getElementById("SCHusername").value = user_id;
    textbox_disabled("SCHusername",-1);

    //開始日付
    document.getElementById("SCHdate").value = start_date;
}

function modal_close()
{
    document.getElementById('dialog').close();
}

function open_multiple_pulldown(obj,id)
{
    //複数選択可能プルダウンをすべて閉じる
    var element = document.getElementsByClassName("multiple_pulldown_box");    
    for(var i = 0; i < element.length; i++)
    {
        element[i].style.display = "none";
    }
   
    //複数選択可能プルダウンの選択肢を表示する
    //id.style.display = "block";
    var bounds = obj.getBoundingClientRect(); 
    var style = id.currentStyle || document.defaultView.getComputedStyle(id, '');
    if (style.display == 'none')
    { //消えてれば表示させる
        id.style.display = 'block';
        id.style.top = bounds.top + obj.offsetHeight + 'px'; //thisの縦幅分下げた縦位置にセット
        id.style.left = bounds.left+'px'; //thisに左詰めにセット
    }
}

function close_multiple_pulldown(id,target)
{
    //複数選択可能プルダウンの選択肢を非表示にする
    if(target == null)
    {
        id.style.display = "none";
    }
    else
    {
        if((id.id + '_nav') != target.id)
        {
            id.style.display = "none";
        }
    }
}

function password_input_open()
{
    var element = document.getElementsByClassName("password_input");
    var flg = document.getElementById('password_input_flg');
    var flg_value = 0;
    for(var i = 0; i < element.length; i++)
    {
        if(flg.value == "0")
        { 
            element[i].style.display = "table-row";
            flg_value = "1";
        }
        else
        {
            element[i].style.display = "none";
            flg_value = "0";
        }
    }
    flg.value = flg_value;
}

function textbox_disabled(name,tabindex_value)
{
    if(tabindex_value == '')
    {
        //入力可とする
        document.getElementById(name).classList.remove('disabled');
        document.getElementById(name).tabIndex = tabindex_value;
    }
    else
    {
        //入力不可とする
       document.getElementById(name).classList.add("disabled");
       document.getElementById(name).tabIndex = tabindex_value;       
    }
}

