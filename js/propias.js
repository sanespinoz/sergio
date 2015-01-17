// // Titulo: Titulo de la ventana
// Url: pagina con parametro 
// alto, ancho: alto y ancho de la ventana.
function abrir_ventana(titulo,url,alto,ancho) {
	var pos_x = (screen.height - alto) / 2;
	var pos_y = (screen.width - ancho) / 2;
        return window.open(url, titulo, "toolbar = 0, status = 0, menubar = 0, resizable = 0, titlebar = 0, hotkeys = 0, height = "+alto+", width = "+ancho+", top = "+pos_y+", left ="+pos_x);
};

// Cierra las ventanas 
function cerrar_ventana(puntero){ // cerrar_ventana
//    if (!puntero){
        puntero.close();
//    }else{
//        window.opener.puntero.close();
    //}
};	



// Muestra un mensaje en javascript
function mensaje(titulo) {
	window.alert(titulo);
};


