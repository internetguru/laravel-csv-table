<div>
    <table>
        <thead>
            <tr>
                @foreach (array_keys($data[0]) as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $rowIndex => $row)
                <tr>
                    @foreach ($row as $columnName => $cell)
                        <td>
                            @if (isset($booleanColumns[$columnName]) && $booleanColumns[$columnName])
                                <input
                                    type="checkbox"
                                    {{ $cell ? 'checked' : '' }}
                                    wire:change="$emit('updateBooleanValue', {{ $rowIndex }}, '{{ $columnName }}', $event.target.checked, 'updateBooleanValue')"
                                >
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <button wire:click="downloadCSV">Download CSV</button>
</div>
