<template>
  <editor />
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component';
import Editor from './components/Editor.vue';

@Options({
  components: {
    Editor,
  },
})
export default class App extends Vue {}
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  // text-align: center;
  // color: #2c3e50;
}

html {
    height: 100%;
}
* {
    margin: 0;
    padding: 0;
}
body {
    font-family: "Georgia", "Arial", "Serif";
    width: 100%;
    position: relative;
    min-height: 100%;
    box-sizing: border-box;
    height: 100%;
    background-color: #222;
    color: #ccc;
}
a, a:hover {
    color: inherit;
    text-decoration: none;
}
a.button {
    padding: 0.5em 1em;
    background-color: #333;
    margin-left: 2em;
}
h2.title {
    display: block;
    width: 100%;
    padding: 0.5em 0.5em 0.5em 1em;
    background-color: #333;
    font-size: 1.5em;
    font-weight: normal;
    box-sizing: border-box;
}
</style>
