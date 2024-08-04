<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Company;

class CompanyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($item, $key) {
                $item['logo'] = $item['logo'] ?? "";
                $item['email'] = $item['email'] ?? "";
                $item['website'] = $item['website'] ?? "";
                return collect($item)
                    ->except('created_at', 'updated_at');
            }),
            'total_rows' => Company::count(),
        ];
    }
}
