@aware(['component'])

@php
    $theme = $component->getTheme();
@endphp

@if ($component->hasConfigurableAreaFor('before-toolbar'))
    @include(
        $component->getConfigurableAreaFor('before-toolbar'),
        $component->getParametersForConfigurableArea('before-toolbar'))
@endif

@if ($theme === 'tailwind')
@elseif ($theme === 'bootstrap-4')
    <style>
        @media only screen and (max-width: 1020px) {
            .sm-d-none {
                display: none;
            }
        }
    </style>

    <div class="row justify-content-between mb-1">
        {{-- <div class=""> --}}
        <div class="col-md-7">
            <div class="d-md-flex justify-content-start">
                @if ($component->hasConfigurableAreaFor('toolbar-left-start'))
                    @include(
                        $component->getConfigurableAreaFor('toolbar-left-start'),
                        $component->getParametersForConfigurableArea('toolbar-left-start'))
                @endif

                @if ($component->reorderIsEnabled())
                    <div class="mr-0 mr-md-2 mb-3 mb-md-0">
                        <button
                            wire:click="{{ $component->currentlyReorderingIsEnabled() ? 'disableReordering' : 'enableReordering' }}"
                            type="button" class="btn btn-default d-block w-100 d-md-inline">
                            @if ($component->currentlyReorderingIsEnabled())
                                @lang('Done Reordering')
                            @else
                                @lang('Reorder')
                            @endif
                        </button>
                    </div>
                @endif

                @if ($component->searchIsEnabled() && $component->searchVisibilityIsEnabled())
                    <div class="mb-1 mx-0 mb-md-1 input-group col-md-3 pl-0">
                        <input wire:model{{ $component->getSearchOptions() }}="{{ $component->getTableName() }}.search"
                            placeholder="{{ __('Search') }}" type="text" class="form-control  border-primary">

                        @if ($component->hasSearch())
                            <div class="input-group-append">
                                <button wire:click.prevent="clearSearch" class="btn btn-outline-secondary"
                                    type="button">
                                    <svg style="width:.75em;height:.75em" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($component->paginationIsEnabled() && $component->perPageVisibilityIsEnabled())
                    <div class="ml-0 sm-d-none" style="width: 65px !important">
                        <select wire:model="perPage" id="perPage"
                            class="form-control btn-primary waves-effect text-white cursor-pointer">
                            @foreach ($component->getPerPageAccepted() as $item)
                                <option value="{{ $item }}"
                                    wire:key="per-page-{{ $item }}-{{ $component->getTableName() }}">
                                    {{ $item === -1 ? __('All') : $item }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($component->filtersAreEnabled() && $component->filtersVisibilityIsEnabled() && $component->hasVisibleFilters())
                    <div class="ml-1 mb-1 mb-md-0 sm-d-none">
                        <div @if ($component->isFilterLayoutPopover()) x-data="{ open: false }"
                            x-on:keydown.escape.stop="open = false"
                            x-on:mousedown.away="open = false" @endif
                            class="btn-group d-block d-md-inline">
                            <div>
                                <button type="button"
                                    class="btn btn-primary text-white   dropdown-toggle d-block w-100 d-md-inline"
                                    @if ($component->isFilterLayoutPopover()) x-on:click="open = !open"
                                    aria-haspopup="true"
                                    x-bind:aria-expanded="open"
                                    aria-expanded="true" @endif
                                    @if ($component->isFilterLayoutSlideDown()) x-on:click="filtersOpen = !filtersOpen" @endif>
                                    @lang('Filters')

                                    @if ($count = $component->getFilterBadgeCount())
                                        <span class="badge badge-info" style="padding: 0.1rem 0.3rem;">
                                            {{ $count }}
                                        </span>
                                    @endif

                                    <span class="caret"></span>
                                </button>
                            </div>

                            @if ($component->isFilterLayoutPopover())
                                <ul x-cloak class="dropdown-menu w-100 mt-md-1" style="width: 30rem !important;"
                                    x-bind:class="{ 'show': open }" role="menu">
                                    @foreach ($component->getFilters() as $filter)
                                        @if ($filter->isVisibleInMenus())
                                            <div wire:key="{{ $component->getTableName() }}-filter-{{ $filter->getKey() }}"
                                                class="px-2">
                                                <label
                                                    for="{{ $component->getTableName() }}-filter-{{ $filter->getKey() }}"
                                                    class="">
                                                    {{ $filter->getName() }}
                                                </label>

                                                {{ $filter->render($component) }}
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        @endif
                                    @endforeach

                                    @if ($component->hasAppliedVisibleFiltersWithValuesThatCanBeCleared())
                                        {{-- <div class="dropdown-divider"></div> --}}

                                        <button wire:click.prevent="setFilterDefaults" x-on:click="open = false"
                                            class="dropdown-item btn text-center">
                                            @lang('Clear')
                                        </button>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($component->hasConfigurableAreaFor('toolbar-left-end'))
                    @include(
                        $component->getConfigurableAreaFor('toolbar-left-end'),
                        $component->getParametersForConfigurableArea('toolbar-left-end'))
                @endif


            </div>
        </div>
        <div class="col-md-5">
            {{-- <div class="d-md-flex justify-content-end"> --}}

            <div class="row justify-content-end">
                @if ($component->hasConfigurableAreaFor('toolbar-right-start'))
                    <div class="col-md-4 col-sm-5 col-5">
                        @include(
                            $component->getConfigurableAreaFor('toolbar-right-start'),
                            $component->getParametersForConfigurableArea('toolbar-right-start'))
                    </div>
                @endif
                <button wire:click="refresh" title="Refresh" class=" btn btn-rounded btn-sm bg-primary waves-effect btn-flat-primary refresh-btn"
                    style="fill:white;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                        <path
                            d="M89.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L370.3 160H320c-17.7 0-32 14.3-32 32s14.3 32 32 32H447.5c0 0 0 0 0 0h.4c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v51.2L398.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C57.2 122 39.6 150.7 28.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM23 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1V448c0 17.7 14.3 32 32 32s32-14.3 32-32V396.9l17.6 17.5 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L109.6 352H160c17.7 0 32-14.3 32-32s-14.3-32-32-32H32.4c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z" />
                    </svg>
                </button>
                @if ($component->showBulkActionsDropdown())
                    <div class="col-md-3 col-sm-5 col-5">
                        <div class="mb-3 mb-md-0">
                            <div class="dropdown d-block d-md-inline">



                                <button class="btn btn-primary text-white   dropdown-toggle d-block w-100 d-md-inline"
                                    type="button" id="{{ $component->getTableName() }}-bulkActionsDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @lang('Bulk')
                                </button>

                                <div class="dropdown-menu dropdown-menu-right w-100"
                                    aria-labelledby="{{ $component->getTableName() }}-bulkActionsDropdown">
                                    @foreach ($component->getBulkActions() as $action => $title)
                                        @if ($action == 'massDelete')
                                            <p wire:click="{{ $action }}" class="d-none" id="clickforDelete"></p>

                                            <a href="#" onclick="wireClickFunctionForDelete()"
                                                wire:key="bulk-action-{{ $action }}-{{ $component->getTableName() }}"
                                                class="dropdown-item">
                                                {{ $title }}
                                            </a>
                                        @else
                                            <a href="#" wire:click="{{ $action }}"
                                                wire:key="bulk-action-{{ $action }}-{{ $component->getTableName() }}"
                                                class="dropdown-item">
                                                {{ $title }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($component->columnSelectIsEnabled())
                    <div class="col-md-4 col-sm-5 col-5 p-0">
                        <div
                            class="@if ($component->getColumnSelectIsHiddenOnMobile()) d-none d-sm-block @elseif ($component->getColumnSelectIsHiddenOnTablet()) d-none d-md-block @endif">


                            <div x-data="{ open: false }" x-on:keydown.escape.stop="open = false"
                                x-on:mousedown.away="open = false" class="dropdown d-block d-md-inline"
                                wire:key="column-select-button-{{ $component->getTableName() }}">
                                <button x-on:click="open = !open"
                                    class="btn btn-primary text-white dropdown-toggle border d-block w-100 d-md-inline"
                                    type="button" id="columnSelect-{{ $component->getTableName() }}"
                                    aria-haspopup="true" x-bind:aria-expanded="open">
                                    @lang('Columns')
                                </button>

                                <div class="dropdown-menu dropdown-menu-right w-100 mt-0 mt-md-2"
                                    x-bind:class="{ 'show': open }" style="width: 13rem !important;transition:1s"
                                    aria-labelledby="columnSelect-{{ $component->getTableName() }}">
                                    <div>
                                        <label wire:loading.attr="disabled" class="px-2">
                                            <input
                                                @if ($component->allDefaultVisibleColumnsAreSelected()) checked
                                            wire:click="deselectAllColumns"
                                        @else
                                            unchecked
                                            wire:click="selectAllColumns" @endif
                                                wire:loading.attr="disabled" type="checkbox" />
                                            <span class="ml-2">{{ __('All Columns') }}</span>
                                        </label>
                                    </div>
                                    @foreach ($component->getColumns() as $column)
                                        @if ($column->isVisible() && $column->isSelectable())
                                            <div
                                                wire:key="columnSelect-{{ $loop->index }}-{{ $component->getTableName() }}">
                                                <label wire:loading.attr="disabled" wire:target="selectedColumns"
                                                    class="px-2 {{ $loop->last ? 'mb-0' : 'mb-1' }}">
                                                    <input wire:model="selectedColumns" wire:target="selectedColumns"
                                                        wire:loading.attr="disabled" type="checkbox"
                                                        value="{{ $column->getSlug() }}" />
                                                    <span class="ml-2">{{ $column->getTitle() }}</span>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($component->hasConfigurableAreaFor('toolbar-right-end'))
                    <div class="col-md-2 col-sm-2 col-2 my-auto">
                        @include(
                            $component->getConfigurableAreaFor('toolbar-right-end'),
                            $component->getParametersForConfigurableArea('toolbar-right-end'))
                    </div>
                @endif
            </div>
            {{-- </div> --}}
            {{-- </div> --}}

            @if (
                $component->filtersAreEnabled() &&
                    $component->filtersVisibilityIsEnabled() &&
                    $component->hasVisibleFilters() &&
                    $component->isFilterLayoutSlideDown())
                <div x-cloak x-show="filtersOpen">
                    <div class="container">
                        <div class="row">
                            @foreach ($component->getFilters() as $filter)
                                @if ($filter->isVisibleInMenus())
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                        <label for="{{ $component->getTableName() }}-filter-{{ $filter->getKey() }}"
                                            class="d-block">
                                            {{ $filter->getName() }}
                                        </label>

                                        {{ $filter->render($component) }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @elseif ($theme === 'bootstrap-5')
@endif

@if ($component->hasConfigurableAreaFor('after-toolbar'))
    @include(
        $component->getConfigurableAreaFor('after-toolbar'),
        $component->getParametersForConfigurableArea('after-toolbar'))
@endif


<script>
    function wireClickFunctionForDelete() {
        const confirmValid = confirm('wanna delete')
        if (confirmValid) {
            $('#clickforDelete').click();
        }
    }
</script>
