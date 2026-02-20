<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $allowedUsers = ['270723-001', '260422-001', '121020-002'];
            if (!Auth::check() || !in_array(Auth::user()->username, $allowedUsers)) {
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

            return redirect()->route('master.line_checked')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
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

            return redirect()->route('master.line_checked')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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

            return redirect()->route('master.category')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
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

            return redirect()->route('master.category')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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

            return redirect()->route('master.department')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
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

            return redirect()->route('master.department')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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

    public function check_item()
    {
        return view('master.check_item');
    }

    public function check_item_table(Request $request)
    {
        $query = DB::table('GenbaAuditItem')->orderBy('SysID', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Scope_item', 'LIKE', "%{$searchValue}%")
                    ->orWhere('Check_item', 'LIKE', "%{$searchValue}%")
                    ->orWhere('Check_item_eng', 'LIKE', "%{$searchValue}%");
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
                    "Category" => $item->Category,
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

            return redirect()->route('master.check_item')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
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

            return redirect()->route('master.check_item')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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
}
