<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $perPage = $request->per_page ?? 20;

        $contacts = Contact::with(['category', 'tags'])
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->gender);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('created_at', $request->date);
            })
            ->latest()
            ->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);
        $tagsId = $validated['tag_ids'] ?? [];
        $contact->tags()->attach($tagsId);
        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();
        $contact->update($validated);
        $tagsId = $validated['tag_ids'] ?? [];
        $contact->tags()->sync($tagsId);
        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))->response()->setStatusCode(200);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
