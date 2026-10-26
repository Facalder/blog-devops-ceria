<?php

use App\Livewire\Admin\Categories\Index as CategoriesIndex;
use App\Livewire\Admin\Posts\Create as PostsCreate;
use App\Livewire\Admin\Posts\Edit as PostsEdit;
use App\Livewire\Admin\Posts\Index as PostsIndex;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $categoryQuery = $request->query('category');
    $selectedCategorySlug = is_string($categoryQuery) ? $categoryQuery : null;

    $searchQuery = $request->query('search');
    $search = is_string($searchQuery) && $searchQuery !== '' ? $searchQuery : null;

    $categories = Category::withCount(['posts' => fn ($q) => $q->published()])
        ->get()
        ->filter(fn ($c) => $c->posts_count > 0);

    $posts = Post::published()
        ->with(['user', 'category'])
        ->when($selectedCategorySlug, function ($query, $slug) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
        })
        ->when($search, function ($query, string $searchTerm) {
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        })
        ->latest('published_at')
        ->paginate(9)
        ->withQueryString();

    return view('welcome', [
        'categories' => $categories,
        'posts' => $posts,
        'selectedCategorySlug' => $selectedCategorySlug,
        'search' => $search,
    ]);
})->name('home');

Route::get('/blog/{post:slug}', function (Post $post) {
    abort_if($post->status !== 'published', 404);

    return view('blog.show', ['post' => $post]);
})->name('blog.show');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Management Blog
    Route::get('dashboard/posts', PostsIndex::class)->name('posts.index');
    Route::get('dashboard/posts/create', PostsCreate::class)->name('posts.create');
    Route::get('dashboard/posts/{post}/edit', PostsEdit::class)->name('posts.edit');

    // Management Categories
    Route::get('dashboard/categories', CategoriesIndex::class)->name('categories.index');

    // Management Users
    Route::get('dashboard/users', UsersIndex::class)->name('users.index');
});

require __DIR__.'/settings.php';
