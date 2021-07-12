<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index(){
        return view('report');
    }
    // Построение запроса и возврат полученных данных
    public function report(Request $request){
        $list = [];
        $result = [];
        if (!empty($request->input('filter'))){
            $filters = json_decode($request->input('filter'));
            if (!empty($filters)){
                foreach($filters as $item){
                    if ($item->name === "District"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Government"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "SchoolType"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "SchoolKind"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "School"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "ExamType"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Subject"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Year1"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Year2"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "repType"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "InVal"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Point1"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Point2"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "SectionReq"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Requirement"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "SectionComp"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "SubSectionComp"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                    if ($item->name === "Competence"){
                        $list[$item->name] = $item->value;
                        continue;
                    }
                }
            }
        }
        
        if ($list['School'] != -1){
            if ($list['Subject'] != -1){
                if ($list['Requirement'] != -1){
                    if ($list['Year1'] != $list['Year2']){
                        $result = DB::select('select * 
                        from
                        (
                            select t.Establishment, t.Points/sum(t.Points) over() as Percent, t.Year
                            from 
                            (
                                select e.ExamID, est.Name as Establishment, t.Year, a.TaskNumber, sum(a.CurrentPoint) as Points
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on est.Code=e.EstablishmentCode
                                join governments g on g.GovernmentCode=est.GovernmentCode
                                join specifications s on s.SchemaID=e.SchemaID
                                join specsrequirements sr on sr.SpecCode=s.SpecID
                                join answers a on a.ExamID=e.ExamID
                                where g.DistrictCode = ? and t.Year >= ? and t.Year <= ? and t.SubjectID = ? and sr.ReqCode = ? and a.TaskNumber=s.TaskNumber
                                group by e.ExamID, a.TaskNumber
                            ) as t
                            group by t.Year, t.Establishment
                        ) as tt', [$list['District'], $list['Year1'], $list['Year2'], $list['Subject'], $list['Requirement']]);
                    }
                    else{
                        $result = DB::select('select * 
                        from
                        (
                            select t.TaskNumber, avg(t.Points) as Points
                            from 
                            (
                                select e.ExamID, a.TaskNumber, sum(a.CurrentPoint) as Points
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on est.Code=e.EstablishmentCode
                                join specifications s on s.SchemaID=e.SchemaID
                                join specsrequirements sr on sr.SpecCode=s.SpecID
                                join answers a on a.ExamID=e.ExamID
                                where est.Code = ? and t.Year = ? and t.SubjectID = ? and sr.ReqCode = ? and a.TaskNumber=s.TaskNumber
                                group by e.ExamID, a.TaskNumber
                            ) as t
                            group by t.TaskNumber
                        ) as tt', [$list['School'], $list['Year1'], $list['Subject'], $list['Requirement']]);
                    }
                }
                else{
                    if ($list['repType'] == 'point'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from 
                            (
                                select avg(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from 
                            (
                                select min(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from 
                            (
                                select max(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p1'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);

                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p2'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p3'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year;
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'pc'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select avg(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select min(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select max(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['School']]);
                        }
                    }
                    else if ($list['repType'] == 'pnp'){
                        if ($list['InVal'] == 0){

                        }
                        else if ($list['InVal'] == 1){

                        }
                        else if ($list['InVal'] == 2){
                            
                        }
                    }
                }
            }
            else{
                if ($list['repType'] == 'point'){
                    if ($list['InVal'] == 0){
                        $result = DB::select('select * 
                        from 
                        (
                            select avg(e.FinalPoints) as Points, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by e.EstablishmentCode
                        ) as t
                        where t.Points >= ? and t.Points <= ?', [ $list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 1){
                        $result = DB::select('select * 
                        from 
                        (
                            select min(e.FinalPoints) as Points, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by e.EstablishmentCode
                        ) as t
                        where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 2){
                        $result = DB::select('select * 
                        from 
                        (
                            select max(e.FinalPoints) as Points, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?
                            group by e.EstablishmentCode
                        ) as t
                        where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                }
                else if ($list['repType'] == 'p1'){
                    if ($list['InVal'] == 0){
                        $result = DB::select('select * 
                        from
                        (
                            select avg(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);

                    }
                    else if ($list['InVal'] == 1){
                        $result = DB::select('select * 
                        from
                        (
                            select min(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 2){
                        $result = DB::select('select * 
                        from
                        (
                            select max(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 1
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                }
                else if ($list['repType'] == 'p2'){
                    if ($list['InVal'] == 0){
                        $result = DB::select('select * 
                        from
                        (
                            select avg(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where and t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 1){
                        $result = DB::select('select * 
                        from
                        (
                            select min(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 2){
                        $result = DB::select('select * 
                        from
                        (
                            select max(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 2
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                }
                else if ($list['repType'] == 'p3'){
                    if ($list['InVal'] == 0){
                        $result = DB::select('select * 
                        from
                        (
                            select avg(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 1){
                        $result = DB::select('select * 
                        from
                        (
                            select min(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                    else if ($list['InVal'] == 2){
                        $result = DB::select('select * 
                        from
                        (
                            select max(t.Points) as Points, t.Subject
                            from 
                            (
                                select sum(a.CurrentPoint) as Points, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join answers a on a.ExamID=e.ExamID
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ? and a.PartID = 3
                                group by e.ExamID
                            ) as t
                        ) as tt
                        where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['School'], $list['Point1'], $list['Point2']]);
                    }
                }
                else if ($list['repType'] == 'pc'){
                    if ($list['InVal'] == 0){
                        $result = DB::select('select avg(e.CompletionPercent) as Percent, s.Name as Subject
                        from exams e join testschema t on e.SchemaID=t.SchemaID
                        join subjects s on s.SubjectCode=t.SubjectID
                        where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?', [$list['Year1'], $list['Year2'], $list['School']]);
                    }
                    else if ($list['InVal'] == 1){
                        $result = DB::select('select min(e.CompletionPercent) as Percent, s.Name as Subject
                        from exams e join testschema t on e.SchemaID=t.SchemaID
                        join subjects s on s.SubjectCode=t.SubjectID
                        where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?', [$list['Year1'], $list['Year2'], $list['School']]);
                    }
                    else if ($list['InVal'] == 2){
                        $result = DB::select('select max(e.CompletionPercent) as Percent, s.Name as Subject
                        from exams e join testschema t on e.SchemaID=t.SchemaID
                        join subjects s on s.SubjectCode=t.SubjectID
                        where t.Year >= ? and t.Year <= ? and e.EstablishmentCode = ?', [$list['Year1'], $list['Year2'], $list['School']]);
                    }
                }
                else if ($list['repType'] == 'pnp'){
                    if ($list['InVal'] == 0){

                    }
                    else if ($list['InVal'] == 1){

                    }
                    else if ($list['InVal'] == 2){
                        
                    }
                }
            }
        }
        else{
            if ($list['Government'] != -1){
                if ($list['Subject'] != -1){
                    if ($list['repType'] == 'point'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from 
                            (
                                select est.Name as Establishment, avg(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from 
                            (
                                select est.Name as Establishment, min(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from 
                            (
                                select est.Name as Establishment, max(e.FinalPoints) as Points, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by t.Year, e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p1'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
    
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p2'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p3'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, avg(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, min(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select t.Establishment, max(t.Points) as Points, t.Year
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Year, t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'pc'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select est.Name as Establishment, avg(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select est.Name as Establishment, min(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select est.Name as Establishment, max(e.CompletionPercent) as Percent, t.Year
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['Government']]);
                        }
                    }
                    else if ($list['repType'] == 'pnp'){
                        if ($list['InVal'] == 0){
    
                        }
                        else if ($list['InVal'] == 1){
    
                        }
                        else if ($list['InVal'] == 2){
                            
                        }
                    }
                }
                else{
                    if ($list['repType'] == 'point'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from 
                            (
                                select avg(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from 
                            (
                                select min(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from 
                            (
                                select max(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                                group by e.EstablishmentCode
                            ) as t
                            where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p1'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
    
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 1
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p2'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 2
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'p3'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select * 
                            from
                            (
                                select avg(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select * 
                            from
                            (
                                select min(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select * 
                            from
                            (
                                select max(t.Points) as Points, t.Establishment, t.Subject
                                from 
                                (
                                    select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join answers a on a.ExamID=e.ExamID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ? and a.PartID = 3
                                    group by e.ExamID
                                ) as t
                                group by t.Establishment
                            ) as tt
                            where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Government'], $list['Point1'], $list['Point2']]);
                        }
                    }
                    else if ($list['repType'] == 'pc'){
                        if ($list['InVal'] == 0){
                            $result = DB::select('select avg(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Government']]);
                        }
                        else if ($list['InVal'] == 1){
                            $result = DB::select('select min(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Government']]);
                        }
                        else if ($list['InVal'] == 2){
                            $result = DB::select('select max(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                            from exams e join testschema t on e.SchemaID=t.SchemaID
                            join establishments est on e.EstablishmentCode=est.Code
                            join subjects s on s.SubjectCode=t.SubjectID
                            where t.Year >= ? and t.Year <= ? and est.GovernmentCode = ?
                            group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Government']]);
                        }
                    }
                    else if ($list['repType'] == 'pnp'){
                        if ($list['InVal'] == 0){
    
                        }
                        else if ($list['InVal'] == 1){
    
                        }
                        else if ($list['InVal'] == 2){
                            
                        }
                    }
                }
            }
            else{
                if ($list['District'] != -1){
                    if ($list['Subject'] != -1){
                        if ($list['repType'] == 'point'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, avg(e.FinalPoints) as Points, t.Year, est.LawAddress
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, min(e.FinalPoints) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, max(e.FinalPoints) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p1'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
        
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment,  min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p2'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p3'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'pc'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select est.Name as Establishment, avg(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select est.Name as Establishment, min(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select est.Name as Establishment, max(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                where t.SubjectID = ? and t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by t.Year, e.EstablishmentCode', [$list['Subject'], $list['Year1'], $list['Year2'], $list['District']]);
                            }
                        }
                        else if ($list['repType'] == 'pnp'){
                            if ($list['InVal'] == 0){
        
                            }
                            else if ($list['InVal'] == 1){
        
                            }
                            else if ($list['InVal'] == 2){
                                
                            }
                        }
                    }
                    else{
                        if ($list['repType'] == 'point'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from 
                                (
                                    select avg(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from 
                                (
                                    select min(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from 
                                (
                                    select max(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on e.EstablishmentCode=est.Code
                                    join governments g on est.GovernmentCode=g.GovernmentCode
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p1'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
        
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p2'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p3'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join establishments est on e.EstablishmentCode=est.Code
                                        join governments g on est.GovernmentCode=g.GovernmentCode
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        where t.Year >= ? and t.Year <= ? and g.DistrictCode = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['District'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'pc'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select avg(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['District']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select min(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['District']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select max(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on e.EstablishmentCode=est.Code
                                join governments g on est.GovernmentCode=g.GovernmentCode
                                join subjects s on s.SubjectCode=t.SubjectID
                                where t.Year >= ? and t.Year <= ? and g.DistrictCode = ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['District']]);
                            }
                        }
                        else if ($list['repType'] == 'pnp'){
                            if ($list['InVal'] == 0){
        
                            }
                            else if ($list['InVal'] == 1){
        
                            }
                            else if ($list['InVal'] == 2){
                                
                            }
                        }
                    }
                }
                else{
                    if ($list['Subject'] != -1){
                        if ($list['repType'] == 'point'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, avg(e.FinalPoints) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, min(e.FinalPoints) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from 
                                (
                                    select est.Name as Establishment, max(e.FinalPoints) as Points, t.Year
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                    group by t.Year, e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p1'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
        
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p2'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p3'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, avg(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, min(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select t.Establishment, max(t.Points) as Points, t.Year
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, t.Year
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and t.SubjectID = ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Year, t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Subject'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'pc'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select est.Name as Establishment, avg(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                group by t.Year, e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Subject']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select est.Name as Establishment, min(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                group by t.Year, e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Subject']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select est.Name as Establishment, max(e.CompletionPercent) as Percent, t.Year
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ? and t.SubjectID = ?
                                group by t.Year, e.EstablishmentCode', [$list['Year1'], $list['Year2'], $list['Subject']]);
                            }
                        }
                        else if ($list['repType'] == 'pnp'){
                            if ($list['InVal'] == 0){
        
                            }
                            else if ($list['InVal'] == 1){
        
                            }
                            else if ($list['InVal'] == 2){
                                
                            }
                        }
                    }
                    else{
                        if ($list['repType'] == 'point'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from 
                                (
                                    select avg(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from 
                                (
                                    select min(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from 
                                (
                                    select max(e.FinalPoints) as Points, est.Name as Establishment, s.Name as Subject
                                    from exams e join testschema t on e.SchemaID=t.SchemaID
                                    join subjects s on s.SubjectCode=t.SubjectID
                                    join establishments est on est.Code=e.EstablishmentCode
                                    where t.Year >= ? and t.Year <= ?
                                    group by e.EstablishmentCode
                                ) as t
                                where t.Points >= ? and t.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p1'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
        
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 1
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p2'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 2
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'p3'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select * 
                                from
                                (
                                    select avg(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select * 
                                from
                                (
                                    select min(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select * 
                                from
                                (
                                    select max(t.Points) as Points, t.Establishment, t.Subject
                                    from 
                                    (
                                        select sum(a.CurrentPoint) as Points, e.ExamID as ExamID, est.Name as Establishment, s.Name as Subject
                                        from exams e join testschema t on e.SchemaID=t.SchemaID
                                        join answers a on a.ExamID=e.ExamID
                                        join subjects s on s.SubjectCode=t.SubjectID
                                        join establishments est on est.Code=e.EstablishmentCode
                                        where t.Year >= ? and t.Year <= ? and a.PartID = 3
                                        group by e.ExamID
                                    ) as t
                                    group by t.Establishment
                                ) as tt
                                where tt.Points >= ? and tt.Points <= ?', [$list['Year1'], $list['Year2'], $list['Point1'], $list['Point2']]);
                            }
                        }
                        else if ($list['repType'] == 'pc'){
                            if ($list['InVal'] == 0){
                                $result = DB::select('select avg(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join subjects s on s.SubjectCode=t.SubjectID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2']]);
                            }
                            else if ($list['InVal'] == 1){
                                $result = DB::select('select min(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join subjects s on s.SubjectCode=t.SubjectID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2']]);
                            }
                            else if ($list['InVal'] == 2){
                                $result = DB::select('select max(e.CompletionPercent) as Percent, est.Name as Establishment, s.Name as Subject
                                from exams e join testschema t on e.SchemaID=t.SchemaID
                                join subjects s on s.SubjectCode=t.SubjectID
                                join establishments est on est.Code=e.EstablishmentCode
                                where t.Year >= ? and t.Year <= ?
                                group by e.EstablishmentCode', [$list['Year1'], $list['Year2']]);
                            }
                        }
                        else if ($list['repType'] == 'pnp'){
                            if ($list['InVal'] == 0){
        
                            }
                            else if ($list['InVal'] == 1){
        
                            }
                            else if ($list['InVal'] == 2){
                                
                            }
                        }
                    }
                }
            }
        }
        return response()->json($result);
    }
    // Получение данных для select'ов при первичной загрузке страницы
    public function get_list(){
        $districts = DB::select('select * from districts');
        $governments = DB::select('select * from governments');
        $schoolkinds = DB::select('select * from schoolkinds');
        $schooltypes = DB::select('select * from schooltypes');
        $establishments = DB::select('select * from establishments');
        $subjects = DB::select('select * from subjects');
        $examtypes = DB::select('select * from examtypes');
        $reqsections = DB::select('select * from requirements where ReqCode REGEXP "^[0-9]$"');
        $requirements = DB::select('select * from requirements where ReqCode REGEXP "[0-9].[0-9]"');
        $compsections = DB::select('select * from competencies where CompCode REGEXP "^[0-9]$"');
        $compsubsections = DB::select('select * from competencies where CompCode REGEXP "^[0-9].[0-9]$"');
        $competencies = DB::select('select * from competencies where CompCode REGEXP "[0-9].[0-9].[0-9]"');

        return view('report', 
        [
            'districts' => $districts,
            'governments' => $governments,
            'establishments' => $establishments,
            'examtypes' => $examtypes,
            'subjects' => $subjects,
            'reqsections' => $reqsections,
            'requirements' => $requirements,
            'compsections' => $compsections,
            'compsubsections' => $compsubsections,
            'competencies' => $competencies,
            'schoolkinds' => $schoolkinds,
            'schooltypes' => $schooltypes
        ]);
    }
    // Фильртация данных во взаимозависимых select'ах
    public function filter_list(Request $request){
        $result = [];
        $additional = [];
        $additional2 = [];
        if ($request->input('district')){
            $district = json_decode($request->input('district'));
            if ($district == -1){
                $result = DB::select('select * from governments');
                $additional = DB::select('select * from establishments');
            }else{
                $result = DB::select('select * from governments where DistrictCode = ?', [$district]);
                foreach($result as $item){
                    $additional = DB:: select('select * from establishments where GovernmentCode = ?', [$item->GovernmentCode]);
                }
            }
        }
        if ($request->input('government')){
            $government = json_decode($request->input('government'));
            if ($government == -1){
                $result = DB::select('select * from establishments');
            }else{
                $result = DB::select('select * from establishments where GovernmentCode = ?', [$government]);
                $additional = DB::select('select DistrictCode from governments where GovernmentCode = ?', [$government]);
            }
        }
        if ($request->input('school')){
            $school = json_decode($request->input('school'));
            if ($school != -1){
                $result = DB::select('select GovernmentCode from establishments where Code = ?', [$school]);
                $additional = DB::select('select distinct g.DistrictCode
                from establishments e join governments g on e.GovernmentCode=g.GovernmentCode
                where Code = ?', [$school]);
            }
        }
        if ($request->input('type')){
            $type = json_decode($request->input('type'));
            if ($type == -1){
                $result = DB::select('select sk.Code as SKCode, sk.Name as SKName
                from establishments e join schoolkinds sk on sk.Code=e.SchoolKindCode
                join governments g on e.GovernmentCode=g.GovernmentCode');
                $additional = DB::select('select e.Code, e.Name, e.GovernmentCode, g.DistrictCode, sk.Code as SKCode, sk.Name as SKName
                from establishments e join schoolkinds sk on sk.Code=e.SchoolKindCode
                join governments g on e.GovernmentCode=g.GovernmentCode');
            }else{
                $result = DB::select('select sk.Code as SKCode, sk.Name as SKName
                from establishments e join schoolkinds sk on sk.Code=e.SchoolKindCode
                join governments g on e.GovernmentCode=g.GovernmentCode
                where sk.SchoolType = ?', [$type]);
                $additional = DB::select('select e.Code, e.Name, e.GovernmentCode, g.DistrictCode, sk.Code as SKCode, sk.Name as SKName
                from establishments e join schoolkinds sk on sk.Code=e.SchoolKindCode
                join governments g on e.GovernmentCode=g.GovernmentCode
                where sk.SchoolType = ?', [$type]);
            }
        }
        if ($request->input('kind')){
            $kind = json_decode($request->input('kind'));
            if ($kind == -1){
                $additional = DB::select('select e.Code, e.Name, sk.SchoolType
                from establishments e join schoolkinds sk on e.SchoolKindCode=sk.Code');
            }else{
                $result = DB::select('select * from schoolkinds where Code = ?', [$kind]);
                $additional = DB::select('select e.Code, e.Name, e.SchoolKindCode, sk.SchoolType, g.GovernmentCode, g.DistrictCode
                from establishments e join schoolkinds sk on sk.Code=e.SchoolKindCode
                join governments g on e.GovernmentCode=g.GovernmentCode
                where sk.Code = ?', [$kind]);
            }
        }
        if ($request->input('examtype')){
            $examtype = json_decode($request->input('examtype'));
            if ($examtype == -1){
                $result = DB::select('select * from subjects');
            }else{
                $result = DB::select('select * from subjects where ExamTypeID = ?', [$examtype]);
            }
        }
        if ($request->input('subject')){
            $subject = json_decode($request->input('subject'));
            if ($subject == -1){
                $forreq = [];
                $forcomp = [];
                array_push($forreq, DB::select('select * from requirements where ReqCode REGEXP "^[0-9]$"'));
                array_push($forreq, DB::select('select * from requirements where ReqCode REGEXP "[0-9].[0-9]"'));
                array_push($forcomp, DB::select('select * from competencies where CompCode REGEXP "^[0-9]$"'));
                array_push($forcomp, DB::select('select * from competencies where CompCode REGEXP "^[0-9].[0-9]$"'));
                array_push($forcomp, DB::select('select * from competencies where CompCode REGEXP "[0-9].[0-9].[0-9]"'));
                $result = $forreq;
                $additional = $forcomp;
            }
            else{
                $forreq = [];
                $forcomp = [];
                array_push($forreq, DB::select('select ReqCode, Description, SectionCode, r.SchemaID 
                from requirements r join testschema t on r.SchemaID=t.SchemaID
                where SubjectID = ? and ReqCode REGEXP "^[0-9]$"', [$subject]));
                array_push($forreq, DB::select('select ReqCode, Description, SectionCode, r.SchemaID 
                from requirements r join testschema t on r.SchemaID=t.SchemaID
                where SubjectID = ? and ReqCode REGEXP "[0-9].[0-9]"', [$subject]));
                array_push($forcomp, DB::select('select CompCode, Description, SectionCode, r.SchemaID 
                from competencies r join testschema t on r.SchemaID=t.SchemaID
                where SubjectID = ? and CompCode REGEXP "^[0-9]$"', [$subject]));
                array_push($forcomp, DB::select('select CompCode, Description, SectionCode, r.SchemaID 
                from competencies r join testschema t on r.SchemaID=t.SchemaID
                where SubjectID = ? and CompCode REGEXP "^[0-9].[0-9]$"', [$subject]));
                array_push($forcomp, DB::select('select CompCode, Description, SectionCode, r.SchemaID 
                from competencies r join testschema t on r.SchemaID=t.SchemaID
                where SubjectID = ? and CompCode REGEXP "[0-9].[0-9].[0-9]"', [$subject]));
                $result = $forreq;
                $additional = $forcomp;
            }
        }
        if ($request->input('section')){
            $section = json_decode($request->input('section'));
            if ($section == -1){
                $result = DB::select('select * from requirements where ReqCode REGEXP "[0-9].[0-9]"');
            }else{
                $result = DB:: select('select * from requirements where SectionCode = ?', [$section]);
            }
        }
        if ($request->input('req')){
            $req = $request->input('req');
            if ($req != -1){
                $result = DB::select('select r.ReqCode
                from requirements r join requirements rR on r.ReqCode=rR.SectionCode
                where rR.ReqCode = ?', [$req]);
            }
        }
        if ($request->input('sectioncomp')){
            $section = json_decode($request->input('sectioncomp'));
            if ($section == -1){
                $result = DB::select('select * from competencies where CompCode REGEXP "^[0-9].[0-9]$"');
                $additional = DB::select('select * from competencies where CompCode REGEXP "[0-9].[0-9].[0-9]"');
            }else{
                $result = DB::select('select * from competencies where SectionCode = ?', [$section]);
                foreach($result as $item){
                    $additional = DB::select('select * from competencies where SectionCode = ?', [$item->CompCode]);
                }
            }
        }
        if ($request->input('subsection')){
            $subsection = json_decode($request->input('subsection'));
            if ($subsection == -1){
                $result = DB::select('select * from competencies where CompCode REGEXP "[0-9].[0-9].[0-9]"');
            }else{
                $result = DB::select('select * from competencies where SectionCode = ?', [$subsection]);
                $additional = DB::select('select SectionCode from competencies where CompCode = ?', [$subsection]);
            }
        }
        if ($request->input('compet')){
            $compet = $request->input('compet');
            if ($compet != -1){
                $result = DB::select('select SectionCode from competencies where CompCode = ?', [$compet]);
                $additional = DB::select('select c.SectionCode
                from competencies c join competencies cR on c.CompCode=cR.SectionCode
                where cR.CompCode = ?', [$compet]);
            }
        }

        return response()->json([
            'result' => $result,
            'additional' => $additional,
            'additional2' => $additional2
            ]);
    }
}
