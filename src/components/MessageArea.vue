<template>
  <ul v-if="action">
      <li v-if="concurrentConnections">
        {{ concurrentConnections }} concurrent connections
      </li>
      <li>
        <span style="padding-right:5px;">
          <strong>{{ now.toLocaleTimeString() }}</strong>
        </span>
        {{ action }}
        <span v-if="message">
          {{ message }}
        </span>
      </li>
  </ul>  
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "MessageArea",
  props: {
    value: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    action() {
      return this.value?.action;
    },
    message() {
      if (['code'].includes(this.value?.action)) {
        return this.value?.message;
      }
      return null;
    },
    data() {
      return this.value?.data;
    },
    concurrentConnections() {
      return this.value?.concurrent_connections;
    },
    client() {
      return this.value?.from;
    },
    ip() {
      return this.value?.from_ip;
    },
    now() {
      this.value?.action;
      this.value?.message;
      return new Date;
    }
  }
});

</script>
