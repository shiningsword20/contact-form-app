<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $categories = Category::all();
        $tags = Tag::all();
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
            ->when($request->filled('category_id'), function($query) use($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('date'), function($query) use($request) {
                $query->whereDate('created_at', $request->date);
            })
            ->latest()->paginate(7);

            return view('admin.index', [
                'categories' => $categories,
                'tags' => $tags,
                'contacts' => $contacts]);
    }

    public function show($contact)
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($contact);
        return view('admin.show', ['contact' => $contact]);
    }

    public function destroy($contact)
    {
        Contact::findOrFail($contact)->delete();
        return redirect('/admin');
    }
}
