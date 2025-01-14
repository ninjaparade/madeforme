<?php

namespace App\Http\Controllers;

use App\Http\Requests\Media\ConfirmRequest;
use App\Http\Requests\Media\StoreRequest;
use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $media = Media::all(['id', 'path', 'status']);

        return Inertia::render('media/index', [
            'media' => $media,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('media/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $service = app(SignedRequestService::class);
        $signatures = $request->withEachUpload(function ($upload): array {

            $service->handle(
                key: $upload['name'],
                content_type: $upload['type'],
                content_length: $upload['size'],
            );

            $media = Media::create(['path' => $upload['name']]);

            return [
                'id' => $media['id'],
                'url' => $signature->getUri(),
                'headers' => array_merge($signature->getHeaders(), array_filter(['Content-Type' => $file->getMimeType(), 'Content-Length' => $file->getSize()])),
                'path' => $media['path'],
            ];
        });

        return Inertia::render('media/create', ['signatures' => $signatures]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

    public function confirm(ConfirmRequest $request)
    {
        Media::whereIn('id', $request->safe()->media)->update(['status' => 'confirmed']);

        return to_route('media.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        //
    }
}
