var rows = document.getElementsByClassName("parking_tr");
var btn = document.getElementById("btnConfirmar");
var map_exists = false;
var map = null;
var k = 0;
var elegido = -1;
var parking = -1;
for(var i = 0; i < rows.length; i++) {
    rows[i].addEventListener("click", bindClick(i));
}

btn.addEventListener("click", confirmSelection());

function bindClick(i) {
    return function() {
        
        for(var k = 0; k < rows.length; k++) {
            rows[k].setAttribute('style','border-bottom: 3px solid important;');
        }

        rows[i].setAttribute('style','border: 3px solid green;');
        elegido = i;
        parking = rows[i].getAttribute("id");
    };
}


function createCirclePushPin(location, radius, fillColor, strokeColor, strokeWidth) {
    strokeWidth = strokeWidth || 0;

    var svg = ['<svg xmlns="http://www.w3.org/2000/svg" width="', (radius * 2),
        '" height="', (radius * 2), '"><circle cx="', radius, '" cy="', radius, '" r="',
        (radius - strokeWidth), '" stroke="', strokeColor, '" stroke-width="', strokeWidth, '" fill="', fillColor, '"/></svg>'];

    return new Microsoft.Maps.Pushpin(location, {
        icon: svg.join(''),
        anchor: new Microsoft.Maps.Point(radius, radius)
    });
}

function GetMap(nombre, latitude, longitude)
{


    var mapa = document.getElementById("myMap");
    if(elegido == -1){
        
        mapa.style.display = 'none';
    }
    else{
        mapa.style.display = 'block';
        if(map_exists == false){

            map = new Microsoft.Maps.Map('#myMap', {
                credentials: 'Ahuolx1vwd2oWopw2ptxY98hnY0JM8NAaTfL8hKXg2ErAJqDyivKsFYIq0yywMeU',
                center: new Microsoft.Maps.Location(latitude, longitude),
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                zoom: 14
            });

            var center = map.getCenter();
    
            //Create custom Pushpin
            var pin = new Microsoft.Maps.Pushpin(center, {
                title: nombre,
                subTitle: '',
                text: 'P'
            });
    
            //Add the pushpin to the map
            map.entities.push(pin);
            map_exists = true;    
        }
        else{
            var location = new Microsoft.Maps.Location(latitude,longitude);
    
            pin = new Microsoft.Maps.Pushpin(location, {
                title: nombre,
                subTitle: '',
                text: 'P'
            });;
            // var pin2 = createCirclePushPin(location, 5, 'rgba(0,50,255,1)');
            map.entities.push(pin);
        }
    }
}

function confirmSelection(){

    return function(){
        if(elegido != -1){
            // btn.innerHTML = elegido;
            //Obtener la direccion de elegido
            var celdas = rows[elegido].getElementsByTagName("td");
            var nombre = celdas[0].textContent;
            var direccion = celdas[1].textContent;
            var latitude = celdas[2].textContent;
            var longitude = celdas[3].textContent;
            GetMap(nombre, parseFloat(latitude),parseFloat(longitude));                        
        }
    };
}

function updateRows(){

    var rows = document.getElementsByClassName("parking_tr");
    var btn = document.getElementById("btnConfirmar");
    var map_exists = false;
    var map = null;
    var k = 0;
    var elegido = -1;
    var parking = -1;
    for(var i = 0; i < rows.length; i++) {
        rows[i].addEventListener("click", bindClick(i));
        console.log("Binded");
    }
}