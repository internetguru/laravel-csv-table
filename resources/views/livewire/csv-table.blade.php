<div wire:lazy>
    <table class="table">
        <thead class="table-light">
            <tr>
                @foreach (array_keys($data[0]) as $header)
                    @if ($hiddenColumns->contains($header))
                        @continue
                    @endif
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
                <tr @if ($row[$rowClassColumn] ?? false) class="{{ $row[$rowClassColumn] }};" @endif>
                    @foreach ($row as $columnName => $cell)
                        @if ($hiddenColumns->contains($columnName))
                            @continue
                        @endif
                        <td>
                            @php
                                $editableColumn = $editableColumns->first(fn($col) => $col['column'] === $columnName);
                            @endphp
                            @if ($editableColumn)
                                @switch($editableColumn['type'])
                                    @case(\Internetguru\CsvTable\Enums\ColType::SELECT)
                                        <x-ig::input
                                            type="select"
                                            wire:change="dispatch('updateColValue', {
                                                column: '{{ $columnName }}',
                                                row: {{ $rowIndex }},
                                                value: $event.target.value,
                                            })"
                                            name="{{ Str::slug($columnName) }}"
                                            :options="$editableColumn['options']"
                                            :value="$cell"
                                        >{{ $columnName }}</x-ig::input>
                                        @break

                                    @default
                                        <x-ig::input
                                            type="{{ $editableColumn['type']->value }}"
                                            wire:change="dispatch('updateColValue', {
                                                column: '{{ $columnName }}',
                                                row: {{ $rowIndex }},
                                                value: $event.target.value,
                                            })"
                                            name="{{ Str::slug($columnName) }}"
                                            value="{{ $cell }}"
                                        >{{ $columnName }}</x-ig::input>
                                @endswitch
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
