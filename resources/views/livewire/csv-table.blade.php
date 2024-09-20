<div wire:lazy>
    <div
        class="table-wrapper"
        x-on:fullscreen="
            const fullscreen = $event.detail.fullscreen
            $el.classList.toggle('fullscreen--active', fullscreen)
        "
    >
        <button
            class="fullscreen btn"
            x-data="{ fullscreen: false }"
            x-on:click.stop="fullscreen = !fullscreen; $dispatch('fullscreen', { fullscreen: fullscreen })"
            x-on:fullscreen.window="$el.classList.toggle('active', $event.detail.fullscreen)"
        >
            <span>
                <i class="fa-solid fa-fw fa-expand"></i>
            </span>
            <span>
                <i class="fa-solid fa-fw fa-compress"></i>
            </span>
        </button>
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
                    @php
                        $stepIndex = ($rowIndex / $lightDarkStep) % 2;
                        $lightDark = $stepIndex < 1 ? '' : 'table-light';
                        $rowClassColumnClass = ($rowClassColumn ?? false) ? $row[$rowClassColumn] : '';
                    @endphp
                    <tr class="{{ $rowClassColumnClass }} {{ $lightDark }}">
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
</div>
