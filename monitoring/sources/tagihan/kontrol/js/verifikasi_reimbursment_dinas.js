jQuery(document).ready(function()
{
    jQuery('#chkAll').click(function (e)
    {
        if (jQuery(this).is(':checked'))
        {
            jQuery('#uniform-chkOne span').addClass('checked');
        }
        else
        {
            jQuery('#uniform-chkOne span').removeClass('checked');

        }

        jQuery('#detailDokumen tbody input[type=checkbox]').prop("checked",this.checked);
    });
});

function showAppr(){

    if (jQuery('#verif_diterima').is(':checked')){

        jQuery('#appr_ulang').hide();

    } else {

        jQuery('#appr_ulang').show();

    }

}

function unsetFilter(idOption)
{
    field = document.getElementById(idOption);
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#"+idOption).trigger("chosen:updated");
	}
}

function getFilter(kodeData, getPar)
{
    unsetFilter('combo6');


	jQuery.post("ajax.php?par[mode]=getFilter&par[kodeData]="+kodeData+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        if (kodeData != ''){
            for(var i=0; i<data.length; i++)
        {
            jQuery("#combo6").append('<option value="'+data[i].id+'">'+data[i].proyek+'</option>');
            jQuery("#combo6").trigger("chosen:updated");
        }
        }
    });
}