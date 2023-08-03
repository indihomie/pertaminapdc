function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
	}
}

function getAtasan(idPegawai = 0, idUnit, getPar)
{
    jQuery("#inp\\[atasan\\]").empty().trigger("chosen:updated");
    jQuery("#inp\\[admin\\]").empty().trigger("chosen:updated");

    // unsetOption('atasan');
    // unsetOption('admin');

	jQuery.post("ajax.php?par[mode]=getAtasan&par[idPegawai]="+idPegawai+"&par[unit]="+idUnit+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[atasan\\]").append('<option value="'+data[i].id+'">'+data[i].nama+'</option>');
            jQuery("#inp\\[atasan\\]").trigger("chosen:updated");
        }

        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[admin\\]").append('<option value="'+data[i].id+'">'+data[i].nama+'</option>');
            jQuery("#inp\\[admin\\]").trigger("chosen:updated");
        }
    });
}

