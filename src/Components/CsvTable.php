<?php

namespace Internetguru\CsvTable\Components;

use Internetguru\CsvTable\Traits\HandleCSV;
use Livewire\Attributes\On;
use Livewire\Component;

class CsvTable extends Component
{
    use HandleCSV;

    public $filePath;

    public $data;

    public $booleanColumns;

    public function mount($filePath, $booleanColumns = [])
    {
        $this->filePath = $filePath;
        $this->booleanColumns = $booleanColumns;
        $this->data = $this->readCSV($filePath);
    }

    public function downloadCSV()
    {
        return $this->responseCsv($this->generateCSV($this->data));
    }

    #[On('updateBooleanValue')]
    public function updateBooleanValue($rowIndex, $columnName, $value)
    {
        // Default implementation for updating boolean values
        $this->data[$rowIndex][$columnName] = $value;
    }

    public function render()
    {
        return view('csv-table::livewire.csv-table');
    }
}
