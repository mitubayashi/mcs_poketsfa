function top_graph(yotei,ziseki,id,title)
{
    const doughnutChart = document.getElementById(id);

    // 真ん中に表示するオブジェクト
    yotei = parseInt(yotei);
    ziseki = parseInt(ziseki);
    var msg = new Array();
    if(yotei == 0)
    {
        msg[0] = "";
        msg[1] = "";
    }
    else
    {
        var nedan = Math.ceil(ziseki/yotei*100*10) / 10; 
        msg[0] = "目標額："+yotei+"円";
        msg[1] = "達成度："+nedan+"%";
    }

    const counter = {
      id: 'counter',
      beforeDraw(chart, args, options) {
        const { ctx, chartArea: { top, right , bottom, left, width, height } } = chart;
        ctx.save();
        ctx.fillStyle = 'black';
        ctx.fillRect(width / 2, top + (height / 2), 0, 0);
        ctx.font = '70% sans-serif';       //真ん中フォントサイズ、フォントタイプ
        ctx.textAlign = 'center';

        // 位置調整
        //console.log("width / 2, top + (height / 2)", width / 2, top + (height / 2));
        ctx.fillText(msg[0], width / 2, top + (height / 2));      //真ん中の文字(上段)
        ctx.font = '70% sans-serif';       //真ん中フォントサイズ、フォントタイプ
        ctx.fillText(msg[1], width / 2, top + (height / 2) + 10);      //真ん中(下段)
      }
    };
    
    var value = new Array();
    value[0] = ziseki;
    if(ziseki >= yotei)
    {//目標額より実績金額が多い場合、未達成額を0とする
        value[1] = 0;
    }
    else
    {
        value[1] = yotei-ziseki;
    }
    
    //予定額が0円の時は表示しない
    if(yotei == 0)
    {
        value[0] = 0;
        value[1] = 0;
    }
    var label = new Array();
    label[0] = "達成額";
    label[1] = "目標額";
    
    const centerDoughnutChart = new Chart(doughnutChart, {
      type: 'doughnut',
      data: {
        //labels: label,
        datasets: [{
          label: '',
          data: value,       
          backgroundColor: [
            'rgba(255, 102, 102, 0.7)',
            'rgba(102, 178, 255, 0.7)',
          ],
          borderColor: [
            'rgba(255, 102, 102, 1)',
            'rgba(102, 178, 255, 1)',
          ],
        },
    ]
      },
      
      options: {
        responsive: true,
        cutout: 50,
        plugins: {
          legend: {
            display: true,
            position: 'right',
            onClick: function(){ return false; },       //ラベルクリック動作
          },
        title: {
            display: true,
            text: title,
            position: 'top',
            align: 'center',
          },
          counter: {
            fontColor: 'red',
            fontSize: '50px',
            fontFamily: 'sans-serif',
          },
        }, 
        tooltips:{
              callbacks: {
                    title: function (){
                    return "aaaaa";
                }
              }
        }
      },
      
      plugins: [counter]
    });
    centerDoughnutChart.canvas.parentNode.style.height = '45%';
    //centerDoughnutChart.data.labels = label;
}

function yozitu_graph(yotei,ziseki,id,title,syosai_id)
{
    var doughnutChart = document.getElementById(id);
    
    // 真ん中に表示するオブジェクト
    yotei = parseInt(yotei);
    ziseki = parseInt(ziseki);
    var msg = new Array();
    msg[0] = "目標額："+yotei+"円";
    var nedan = Math.ceil(ziseki/yotei*100*10) / 10;
    msg[1] = "達成度："+nedan+"%";
    
    const counter = {
      id: 'counter',
      beforeDraw(chart, args, options) {
        const { ctx, chartArea: { top, right , bottom, left, width, height } } = chart;
        ctx.save();
        ctx.fillStyle = 'black';
        ctx.fillRect(width / 2, top + (height / 2), 0, 0);
        ctx.font = '70% sans-serif';       //真ん中フォントサイズ、フォントタイプ
        ctx.textAlign = 'center';

        // 位置調整
        //console.log("width / 2, top + (height / 2)", width / 2, top + (height / 2));
        ctx.fillText(msg[0], width / 2, top + (height / 2));      //真ん中の文字(上段)
        ctx.font = '70% sans-serif';       //真ん中フォントサイズ、フォントタイプ
        ctx.fillText(msg[1], width / 2, top + (height / 2) + 10);      //真ん中(下段)
      }
    };
    
    var value = new Array();
    value[0] = ziseki;
    if(ziseki >= yotei)
    {//目標額より実績金額が多い場合、未達成額を0とする
        value[1] = 0;
    }
    else
    {
        value[1] = yotei-ziseki;
    }
    var label = new Array();
    label[0] = "達成額";
    label[1] = "目標額";
    
    
    var syosai = document.getElementById(syosai_id);
    syosai.innerHTML = "予定受注額：" + yotei + "円<br>" + 
                                  "達成額　　：" + ziseki + "円<br>" + 
                                  "未達成額　：" + value[1] + "円<br>" +
                                  "達成率　　：" + nedan + "%";
                          
    const centerDoughnutChart = new Chart(doughnutChart, {
      type: 'doughnut',
      data: {
        //labels: label,
        datasets: [{
          label: '',
          data: value,       
          backgroundColor: [
            'rgba(255, 102, 102, 0.7)',
            'rgba(102, 178, 255, 0.7)',
          ],
          borderColor: [
            'rgba(255, 102, 102, 1)',
            'rgba(102, 178, 255, 1)',
          ],
        },
    ]
      },
      
      options: {
        responsive: true,
        cutout: 50,
        plugins: {
          legend: {
            display: true,
            position: 'right',
            onClick: function(){ return false; },       //ラベルクリック動作
          },
        title: {
            display: true,
            text: title,
            position: 'top',
            align: 'center',
          },
          counter: {
            fontColor: 'red',
            fontSize: '50px',
            fontFamily: 'sans-serif',
          },
        }, 
        tooltips:{
              callbacks: {
                    title: function (){
                        return "";
                    }
              }
        }
      },
      
      plugins: [counter]
    });

}
