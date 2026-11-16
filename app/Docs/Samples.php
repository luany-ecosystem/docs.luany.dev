<?php

namespace App\Docs;

/**
 * Code shown on the landing page. Kept in PHP (not in the LTE view) so the
 * samples' own {{ }} and @directives are printed, not compiled. Every sample
 * uses the documented API of the current releases.
 */
final class Samples
{
    /** @return array<string, array{file: string, lang: string, code: string}> */
    public static function tabs(): array
    {
        return [
            'routing' => ['file' => 'routes/http.php', 'lang' => 'php', 'code' => <<<'PHP'
<?php

use App\Controllers\PostController;
use App\Http\Middleware\AuthMiddleware;
use Luany\Core\Routing\Route;

Route::get('/', fn () => view('pages.home'))->name('home');

// /posts/7 → int 7 · /posts/abc → 404
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

Route::prefix('admin')->middleware(AuthMiddleware::class)->group(function () {
    Route::resource('posts', PostController::class);
});
PHP],
            'controller' => ['file' => 'app/Controllers/PostController.php', 'lang' => 'php', 'code' => <<<'PHP'
<?php

namespace App\Controllers;

use App\Models\Post;
use Luany\Core\Http\Request;
use Luany\Core\Http\Response;

class PostController extends Controller
{
    public function show(int $id): string
    {
        return view('pages.posts.show', ['post' => Post::findOrFail($id)]);
    }

    public function store(Request $request): Response
    {
        $data = validate($request->body(), [
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        Post::create($data);
        flash('success', 'Post created.');

        return redirect('/posts');
    }
}
PHP],
            'view' => ['file' => 'views/pages/posts/index.lte', 'lang' => 'lte', 'code' => <<<'LTE'
@extends('layouts.main')

@section('title', 'Posts')

@section('content')
    <h1>Posts</h1>

    @forelse($posts as $post)
        <article @class(['post', 'is-draft' => !$post->published])>
            <a href="{{ route('posts.show', ['id' => $post->id]) }}">{{ $post->title }}</a>
            <small>{{ $post->author->name }}</small>
        </article>
    @empty
        <p>No posts yet.</p>
    @endforelse

    <form method="POST" action="/posts">
        @csrf
        <input name="title" required>
        <button>Publish</button>
    </form>
@endsection
LTE],
            'model' => ['file' => 'app/Models/Post.php', 'lang' => 'php', 'code' => <<<'PHP'
<?php

namespace App\Models;

use Luany\Database\Concerns\SoftDeletes;
use Luany\Database\Model;
use Luany\Database\ModelQuery;
use Luany\Database\Relations\BelongsTo;
use Luany\Database\Relations\HasMany;

class Post extends Model
{
    use SoftDeletes;

    protected string $table   = 'posts';
    protected array $fillable = ['user_id', 'title', 'body', 'published'];
    protected array $casts    = ['published' => 'boolean'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopePublished(ModelQuery $query): void
    {
        $query->where('published', '=', 1);
    }
}

// Two queries, never N + 1:
$posts = Post::with('author')->where('published', '=', 1)->paginate(20, $page);
PHP],
            'migration' => ['file' => 'database/migrations/2026_11_11_093000_create_posts_table.php', 'lang' => 'php', 'code' => <<<'PHP'
<?php

use Luany\Database\Migration\Migration;
use Luany\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(\PDO $pdo): void
    {
        $this->schema($pdo)->create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(\PDO $pdo): void
    {
        $this->schema($pdo)->dropIfExists('posts');
    }
};
PHP],
            'cli' => ['file' => 'Terminal', 'lang' => 'bash', 'code' => <<<'SH'
$ luany make:feature Product name:string price:decimal category_id:foreign

  →  Creating feature [Product]...

  ✓  Model created:      app/Models/Product.php
  ✓  Controller created: app/Controllers/ProductController.php
  ✓  Migration created:  database/migrations/2026_11_11_101500_create_products_table.php
  ✓  View created:       views/pages/products/index.lte
  ✓  View created:       views/pages/products/show.lte
  ✓  View created:       views/pages/products/create.lte
  ✓  View created:       views/pages/products/edit.lte
  ✓  Routes created:     routes/products.php

$ luany migrate && luany dev
SH],
        ];
    }

    /** Small snippets for the floating cards of the hero. */
    public static function hero(): array
    {
        return [
            'route' => <<<'PHP'
Route::get('/posts/{id}',
    [PostController::class, 'show'])
    ->name('posts.show');
PHP,
            'view' => <<<'LTE'
@foreach($posts as $post)
    <h2>{{ $post->title }}</h2>
@endforeach
LTE,
        ];
    }
}
