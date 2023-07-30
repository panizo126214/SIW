var rows = document.getElementsByClassName("user_tr");
var btn = document.getElementById("btnEliminar");

var k = 0;
var elegido = -1;

for(var i = 0; i < rows.length; i++) {
    rows[i].addEventListener("click", bindClick(i));
}


function bindClick(i) {
    return function() {
        
        for(var k = 0; k < rows.length; k++) {
            rows[k].setAttribute('style','border-top: 0px solid important;');
            rows[k].setAttribute('style','border-left: 0px solid important;');
            rows[k].setAttribute('style','border-right: 0px solid important;');
            rows[k].setAttribute('style','border-bottom: 3px solid important;');
        }

        rows[i].setAttribute('style','border: 3px solid green;');
        
        elegido = i;


        console.log(rows[elegido]);
    };
}


// btn.addEventListener("click", deleteSelection());