<?php

namespace Internetguru\CsvTable\Components;

use Internetguru\CsvTable\Traits\HandleCSV;
use Livewire\Attributes\On;
use Livewire\Component;

class CsvTable extends Component
{
    use HandleCSV;

    public $data;

    public $booleanColumns;

    public function mount($csvUrl = null, $csvProviderFunction = null, $booleanColumns = [])
    {
        if (is_null($csvUrl) && is_null($csvProviderFunction)) {
            throw new \Exception('Either csvUrl or csvProviderFunction must be provided');
        }
        $this->booleanColumns = $booleanColumns;
        $functionData = explode('@', $csvProviderFunction);
        $args = explode(',', $functionData[1]);
        $this->data = $csvProviderFunction ? call_user_func($functionData[0], ...$args) : $this->readCSV($csvUrl);
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
