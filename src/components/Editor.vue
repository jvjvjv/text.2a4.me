<template>
  <prism-editor
    v-model="code"
    :highlight="highlighter"
    line-numbers
    class="editor"
  />
</template>

<script>
import { defineComponent } from "vue";
import { PrismEditor } from "vue-prism-editor";
import "vue-prism-editor/dist/prismeditor.min.css";

// import highlighting library (you can use any library you want just return html string)
import { highlight, languages } from "prismjs/components/prism-core";
import "prismjs/components/prism-clike";
import "prismjs/components/prism-javascript";
import "prismjs/themes/prism-tomorrow.css"; // import syntax highlighting styles

export default defineComponent({
  components: {
    PrismEditor,
  },
  data: () => ({
    code: "",
    lastCursorPosition: 0,
    timeoutTyping: null,
    hostname: window.location.hostname,
    port: 8080,
    ws: null,
    remoteContent: "",
    isTyping: false,
    dataHasComeWhileYouTyping: false,
  }),
  watch: {
    code: "doSyncCode",
  },
  created() {
    this.initializeWebsocket();
  },
  mounted() {
    this.initializeKeyListeners();
  },
  beforeUnmount() {
    this.removeKeyListeners();
  },
  methods: {
    initializeKeyListeners() {
      this.$el.addEventListener("keydown", this.doCopyPasteValidation);
    },

    removeKeyListeners() {
      this.$el.removeEventListener("keydown", this.doCopyPasteValidation);
    },

    initializeWebsocket() {
      const wsProtocol = window.location.protocol.startsWith('https') ? 'wss' : 'ws';
      this.ws = new WebSocket(`${wsProtocol}://${this.hostname}:${this.port}`);
      this.ws.onerror = () =>
        alert(
          `Websocket is currently unavailable. Work will not be synchronized.`
        );
      this.ws.onmessage = this.doReceiveSync;
    },

    highlighter(code) {
      return highlight(code, languages.js); // languages.<insert language> to return html with markup
    },

    doReceiveSync(event) {
      const data = JSON.parse(event.data);
      if (this.isTyping) {
        this.dataHasComeWhileYouTyping = true;
        // this.remoteContent = e.data;
        return;
      }

      switch (data.action) {
        case 'code':
          this.code = data.message;
          break;
        case 'action':
          console.log(`${data.message}, ${new Date().toISOString()}`);
          break;
      }
      this.remoteContent = data;
    },

    doSyncCode() {
      this.isTyping = true;
      clearTimeout(this.timeoutTyping);

      this.timeoutTyping = setTimeout(() => {
        // lastCursorPosition = this.code;
        this.isTyping = false;

        if (this.dataHasComeWhileYouTyping) {
          // this.code = this.remoteContent;
          this.dataHasComeWhileYouTyping = false;
        }
        this.ws.send(
          JSON.stringify({
            action: "code",
            message: this.code,
          })
        );
      }, 128);
    },

    doCopyPasteValidation(e) {
      const { key, ctrlKey, metaKey } = e;
      let action;
      let message;

      if (ctrlKey || metaKey) {
        switch (key) {
          case "v":
            action = "action";
            message = "paste";
            break;
          case "x":
            action = "action";
            message = "cut";
            break;
          case "c":
            action = "action";
            message = "copy";
            break;
        }
        if (action && message) {
          this.ws.send(
            JSON.stringify({
              action,
              message,
            })
          );
        }
      }
    },
  },
});
</script>

<style scoped lang="scss">
.editor {
  height: calc(100vh - 4em);
  width: 100vw;
  position: relative;
  font-family: monospace;
  font-size: 14pt;

  padding-bottom: 4em;
}
</style>
