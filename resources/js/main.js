let js_modal_flash_create = function () {
    let html = '<div class="modal"  id="js_modal_flash_message">\
            <input id="inp_mod_flash" type = "checkbox" />\
                <label for="inp_mod_flash" class="overlay "></label>\
                <article class="">\
                    <header class="bgcolor">\
                        <p>&nbsp;</p>\
                        <label id="close_for_js_mod_h" for="inp_mod_flash" class="close">&times;</label>\
                    </header>\
                    <section class="content bgcontent flash_child">\
                        <p id="js_flash_message_p" class="py1 bg-black red"></p>\
                    </section>\
                    <footer class="bgcontent" >\
                        <label id="close_for_js_mod_f" for="inp_mod_flash" class="button">Закрыть</label>\
                    </footer >\
                </article></div>';

    document.body.insertAdjacentHTML("beforeend", html);
};

window.Main = {
    phone_elem: document.getElementById("user_phone_input"),
    jsModaFlash: function (messages, { mod, mod_input, message_p } = {}) {
        if (mod && mod_input && message_p) {
            mod_input.checked = true;
            message_p.innerHTML = messages;
        } else {
            console.warn('Js modal flash element not isset');
        }
    },
    // other functions...
}

document.addEventListener('DOMContentLoaded', function () {
    js_modal_flash_create();
    window.Main.elements = {
        mod: document.getElementById("js_modal_flash_message"),
        mod_input: document.getElementById("inp_mod_flash"),
        message_p: document.getElementById("js_flash_message_p")
    }
});
/* <!-- js for esc on modal (in Home part of site that based on PicnicCSS) --> */
document.onkeydown = function (event) {
    if (event.key == "Escape") {
        var mods = document.querySelectorAll('.modal > [type=checkbox]');
        [].forEach.call(mods, function (mod) { mod.checked = false; });
    }
}