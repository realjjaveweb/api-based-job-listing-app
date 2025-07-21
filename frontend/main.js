import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import JobList from "./components/JobList.vue";
import JobDetail from "./components/JobDetail.vue";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import "@mdi/font/css/materialdesignicons.css";

const vuetify = createVuetify({
  components,
  directives,
});

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", component: JobList },
    { path: "/job/:id", component: JobDetail },
  ],
});

const app = createApp(App);
app.use(router);
app.use(vuetify);
app.mount("#app");
