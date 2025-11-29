import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

window.Echo.channel("project." + projectId).listen(".batch.finished", (e) => {
    console.log("Batch selesai!", e.taskLogId);
    // bisa tutup modal / reload halaman / sweet-alert
});
