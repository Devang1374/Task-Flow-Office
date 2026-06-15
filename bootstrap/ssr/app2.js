import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.Pusher = Pusher;
window.Echo = new Echo({
  broadcaster: "pusher",
  key: "84b2883da74d45c5a2f7",
  cluster: "mt1",
  forceTLS: true
});
