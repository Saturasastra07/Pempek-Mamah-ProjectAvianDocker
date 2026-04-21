<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'receiver_name' => 'required',
            'phone_number' => 'required|numeric',
            'full_address' => 'required',
            'district' => 'required',
            'city' => 'required',
        ]);

        $isFirst = Address::where('user_id', auth()->id())->count() == 0;
        $fullAddress = $request->full_address . ', ' . $request->district . ', ' . $request->city . ', Indonesia';

        $lat = null;
        $lng = null;

        try {
            $response = Http::timeout(5)->withHeaders([
                'User-Agent' => 'PempekMamahDhaniApp/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $fullAddress,
                'format' => 'json',
                'limit'  => 1
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data)) {
                $lat = $data[0]['lat'];
                $lng = $data[0]['lon'];
            }

        } catch (\Exception $e) {
            // Gagal geocode, lat/lng tetap null, lanjut simpan
            \Log::warning('Geocode gagal: ' . $e->getMessage());
        }

        Address::create([
            'user_id'       => auth()->id(),
            'label'         => $request->label,
            'receiver_name' => $request->receiver_name,
            'phone_number'  => $request->phone_number,
            'full_address'  => $request->full_address,
            'district'      => $request->district,
            'city'          => $request->city,
            'lat'           => $lat,
            'lng'           => $lng,
            'is_default'    => $isFirst ? 1 : 0,
        ]);

        if ($request->has('redirect_to')) {
            return redirect()->route($request->redirect_to)->with('success', 'Alamat Berhasil Disimpan!');
        }

        return redirect()->route('profile')->with('success', 'Alamat Berhasil Disimpan!');
    }

    public function setDefault($id) {
        \App\Models\Address::where('user_id', auth()->id())->update(['is_default' => 0]);
        \App\Models\Address::where('id', $id)->where('user_id', auth()->id())->update(['is_default' => 1]);

        $allAddresses = \App\Models\Address::where('user_id', auth()->id())->orderByDesc('is_default')->get();
        return response()->json([
            'success' => true,
            'addresses' => $allAddresses
        ]);
    }

    public function update(Request $request, $id) {
        $address = Address::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $fullAddress = $request->full_address . ', ' . $request->district . ', ' . $request->city;

        $lat = null;
        $lng = null;

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'PempekMamahDhaniApp'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $fullAddress,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $lat = $response->json()[0]['lat'];
                $lng = $response->json()[0]['lon'];
            }
        } catch (\Exception $e) {}

        $address->update([
            'label' => $request->label,
            'receiver_name' => $request->receiver_name,
            'phone_number' => $request->phone_number,
            'full_address' => $request->full_address,
            'district' => $request->district,
            'city' => $request->city,
            'lat' => $lat,
            'lng' => $lng,
        ]);

        if ($request->has('redirect_to')) {
            return redirect()->route($request->redirect_to)->with('success', 'Alamat berhasil diperbarui!');
        }
        
        return redirect()->route('profile')->with('success', 'Alamat berhasil diperbarui!');
    }
}