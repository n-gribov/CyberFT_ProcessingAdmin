var Charts = {
    draw: function (drawAreaSelector, config) {
        var canvas = document.createElement('canvas');
        var drawArea = document.querySelector(drawAreaSelector);
        drawArea.appendChild(canvas);
        var context = canvas.getContext('2d');
        new Chart(context, config);
    }
};
