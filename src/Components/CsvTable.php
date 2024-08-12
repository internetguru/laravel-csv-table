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

    public function mount($filePath = null, $csvProviderFunction = null, $booleanColumns = [])
    {
        if (! $filePath && ! $csvProviderFunction) {
            throw new \Exception('Either filePath or csvProviderFunction must be provided.');
        }
        $this->booleanColumns = $booleanColumns;
        if ($csvProviderFunction) {
            $fnParts = explode('@', $csvProviderFunction);
            $csvProviderFunction = [app($fnParts[0]), $fnParts[1]];
            $this->data = $this->parseCsv($csvProviderFunction()->getContent());
        } else {
            $this->data = $filePath ? $this->readCSV($filePath) : [];
        }
    }

    public function download()
    {
        dd(1);

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
