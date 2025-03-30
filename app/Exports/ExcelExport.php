<?php

namespace App\Exports;

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ExcelExport implements FromView
{
    protected $fieldData;

    public function __construct(array $fieldData)
    {
        $this->filedData = $fieldData;
    }

    public function view(): View
    {
        $data = $this->filedData;
        return view('pages.export.form_data', $data);
    }
}