<?php

namespace Internetguru\CsvTable\Enums;

enum ColType: string
{
    case DATE = 'date';
    case NUMBER = 'number';
    case TEXT = 'text';
    case SELECT = 'select';
}
