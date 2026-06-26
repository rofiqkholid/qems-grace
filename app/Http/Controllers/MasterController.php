<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserMenuPermission;
use App\Models\Menu;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return response()->view('direct_403.direct_403');
            }
            
            $menuId = 95;
            if ($request->is('*user-management*')) {
                $menuId = 103;
            } elseif ($request->is('*user-setting*')) {
                $menuId = 105;
            } elseif ($request->is('*menu-management*')) {
                $menuId = 105;
            } elseif ($request->is('*intr-check-item*')) {
                $menuId = 109;
            }
            if (!UserMenuPermission::canView($menuId)) {
                return response()->view('direct_403.direct_403');
            }
            return $next($request);
        });
    }

    public function line_checked()
    {
        return view('master.line_checked');
    }

    public function line_checked_table(Request $request)
    {
        $query = DB::table('Genba_Area')->orderBy('SysID', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Process', 'LIKE', "%{$searchValue}%")
                    ->orWhere('Area_name', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('Genba_Area')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "Process" => $item->Process,
                    "Area_name" => $item->Area_name,
                    "SysID" => $item->SysID,
                    "action" => '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="View" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-sysid="' . $item->SysID . '"
                                    data-process="' . $item->Process . '"
                                    data-areaname="' . $item->Area_name . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->SysID . ',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_line_checked(Request $request)
    {
        $request->validate([
            'process' => 'required',
            'area_name' => 'required',
        ]);

        try {
            DB::table('Genba_Area')->insert([
                'Process' => $request->process,
                'Area_name' => $request->area_name,
            ]);

            return redirect()->route('master.line_checked')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_line_checked(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
            'process' => 'required',
            'area_name' => 'required',
        ]);

        try {
            DB::table('Genba_Area')
                ->where('SysID', $request->sys_id)
                ->update([
                    'Process' => $request->process,
                    'Area_name' => $request->area_name,
                ]);

            return redirect()->route('master.line_checked')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_line_checked(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
        ]);

        try {
            DB::table('Genba_Area')->where('SysID', $request->sys_id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function category()
    {
        return view('master.category');
    }

    public function category_table(Request $request)
    {
        $query = DB::table('GenbaCategory')->orderBy('SysID', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Category', 'LIKE', "%{$searchValue}%")
                    ->orWhere('Description', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('GenbaCategory')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "Category" => $item->Category,
                    "Description" => $item->Description,
                    "SysID" => $item->SysID,
                    "action" => '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-sysid="' . $item->SysID . '"
                                    data-category="' . $item->Category . '"
                                    data-description="' . $item->Description . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->SysID . ',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_category(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'description' => 'required',
        ]);

        try {
            DB::table('GenbaCategory')->insert([
                'Category' => $request->category,
                'Description' => $request->description,
            ]);

            return redirect()->route('master.category')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_category(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
            'category' => 'required',
            'description' => 'required',
        ]);

        try {
            DB::table('GenbaCategory')
                ->where('SysID', $request->sys_id)
                ->update([
                    'Category' => $request->category,
                    'Description' => $request->description,
                ]);

            return redirect()->route('master.category')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_category(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
        ]);

        try {
            DB::table('GenbaCategory')->where('SysID', $request->sys_id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    public function department()
    {
        return view('master.department');
    }

    public function department_table(Request $request)
    {
        $query = DB::table('GenbaDept')->orderBy('Key1', 'asc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Key1', 'LIKE', "%{$searchValue}%")
                    ->orWhere('Desc', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('GenbaDept')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "Key1" => $item->Key1,
                    "Desc" => $item->Desc,
                    "action" => '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-key1="' . $item->Key1 . '"
                                    data-desc="' . $item->Desc . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(\'' . $item->Key1 . '\',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_department(Request $request)
    {
        $request->validate([
            'key1' => 'required|unique:GenbaDept,Key1',
            'desc' => 'required',
        ]);

        try {
            DB::table('GenbaDept')->insert([
                'Company' => 'SAI',
                'Key1' => $request->key1,
                'Desc' => $request->desc,
                'Checkbox01' => '1',
            ]);

            return redirect()->route('master.department')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_department(Request $request)
    {
        $request->validate([
            'key1' => 'required',
            'desc' => 'required',
        ]);

        try {
            DB::table('GenbaDept')
                ->where('Key1', $request->key1)
                ->update([
                    'Desc' => $request->desc,
                ]);

            return redirect()->route('master.department')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_department(Request $request)
    {
        $request->validate([
            'key1' => 'required',
        ]);

        try {
            DB::table('GenbaDept')->where('Key1', $request->key1)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function clauses()
    {
        return view('master.clauses');
    }

    public function clauses_table(Request $request)
    {
        $query = DB::table('CsKlausul')->orderBy('id', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('clause_no', 'LIKE', "%{$searchValue}%")
                    ->orWhere('clause_title', 'LIKE', "%{$searchValue}%")
                    ->orWhere('clauses', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('CsKlausul')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "id" => $item->id,
                    "clause_no" => $item->clause_no,
                    "clause_title" => $item->clause_title,
                    "clauses" => $item->clauses,
                    "action" => '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-id="' . $item->id . '"
                                    data-clause_no="' . htmlspecialchars($item->clause_no, ENT_QUOTES) . '"
                                    data-clause_title="' . htmlspecialchars($item->clause_title, ENT_QUOTES) . '"
                                    data-clauses="' . htmlspecialchars($item->clauses ?? '', ENT_QUOTES) . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->id . ',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_clauses(Request $request)
    {
        $request->validate([
            'clause_no' => 'required|unique:CsKlausul,clause_no',
            'clause_title' => 'required',
            'clauses' => 'nullable',
        ]);

        try {
            DB::table('CsKlausul')->insert([
                'clause_no' => $request->clause_no,
                'clause_title' => $request->clause_title,
                'clauses' => $request->clauses,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('master.clauses')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_clauses(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'clause_no' => 'required|unique:CsKlausul,clause_no,' . $request->id,
            'clause_title' => 'required',
            'clauses' => 'nullable',
        ]);

        try {
            DB::table('CsKlausul')
                ->where('id', $request->id)
                ->update([
                    'clause_no' => $request->clause_no,
                    'clause_title' => $request->clause_title,
                    'clauses' => $request->clauses,
                    'updated_at' => now(),
                ]);

            return redirect()->route('master.clauses')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_clauses(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::table('CsKlausul')->where('id', $request->id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function check_item()
    {
        $categories = DB::table('GenbaCategory')->get();
        return view('master.check_item', compact('categories'));
    }

    public function check_item_table(Request $request)
    {
        $query = DB::table('GenbaAuditItem as a')
            ->leftJoin('GenbaCategory as b', 'b.SysID', '=', 'a.Category')
            ->select('a.*', 'b.Category as CategoryName')
            ->orderBy('a.SysID', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('a.Scope_item', 'LIKE', "%{$searchValue}%")
                    ->orWhere('a.Check_item', 'LIKE', "%{$searchValue}%")
                    ->orWhere('a.Check_item_eng', 'LIKE', "%{$searchValue}%")
                    ->orWhere('b.Category', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('GenbaAuditItem')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "Scope_id" => $item->Scope_id,
                    "Category" => $item->CategoryName,
                    "Category_id" => $item->Category,
                    "Scope_item" => $item->Scope_item,
                    "Check_item" => $item->Check_item,
                    "Check_item_eng" => $item->Check_item_eng,
                    "SysID" => $item->SysID,
                    "action" => '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-sysid="' . $item->SysID . '"
                                    data-scope_id="' . $item->Scope_id . '"
                                    data-category="' . $item->Category . '"
                                    data-scope_item="' . $item->Scope_item . '"
                                    data-check_item="' . $item->Check_item . '"
                                    data-check_item_eng="' . $item->Check_item_eng . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->SysID . ',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_check_item(Request $request)
    {
        $request->validate([
            'scope_id' => 'required',
            'category' => 'required',
            'scope_item' => 'required',
            'check_item' => 'required',
            'check_item_eng' => 'required',
        ]);

        try {
            DB::table('GenbaAuditItem')->insert([
                'Scope_id' => $request->scope_id,
                'Category' => $request->category,
                'Scope_item' => $request->scope_item,
                'Check_item' => $request->check_item,
                'Check_item_eng' => $request->check_item_eng,
            ]);

            return redirect()->route('master.check_item')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_check_item(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
            'scope_id' => 'required',
            'category' => 'required',
            'scope_item' => 'required',
            'check_item' => 'required',
            'check_item_eng' => 'required',
        ]);

        try {
            DB::table('GenbaAuditItem')
                ->where('SysID', $request->sys_id)
                ->update([
                    'Scope_id' => $request->scope_id,
                    'Category' => $request->category,
                    'Scope_item' => $request->scope_item,
                    'Check_item' => $request->check_item,
                    'Check_item_eng' => $request->check_item_eng,
                ]);

            return redirect()->route('master.check_item')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_check_item(Request $request)
    {
        $request->validate([
            'sys_id' => 'required',
        ]);

        try {
            DB::table('GenbaAuditItem')->where('SysID', $request->sys_id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function intr_check_item()
    {
        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();
        return view('master.intr_check_item', compact('departments'));
    }

    public function intr_check_item_table(Request $request)
    {
        $query = DB::table('CsChecksheetItem')
            ->where('is_active', 1)
            ->orderBy('id', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('check_item_idn', 'LIKE', "%{$searchValue}%")
                    ->orWhere('check_item_en', 'LIKE', "%{$searchValue}%")
                    ->orWhere('department', 'LIKE', "%{$searchValue}%")
                    ->orWhere('scope_item', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records
        $totalRecords = DB::table('CsChecksheetItem')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "id" => $item->id,
                    "check_item_idn" => $item->check_item_idn,
                    "check_item_en" => $item->check_item_en,
                    "department" => $item->department,
                    "scope_item" => $item->scope_item,
                    "is_active" => $item->is_active,
                    "action" => '<div class="flex items-center justify-start gap-2">
                                <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-id="' . $item->id . '"
                                    data-check_item_idn="' . htmlspecialchars($item->check_item_idn, ENT_QUOTES) . '"
                                    data-check_item_en="' . htmlspecialchars($item->check_item_en, ENT_QUOTES) . '"
                                    data-department="' . htmlspecialchars($item->department, ENT_QUOTES) . '"
                                    data-scope_item="' . htmlspecialchars($item->scope_item, ENT_QUOTES) . '"
                                    data-is_active="' . $item->is_active . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->id . ',' . ($start + $key + 1) . ')">
                                    
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>'
                ];
            })
        ];

        return response()->json($response);
    }

    public function store_intr_check_item(Request $request)
    {
        $request->validate([
            'check_item_idn' => 'required',
            'check_item_en' => 'required',
            'department' => 'required',
        ]);

        try {
            DB::table('CsChecksheetItem')->insert([
                'check_item_idn' => $request->check_item_idn,
                'check_item_en' => $request->check_item_en,
                'department' => $request->department,
                'scope_item' => $request->scope_item,
                'is_active' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);

            return redirect()->route('master.intr_check_item')->with('success', 'Data added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
        }
    }

    public function update_intr_check_item(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'check_item_idn' => 'required',
            'check_item_en' => 'required',
            'department' => 'required',
        ]);

        try {
            DB::table('CsChecksheetItem')
                ->where('id', $request->id)
                ->update([
                    'check_item_idn' => $request->check_item_idn,
                    'check_item_en' => $request->check_item_en,
                    'department' => $request->department,
                    'scope_item' => $request->scope_item,
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return redirect()->route('master.intr_check_item')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function delete_intr_check_item(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::table('CsChecksheetItem')
                ->where('id', $request->id)
                ->update([
                    'is_active' => 0,
                    'updated_at' => \Carbon\Carbon::now()
                ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function user_management()
    {
        return view('setting.user_management');
    }

    public function user_management_table(Request $request)
    {
        $query = DB::table('users')->orderBy('id', 'asc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('username', 'LIKE', "%{$searchValue}%")
                    ->orWhere('full_name', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalRecords = DB::table('users')->count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        // All menus to pass into action button
        $allMenus = DB::table('t100_menus')->orderBy('level_menu_id')->orderBy('sequence_id')->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request, $allMenus) {
                $start = $request->start ?? 0;
                
                // Get permissions for this user
                $userPermissions = DB::table('t100_user_menus_permission')
                    ->where('id_user', $item->id)
                    ->get()
                    ->keyBy('id_menus');

                // Map permissions status
                $permissionsMapped = $allMenus->map(function($menu) use ($userPermissions) {
                    $perm = $userPermissions->get($menu->id);
                    return [
                        'id' => $menu->id,
                        'menu_name' => $menu->menu_name,
                        'level_menu_id' => $menu->level_menu_id,
                        'is_view' => $perm ? $perm->is_view : 0,
                        'is_delete' => $perm ? $perm->is_delete : 0,
                    ];
                });

                $permissionsJson = json_encode($permissionsMapped);
                $escapedJson = htmlspecialchars($permissionsJson, ENT_QUOTES, 'UTF-8');
                $escapedFullName = htmlspecialchars($item->full_name, ENT_QUOTES, 'UTF-8');

                $actionButton = '
                    <button type="button" 
                        class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold transition-colors"
                        onclick="openPermissionsModal(' . $item->id . ', \'' . $item->username . '\', \'' . $escapedFullName . '\', \'' . $escapedJson . '\')">
                        <i class="fa-solid fa-key mr-1"></i> Permissions
                    </button>';

                return [
                    "no" => $start + $key + 1,
                    "username" => $item->username,
                    "full_name" => $item->full_name,
                    "role_id" => $item->role_id,
                    "action" => $actionButton
                ];
            })
        ]);
    }

    public function update_user_permission(Request $request)
    {
        $userId = $request->input('user_id');
        $permissions = $request->input('permissions', []);

        try {
            DB::beginTransaction();

            // Fetch all menu IDs
            $allMenuIds = DB::table('t100_menus')->pluck('id')->toArray();

            foreach ($allMenuIds as $menuId) {
                $isView = isset($permissions[$menuId]['is_view']) ? 1 : 0;
                $isDelete = isset($permissions[$menuId]['is_delete']) ? 1 : 0;

                DB::table('t100_user_menus_permission')->updateOrInsert(
                    [
                        'id_user' => $userId,
                        'id_menus' => $menuId
                    ],
                    [
                        'is_view' => $isView,
                        'is_delete' => $isDelete
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permissions updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating permissions: ' . $e->getMessage()]);
        }
    }

    public function menu_management()
    {
        return view('setting.menu_management');
    }

    public function menu_management_table(Request $request)
    {
        $query = DB::table('t100_menus')->orderBy('level_menu_id', 'asc')->orderBy('sequence_id', 'asc');

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('menu_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('menu', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalRecords = DB::table('t100_menus')->count();
        $filteredRecords = $query->count();

        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                return [
                    "no" => $start + $key + 1,
                    "id" => $item->id,
                    "menu_name" => $item->menu_name,
                    "menu" => $item->menu,
                    "level_menu_id" => $item->level_menu_id,
                    "sequence_id" => $item->sequence_id,
                ];
            })
        ]);
    }

    public function user_list(Request $request)
    {
        $search = $request->input('search');
        $query = DB::table('users');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('full_name', 'asc')->paginate(15);

        // Fetch all level 2 menus for tags
        $allMenus = DB::table('t100_menus')->where('level_menu_id', 2)->get()->keyBy('id');

        $data = collect($users->items())->map(function ($user) use ($allMenus) {
            // Get viewable menu permissions
            $viewMenuIds = DB::table('t100_user_menus_permission')
                ->where('id_user', $user->id)
                ->where('is_view', 1)
                ->pluck('id_menus')
                ->toArray();

            $tags = [];
            foreach ($viewMenuIds as $menuId) {
                if (isset($allMenus[$menuId])) {
                    $tags[] = $allMenus[$menuId]->menu_name;
                }
            }

            return [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'email' => $user->email ?? '-',
                'role_id' => $user->role_id,
                'avatar' => $user->avatar ?? 'blank.png',
                'tags' => $tags
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
            'per_page' => $users->perPage()
        ]);
    }

    public function get_user_permissions($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $orderedIds = Menu::getOrderedIds();
        $menusKeyed = DB::table('t100_menus')->get()->keyBy('id');
        
        $allMenus = collect();
        foreach ($orderedIds as $menuId) {
            if (isset($menusKeyed[$menuId])) {
                $allMenus->push($menusKeyed[$menuId]);
            }
        }

        // Get permissions for this user
        $userPermissions = DB::table('t100_user_menus_permission')
            ->where('id_user', $id)
            ->get()
            ->keyBy('id_menus');

        // Map menus to their permissions
        $permissionsMapped = $allMenus->map(function($menu) use ($userPermissions) {
            $perm = $userPermissions->get($menu->id);
            return [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'level_menu_id' => $menu->level_menu_id,
                'is_view' => $perm ? $perm->is_view : 0,
                'is_delete' => $perm ? $perm->is_delete : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role_id' => $user->role_id
            ],
            'permissions' => $permissionsMapped
        ]);
    }

    public function user_setting()
    {
        $user = Auth::user();
        return view('setting.user_setting', compact('user'));
    }

    public function update_user_setting(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'full_name' => 'required|string|max:255',
            'call_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $userId = $request->user_id;
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['user_id' => 'User not found.']);
        }

        $data = [
            'full_name' => $request->full_name,
            'call_name' => $request->call_name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename);
            
            // Delete old avatar if exists and not blank
            if ($user->avatar && $user->avatar !== 'blank.png' && file_exists(public_path('image/' . $user->avatar))) {
                @unlink(public_path('image/' . $user->avatar));
            }
            
            $data['avatar'] = $filename;
        }

        DB::table('users')->where('id', $userId)->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'user' => DB::table('users')->where('id', $userId)->first()
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.')->with('selected_user_id', $userId);
    }
}
