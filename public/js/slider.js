
var slider = document.getElementById('slider');
var slider2 = document.getElementById('slider2');

noUiSlider.create(slider, {
    start: [0, 100],
    connect: true,
    step:1,
    range: {
        'min': 0,
        'max': 100
    }
});
noUiSlider.create(slider2, {
    start: [2016, 2020],
    connect: true,
    step:1,
    range: {
        'min': 2016,
        'max': 2020
    }
});

var boxes = [document.getElementById('Point1'),document.getElementById('Point2')];
slider.noUiSlider.on('update',function(values, handle){
    boxes[handle].value = Math.round(values[handle]);
});
var boxes2 = [document.getElementById('Year1'),document.getElementById('Year2')];
slider2.noUiSlider.on('update',function(values, handle){
    boxes2[handle].value = Math.round(values[handle]);
});