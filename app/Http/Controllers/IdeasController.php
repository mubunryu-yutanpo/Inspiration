<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ValidRequest;
use App\User;
use App\Category;
use App\Check;
use App\Idea;
use App\Purchase;
use App\Review;

class IdeasController extends Controller
{

    // コントローラーのプロパティを定義
    // protected $user;
    // protected $user_id;

    // public function __construct(){
    //     $this->middleware('auth')->except('index');

    //     $this->user = Auth::user();
    //     $this->user_id = $this->user ? $this->user->id : null;
    // }


    // ========アイデア新規投稿処理========
    public function ideaCreate(ValidRequest $request)
    {
        $user_id = Auth::id();
        $idea = new Idea;

        // サムネ画像のパス名を変数に
        if($request->sumbnail !== null){
            $avatar = $request->file('sumbnail');
            $filename = $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads'), $filename);
        }else{
            $filename = 'sumbnail-default.png';
        }

        // DBに保存
        $idea->fill([
            'user_id'     => $user_id,
            'category_id' => $request->category,
            'title'       => $request->title,
            'sumbnail'    => '/uploads/'.$filename,
            'summary'     => $request->summary,
            'description' => $request->description,
            'price'       => $request->price,
        ])->save();
    
        return redirect('mypage')->with('flash_message', __('registered!'));
    }

    // ========アイデア編集画面へ========
    public function ideaEdit($id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        // 誰かに購入されている場合は編集・削除できなくなる
        $canEdit = true;
        $purchased = Purchase::where('idea_id', $id)->first();
        if($purchased !== null){
            $canEdit = false;
        }
        $idea = Idea::find($id);

        $data = [
            'idea'    => $idea,
            'canEdit' => $canEdit,
        ];

        return response()->json($data);
    }


    // ========アイデア編集（更新）処理========
    public function ideaUpdate(ValidRequest $request, $id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        $user_id = Auth::id();
        $idea = Idea::find($id);

        // サムネ画像のパス名を変数に
        if($request->sumbnail !== null){
            $avatar = $request->file('sumbnail');
            $filename = $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads'), $filename);
        }else{
            $filename = 'sumbnail-default.png';
        }

        // アイデアの所持者とアクセスしているユーザーが異なる場合、リダイレクトする
        if ($user_id !== Auth::user()->id) {
            return redirect('/')->with('flash_message', '失敗しました');
        }

        // DB情報更新
        $idea->update([
            'user_id'     => $user_id,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'sumbnail'    => '/uploads/'.$filename,
            'summary'     => $request->summary,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return redirect('/mypage')->with('flash_message', __('updated!') );
    }


    // ========アイデア削除処理========
    public function ideaDelete($id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        $user_id = Auth::id();
        $idea = Idea::find($id);
        $owner_id = $idea->user_id;

        // アイデアが存在しない場合
        if (!$idea) {
            return redirect('/')->with('flash_message', '失敗しました');
        }

        // アイデアの所持者とアクセスしているユーザーが異なる場合
        if ($owner_id !== $user_id) {
            return redirect('/')->with('flash_message', '失敗しました');
        }

        $idea->delete();

        return redirect('/mypage')->with('flash_message', __('deleted!') );
    }

        
    // ========気になるリストへ========
    public function checkIdeas($id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        $checkIdeas = null;
        $checks = Check::where('user_id', $id)->get();

        if ($checks->isNotEmpty()) {
            $idea_ids = $checks->pluck('idea_id')->toArray();
            $ideas = Idea::whereIn('id', $ideaI_ids)->paginate(10);
            $checkIdeas = $ideas;
        }

        $data = [
            'checkIdeas' => $checkIdeas,
        ];

        return response()->json($data);
    }



    // ========レビュー投稿========
    public function postReview(ValidRequest $request, $id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        $user_id = Auth::id();
        $review = new Review;

        $review->fill([
            'user_id' => $user_id,
            'idea_id' => $id,
            'comment' => $request->comment,
            'score'   => $request->score,
        ])->save();

        return redirect('mypage')->with('flash_message', 'レビューを投稿しました！');

    }

    // // ========購入したアイデア取得処理========
    // public function boughtIdeas($id){
    //     if(!ctype_digit($id)){
    //         return redirect('/')->with('flash_message', __('不正な操作が行われました'));
    //     }

    //     $boughtList = null;
    //     // 自分が購入したアイデアを1ページ10件表示するように取得
    //     $boughts = $this->user
    //                ->purchase()
    //                ->with('idea')
    //                ->paginate(10);
        
    //     if($boughts->isNotEmpty()){
    //         $boughtList = $boughts;
    //     }

    //     $data = [
    //         'boughtList' => $boughtList,
    //     ];

    //     return response()->json($data);
    // }

    // // ========投稿したアイデア一覧へ========
    // public function indexPosts($id){

    //     $postsList = null;

    //     $posts = Idea::where('user_id', $id)->paginate(10);
    //     if($posts->isNotEmpty() ){
    //         $postsList = $posts;
    //     }

    //     $data = [
    //         'postsList' => $postsList,
    //     ];

    //     return response()->json($data);
    // }

}
