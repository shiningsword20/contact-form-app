<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();
        Tag::create($validated);
        return redirect('/admin');
    }

    public function edit($tag)
    {
        $tag = Tag::findOrFail($tag);
        return view('admin.tags.edit', ['tag' => $tag]);
    }

    public function update(UpdateTagRequest $request, $tag)
    {
        $validated = $request->validated();
        $tag = Tag::findOrFail($tag);
        $tag->name = $validated['name'];
        $tag->save();
        return redirect('/admin');
    }

    public function destroy($tag)
    {
        Tag::findOrFail($tag)->delete();
        return redirect('/admin');
    }
}
