<template>
  <div id="showing-board">
      <div id="answer-box">
        <span>输入答案: </span>
        <input id="answer" type="text">
        <button id="submit">提交</button>
        <label id="info"></label>
    </div>
    <canvas id="showing" width="480" height="320" style="border: 1px solid #999;"></canvas>

    </div>
</template>

<script>
   'use strict'
    export default {
      name: 'showing-board',
        mounted() {
            const ws = new WebSocket('ws://cp01-mawmd-rd03.epc.baidu.com:8090');
            const canvas = document.getElementById('showing')
            const cxt = canvas.getContext('2d')

            //起始位置开关
            let moveToSwitch = 1
            ws.onmessage = (msg) => {
              let pathObj = msg.data.split('|')
                cxt.strokeStyle = pathObj[5] //线条颜色
                cxt.lineWidth = pathObj[4];//线条的宽度 
                //this.ctx.lineCap="butt";
                cxt.lineJoin = "round";//context.lineJoin - 指定两条线段的连接方式 
              //console.log("msg.data" + msg.data);
              //console.log("pathObj" + pathObj);
              //按下鼠标,松开鼠标为完成一次绘画
              //计算本次绘画的起始位置(非松开鼠标和清空画板操作)
              if (moveToSwitch && msg.data != 'stop' && msg.data != 'clear') {
                  cxt.beginPath()
                  cxt.moveTo(pathObj[0], pathObj[1])
                  //设置开关为非起始位置
                  moveToSwitch = 0
              } 
              if (!moveToSwitch && msg.data == 'stop') {
                  //松开鼠标了,再次画画时，重新计算绘画起始位置
                  //cxt.beginPath()
                  //cxt.moveTo(pathObj[0], pathObj[1])
                  moveToSwitch = 1
              } 
              cxt.lineTo(pathObj[2], pathObj[3])
              cxt.stroke()
              //清空画版
              if (msg.data == 'clear') {
                  cxt.clearRect(0, 0, 480,320)
              } 
              if (msg.data == 'success') {
                  //alert('哇,恭喜,答对啦')
                  document.getElementById('info').innerHTML = '哇,恭喜,答对啦'
              }
              if (msg.data == 'error') {
                  //alert('哎呀，猜错啦，继续努力')
                  document.getElementById('info').innerHTML = '哎呀，猜错啦，继续努力'
              }
            }
            ws.onopen = () => {
                let submitBtn = document.getElementById('submit')
                submitBtn.onclick = () => {
                    let keyword = document.getElementById('answer').value
                    ws.send('answer:' + keyword)
                }
            }
        }
    }
</script>

<style scoped>
    #showing {
        background: #fff;
    }
    #answer-box {
        margin: 10px 0;
    }
</style>
