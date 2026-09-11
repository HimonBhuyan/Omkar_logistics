<form method="{{ $method ?? 'GET' }}" action="{{ $actionUrl }}" id="{{ $formId ?? 'biltyFilterForm' }}">
    @if(isset($csrf) && $csrf) @csrf @endif
    <input type="hidden" name="search_submitted" value="1">
    
    <div class="filter-section">
        <div class="filter-grid">
            
            <div class="filter-group">
                <label for="consignor_text">CONSIGNOR</label>
                <div class="autocomplete-wrapper">
                    <input type="text" name="consignor_name" id="consignor_text" class="autocomplete-input" placeholder="TYPE CONSIGNOR NAME..." autocomplete="off" value="{{ request('consignor_name') }}">
                    <div class="autocomplete-dropdown" id="consignor_dropdown"></div>
                </div>
            </div>

            <div class="filter-group">
                <label for="consignee_text">CONSIGNEE</label>
                <div class="autocomplete-wrapper">
                    <input type="text" name="consignee_name" id="consignee_text" class="autocomplete-input" placeholder="TYPE CONSIGNEE NAME..." autocomplete="off" value="{{ request('consignee_name') }}">
                    <div class="autocomplete-dropdown" id="consignee_dropdown"></div>
                </div>
            </div>

            <div class="filter-group">
                <label for="billing_party_text">PARTY</label>
                <div class="autocomplete-wrapper">
                    <input type="text" name="billing_party_name" id="billing_party_text" class="autocomplete-input" placeholder="TYPE PARTY NAME..." autocomplete="off" value="{{ request('billing_party_name') }}">
                    <div class="autocomplete-dropdown" id="billing_party_dropdown"></div>
                </div>
            </div>

            @if($showDates ?? true)
            <div class="filter-group">
                <label for="from_date">FROM</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date', date('Y-m-d')) }}">
            </div>

            <div class="filter-group">
                <label for="to_date">TO</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date', date('Y-m-d')) }}">
            </div>
            @endif

        </div>

        <div class="filter-grid" style="margin-top: 8px;">
            <div class="filter-group">
                <label for="from_location_text">FROM LOC.</label>
                <div class="autocomplete-wrapper">
                    <input type="text" name="from_location_name" id="from_location_text" class="autocomplete-input" placeholder="TYPE CITY NAME..." autocomplete="off" value="{{ request('from_location_name') }}">
                    <div class="autocomplete-dropdown" id="from_location_dropdown"></div>
                </div>
            </div>

            <div class="filter-group">
                <label for="to_location_text">TO LOC.</label>
                <div class="autocomplete-wrapper">
                    <input type="text" name="to_location_name" id="to_location_text" class="autocomplete-input" placeholder="TYPE CITY NAME..." autocomplete="off" value="{{ request('to_location_name') }}">
                    <div class="autocomplete-dropdown" id="to_location_dropdown"></div>
                </div>
            </div>

            <div class="filter-group" style="max-width: 125px;">
                <label for="series" style="min-width: unset; font-size: 11px;">SERIES</label>
                @php
                    if (!isset($seriesList)) {
                        try {
                            $seriesList = \App\Models\Series::orderBy('name', 'asc')->get();
                        } catch (\Throwable $e) {
                            $seriesList = collect();
                        }
                    }
                @endphp
                <select name="series" id="series" style="width: 70px; font-size: 11px; height: 24px; border: 1px solid #7f9db9; padding: 1px 3px;">
                    <option value="">-- ALL --</option>
                    @foreach($seriesList as $s)
                        <option value="{{ $s->name }}" {{ request('series') == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" style="max-width: 190px;">
                <label for="vehicle_no" style="min-width: unset; font-size: 11px;">VEHICLE NO.</label>
                <input type="text" name="vehicle_no" id="vehicle_no" value="{{ request('vehicle_no') }}" placeholder="SEARCH VEHICLE NO." style="width: 115px; font-size: 11px;">
            </div>

            <div class="filter-group" style="max-width: 175px;">
                <label for="shipping_status_filter" style="min-width: unset; font-size: 11px;">SHIP STATUS</label>
                @php
                    try {
                        $allStatuses = \App\Models\ShippingStatus::orderBy('id')->pluck('name')->toArray();
                    } catch (\Throwable $e) {
                        $allStatuses = [];
                    }
                    if (empty($allStatuses)) {
                        $allStatuses = ['Booked', 'Shipped', 'In Transit', 'Delivered'];
                    }
                @endphp
                <select name="shipping_status" id="shipping_status_filter" style="height:24px; font-size:11px; border:1px solid #7f9db9; padding:2px 4px; width:100px; max-width:105px;">
                    <option value="">-- ALL --</option>
                    @foreach($allStatuses as $st)
                        <option value="{{ $st }}" {{ request('shipping_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-search">
                    🔍 SEARCH
                </button>
                @if($showClear ?? true)
                <button type="button" onclick="{{ $onClear ?? 'clearFilters(event)' }}" class="btn-clear" title="Clear search filters preserving date range">
                    🧹 CLEAR
                </button>
                @endif
            </div>
        </div>

        @if($showBillingMode ?? true)
        <div class="filter-checkboxes">
            <span style="font-weight:700;">BILLING MODE:</span>
            <label class="checkbox-item">
                <input type="checkbox" name="mop_paid" value="1" {{ ($mopPaidChecked ?? true) ? 'checked' : '' }}> PAID
            </label>
            <label class="checkbox-item">
                <input type="checkbox" name="mop_topay" value="1" {{ ($mopTopayChecked ?? true) ? 'checked' : '' }}> TO PAY
            </label>
            <label class="checkbox-item">
                <input type="checkbox" name="mop_tbb" value="1" {{ ($mopTbbChecked ?? true) ? 'checked' : '' }}> T.B.B.
            </label>
        </div>
        @endif
    </div>
</form>
