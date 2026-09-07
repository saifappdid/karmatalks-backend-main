{{-- @if (strpos(Route::currentRouteName(), 'sliders') != '')
    <button onclick="window.location.href='{{ route($route) }}?table[filters][status]=active'"
        id="active-button-component" @class([
            'form-control w-25 btn-outline-success text-white mx-1 btn-sm',
            'btn-success' => $this->getAppliedFilters()['status'] == 'active',
            'text-secondary',
        ])>Active</button>
    <button onclick="window.location.href='{{ route($route) }}?table[filters][status]=blocked'"
        id="blocked-button-component" @class([
            'form-control w-25 btn-outline-success text-white btn-sm',
            'btn-success' => $this->getAppliedFilters()['status'] == 'blocked',
            'text-secondary',
        ])>Blocked</button>
@else --}}

{{-- @dump(strpos(Route::currentRouteName(), 'sliders') != '') --}}

<ul class="nav ml-1 nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item w-50">
        <a @class([
            'nav-link',
            'active' => $this->getAppliedFilters()['status'] == 'active',
            'bg-light' => $this->getAppliedFilters()['status'] == 'active',
        ]) id="home-tab"
            wire:click="status('active')"
            data-toggle="tab">Active</a>
    </li>
    <li class="nav-item w-50">
        <a @class([
            'nav-link',
            'active' => $this->getAppliedFilters()['status'] == 'blocked',
            'bg-light' => $this->getAppliedFilters()['status'] == 'blocked',
        ]) wire:click="status('blocked')"
            id="profile-tab" 
            data-toggle="tab">Inactive</a>
    </li>
</ul>


{{-- @endif --}}