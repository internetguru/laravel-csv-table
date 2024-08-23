<?php

namespace Internetguru\CsvTable\Components;

use Internetguru\CsvTable\Traits\HandleCSV;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CsvTable extends Component
{
    use HandleCSV;

    #[Locked]
    public $originalData;

    #[Locked]
    public $data;

    #[Locked]
    public $editableColumns;

    #[Locked]
    public $hiddenColumns;

    #[Locked]
    public $sortColumn = null;

    #[Locked]
    public $sortDirection = 'asc';

    #[Locked]
    public $showRoute;

    public function mount($showRoute = null, $filePath = null, $csvProviderFunction = null, $editableColumns = [], $hiddenColumns = [])
    {
        if (! $filePath && ! $csvProviderFunction) {
            throw new \Exception('Either filePath or csvProviderFunction must be provided.');
        }
        $this->editableColumns = $editableColumns;
        $this->hiddenColumns = $hiddenColumns;
        $this->showRoute = $showRoute;
        if ($csvProviderFunction) {
            $fnParts = explode('@', $csvProviderFunction);
            $csvProviderFunction = [app($fnParts[0]), $fnParts[1]];
            $this->data = $this->parseCsv($csvProviderFunction()->getContent());
        } else {
            $this->data = $filePath ? $this->readCSV($filePath) : [];
        }
        $this->originalData = $this->data;
    }

    #[On('updateColValue')]
    public function updateColValue($column, $row, $value)
    {
        // Default implementation for updating values
        $this->data[$row][$column] = $value;
    }

    public function sort($column)
    {
        // Reset sorting if sort column is clicked three times
        if ($this->sortDirection == 'asc' && $this->sortColumn == $column) {
            $this->sortColumn = null;
            $this->data = $this->originalData;

            return;
        }
        // Reset sort direction if column is changed
        if ($this->sortColumn !== $column) {
            $this->reset('sortDirection');
        }
        // Sort data
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortColumn = $column;
        $this->data = collect($this->data)->sortBy(function ($row) {
            // Sort by ASCII representation of the column value
            return iconv('UTF-8', 'ASCII//TRANSLIT', $row[$this->sortColumn]);
        }, SORT_REGULAR, $this->sortDirection === 'asc')->values()->toArray();
    }

    public function render()
    {
        return view('csv-table::livewire.csv-table');
    }
}
