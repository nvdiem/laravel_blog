<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'tags')
            ->where('status', 'published')
            ->latest()
            ->paginate(6);

        return view('home', compact('posts'));
    }
}
