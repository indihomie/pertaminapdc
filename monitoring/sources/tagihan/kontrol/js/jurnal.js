function getNilai() {

    var ppnSPK = jQuery("#ppnSPK").val();

    var nilai = jQuery("#inp\\[nilai\\]").val();
    var nilai = convert(nilai);

    var sisa = jQuery("#sisaAwal").val();
    var sisa = convert(sisa);

    if (jQuery("#ppn_yes").is(":checked"))
    {
        var nilaiPPN = nilai * (ppnSPK / 100);

        var total = (nilai * 1) + (nilaiPPN * 1);

        var sisa = sisa - total;

        var sisa = formatNumber(sisa);
        var nilaiPPN = formatNumber(nilaiPPN);
        var total = formatNumber(total);

        jQuery("#sisa").val(sisa);
        jQuery("#inp\\[nilai_ppn\\]").val(nilaiPPN);
        jQuery("#inp\\[nilai_plus_ppn\\]").val(total);
    }

    if (jQuery("#ppn_no").is(":checked"))
    {
        var sisa = sisa - nilai;

        var sisa = formatNumber(sisa);
        var total = formatNumber(nilai);

        jQuery("#sisa").val(sisa);
        jQuery("#inp\\[nilai_ppn\\]").val('0');
        jQuery("#inp\\[nilai_plus_ppn\\]").val(total);
    }

}

/*
function getNilai(besaran) {
    total = jQuery("#total").val();

    besaran = parseInt(besaran);
    total = parseInt(total);

    persen = besaran / 100;
    senilai = total * persen;

    jQuery("#inp\\[nilai\\]").val(formatNumber(senilai));
}
*/

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

function getKodeCompany(idCompany, getPar){

    jQuery.post("ajax.php?par[mode]=getKodeCompany&par[idCompany]="+idCompany+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        jQuery("#inp\\[pengajuan_customer_code\\]").val(data[0].nomorSupplier);
    });

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