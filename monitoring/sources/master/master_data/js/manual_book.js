function getSubModul(getPar){
    kategoriModul = document.getElementById('inp[kategoriModul]');
    kodeModul = document.getElementById('inp[kodeModul]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=kodeModul.options.length-1; i>=0; i--){
                kodeModul.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");                     
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "Sub Kategori";
                kodeModul.options.add(opt);
                for(var i=0; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];
                    if(opt.value) kodeModul.options.add(opt);
                    jQuery("#inp\\[kodeModul\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id&par[kategoriModul]="+ kategoriModul.value + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getKodeSite(getPar){
    kodeModul = document.getElementById('inp[kodeModul]');
    kodeSite = document.getElementById('par[kodeSite]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=kodeSite.options.length-1; i>=0; i--){
                kodeSite.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");                     
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "Menu";
                kodeSite.options.add(opt);
                for(var i=0; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];
                    if(opt.value) kodeSite.options.add(opt);
                    jQuery("#par\\[kodeSite\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id2&par[kodeModul]="+ kodeModul.value + getPar, true);
    xmlHttp.send(null);
    return false;
}