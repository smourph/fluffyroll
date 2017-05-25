$(function () {
    var roulette = $('.roulette'),
        startButton = $('.btn.start'),
        btnContainer = $('.container-buttons'),
        nameDiv = $('.name'),
        infoContainer = $('.container-teddy-name');

    var option = {
        speed: 20,
        duration: 3,
        startCallback: function () {
            btnContainer.hide();
            startButton.off('click');

            if (infoContainer.is('visible')) {
                nameDiv.empty();
                infoContainer.hide();
            }
        },
        slowDownCallback: function () {
        },
        stopCallback: function (stopElement) {
            var teddyName = stopElement.data('name');
            nameDiv.html(teddyName);
            infoContainer.show();

            stopElement.off('click').click(function () {
                roulette.roulette('start');
            });
        }
    };

    startButton.click(function () {
        roulette.roulette(option);
        roulette.roulette('start');
    });
});