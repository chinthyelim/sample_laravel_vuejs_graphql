<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeCollection;

class EmployeeController extends Controller
{
    public function list(): View
    {
        return view('employees');
    }

    public function index(Request $request)
    {
        if ($request->current_page_number && $request->rows_per_page) {
            $currentPage = $request->current_page_number;
            // Set the paginator to the current page
            Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });
            $employees = Employee::orderBy('created_at', 'desc')->public()->paginate($request->rows_per_page);
        } else {
            $employees = Employee::orderBy('created_at', 'desc')->public()->get();
        }
        return response()->json(new EmployeeCollection($employees), 200);
    }

    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        return response()->json(new EmployeeResource($employee), 201);
    }

    public function show(int $id)
    {
        $employee = Employee::public()->findOrFail($id);
        return response()->json(new EmployeeResource($employee), 200);
    }

    public function update(EmployeeRequest $request, int $id)
    {
        Employee::public()->findOrFail($id)->update($request->validated());
        $employee = Employee::public()->findOrFail($id);
        return response()->json(new EmployeeResource($employee), 200);
    }

    public function destroy(int $id)
    {
        Employee::destroy($id);
        return response('', 204);
    }
}
