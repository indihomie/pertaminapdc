function setTahapan(val)
{
    if (val == 'TR') {

        jQuery("#nilai_tahapan_termin").show();
        jQuery("#nilai_tahapan_fullpayment").hide();
        jQuery("#nilai_tahapan_bertahap").hide();
    }

    if (val == 'FP') {

        jQuery("#nilai_tahapan_termin").hide();
        jQuery("#nilai_tahapan_fullpayment").show();
        jQuery("#nilai_tahapan_bertahap").hide();
    }

    if (val == 'BT') {

        jQuery("#nilai_tahapan_termin").hide();
        jQuery("#nilai_tahapan_fullpayment").hide();
        jQuery("#nilai_tahapan_bertahap").show();
    }
}

function checkUangMuka()
{
    if (jQuery("#inp\\[uang_muka\\]").is(':checked')) {

        jQuery("#inp\\[nilai_uang_muka\\]").val(10);

    } else {

        jQuery("#inp\\[nilai_uang_muka\\]").val(0);

    }
}

function checkRetensi()
{
    if (jQuery("#inp\\[retensi\\]").is(':checked')) {

        jQuery("#inp\\[nilai_retensi\\]").val(5);

    } else {

        jQuery("#inp\\[nilai_retensi\\]").val(0);

    }
}

function getTotal()
{
    var nilai = jQuery("#inp\\[nilai\\]").val();
    var ppn = jQuery("#inp\\[ppn\\]").val();
    /* var pph = jQuery("#inp\\[pph\\]").val();
    var diskon = jQuery("#inp\\[diskon\\]").val(); */


    var nilai = convert(nilai);
    var ppn = convert(ppn);
   /* var pph = convert(pph);
    var diskon = convert(diskon); */

    var nilai = parseInt(nilai);
    var ppn = parseInt(ppn);
    /* var pph = parseInt(pph);
    var diskon = parseInt(diskon); */

    var nilaiPPN = nilai * (ppn / 100);
    var total    = nilai + nilaiPPN;

    var total = formatNumber(total);

    jQuery("#inp\\[total\\]").val(total);
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

function getProyek(id_cc, getPar)
{

    unsetOption('id_proyek');

	jQuery.post("ajax.php?par[mode]=getProyek&par[id_cc]="+id_cc+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_proyek\\]").append('<option value="'+data[i].id+'">'+data[i].nomor+' - '+data[i].proyek+'</option>');
            jQuery("#inp\\[id_proyek\\]").trigger("chosen:updated");
        }
    });
}

function getPemohon(id_cc, getPar)
{

    unsetOption('id_supplier');

    jQuery.post("ajax.php?par[mode]=getPemohon&par[id_cc]="+id_cc+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[id_supplier\\]").append('<option value="'+data[i].id+'">'+data[i].nama+'</option>');
            jQuery("#inp\\[id_supplier\\]").trigger("chosen:updated");
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