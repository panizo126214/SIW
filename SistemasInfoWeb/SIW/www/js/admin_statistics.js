var data = [{
    values: [numNormalUsers, numAdminusers],
    labels: ['Admins', 'Normal'],
    type: 'pie'
}];

var layout = {
    height: 400,
    width: 500
};

Plotly.newPlot('divNumUsers', data, layout);


var data = [{
    x: $users,
    y: $views,
    type: 'bar'
}];
        
Plotly.newPlot('divNumViews', data);
