<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $service;
    public function __construct(MediaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAll());
    }
    public function store(Request $request)
    {
        return response()->json($this->service->create($request->all()));
    }
    public function show($id)
    {
        return response()->json($this->service->find($id));
    }
    public function update(Request $request, $id)
    {
        return response()->json($this->service->update($id, $request->all()));
    }
    public function destroy($id)
    {
        return response()->json($this->service->delete($id));
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads/media', 'public');

            $media = $this->service->create([
                'name' => $file->getClientOriginalName(),
                'src' => $path,
                'type' => $file->getClientMimeType(),
            ]);

            return response()->json($media);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
