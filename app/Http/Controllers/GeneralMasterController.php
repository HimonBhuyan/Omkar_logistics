<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\StateModel;
use App\Models\CityModel;
use App\Models\MeasurementUnit;

class GeneralMasterController extends Controller
{
    public function countryIndex($id = null)
    {
        $countries = Country::orderBy('name')->get();
        $selected = $id ? Country::findOrFail($id) : new Country();
        return view('master.country', compact('countries', 'selected'));
    }

    public function countryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:countries,name,' . $request->id,
            'code' => 'nullable|string|max:50',
        ]);
        if ($request->id) {
            Country::findOrFail($request->id)->update($data);
            return redirect()->route('master.country.load', $request->id)->with('success', 'Country updated successfully.');
        }
        $country = Country::create($data);
        return redirect()->route('master.country.load', $country->id)->with('success', 'Country added successfully.');
    }

    public function countryDestroy($id)
    {
        Country::findOrFail($id)->delete();
        return redirect()->route('master.country')->with('success', 'Country deleted.');
    }

    public function countryBulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Country::whereIn('id', $ids)->delete();
            return redirect()->route('master.country')->with('success', 'Selected countries deleted.');
        }
        return redirect()->route('master.country')->with('error', 'No countries selected.');
    }

    public function stateIndex(Request $request, $id = null)
    {
        $states = StateModel::with('countryRelation')->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        $selected = $id ? StateModel::findOrFail($id) : new StateModel([
            'code' => $request->query('code', ''),
        ]);
        return view('master.state', compact('states', 'countries', 'selected'));
    }

    public function stateStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:states,name,' . $request->id,
            'code'       => 'required|string|max:50|unique:states,code,' . $request->id,
            'short_name' => 'nullable|string|max:50',
            'country_id' => 'required|exists:countries,id',
        ], [
            'code.unique' => 'The state code ":input" is already taken by another state.',
            'name.unique' => 'The state name ":input" already exists.',
        ]);

        if ($request->id) {
            StateModel::findOrFail($request->id)->update($data);
            return redirect()->route('master.state.load', $request->id)->with('success', 'State updated successfully.');
        }
        $state = StateModel::create($data);
        return redirect()->route('master.state.load', $state->id)->with('success', 'State added successfully.');
    }

    public function stateDestroy($id)
    {
        StateModel::findOrFail($id)->delete();
        return redirect()->route('master.state')->with('success', 'State deleted.');
    }

    public function stateBulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            StateModel::whereIn('id', $ids)->delete();
            return redirect()->route('master.state')->with('success', 'Selected states deleted.');
        }
        return redirect()->route('master.state')->with('error', 'No states selected.');
    }

    public function cityIndex($id = null)
    {
        $cities = CityModel::with('stateRelation')->orderBy('name')->get();
        $states = StateModel::orderBy('name')->get();
        $selected = $id ? CityModel::findOrFail($id) : new CityModel();
        return view('master.city', compact('cities', 'states', 'selected'));
    }

    public function cityStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:cities,name,' . $request->id,
            'short_name' => 'nullable|string|max:50',
            'state_id'   => 'required|exists:states,id',
        ]);

        if ($request->id) {
            CityModel::findOrFail($request->id)->update($data);
            return redirect()->route('master.city.load', $request->id)->with('success', 'City updated successfully.');
        }
        $city = CityModel::create($data);
        return redirect()->route('master.city.load', $city->id)->with('success', 'City added successfully.');
    }

    public function cityDestroy($id)
    {
        CityModel::findOrFail($id)->delete();
        return redirect()->route('master.city')->with('success', 'City deleted.');
    }

    public function cityBulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            CityModel::whereIn('id', $ids)->delete();
            return redirect()->route('master.city')->with('success', 'Selected cities deleted.');
        }
        return redirect()->route('master.city')->with('error', 'No cities selected.');
    }

    public function measurementUnitIndex(Request $request, $id = null)
    {
        $query = MeasurementUnit::query();
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($sub) use ($q) {
                $sub->where('unit_code', 'like', "%{$q}%")
                    ->orWhere('unit_name', 'like', "%{$q}%");
            });
        }
        $units = $query->orderBy('unit_code')->get();
        $selected = $id ? MeasurementUnit::findOrFail($id) : new MeasurementUnit(['is_active' => true, 'unit_type' => 'weight']);
        return view('master.measurement_unit', compact('units', 'selected'));
    }

    public function measurementUnitStore(Request $request)
    {
        $data = $request->validate([
            'unit_code' => 'required|string|max:50|unique:measurement_units,unit_code,' . $request->id,
            'unit_name' => 'required|string|max:100',
            'unit_type' => 'required|string|in:weight,fixed',
            'package_label' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        if ($request->filled('package_label')) {
            $data['package_label'] = trim($request->package_label);
        } else {
            $uCode = strtoupper(trim($request->unit_code ?? ''));
            if (str_contains($uCode, 'BOX')) {
                $data['package_label'] = 'NoOfBoxes';
            } elseif (str_contains($uCode, 'CASE')) {
                $data['package_label'] = 'NoOfCases';
            } elseif (str_contains($uCode, 'PCS') || str_contains($uCode, 'PIECE')) {
                $data['package_label'] = 'NoOfPcs';
            } elseif (str_contains($uCode, 'BAG')) {
                $data['package_label'] = 'NoOfBags';
            } elseif (str_contains($uCode, 'DRUM')) {
                $data['package_label'] = 'NoOfDrums';
            } elseif (str_contains($uCode, 'CARTON')) {
                $data['package_label'] = 'NoOfCartons';
            } else {
                $data['package_label'] = 'NoOfPkgs';
            }
        }
        $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        if ($request->id) {
            $unit = MeasurementUnit::findOrFail($request->id);
            // Protect system units from changing unit_code
            if ($unit->is_system || in_array(strtoupper($unit->unit_code), ['KG', 'FIXED'])) {
                unset($data['unit_code']);
            }
            $unit->update($data);
            return redirect()->route('master.measurement-unit.load', $request->id)->with('success', 'Measurement Unit updated successfully.');
        }

        $unit = MeasurementUnit::create($data);
        return redirect()->route('master.measurement-unit.load', $unit->id)->with('success', 'Measurement Unit added successfully.');
    }

    public function measurementUnitDestroy($id)
    {
        $unit = MeasurementUnit::findOrFail($id);
        if ($unit->is_system || in_array(strtoupper($unit->unit_code), ['KG', 'FIXED'])) {
            return redirect()->route('master.measurement-unit')->with('error', 'System default units (KG and FIXED) cannot be deleted.');
        }
        $unit->delete();
        return redirect()->route('master.measurement-unit')->with('success', 'Measurement Unit deleted successfully.');
    }

    public function measurementUnitBulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            $deletedCount = MeasurementUnit::whereIn('id', $ids)
                ->where('is_system', false)
                ->whereNotIn(DB::raw('UPPER(unit_code)'), ['KG', 'FIXED'])
                ->delete();
            return redirect()->route('master.measurement-unit')->with('success', "{$deletedCount} custom measurement unit(s) deleted successfully.");
        }
        return redirect()->route('master.measurement-unit')->with('error', 'No measurement units selected.');
    }
}
