var ChartsColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)',
    darkGrey: 'rgb(101, 103, 107)',

    colorsNames: [
        'red',
        'orange',
        'yellow',
        'green',
        'blue',
        'purple',
        'grey'
    ],

    getList: function (size) {
        var colors = [];
        for (var i = 0; i < size; i++) {
            var colorName = this.colorsNames[i % this.colorsNames.length];
            colors.push(this[colorName]);
        }
        return colors;
    },

    makeTransparent: function (colorName, alpha) {
        var color = this[colorName];
        return color
            .replace('rgb(', 'rgba(')
            .replace(')', ', ' + alpha + ')');
    }
};
