function drawTopParticipantsChart(drawAreaSelector, stats) {
    var data = stats.map(function (item) {
        return item.count;
    });
    var colors = ChartsColors.getList(stats.length);
    var labels = stats.map(function (item) {
        return item.swiftCode;
    });
    var config = {
        type: 'horizontalBar',
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: colors
                }
            ]
        },
        options: {
            legend: {
                display: false
            }
        }
    };
    Charts.draw(drawAreaSelector, config);
}

window.onload = function() {
    drawTopParticipantsChart('#senders-stats', gon.topSenders);
    drawTopParticipantsChart('#receivers-stats', gon.topReceivers);
};
