$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    Locate();
    Subjects();
    Requirements();
    Competence();
    $('#repType').on('change', function(e){
        e.preventDefault();
        if ($(this).val() == 'point' || $(this).val() == 'p1' || $(this).val() == 'p2' || $(this).val() == 'p3'){
            $('#slide').show();
        }
        else{
            $('#slide').hide();
        }
    });
    $('#dg1').on('click', function(){
        $(this).css("border-color","green");
        $(this).css("border-style","solid");
        $('#dg2').css("border-color","none");
        $('#dg2').css("border-style","none");
        $('#resultHeader').text('Диаграмма');
    });
    $('#dg2').on('click', function(){
        $(this).css("border-color","green");
        $(this).css("border-style","solid");
        $('#dg1').css("border-color","none");
        $('#dg1').css("border-style","none");
        $('#resultHeader').text('Карта');
    });
    $('.bb').on('click', function(e){
        e.preventDefault();
        switch(e.target.innerHTML){
            case 'Среднее значение':
                if ($('#InVal').val() == 0){
                    $('#InVal').val(-1); 
                    $('#repAvg').css("background-color","transparent");
                    $('#repMin').css("background-color","transparent");
                    $('#repMax').css("background-color","transparent");
                }
                else{
                    $('#InVal').val(0); 
                    $('#repAvg').css("background-color","rgb(109, 253, 109)");
                    $('#repMin').css("background-color","transparent");
                    $('#repMax').css("background-color","transparent");
                }
                break;
            case 'Минимальное значение':
                if ($('#InVal').val() == 1){
                    $('#InVal').val(-1); 
                    $('#repAvg').css("background-color","transparent");
                    $('#repMin').css("background-color","transparent");
                    $('#repMax').css("background-color","transparent");
                }
                else{
                    $('#InVal').val(1); 
                    $('#repAvg').css("background-color","transparent");
                    $('#repMin').css("background-color","rgb(109, 253, 109)");
                    $('#repMax').css("background-color","transparent");
                }
                break;
            case 'Максимальное значение':
                if ($('#InVal').val() == 2){
                    $('#InVal').val(-1); 
                    $('#repAvg').css("background-color","transparent");
                    $('#repMin').css("background-color","transparent");
                    $('#repMax').css("background-color","transparent");
                }
                else{
                    $('#InVal').val(2); 
                    $('#repAvg').css("background-color","transparent");
                    $('#repMin').css("background-color","transparent");
                    $('#repMax').css("background-color","rgb(109, 253, 109)");
                }
                break;
        }
        
    });
    // Загрузка API визуализации диаграмм
    google.charts.load('current', {'packages':['corechart']});
    // Вызов функции отрисовки диаграмм
    google.charts.setOnLoadCallback(load_draw_chart);
});
function PrettyArr(array){
    let result = [];
    for(let i = 0; i < array.length; i++){
        let tmp = [];
        tmp.push(array[i][1]['Establishment']);
        tmp.push(parseFloat(array[i][1]['Points']));
        tmp.push(array[i][1]['Year']);
        tmp.push(array[i][1]['LawAddress']);
        result.push(tmp);
    }
    return result;
}
// группировка массива по полю
function GroupByField(array,fieldNum){
    let result = [];
    let flags = [];
    for(let i = 0; i <array.length;i++){
        flags.push(true);
    }
    for(let i = 0; i <array.length;i++){
        let tmp = [];
        if(flags[i]){
            tmp.push(array[i]);
            flags[i] = false;
            for(let j = i; j<array.length;j++){
                if(array[i][fieldNum] == array[j][fieldNum] && flags[j]){
                    tmp.push(array[j]);
                    flags[j] = false;
                } 
            }
            result.push(tmp);
        }
    }
    for (let i = 0; i < result.length; i++){
        for (let j = 1; j < result[i].length; j++){
            const current = result[i][j];
            let j1 = j;
            while(j1 > 0 && result[i][j1 - 1][2] > current[2]){
                result[i][j1] = result[i][j1 - 1];
                j1--;
            }
            result[i][j1] = current;
        }
    }
    return result;
}
// Функция отправки AJAX-запроса и обработки полученных данных
function load_draw_chart(){
    $("#filter").on('submit', function(e){
        e.preventDefault();
        $(this).data('filter', JSON.stringify($(this).serializeArray()));
        var data = $(this).data('filter');
        var $form = $(this);
        var url = $form.attr('action');
        var posting = $.post(url, {'filter': data});
        var result = [];
        let years = [];
        let year1 = $('#Year1').val();
        let year2 = $('#Year2').val();
        let header = [];
        if ($('#School').val() == -1){
            header = ['School'];
        }
        for(let i = year1; i<=year2;i++){
            header.push(i.toString());
            years.push(parseInt(i));
        }
        result.push(header);
        posting.done(function(data){
            let arr = Object.entries(data);
            arr = PrettyArr(arr);
            // группировка по образовательному учреждению
            let groups = GroupByField(arr,0);
            let address = [];
            groups.forEach(function(item) {
                let tmp = [];
                tmp.push(item[0][0]);
                for(let i = 0; i < item.length;i++){
                    for(let j = 0; j < years.length;j++){
                        if(item[i] != undefined && item[i][2] == years[j]){
                            tmp.push(item[i][1]);
                            i++;
                        }
                        else{
                            tmp.push(0);
                        }
                    }
                }
                address.push(item[0][3]);
                result.push(tmp);
            });
            // Функция отрисовки диаграмм
            drawChart(result);
            if (year1 == year2){
                // Инициализация карты
                ymaps.ready(init);
                // Отрисовка информации на карте
                DrawMap(address, groups);
            }
        });
    });

}
// Перевод палитры HSL в RGB
function HslToRGB(value){
    let H = value, S = 1, L = 1/2;
    let C = (1 - Math.abs(2*L-1)) * S;
    let X = C * (1-Math.abs( (H/60)%2 - 1 ));
    let m = L-C/2;
    let R0,G0,B0;
    if(H >59){
        R0 = X;
        G0 = C;
        B0 = 0;
    }
    else{
        R0 = C;
        G0 = X;
        B0 = 0;
    }
    let R = (R0+m)*255;
    let G = (G0+m)*255;
    let B = (B0+m)*255;
    return "rgb("+Math.round(R)+","+Math.round(G)+","+B+")";
}
var myMap;
// Функция отрисовки Placemark и Circle на карте
function DrawMap(address, groups){
    for (let i = 0; i < address.length; i++){
        ymaps.geocode(address[i], {
            results: 1
        }).then(function(res){
            var firstGeoObject = res.geoObjects.get(0),
                coords = firstGeoObject.geometry.getCoordinates();
            var myPlacemark = new ymaps.Placemark(coords, {
                balloonContentHeader: groups[i][0][0],
                balloonContentBody: "Средний балл: "+Math.round(groups[i][0][1])  
            });
            var myCircle = new ymaps.Circle([coords, 200], {}, {
                draggable: false,
                fillColor: HslToRGB(groups[i][0][1]),
                fillOpacity: 0.8,
                strokeColor: HslToRGB(groups[i][0][1]),
                strokeOpacity: 0.8,
                strokeWidth: 1
                });
            myMap.geoObjects.add(myCircle);
            myMap.geoObjects.add(myPlacemark);
        });
    }
}
// Функция инициализации карты
function init(){
    myMap = new ymaps.Map("map", {
        center: [57.153033, 65.534328],
        zoom: 5
    });
}
// Функция отрисовки диаграмм
function drawChart(arr) {
    var data = new google.visualization.DataTable();
    var index = 0;
    var t = [];
    arr.forEach(function(item){
        if (index == 0){
            $.each(item, function(i, el){
                if (i == 0){
                    data.addColumn('string', el);
                }
                else{
                    data.addColumn('number', el);
                }
            });
            index++;
        }
        else{
            t.push(item);
        }
    });
    data.addRows(t);

    var options = {
                width:$('#chart_div').width,
                height:$('#chart_div').height
            };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
// Взаимозависимость select'ов отвечающих за расположение
function Locate(){
    $('#District').on('change', function(e){
        e.preventDefault();
        var district = $(this).val();
        var url = '/';
        var posting = $.post(url, {'district': district});
        posting.done(function(data){
            str1 = `<option value="-1">Все</option>`;
            str2 = `<option value="-1">Все</option>`;
            data.result.forEach(function(item){
                str1 += `<option value="${item.GovernmentCode}">${item.Name}</option>`;
            });
            data.additional.forEach(function(item){
                str2 += `<option value="${item.Code}">${item.Name}</option>`;
            });
            $('#Government').html(str1);
            $('#School').html(str2);
        });
    });
    $('#Government').on('change', function(e){
        e.preventDefault();
        var government = $(this).val();
        var url = '/';
        var posting = $.post(url, {'government': government});
        posting.done(function(data){
            str = `<option value="-1">Все</option>`;
            data.result.forEach(function(item){
                str += `<option value="${item.Code}">${item.Name}</option>`;
            });
            data.additional.forEach(function(item){
                $('#District').val(item.DistrictCode);
            });
            if (government == -1){
                $('#District').val(government);
            }
            $('#School').html(str);
        });
    });
    $('#School').on('change', function(e){
        e.preventDefault();
        var school = $(this).val();
        var url = '/';
        var posting = $.post(url, {'school': school});
        posting.done(function(data){
            data.result.forEach(function(item){
                $('#Government').val(item.GovernmentCode);
            });
            data.additional.forEach(function(item){
                $('#District').val(item.DistrictCode);
            });
        });
    });
    $('#SchoolType').on('change', function(e){
        e.preventDefault();
        var type = $(this).val();
        var url = '/';
        var posting = $.post(url, {'type':type});
        posting.done(function(data){
            arr = [];
            str1 = `<option value="-1">Все</option>`;
            str2 = str1;
            data.result.forEach(function(item){
                if ($('#District').val() != -1){
                    if ($('#Government').val() != -1){
                        if (item.GovernmentCode == $('#Government').val() && item.DistrictCode == $('#District').val()){
                            str = `<option value="${item.SKCode}">${item.SKName}</option>`;
                            if ($.inArray(str, arr) == -1){
                                arr.push(str);
                            }
                        }
                    }
                    else{
                        if (item.DistrictCode == $('#District').val()){
                            str = `<option value="${item.SKCode}">${item.SKName}</option>`;
                            if ($.inArray(str, arr) == -1){
                                arr.push(str);
                            }
                        }
                    }
                }
                else{
                    if ($('#Government').val() != -1){
                        if (item.GovernmentCode == $('#Government').val()){
                            str = `<option value="${item.SKCode}">${item.SKName}</option>`;
                            if ($.inArray(str, arr) == -1){
                                arr.push(str);
                            }
                        }
                    }
                    else{
                        str = `<option value="${item.SKCode}">${item.SKName}</option>`;
                            if ($.inArray(str, arr) == -1){
                                arr.push(str);
                            }
                    }
                }
            });
            data.additional.forEach(function(item){
                if ($('#District').val() != -1){
                    if ($('#Government').val() != -1){
                        if (item.GovernmentCode == $('#Government').val() && item.DistrictCode == $('#District').val()){
                            str2 += `<option value="${item.Code}">${item.Name}</option>`;
                        }
                    }
                    else{
                        if (item.DistrictCode == $('#District').val()){
                            str2 += `<option value="${item.Code}">${item.Name}</option>`;
                        }
                    }
                }
                else{
                    if ($('#Government').val() != -1){
                        if (item.GovernmentCode == $('#Government').val()){
                            str2 += `<option value="${item.Code}">${item.Name}</option>`;
                        }
                    }
                    else{
                        str2 += `<option value="${item.Code}">${item.Name}</option>`;
                    }
                }
            });
            var s = arr.join(' ');
            str1 += s;
            $('#SchoolKind').html(str1);
            $('#School').html(str2);
        });
    });
    $('#SchoolKind').on('change', function(e){
        e.preventDefault();
        var kind = $(this).val();
        var url = '/';
        var posting = $.post(url, {'kind':kind});
        posting.done(function(data){
            str = `<option value="-1">Все</option>`;
            data.result.forEach(function(item){
                $('#SchoolType').val(item.SchoolType);
            });
            if (kind == -1){
                data.additional.forEach(function(item){
                    if ($('#District').val() != -1){
                        if ($('#Government').val() != -1){
                            if (item.SchoolType == $('#SchoolType').val() && 
                            item.GovernmentCode == $('#Government').val() && 
                            item.DistrictCode == $('#District').val()){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                        else{
                            if (item.SchoolType == $('#SchoolType').val() && item.DistrictCode == $('#District').val()){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                    }
                    else{
                        if ($('#Government').val() != -1){
                            if (item.SchoolType == $('#SchoolType').val() && item.GovernmentCode == $('#Government').val()){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                        else{
                            if (item.SchoolType == $('#SchoolType').val()){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                    }
                });
            }else{
                data.additional.forEach(function(item){
                    if ($('#District').val() != -1){
                        if ($('#Government').val() != -1){
                            if (item.SchoolType == $('#SchoolType').val() && 
                            item.GovernmentCode == $('#Government').val() && 
                            item.DistrictCode == $('#District').val() &&
                            item.SchoolKindCode == kind){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                        else{
                            if (item.SchoolType == $('#SchoolType').val() && 
                            item.DistrictCode == $('#District').val() &&
                            item.SchoolKindCode == kind){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                    }
                    else{
                        if ($('#Government').val() != -1){
                            if (item.SchoolType == $('#SchoolType').val() && 
                            item.GovernmentCode == $('#Government').val() &&
                            item.SchoolKindCode == kind){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                        else{
                            if (item.SchoolType == $('#SchoolType').val() && item.SchoolKindCode == kind){
                                str += `<option value="${item.Code}">${item.Name}</option>`;
                            }
                        }
                    }
                });
            }
            $('#School').html(str);
        });
    });
}
// Взаимозависимость select'ов отвечающих за предмет
function Subjects(){
    $('#ExamType').on('change', function(e){
        e.preventDefault();
        var examtype = $(this).val();
        var url = '/';
        console.log(examtype);
        var posting = $.post(url, {'examtype': examtype});
        posting.done(function(data){
            str1 = `<option value="-1" class="-1">Все</option>`;
            data.result.forEach(function(item){
                str1 += `<option value="${item.SubjectCode}">${item.Name}</option>`;
            });
            $('#Subject').html(str1);
        });
    });
    $('#Subject').on('change', function(e){
        e.preventDefault();
        var examtype = $('#Subject option:selected').attr('class');
        if (examtype != -1){
            $('#ExamType').val(examtype);
        }
        else{
            $('#ExamType').val(-1);
        }
        var subject = $(this).val();
        var url = '/';
        var posting = $.post(url, {'subject':subject});
        posting.done(function(data){
            str1 = `<option value="-1">Все</option>`;
            str2 = str1;
            str3 = str1;
            str4 = str1;
            str5 = str1;
            $.each(data.result, function(index, el){
                if (index == 0){
                    el.forEach(function(item){
                        str1 += `<option value="${item.ReqCode}">${item.ReqCode} - ${item.Description}</option>`;
                    });
                }else{
                    el.forEach(function(item){
                        str2 += `<option value="${item.ReqCode}">${item.ReqCode} - ${item.Description}</option>`;
                    });
                }
            });
            $.each(data.additional, function(index, el){
                if (index == 0){
                    el.forEach(function(item){
                        str3 += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
                    });
                }else if (index == 1){
                    el.forEach(function(item){
                        str4 += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
                    });
                }else{
                    el.forEach(function(item){
                        str5 += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
                    });
                }
            });
            $('#SectionReq').html(str1);
            $('#Requirement').html(str2);
            $('#SectionComp').html(str3);
            $('#SubSectionComp').html(str4);
            $('#Competence').html(str5);
        });
    });
}
// Взаимозависимость select'ов отвечающих за требования к уровню подготовки
function Requirements(){
    $('#SectionReq').on('change', function(e){
        e.preventDefault();
        var section = $(this).val();
        var url = '/';
        var posting = $.post(url, {'section':section});
        posting.done(function(data){
            str = `<option value="-1">Все</option>`;
            data.result.forEach(function(item){
                str += `<option value="${item.ReqCode}">${item.ReqCode} - ${item.Description}</option>`;
            });
            $('#Requirement').html(str);
        });
    });
    $('#Requirement').on('change', function(e){
        e.preventDefault();
        var req = $(this).val();
        var url = '/';
        var posting = $.post(url, {'req':req});
        posting.done(function(data){
            data.result.forEach(function(item){
                $('#SectionReq').val(item.ReqCode);
            });
        });
    });
}
// Взаимозависимость select'ов отвечающих за элементы содержания
function Competence(){
    $('#SectionComp').on('change', function(e){
        e.preventDefault();
        var section = $(this).val();
        var url = '/';
        var posting = $.post(url, {'sectioncomp':section});
        posting.done(function(data){
            str1 = `<option value="-1">Все</option>`;
            str2 = str1;
            data.result.forEach(function(item){
                str1 += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
            });
            data.additional.forEach(function(item){
                str2 += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
            });
            $('#SubSectionComp').html(str1);
            $('#Competence').html(str2);
        });
    });
    $('#SubSectionComp').on('change', function(e){
        e.preventDefault();
        var subsection = $(this).val();
        var url = '/';
        var posting = $.post(url, {'subsection':subsection});
        posting.done(function(data){
            str = `<option value="-1">Все</option>`;
            data.result.forEach(function(item){
                str += `<option value="${item.CompCode}">${item.CompCode} - ${item.Description}</option>`;
            });
            data.additional.forEach(function(item){
                $('#SectionComp').val(item.SectionCode);
            });
            if (subsection == -1){
                $('#SectionComp').val(subsection);
            }
            $('#Competence').html(str);
        });
    });
    $('#Competence').on('change', function(e){
        e.preventDefault();
        var compet = $(this).val();
        var url = '/';
        var posting = $.post(url, {'compet':compet});
        posting.done(function(data){
            data.result.forEach(function(item){
                $('#SubSectionComp').val(item.SectionCode);
            });
            data.additional.forEach(function(item){
                $('#SectionComp').val(item.SectionCode);
            });
        });
    });
}