 function objetus() {
 try {
                objetus = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
                try {
                        objetus= new ActiveXObject("Microsoft.XMLHTTP");
                } catch (E) {
                        objetus= false;
                }
        }
        if (!objetus && typeof XMLHttpRequest!='undefined') {
                objetus = new XMLHttpRequest();
        }
        return objetus
}
 var http = objetus(); // We create the HTTP Object
 