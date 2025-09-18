<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Core\CommonUtility;
use App\Exports\UserExport;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $sortField = $request->input('sortField', 1);
            $sortOrder = $request->input('sortOrder', 1);
            $filters = [
                'keyword' => $request->input('keyword'),
                'course_id' => $request->input('course_id')
            ];
            return CommonUtility::getSuccessResponse(
                $this->userService->getListUser($limit, $page, $filters, $sortField, $sortOrder), "success"
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list users fail");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userService->create($request->all()), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Save user fail");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userService->find($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get user detail fail");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['id'] = $id;
            return CommonUtility::getSuccessResponse($this->userService->update($data), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update user fail");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus($id, Request $request)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userService->updatetatus($id, $request->status), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Delete user fail");
        }
    }

    /**
    * Export users to excel
    */
    public function exportExcel()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }
}
