<div>
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                @foreach (array_keys($data[0]) as $header)
                    <th scope="col" wire:click="sort('{{ $header }}')">
                        {{ $header }}
                        @if ($sortColumn == $header)
                            @if ($sortDirection === 'asc')
                                <i class="fa-solid fa-arrow-up"></i>
                            @else
                                <i class="fa-solid fa-arrow-down"></i>
                            @endif
                        @else
                            <i class="fa-solid fa-arrows-up-down"></i>
                        @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $rowIndex => $row)
                <tr>
                    @foreach ($row as $columnName => $cell)
                        <td>
                            @php
                                $editableCol = $editableColumns[$columnName] ?? $editableColumns[$loop->index] ?? false;
                            @endphp
                            @if ($editableCol)
                                <x-ig::input
                                    type="{{ $editableCol->value }}"
                                    wire:change="dispatch('updateColValue', {
                                        column: '{{ $columnName }}',
                                        row: {{ $rowIndex }},
                                        value: $event.target.value,
                                    })"
                                    value="{{ $cell }}"
                                    name="{{ Str::slug($columnName) }}"
                                >{{ $columnName }}</x-ig::input>
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
