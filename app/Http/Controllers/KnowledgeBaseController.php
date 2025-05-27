<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $articles = KnowledgeBaseArticle::with('tag')
            ->latest()
            ->paginate(15);

        return view('knowledge-base.index', compact('articles'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('knowledge-base.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'keywords' => 'required|string',
            'tag_id' => 'nullable|exists:tags,id'
        ]);

        $keywords = array_map('trim', explode(',', $validated['keywords']));

        KnowledgeBaseArticle::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'keywords' => $keywords,
            'tag_id' => $validated['tag_id']
        ]);

        return redirect()->route('knowledge-base.index')
            ->with('success', 'Article created successfully');
    }

    public function suggest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $text = strtolower($request->text);
        $articles = KnowledgeBaseArticle::query()
            ->where(function($query) use ($text) {
                foreach (explode(' ', $text) as $word) {
                    if (strlen($word) > 3) {
                        $query->orWhereJsonContains('keywords', $word);
                    }
                }
            })
            ->limit(5)
            ->get();

        return response()->json($articles);
    }
}
