function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
    for(var i=field.options.length-1; i>=1; i--){
        field.remove(i);
        jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
    }
}

function getPegawai(id_sbu, getPar)
{

    unsetOption('id_pegawai');

	jQuery.post("ajax.php?par[mode]=getPegawai&par[id_sbu]="+id_sbu+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_pegawai\\]").append('<option value="'+data[i].id+'">'+data[i].nama+'</option>');
            jQuery("#inp\\[id_pegawai\\]").trigger("chosen:updated");
        }
    });
}