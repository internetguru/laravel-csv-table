<?php

namespace Internetguru\CsvTable;

use Illuminate\Support\ServiceProvider;
use Internetguru\CsvTable\Components\CsvTable;
use Livewire\Livewire;

class CsvTableServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'csv-table');

        Livewire::component('csv-table', CsvTable::class);
    }
}
