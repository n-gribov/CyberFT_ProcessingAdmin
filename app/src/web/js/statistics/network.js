var years = [15, 16, 17, 18];
var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function randomInt(from, to) {
    var value = from + Math.random() * (to - from);
    return Math.round(value);
}

function generateLabels() {
    var labels = [];
    for (var yearIndex = 0; yearIndex < years.length; yearIndex++) {
        for (var monthIndex = 0; monthIndex < months.length; monthIndex++) {
            labels.push(months[monthIndex] + ' ' + years[yearIndex]);
        }
    }
    return labels;
}

function drawLineChart(drawAreaSelector, datasets) {
    var config = {
        type: 'line',
        data: {
            labels: generateLabels(),
            datasets: datasets
        },
        options: {
            legend: {
                reverse: true
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                itemSort: function(a, b) {
                    return b.datasetIndex - a.datasetIndex
                },
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    }
                }]
            }
        }
    };
    Charts.draw(drawAreaSelector, config);
}

function drawDocumentsChart(drawAreaSelector) {
    var allDocuments = [];
    var failedDocuments = [];
    var currentValue = 0;
    for (var yearIndex = 0; yearIndex < years.length; yearIndex++) {
        for (var monthIndex = 0; monthIndex < months.length; monthIndex++) {
            if (currentValue < 1000) {
                currentValue += randomInt(0, 200);
            } else {
                currentValue += randomInt(-100, 115);
            }
            allDocuments.push(currentValue);
            failedDocuments.push(randomInt(0, currentValue / 30));
        }
    }

    var datasets = [
        {
            label: 'Failed',
            backgroundColor: ChartsColors.makeTransparent('red', 0.5),
            borderColor: ChartsColors.red,
            data: failedDocuments,
        },
        {
            label: 'All',
            backgroundColor: ChartsColors.makeTransparent('green', 0.5),
            borderColor: ChartsColors.green,
            data: allDocuments,
            fill: '-1'
        }
    ];

    drawLineChart(drawAreaSelector, datasets);
}

function drawNodesChart(drawAreaSelector) {
    var all = [];
    var deleted = [];
    var inactive = [];
    var allCount = 0;
    var deletedCount = 0;
    var blockedCount = 0;
    for (var yearIndex = 0; yearIndex < years.length; yearIndex++) {
        for (var monthIndex = 0; monthIndex < months.length; monthIndex++) {
            if (allCount < 50) {
                allCount += randomInt(0, 11);
            } else {
                allCount += randomInt(0, 5);
            }
            all.push(allCount);
            deletedCount += randomInt(randomInt(0, 1), allCount / 300);
            blockedCount += randomInt(randomInt(0, 1), allCount / 300);
            inactive.push(deletedCount + blockedCount);
            deleted.push(deletedCount);
        }
    }

    var datasets = [
        {
            label: 'Deleted',
            backgroundColor: ChartsColors.makeTransparent('darkGrey', 0.7),
            borderColor: ChartsColors.darkGrey,
            data: deleted,
        },
        {
            label: 'Inactive',
            backgroundColor: ChartsColors.makeTransparent('red', 0.5),
            borderColor: ChartsColors.red,
            data: inactive,
            fill: '-1'
        },
        {
            label: 'All',
            backgroundColor: ChartsColors.makeTransparent('green', 0.5),
            borderColor: ChartsColors.green,
            data: all,
            fill: '-1'
        }
    ];

    drawLineChart(drawAreaSelector, datasets);
}

window.onload = function() {
    drawDocumentsChart('#documents-stats');
    drawNodesChart('#participants-stats');
    drawNodesChart('#terminals-stats');
};