<template>
    <div id="drawing-board">
    <div id="keyword-box">
        <span>绘画内容: </span>
        <span id="keyword"></span>
        <button id="btn">清空画板</button>
        <button id="changebtn">换一个词</button>
    </div>
    <canvas id="canvas" width="480" height="320" style="border: 1px solid #999;"></canvas>
    </div>
</template>

<script>
'use strict'

class Draw {
    constructor(el) {
        this.el = el
        this.canvas = document.getElementById(this.el)
        this.cxt = this.canvas.getContext('2d')
        this.stage_info = canvas.getBoundingClientRect()
        this.path = {
            beginX: 0,
            beginY: 0,
            endX: 0,
            endY: 0
        }
    }

    init(ws, btn,changeBtn,vueInstance) {
        //console.log(vueInstance)
        //按下鼠标事件
        this.canvas.onmousedown = () => {
            this.drawBegin(event, ws,vueInstance)
        }
        //开始触摸屏幕事件
        this.canvas.ontouchstart = () => {
            this.drawBegin(event.touches[0], ws,vueInstance)
        }
        //松开鼠标事件
        this.canvas.onmouseup = () => {
            this.drawEnd()
            ws.send('stop')
        }
        //离开触摸区域
        this.canvas.ontouchend = () => {
            this.drawEnd()
            ws.send('stop')
        }
        this.clearCanvas(ws, btn)
        this.changeWord(ws,vueInstance, changeBtn)
    }
    //开始绘画
    drawBegin(e, ws,vueInstance) {
        window.getSelection() ? window.getSelection().removeAllRanges() : document.selection.empty()
        this.cxt.strokeStyle = vueInstance.now().drawcolor
        this.cxt.lineWidth = vueInstance.now().drawsize;//线条的宽度 
        //this.ctx.lineCap="butt";
        this.cxt.lineJoin = "round";//context.lineJoin - 指定两条线段的连接方式 
        this.cxt.beginPath()
        this.cxt.moveTo(
            e.clientX - this.stage_info.left,
            e.clientY - this.stage_info.top
        )

        this.path.beginX = e.clientX - this.stage_info.left
        this.path.beginY = e.clientY - this.stage_info.top

        //鼠标移动,实时计算坐标信息
        document.onmousemove = () => {
            this.drawing(event, ws,vueInstance)
        }
        document.ontouchmove = () => {
            //alert('touch move')
            this.drawing(event.changedTouches[0], ws,vueInstance)
        }
        // document.onmouseup = this.drawEnd
    }
    drawing(e, ws,vueInstance) {
        this.cxt.lineTo(
            e.clientX - this.stage_info.left,
            e.clientY - this.stage_info.top
        )
        this.path.endX = e.clientX - this.stage_info.left
        this.path.endY = e.clientY - this.stage_info.top
        ws.send(this.path.beginX + '|' + this.path.beginY + '|' + this.path.endX + '|' + this.path.endY + '|' + this.cxt.lineWidth + '|' + this.cxt.strokeStyle)

        this.cxt.stroke()
    }
    drawEnd() {
        document.onmousemove = document.onmouseup = null
        document.ontouchmove = document.ontouchend = null
    }
    //清除画版
    clearCanvas(ws, btn) {
        btn.onclick = () => {
            this.cxt.clearRect(0, 0, 480, 320)
            ws.send('clear')
        }
    }
    //更换关键词
    changeWord(ws,vue, btn) {
        btn.onclick = () => {
            vue.init()
        }
    }
}

export default {
  name: 'drawing-board',
  //props:['drawcolor'],
  // 通过父组件App.vue传递值进来
  props:{drawcolor:{default:'#ccc'},drawsize:{default:5}},
  computed: {
    // a computed getter
    mydrawcolor: function () {
      // `this` points to the vm instance
      return this.drawcolor
    }
  },
  methods:{
    //实时获取画笔的颜色,大小等
    now:function(){
        return {drawcolor:this.drawcolor,drawsize:this.drawsize}
    },
    //初始化操作
    init:function(){
        const ws = new WebSocket('ws://cp01-mawmd-rd03.epc.baidu.com:8090')
        let draw = new Draw('canvas')
        let btn = document.getElementById('btn')
        let changeBtn = document.getElementById('changebtn')
        let data = [this.drawcolor]
        ws.onopen = () => {
            ws.send('start-draw');
            draw.init(ws, btn,changeBtn,this)
        }
        ws.onmessage = (msg) => {
            msg.data.split(':')[0] == 'keyword' ?
                document.getElementById('keyword').innerHTML = msg.data.split(':')[1] :''
        }
    }

  },
    //准备好操作
    mounted() {
        this.init()
    }
}
</script>

<style scoped>
    #canvas {
        background: #fff;
        cursor: default;
    }
    #keyword-box {
        margin: 10px 0;
    }
</style>
