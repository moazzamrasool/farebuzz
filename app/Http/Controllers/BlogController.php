<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::forSite()->published()->latest('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $blogs = $query->paginate(9)->withQueryString();

        return view('blog.index', compact('blogs'));
    }

    public function show(Blog $blog)
    {
        abort_unless($blog->status === 'published', 404);

        $related = Blog::forSite()->published()
            ->where('category', $blog->category)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('blog', 'related'));
    }
}
