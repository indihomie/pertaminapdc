function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
	}
}

function getSPK(idSupplier, getPar)
{
    unsetOption('id_spk');
    unsetOption('id_termin');

	jQuery.post( "ajax.php?par[mode]=getSPK&par[id_supplier]="+idSupplier+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_spk\\]").append('<option value="'+data[i].id+'">'+data[i].nomor+'</option>');
            jQuery("#inp\\[id_spk\\]").trigger("chosen:updated");
        }
    });
}

function getTermin(idSPK, getPar)
{
    unsetOption('id_termin');

	jQuery.post( "ajax.php?par[mode]=getTermin&par[id_spk]="+idSPK+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_termin\\]").append('<option value="'+data[i].id+'">'+data[i].termin+'</option>');
            jQuery("#inp\\[id_termin\\]").trigger("chosen:updated");
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