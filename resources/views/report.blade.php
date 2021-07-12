
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeepCrowbar</title>
    {{-- Подключение карты --}}
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a341a481-a792-4cbd-b917-8dd58dbac84d&lang=ru_RU" type="text/javascript"></script>
    {{-- Подключение Bootstrap 5.0.1 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    {{-- Подключение JQuery 3.6.0 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    {{-- Подключение стилей --}}
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    {{-- Подключение Google API --}}
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse ">
          <div class="position-sticky pt-3">
            <form id="filter" method="POST" action="{{ route('user.report') }}">
              @csrf
              <ul class="nav flex-column">
                <li class='nav-item'>
                  <div class="accordion" id="accordionExample2">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          Настройка запроса
                        </button>
                      </h2>
                      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
                        <div class="accordion-body">
                          <ul class="nav flex-column">
                            <li class="nav-item">
                              <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted border-bottom">
                                <span>Настройка запроса</span>
                                <a class="link-secondary" href="#" aria-label="Add a new report">
                                  <span data-feather="plus-circle"></span>
                                </a>
                              </h6>   
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="file">Район</span>
                                <select class="form-select form-select-sm" name="District" id="District">
                                  <option value="-1">Все</option>
                                  @foreach ($districts as $district)
                                  <option value="{{ $district->DistrictCode }}">{{ $district->Name }}</option>
                                  @endforeach           
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Огран управления</span>
                                <select class="form-select form-select-sm" name="Government" id="Government">
                                  <option value="-1">Все</option>
                                  @foreach ($governments as $government)
                                  <option value="{{ $government->GovernmentCode }}">{{ $government->Name }}</option>
                                  @endforeach                 
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="bar-chart-2">Тип школы</span>
                                <select class="form-select form-select-sm" name="SchoolType" id="SchoolType">
                                  <option value="-1">Все</option>
                                  @foreach ($schooltypes as $schooltype)
                                  <option value="{{ $schooltype->Code }}">{{ $schooltype->Name }}</option>
                                  @endforeach                  
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="users">Вид школы</span>
                                <select class="form-select form-select-sm" name="SchoolKind" id="SchoolKind">
                                  <option value="-1">Все</option>  
                                  @foreach ($schoolkinds as $schoolkind)
                                  <option value="{{ $schoolkind->Code }}">{{ $schoolkind->Name }}</option>
                                  @endforeach               
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="layers">Школа</span>
                                <select class="form-select form-select-sm" name="School" id="School">
                                  <option value="-1">Все</option>
                                  @foreach ($establishments as $establishment)
                                  <option value="{{ $establishment->Code }}">{{ $establishment->Name }}</option>
                                  @endforeach                 
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Вид экзамена</span>
                                <select class="form-select form-select-sm" name="ExamType" id="ExamType">
                                  <option value="-1">Все</option>
                                  @foreach ($examtypes as $examtype)
                                  <option value="{{ $examtype->ExamTypeID }}">{{ $examtype->Name }}</option>
                                  @endforeach                 
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Предмет</span>
                                <select class="form-select form-select-sm" name="Subject" id="Subject">
                                  <option value="-1" class="-1">Все</option>
                                  @foreach ($subjects as $subject)
                                      <option value="{{ $subject->SubjectCode }}" class="{{ $subject->ExamTypeID }}">{{ $subject->Name }}</option>
                                  @endforeach                 
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Год</span>
                              </a>
                              <div id='slider2'></div>
                              <div class='container'>
                                <div class='row justify-content-around'>
                                  <input class='box' name="Year1" id='Year1'>
                                  <input class='box' name="Year2" id='Year2'>  
                                </div>
                              </div>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Критерии</span>
                                <select class="form-select form-select-sm" name="repType" id="repType">
                                  <option value="point">Балл</option>
                                  <option value="pnp">Сдавших/не сдавших</option>
                                  <option value="pc">Процент выполнения</option>
                                  <option value="p1">Часть 1</option>
                                  <option value="p2">Часть 2</option>
                                  <option value="p3">Устная часть</option>
                                </select>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Выбор типа отчета</span>
                                  <div class='container'>
                                    <div class=' justify-content-around'>
                                      <div class='bb' id="repAvg" style='background-color: rgb(109, 253, 109)'>Среднее значение</div>
                                      <div class='bb' id="repMin">Минимальное значение</div>
                                      <div class='bb' id="repMax">Максимальное значение</div>
                                      <input name="InVal" id="InVal" value="0" hidden/>
                                    </div>
                                  </div>
                                </a>
                            </li>
                            <li class="nav-item" id="slide">
                              <a class="nav-link" href="#">
                                <span data-feather="shopping-cart">Балл</span>                
                              </a>
                              <div id='slider'></div>
                              <div class='container'>
                                <div class='row justify-content-around'>
                                  <input class='box' name="Point1" id='Point1'>
                                  <input class='box' name="Point2" id='Point2'>  
                                </div>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="nav-item">
                  <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                          Компетенции
                        </button>
                      </h2>
                      <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul class="nav flex-column">
                              <li class="nav-item">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted border-bottom">
                                    <span>Требования к уровню подготовки</span>
                                    <a class="link-secondary" href="#" aria-label="Add a new report">
                                      <span data-feather="plus-circle"></span>
                                    </a>
                                  </h6>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" href="#">
                                  <span data-feather="bar-chart-2">Раздел</span>
                                </a>
                                <select class="form-control col" name="SectionReq" id="SectionReq">
                                  <option value="-1">Все</option>
                                  @foreach ($reqsections as $reqsection)
                                      <option value="{{ $reqsection->ReqCode }}">{{ $reqsection->ReqCode }} - {{ $reqsection->Description }}</option>
                                  @endforeach                 
                                </select>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" href="#">
                                  <span data-feather="bar-chart-2">Требование</span>
                                </a>
                                <select class="form-control col" name="Requirement" id="Requirement">
                                  <option value="-1">Все</option>
                                  @foreach ($requirements as $requirement)
                                      <option value="{{ $requirement->ReqCode }}">{{ $requirement->ReqCode }} - {{ $requirement->Description }}</option>
                                  @endforeach                 
                                </select>
                              </li>
                              <li class="nav-item">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted border-bottom">
                                    <span>Элементы содержания</span>
                                    <a class="link-secondary" href="#" aria-label="Add a new report">
                                      <span data-feather="plus-circle"></span>
                                    </a>
                                  </h6>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" href="#">
                                  <span data-feather="bar-chart-2">Раздел</span>
                                </a>
                                <select class="form-control col" name="SectionComp" id="SectionComp">
                                  <option value="-1">Все</option>
                                  @foreach ($compsections as $compsection)
                                      <option value="{{ $compsection->CompCode }}">{{ $compsection->CompCode }} - {{ $compsection->Description }}</option>
                                  @endforeach                
                                </select>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" href="#">
                                  <span data-feather="bar-chart-2">Подраздел</span>
                                </a>
                                <select class="form-control col" name="SubSectionComp" id="SubSectionComp">
                                  <option value="-1">Все</option>
                                  @foreach ($compsubsections as $compsubsection)
                                      <option value="{{ $compsubsection->CompCode }}">{{ $compsubsection->CompCode }} - {{ $compsubsection->Description }}</option>
                                  @endforeach              
                                </select>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" href="#">
                                  <span data-feather="bar-chart-2">Элемент содержания</span>
                                </a>
                                <select class="form-control col" name="Competence" id="Competence">
                                  <option value="-1">Все</option>
                                  @foreach ($competencies as $competence)
                                      <option value="{{ $competence->CompCode }}">{{ $competence->CompCode }} - {{ $competence->Description }}</option>
                                  @endforeach               
                                </select>
                              </li>
                            </ul>
                        </div>
                      </div>
                    </div>
                </li>
              </ul>
              <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted border-bottom">
                <span>Выбор диаграммы</span>
                <a class="link-secondary" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
                </a>
              </h6>
              <ul class="nav flex-column mb-2">
                <li class="nav-item">
                  <div class="nav-link" id="dg1">
                    <img class='diag' src="{{asset('picts/img2.png')}}" alt="">
                    <span data-feather="file">Столбчатая диаграмма</span>
                  </div>
                </li>
                <li class="nav-item">
                  <div class="nav-link" id="dg2">
                    <img src="{{asset('picts/map.png')}}" alt="">
                    <span data-feather="layers">Карта</span>
                  </div>
                </li>
                
              </ul>
              <button type="submit" class="btn btn-primary col-6" id="Apply">Построить</button>
            </form>
          
          </div>
        </nav>
      </div>
      <main class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2" id="resultHeader">Диаграмма</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
              <button type="button" class="btn btn-sm btn-outline-secondary">Сохранить</button>
            </div>
          </div>
        </div>
        <div class = "container-fluid map" id="chart_div"></div>
        <div class="container-fluid map" id="map"></div>
      </main>
    </div>
    {{-- Подключение основного файла со скриптами --}}
    <script src="{{ asset('js/request.js') }}" defer></script>
    {{-- Подключение nouislider --}}
    <script src="{{ asset('js/nouislider.min.js') }}" defer></script>
    {{-- Подключение логики работы слайдера --}}
    <script src="{{ asset('js/slider.js') }}" defer></script>
  </body>
</html>