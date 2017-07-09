<?php
namespace App\Http\Controllers\blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Cache;

use App\Post;

class BlogController extends Controller
{
    
	public function getIndex() {
		$posts = Post::orderBy('id', 'desc')->paginate(10);

		return view('blog.blog.index')->withPosts($posts);
	}

    public function getSingle($slug) {

        // fetch from the DB based on slug
    	$post = Post::where('slug', $slug)->first();
        
        // mas recientes
        $posts = Cache::get('recentPostkey');
    	// return the view and pass in the post object
    	
        return view('blog.blog.single')
                ->withPosts($posts)
                ->withPost($post);
    }
}
