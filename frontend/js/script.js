// Minimal script.js for SPApp navigation only
$(document).ready(function() {
    var app = $.spapp({
        defaultView: "#main",
        templateDir: "./frontend/views/"
    });


    app.run();
});
