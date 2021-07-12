<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Конструктор отчета</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        {{-- <link href="{{ asset('css/signin.css') }}" rel="stylesheet"> --}}
        <!--Load the AJAX API-->
        {{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">

        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.arrayToDataTable([
            ['Школа', 'Средний балл'], 
            ['Департамент по социальным вопросам администрации г.Ишима', 38],
            ['МАОУ ИГОЛ им.Е.Г.Лукьянец', 56],
            ['МАОУ СОШ №1 г.Ишима', 57],
            ['МАОУ СОШ №12 г.Ишима', 57],
            ['МАОУ СОШ №2 г.Ишима', 57],
            ['МАОУ СОШ №31 г.Ишима', 56],
            ['МАОУ СОШ №4 г.Ишима', 56],
            ['МАОУ СОШ №5 г.Ишима', 56],
            ['МАОУ СОШ №7 г.Ишима', 56],
            ['МАОУ СОШ №8 г.Ишима', 56],
            ['ОЧУ "Ишимская православная гимназия"', 56]
            ]);

            // Set chart options
            var options = {pieHole: 0.4,pieSliceText: 'value'};

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.PieChart(document.getElementById('chart'));
            chart.draw(data, options);
        }
        </script> --}}
    </head>

    <body class="container-fluid">
        <div class="row">
            <div class="col-4 border">
                <form id="filter" method="POST" action="{{ route('user.report') }}">
                    {{-- Request --}}
                    @csrf
                    <div class="form-group my-2 mx-1 row">
                        <label for="District" class="col my-auto">Район</label>
                        <select class="form-control col" name="District" id="District">
                            <option value="-1">Все</option>
                            @foreach ($districts as $district)
                            <option value="{{ $district->DistrictCode }}">{{ $district->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="Government" class="col my-auto">Орган управления</label>
                        <select class="form-control col" name="Government" id="Government">
                            <option value="-1">Все</option>
                            @foreach ($governments as $government)
                            <option value="{{ $government->GovernmentCode }}">{{ $government->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="School" class="col my-auto">Образовательное учреждение</label>
                        <select class="form-control col" name="School" id="School">
                            <option value="-1">Все</option>
                            @foreach ($establishments as $establishment)
                            <option value="{{ $establishment->Code }}">{{ $establishment->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="ExamType" class="col my-auto">Вид экзамена</label>
                        <select class="form-control col" name="ExamType" id="ExamType">
                            <option value="-1">Все</option>
                            @foreach ($examtypes as $examtype)
                                <option value="{{ $examtype->ExamTypeID }}">{{ $examtype->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="Year" class="col my-auto">Год</label>
                        <select class="form-control col" name="Year" id="Year">
                            <option value="-1">Все</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="Subject" class="col my-auto">Предмет<input class="form-check-input my-1 mx-1" type="checkbox" name="SubjectCheck" id="SubjectCheck"></label>
                        <select class="form-control col" name="Subject" id="Subject">
                            <option value="-1">Все</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->SubjectCode }}" class="{{ $subject->ExamTypeID }}">{{ $subject->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-2 mx-1 row">
                        <label for="Subject" class="col my-auto">Компетенции<input class="form-check-input my-1 mx-1" type="checkbox" name="CompetCheck" id="CompetCheck"></label>
                    </div>
                    <div class="form-group my-2 mx-1 row" id="Requirements">
                        <label for="Subject" class="col my-auto">Требования к уровню подготовки<input class="form-check-input my-1 mx-1" type="checkbox" name="RequirementCheck" id="RequirementCheck"></label>
                        <div class="form-group border" id="Req">
                            <div class="form-group my-2 mx-1 row">
                                <label for="School" class="col my-auto">Раздел</label>
                                <select class="form-control col" name="SectionReq" id="SectionReq">
                                    <option value="-1">Все</option>
                                    @foreach ($reqsections as $reqsection)
                                        <option value="{{ $reqsection->ReqCode }}">{{ $reqsection->ReqCode }} - {{ $reqsection->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group my-2 mx-1 row">
                                <label for="School" class="col my-auto">Требование</label>
                                <select class="form-control col" name="Requirement" id="Requirement">
                                    <option value="-1">Все</option>
                                    @foreach ($requirements as $requirement)
                                        <option value="{{ $requirement->ReqCode }}">{{ $requirement->ReqCode }} - {{ $requirement->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group my-2 mx-1 row" id="Competencies">
                        <label for="Subject" class="col my-auto">Элементы содержания<input class="form-check-input my-1 mx-1" type="checkbox" name="CompetenceCheck" id="CompetenceCheck"></label>
                        <div class="form-group border" id="Comp">
                            <div class="form-group my-2 mx-1 row">
                                <label for="School" class="col my-auto">Раздел</label>
                                <select class="form-control col" name="SectionComp" id="SectionComp">
                                    <option value="-1">Все</option>
                                    @foreach ($compsections as $compsection)
                                        <option value="{{ $compsection->CompCode }}">{{ $compsection->CompCode }} - {{ $compsection->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group my-2 mx-1 row">
                                <label for="School" class="col my-auto">Подраздел</label>
                                <select class="form-control col" name="SubSectionComp" id="SubSectionComp">
                                    <option value="-1">Все</option>
                                    @foreach ($compsubsections as $compsubsection)
                                        <option value="{{ $compsubsection->CompCode }}">{{ $compsubsection->CompCode }} - {{ $compsubsection->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group my-2 mx-1 row">
                                <label for="School" class="col my-auto">Элемент содержания</label>
                                <select class="form-control col" name="Competence" id="Competence">
                                    <option value="-1">Все</option>
                                    @foreach ($competencies as $competence)
                                        <option value="{{ $competence->CompCode }}">{{ $competence->CompCode }} - {{ $competence->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <table class="table my-2 mx-1 border" id="tableCheck">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">AVG</th>
                                <th scope="col">MAX</th>
                                <th scope="col">MIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Первичный балл</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck1" id="AvgCheck1"></th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck1" id="AvgCheck1"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck1" id="MinCheck1"></th>
                            </tr>
                            <tr>
                                <th scope="row">Процент выполненния</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck2" id="AvgCheck2"></th>
                                <th><input class="form-check-input" type="checkbox" name="MaxCheck2" id="MaxCheck2"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck2" id="MinCheck2"></th>
                            </tr>
                            <tr>
                                <th scope="row">Итоговый балл</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck3" id="AvgCheck3"></th>
                                <th><input class="form-check-input" type="checkbox" name="MaxCheck3" id="MaxCheck3"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck3" id="MinCheck3"></th>
                            </tr>
                            <tr>
                                <th scope="row">Балл за часть с кратким ответом</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck4" id="AvgCheck4"></th>
                                <th><input class="form-check-input" type="checkbox" name="MaxCheck4" id="MaxCheck4"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck4" id="MinCheck4"></th>
                            </tr>
                            <tr>
                                <th scope="row">Балл за часть с развернутым ответом</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck5" id="AvgCheck5"></th>
                                <th><input class="form-check-input" type="checkbox" name="MaxCheck5" id="MaxCheck5"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck5" id="MinCheck5"></th>
                            </tr>
                            {{-- <tr>
                                <th scope="row">Балл за устную часть</th>
                                <th><input class="form-check-input" type="checkbox" name="AvgCheck6" id="AvgCheck6"></th>
                                <th><input class="form-check-input" type="checkbox" name="MaxCheck6" id="MaxCheck6"></th>
                                <th><input class="form-check-input" type="checkbox" name="MinCheck6" id="MinCheck6"></th>
                            </tr> --}}
                        </tbody>
                    </table>
    
                    <div class="form-group my-2 mx-1 row">
                        <label for="Government" class="col my-auto"> Тип диаграммы </label>
                        <select class="form-control col" name="Government" id="Government" value="" >
                            <option>Круговая</option>
                            {{-- <option>Диаграмма</option>
                            <option>График</option> --}}
                        </select>
                    </div>
    
                    <div class="btn-group my-2 mx-1 col-12" role="group">
                        <button type="submit" class="btn btn-primary col-6" id="Apply">Построить</button>
                        <button type="button" class="btn btn-primary col-6">Сохранить шаблон</button>
                    </div>
                </form>
            </div>
            <div class="col-8 border">
                {{-- Report --}}
                <table class="table my-2 mx-1 border report">
                    <thead>
                        <tr>
                            <th scope="col">SubjectCode</th>
                            <th scope="col">Name</th>
                            <th scope="col">ExamType</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                <div id="chart" style="width: 900px; height: 500px;"></div>
            </div>
        </div>
        <script src="{{ asset('js/request.js') }}" defer>
            // $(function () {
            //     $("#filter").on('submit', function(e){
            //         e.preventDefault();
            //         $.ajaxSetup({
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             }
            //         });
            //         $(this).data('filter', JSON.stringify($(this).serializeArray()));
            //         var $form = $( this );
            //         var term = $(this).data('filter');
            //         var url = $form.attr( 'action' );
            //         console.log($(this).data('filter'));
            //         var posting = $.post(url, {filter: term});
            //         $('table.report tbody').html('');
            //         posting.done(function(data){
            //             data.forEach(function(item) {
            //                 console.log(item);
            //                 str = `<tr>
            //                     <td>${item.SubjectCode}</td>
            //                     <td>${item.Name}</td>
            //                     <td>${item.ExamType}</td>
            //                 </tr>`;
            //                 $('table.report tbody').append(str);
            //             });
            //         });
            //     });
            // });
        </script>
    </body>
</html>