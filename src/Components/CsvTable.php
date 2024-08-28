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
    public $rowClassColumn;

    #[Locked]
    public $sortColumn = null;

    #[Locked]
    public $sortDirection = 'asc';

    #[Locked]
    public $dataNumKeyToColKey = [];

    #[Locked]
    public $dataColKeyToNumKey = [];

    public function mount(
        $csvFilePath = null,
        $csvProviderFunction = null,
        $editableColumns = [],
        $hiddenColumns = [],
        $rowClassColumn = null
    ) {
        if (! $csvFilePath && ! $csvProviderFunction) {
            throw new \Exception('Either csvFilePath or csvProviderFunction must be provided.');
        }
        if ($csvProviderFunction) {
            $fnParts = explode('@', $csvProviderFunction);
            $csvProviderFunction = [app($fnParts[0]), $fnParts[1]];
            $this->data = $this->parseCsv($csvProviderFunction()->getContent());
        } else {
            $this->data = $csvFilePath ? $this->readCSV($csvFilePath) : [];
        }
        $this->editableColumns = collect(array_map(function ($column) {
            $column['column'] = $this->normalizeColumnKey($column['column']);

            return $column; // You need to return the modified $column
        }, $editableColumns));
        $this->dataNumKeyToColKey = array_flip(array_keys(current($this->data)));
        $this->dataColKeyToNumKey = array_flip($this->dataNumKeyToColKey);
        $this->hiddenColumns = collect($this->normalizeColumnKeys($hiddenColumns));
        $this->rowClassColumn = $this->normalizeColumnKey($rowClassColumn);
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

    public function normalizeColumnKeys($keys)
    {
        return array_map(function ($key) {
            return $this->normalizeColumnKey($key);
        }, $keys);
    }

    /**
     * Normalize column key to match the key in the data array
     *
     * @param  string|int  $key
     * @return string|null
     */
    public function normalizeColumnKey($key)
    {
        // try to use the key as is
        if (isset(current($this->data)[$key])) {
            return $key;
        }
        // try to find the key by index
        $keys = array_keys(current($this->data));

        return $keys[$key] ?? null;
    }
}
