
var downloadButton = document.getElementById('btnDescargarPdf');
downloadButton.addEventListener('click', function() {
    

    console.log(document.getElementsByTagName('canvas'));
    var canvasElement = document.getElementsByTagName('canvas')[2];
    var html = document.documentElement.outerHTML;
    
 


    const rows = Array.from(document.querySelectorAll('table tr'))
    .map(row => Array.from(row.querySelectorAll('td')).map(cell => cell.textContent));


    var doc = new jsPDF();
    var title = "Información Parkings MovMad";
    var descripcion = "MovMad es un servicio web donde podrás encontrar información sobre las líneas de bus"; 
    var descripcion2 = "y parkings de Madrid, sin importar si buscas aparcamiento para coche o bici." 
    var descripcion3 = "Recuerda, estás a un solo click de encontrar plaza.";

    var xPos = 15;
    var yPos = 15;

    doc.setFontSize(20);
    doc.text(xPos, yPos, title);
    yPos += 10;

    doc.setFontSize(12);
    doc.text(xPos, yPos, descripcion);
    yPos += 10;
    doc.setFontSize(12);
    doc.text(xPos, yPos, descripcion2);
    yPos += 10;
    doc.setFontSize(12);
    doc.text(xPos, yPos, descripcion3);

    yPos += 15;


    doc.addPage();

    //Añadir la tabla al documento pdf
    doc.text("Lista de parkings MovMad", doc.internal.pageSize.width / 4, 50, { align: 'center' });
    doc.autoTable({
        html: '.table',
        startY: 30,
        theme: 'grid',
        columnStyles: {
            0: {
                halign: 'right',
                tableWidth: 100,
                },
            1: {
                tableWidth: 100,
               },
            2: {
                halign: 'right',
                tableWidth: 100,
               },
            3: {
                halign: 'right',
                tableWidth: 100,
               }
        },

    });


    // Guardar el documento como un archivo PDF
    doc.save('parkings_parkmad.pdf');

});