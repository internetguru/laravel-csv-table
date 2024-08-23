<div wire:lazy>
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                @foreach (array_keys($data[0]) as $header)
                    @php
                        $hiddenCol = $hiddenColumns[$header] ?? $hiddenColumns[$loop->index] ?? false;
                    @endphp
                    @if ($hiddenCol !== false)
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
                <tr>
                    @foreach ($row as $columnName => $cell)
                        @php
                            $hiddenCol = $hiddenColumns[$columnName] ?? $hiddenColumns[$loop->index] ?? false;
                        @endphp
                        @if ($hiddenCol !== false)
                            @continue
                        @endif
                        <td>
                            @php
                                $editableCol = $editableColumns[$columnName] ?? $editableColumns[$loop->index] ?? false;
                            @endphp
                            @if ($editableCol !== false)
                                <x-ig::input
                                    type="{{ $editableCol->value }}"
                                    wire:change="dispatch('updateColValue', {
                                        column: '{{ $columnName }}',
                                        row: {{ $rowIndex }},
                                        value: $event.target.value,
                                    })"
                                    name="{{ Str::slug($columnName) }}"
                                    value="{{ $cell }}"
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
