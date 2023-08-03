function getNilai(besaran) {
    total = jQuery("#total").val();

    besaran = parseInt(besaran);
    total = parseInt(total);

    persen = besaran / 100;
    senilai = total * persen;

    jQuery("#inp\\[nilai\\]").val(formatNumber(senilai));
}

function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
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

function getProyek(kodeData, getPar)
{

    unsetOption('id_proyek');

	jQuery.post("ajax.php?par[mode]=getProyek&par[kodeData]="+kodeData+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_proyek\\]").append('<option value="'+data[i].id+'">'+data[i].proyek+'</option>');
            jQuery("#inp\\[id_proyek\\]").trigger("chosen:updated");
        }
    });
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