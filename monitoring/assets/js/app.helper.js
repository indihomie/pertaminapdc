function reloadPage() {
    parent.window.location.reload()
}

function toggleLoader(status = null) {

    if (status == null) {
        jQuery('#loader').toggle()
        return
    }

    if (status) {
        jQuery('#loader').show()
        return
    }

    jQuery('#loader').hide()
}

function toggleHTML(hide, show) {
    $(hide).hide()
    $(show).show()
}

function toggleCheck(status, target) {
    $(target).prop('checked', status)
}

function openBox(url, w, h, isCancellable = true) {

    let option

    if (isCancellable) {
        option = {iframe: url, width: w, height: h, close: true, animate: false, opacity: 50}
    } else {
        option = {iframe: url, width: w, height: h, close: false, animate: false, opacity: 50, isCancellable: false}
    }

    tiny.box.show(option);
}

function closeBox() {
    parent.tiny.box.hide();
}

function taskBackground(parameter, loader = true) {

    if (loader) {
        toggleLoader(true)
    }

    jQuery("body").append(`<iframe src='void.php?${parameter}' class='hidden'></iframe>`)
}

function getSub(parentId, childId, getPar, mode = "subData") {
    /* CREATED BY APP */
    kodeInduk = document.getElementById(parentId).value;

    elements = document.getElementById(childId);
    for (var i = elements.options.length - 1; i >= 1; i--) {
        elements.remove(i);
    }
    if (kodeInduk != '') {
        jQuery.get("ajax.php?par[mode]=" + mode + "&par[parentId]=" + kodeInduk + getPar + "").done(function (result) {
            data = $.parseJSON(result);
            for (var i = 0; i < data.length; i++) {
                jQuery("#" + childId).append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
            }
        });
    }

    console.log("ajax.php?par[mode]=" + mode + "&par[parentId]=" + kodeInduk + getPar);
}

jQuery(document).ready(function () {

    refreshFsLightbox()

    jQuery(".file-change").change(function () {

        let container_upload = jQuery(`#${this.getAttribute('for')}-uploader`)

        let field_input = jQuery(`#${this.id}`)

        let button_delete = jQuery(`#${this.getAttribute('for')}-delete`)
        let button_viewer = jQuery(`#${this.getAttribute('for')}-viewer`)
        let button_download = jQuery(`#${this.getAttribute('for')}-download`)

        button_delete.toggle(field_input.val() !== "")
        button_viewer.hide()
        button_download.hide()
    })

    jQuery(".file-delete").click(function () {

        let container_upload = jQuery(`#${this.getAttribute('for')}-uploader`)

        let field_input = jQuery(`#${this.getAttribute('for')}-input`)
        let field_status = jQuery(`#${this.getAttribute('for')}-status`)

        let button_delete = jQuery(`#${this.id}`)
        let button_viewer = jQuery(`#${this.getAttribute('for')}-viewer`)
        let button_download = jQuery(`#${this.getAttribute('for')}-download`)

        container_upload.show()
        field_input.val('').uniform()
        field_status.val('1')
        button_delete.hide()
        button_viewer.hide()
        button_download.hide()

    })

    jQuery(".chosen-container").each(function () {

        let id_chosen = jQuery(this).attr("id").replace("_chosen", "")

        let field_chosen = jQuery(`#${id_chosen}`)
        let field_container = jQuery(this)

        let class_available = field_chosen.attr("class")

        if (class_available !== undefined) {
            field_container.addClass(class_available.replace("chosen-select", ""))
        }

    })

    jQuery(".mask-email").each(function () {
        Inputmask(
            'text',
            {
                mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
                greedy: false,
                onBeforePaste: function (pastedValue, opts) {
                    pastedValue = pastedValue.toLowerCase();
                    return pastedValue.replace("mailto:", "");
                }, definitions: {
                    '*': {
                        validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                        casing: "lower"
                    }
                }
            }
        ).mask(jQuery(this))
    });

    jQuery(".mask-npwp").each(function () {
        Inputmask(
            'text',
            {
                mask: '99.999.999.9-999.999'
            }
        ).mask(jQuery(this))
    });

})
