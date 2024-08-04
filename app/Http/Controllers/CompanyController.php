<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyCreated;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyCollection;
use Illuminate\Support\Facades\Storage;
use Exception;

class CompanyController extends Controller
{
    public function list(): View
    {
        return view('companies');
    }

    public function index(Request $request)
    {
        if ($request->current_page_number && $request->rows_per_page) {
            $currentPage = $request->current_page_number;
            // Set the paginator to the current page
            Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });
            $companies = Company::orderBy('created_at', 'desc')->paginate($request->rows_per_page);
        } else {
            $companies = Company::orderBy('created_at', 'desc')->get();
        }
        return response()->json(new CompanyCollection($companies), 200);
    }

    private function saveLogo(Request $request): string
    {
        $image = $request->logoContent;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $arrayLogoFilename = explode('.', $request->logo);
        array_pop($arrayLogoFilename);
        $imageFileName = implode('.', $arrayLogoFilename) . '.png';
        Storage::disk('public')->put($imageFileName, base64_decode($image));
        Storage::setVisibility($imageFileName, 'public');
        return $imageFileName;
    }

    public function store(CompanyRequest $request)
    {
        $imageFileName = $this->saveLogo($request);
        $company = Company::create(array(
            'name' => $request->name,
            'email' => $request->email,
            'logo' => $imageFileName,
            'website' => $request->website
        ));
        Mail::to($request->user())->send(new CompanyCreated($company));
        return response()->json(new CompanyResource($company), 201);
    }

    public function show(int $id)
    {
        $company = Company::findOrFail($id);
        return response()->json(new CompanyResource($company), 200);
    }

    public function update(CompanyRequest $request, int $id)
    {
        $imageFileName = $this->saveLogo($request);
        Company::findOrFail($id)->update(array(
            'name' => $request->name,
            'email' => $request->email,
            'logo' => $imageFileName,
            'website' => $request->website
        ));
        $company = Company::findOrFail($id);
        return response()->json(new CompanyResource($company), 200);
    }

    public function destroy(int $id)
    {
        try {
            Company::destroy($id);
        } catch (Exception) {
            abort(400, 'Cannot delete company has employees.');
        }
        return response('', 204);
    }
}
