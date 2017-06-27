<template>
  <div id="app">
      <button  @click='draw' :class ="{active:player==1}">我来画</button>
      <button  @click='guess' :class="{active:player==2}">我来猜</button>
      <div v-if='this.player == 1'>  
        <label>画笔大小: </label><input v-model="brushsize">
        <label>画笔颜色: </label><colorPicker v-model="brushcolor" @change="headleChangeColor"></colorPicker>
      </div>
      <drawing-board  :drawsize="brushsize" :drawcolor="brushcolor" v-if='this.player == 1'></drawing-board>
      <showing-board v-else-if='this.player == 2'></showing-board>
  </div>
</template>

<script>
import Vue from 'vue'
import DrawingBoard from './components/drawing-board.vue'
import showingBoard from './components/showing-board.vue'
import colorPicker from './components/colorPicker.vue'

export default {
  name: 'app',
    data() {
      return {
        player: 0,
        brushcolor:"#f00",
        brushsize:5,
        active:{
          'activeClass':false
        }
      }
    },
    components: {
      DrawingBoard,
      showingBoard,
      colorPicker
    },
    methods: {
      draw(e) {
        //console.log(e)
        this.player = 1
        this.active.activeClass = true
      },
      guess() {
        this.player = 2
      },
      replay() {
        this.player = 0
        location.reload()
      },
      headleChangeColor(color) {
        this.brushcolor = color
      }
    }
}
</script>

<style>
body {
  background: #fff;
}
.active { 
font-weight:bold;
}
</style>
