<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->safe()->only('title', 'content');
        $post = Post::create($validated);
        return new PostResource($post); // hoặc dùng $post->toResource();
    }

    /**
     * Display the specified resource.
     */
    // laravel có cơ chế Route Model Binding (https://laravel.com/docs/12.x/routing#route-model-binding). Không cần truyền id rồi từ id query database rồi mới show. Truyền trực tiếp param là object luôn
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
