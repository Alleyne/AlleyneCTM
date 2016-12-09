<?php

namespace App\Http\Controllers\blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;
use App\Comment;
use App\Post;
use Session;
use Guzzlehttp\Client;

class CommentsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }

/*    public function __construct()
    {
        $this->middleware('auth', ['except' => 'store']);
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id)
    {
        $this->validate($request, array(
            'comment'   =>  'required|min:5|max:2000'
            ));

        $token = $request->input('g-recaptcha-response');

        if ($token) {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => array(
                        'secret' => '6LfPuwsUAAAAAG_G6mjC7XYxl0aPlJwdBWSV6-GW',
                        'response' => $token
                        )
                ]);
        
            $results = json_decode($response->getBody()->getContents());
            if ($results->success) {
                $post = Post::find($post_id);

                $comment = new Comment();
                $comment->name = Auth::user()->nombre_completo;
                $comment->email = Auth::user()->email;
                $comment->comment = $request->comment;
                $comment->approved = true;
                $comment->post()->associate($post);

                $comment->save();
                Session::flash('success', 'Comentario fue agregado!');
                return redirect()->route('blog.single', [$post->slug]);
            
            } else {
                Session::flash('error', 'Permiso para agregar comentarios ha sido denegado!');
                return redirect()->route('blog.single', [$post->slug]);
            }

        } else {
            Session::flash('error', 'Permiso para agregar comentarios ha sido denegado!');
            return redirect()->back()->withInput($request->input());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comment = Comment::find($id);
        return view('blog.comments.edit')->withComment($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        $this->validate($request, array('comment' => 'required'));

        $comment->comment = $request->comment;
        $comment->save();

        Session::flash('success', 'Comentario fue actualizado!');

        return redirect()->route('posts.show', $comment->post->id);
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        return view('blog.comments.delete')->withComment($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        $post_id = $comment->post->id;
        $comment->delete();

        Session::flash('success', 'Comentario ha sido borrado!');

        return redirect()->route('posts.show', $post_id);
    }
}
