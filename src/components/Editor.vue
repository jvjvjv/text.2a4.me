<template>
  <prism-editor
    ref="editor"
    v-model="code"
    :highlight="highlighter"
    line-numbers
    class="editor"
    @click="doFocusEditorTextArea"
    @keydown="doCopyPasteValidation"
  />
  <message-area
    :value="remoteContent"
    class="messaging"
  />
</template>

<script>
import { defineComponent } from "vue";
import { PrismEditor } from "vue-prism-editor";
import "vue-prism-editor/dist/prismeditor.min.css";
import MessageArea from "./MessageArea.vue";

// import highlighting library (you can use any library you want just return html string)
import { highlight, languages } from "prismjs/components/prism-core";
import "prismjs/components/prism-clike";
import "prismjs/components/prism-javascript";
import "prismjs/themes/prism-tomorrow.css"; // import syntax highlighting styles

export default defineComponent({
  components: {
    PrismEditor,
    MessageArea,
  },
  data: () => ({
    code: "",
    lastCursorPosition: 0,
    timeoutTyping: null,
    hostname: window.location.hostname,
    port: process.env.VUE_APP_WEBSOCKET_PORT,
    ws: null,
    remoteContent: null,
    isTyping: false,
    dataHasComeWhileYouTyping: false,
  }),
  watch: {
    code: "doSyncCode",
  },
  created() {
    this.initializeWebsocket();
  },
  methods: {
    initializeWebsocket() {
      const wsProtocol = window.location.protocol.startsWith("https")
        ? "wss"
        : "ws";
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

    doReceiveSync({ data }) {
      try {
        data = JSON.parse(data);
        if (this.isTyping) {
          this.dataHasComeWhileYouTyping = true;
          return;
        }

        switch (data.action) {
          case "code":
            this.code = data.message;
            break;
          case "paste":
          case "copy":
          case "cut":
          case "select-all":
            console.log(`${new Date().toLocaleString()}: ${data.action} from ${data.from} (${data.from_ip})`);
            break;
        }
        this.remoteContent = data;
      } catch (error) {
        return;
      }
    },

    doFocusEditorTextArea() {
      this.$refs.editor.$el.querySelector('textarea').focus();
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
        const data = {
          action: "code",
          message: this.code,
        };
        this.ws.send(JSON.stringify(data));
        this.remoteContent = data;
      }, 400);
    },

    doCopyPasteValidation(e) {
      const { key, ctrlKey, metaKey } = e;
      let action;
      let message;
      e.stopPropagation();

      if (ctrlKey || metaKey) {
        switch (key) {
          case "a":
            action = "select-all";
            message = this.code;
            break;
          case "v":
            action = "paste";
            message = this.code;
            break;
          case "x":
            action = "cut";
            message = this.code;
            break;
          case "c":
            action = "copy";
            message = this.code;
            break;
        }
        if (action && message) {
          const data = {
            action,
            message,
          };
          this.ws.send(
            JSON.stringify(data)
          );
          this.remoteContent = data;
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

  padding-bottom: 0em;
}
.messaging {
  height: 4em;
  padding: 5px;
  background-color: rgb(0,0,0);
  color: rgb(240,240,240)
}
</style>
