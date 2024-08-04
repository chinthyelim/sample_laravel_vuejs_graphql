<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Employee;

class EmployeeCollection extends ResourceCollection
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
                $item['company_logo'] = $item['logo'] ?? "";
                $item['company_name'] = $item['name'];
                $item['email'] = $item['email'] ?? "";
                $item['phone'] = $item['phone'] ?? "";
                return collect($item)
                    ->except('created_at', 'updated_at');
            }),
            'total_rows' => Employee::count(),
        ];
    }
}
