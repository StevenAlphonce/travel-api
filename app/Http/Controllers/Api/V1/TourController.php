<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request, Travel $travel)
    {
        $tours = $travel->tours()
            ->when($request->filled('priceFrom'), function ($query) use ($request) {
                $query->where('price', '>=', $this->priceToCents($request->input('priceFrom')));
            })
            ->when($request->filled('priceTo'), function ($query) use ($request) {
                $query->where('price', '<=', $this->priceToCents($request->input('priceTo')));
            })
            ->when($request->filled('dateFrom'), function ($query) use ($request) {
                $query->where('start_date', '>=', $request->input('dateFrom'));
            })
            ->when($request->filled('dateTo'), function ($query) use ($request) {
                $query->where('start_date', '<=', $request->input('dateTo'));
            });

        if ($request->input('sortBy') === 'price') {
            $tours->orderBy('price', $request->input('sortDirection') === 'desc' ? 'desc' : 'asc');
        }

        $tours = $tours->orderBy('start_date')->paginate(10);

        return TourResource::collection($tours);

    }

    private function priceToCents(mixed $price): int
    {
        return (int) round(((float) $price) * 100);
    }
}
