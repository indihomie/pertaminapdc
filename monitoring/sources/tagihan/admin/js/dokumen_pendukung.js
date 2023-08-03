function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
	}
}

function unsetField(idOption)
{
    field = document.getElementById('inp['+idOption+']');
    for(var i=field.length-1; i>=1; i--){
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

function getJenis(kodeInduk, getPar)
{

    unsetOption('jenis');


	jQuery.post("ajax.php?par[mode]=getJenis&par[kodeInduk]="+kodeInduk+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        if (kodeInduk != ''){
            for(var i=0; i<data.length; i++)
            {
                jQuery("#inp\\[jenis\\]").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');
                jQuery("#inp\\[jenis\\]").trigger("chosen:updated");
            }
        }
        
    });
}

function getFilter(kodeInduk, getPar)
{
    unsetFilter('combo2');


	jQuery.post("ajax.php?par[mode]=getJenis&par[kodeInduk]="+kodeInduk+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        if (kodeInduk != ''){
            for(var i=0; i<data.length; i++)
        {
            jQuery("#combo2").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');
            jQuery("#combo2").trigger("chosen:updated");
        }
        }
    });
}

function urut(getPar){
    jenis=document.getElementById("inp[jenis]").value;
    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function(){
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){
            if(xmlHttp.responseText){
                document.getElementById("inp[urut]").value = xmlHttp.responseText;
            }
        }
    }

    xmlHttp.open("GET", "ajax.php?par[mode]=urut&inp[jenis]=" + jenis + getPar, true);
    xmlHttp.send(null);
    return false;
}

