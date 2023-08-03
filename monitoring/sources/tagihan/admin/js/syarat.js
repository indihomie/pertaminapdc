function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
	}
}

function getJenis(kategori, getPar)
{
    unsetOption('jenis');

	jQuery.post("ajax.php?par[mode]=getJenis&par[kategori]="+ kategori + getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[jenis\\]").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');
            jQuery("#inp\\[jenis\\]").trigger("chosen:updated");
        }
    });
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

function checkMandatory()
{
    if (jQuery("#inp\\[mandatory\\]").is(':checked')) {

        jQuery("#inp\\mandatory\\]").val(1);

    } else {

        jQuery("#inp\\[mandatory\\]").val(0);

    }
}