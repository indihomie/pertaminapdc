function showAppr(){

    if (jQuery('#verif_diterima').is(':checked')){

        jQuery('#appr_ulang').hide();

    } else {

        jQuery('#appr_ulang').show();

    }

}