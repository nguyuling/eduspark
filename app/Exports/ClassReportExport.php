<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassReportExport implements FromCollection, WithHeadings
{
    protected $rows;
    protected $classAverage;

    public function __construct(Collection $rows, $classAverage)
    {
        $this->rows = $rows;
        $this->classAverage = $classAverage;
    }

    public function collection()
    {
        $collection = $this->rows->map(function($r){
            return [
                $r['id'], $r['name'], number_format($r['average'],2),
                number_format($r['attendanceRate'],1), number_format($r['improvementRate'],1)
            ];
        })->values();

        $collection->push([]);
        $collection->push(['Class Average', number_format($this->classAverage,2)]);
        return new Collection($collection);
    }

    public function headings(): array
    {
        return ['ID','Name','Average Score','Attendance %','Improvement %'];
    }
}
