<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $request->flash();
        $category = Category::find($validated['category_id']);
        $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

        return view('contact.confirm', [
            'validated' => $validated,
            'category' => $category,
            'tags' => $tags,
        ]);
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);
        $tagsId = $validated['tag_ids'] ?? [];
        $contact->tags()->attach($tagsId);

        return redirect('/thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }

    public function export(ExportContactRequest $request)
    {
        $contacts = Contact::with(['category', 'tags'])
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('gender') && $request->gender != 0, function ($query) use ($request) {
                $query->where('gender', $request->gender);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('created_at', $request->date);
            })
            ->latest()->get();
    }
}
