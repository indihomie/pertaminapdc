function getKota(getPar){
    /*alert(getPar);*/
    provinsi = document.getElementById('inp[provinsi]');
    kota = document.getElementById('inp[kota]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=kota.options.length-1; i>=0; i--){
                kota.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");                     
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "Pilih Kota";
                kota.options.add(opt);
                for(var i=1; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];
                    if(opt.value) kota.options.add(opt);
                      jQuery("#inp\\[kota\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=kota&par[provinsi]="+ provinsi.value + getPar, true);
    xmlHttp.send(null);
    return false;
}