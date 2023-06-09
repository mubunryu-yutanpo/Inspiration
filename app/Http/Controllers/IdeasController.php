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

    // ========アイデア新規投稿処理========
    public function ideaCreate(ValidRequest $request)
    {        
        $user_id = Auth::id();
        $idea = new Idea;

        // サムネ画像のパス名を変数に
        if($request->thumbnail !== null){
            $avatar = $request->file('thumbnail');
            $filename = $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads'), $filename);
        }else{
            $filename = 'thumbnail-default.png';
        }

        // DBに保存
        $idea->fill([
            'user_id'     => $user_id,
            'category_id' => $request->category,
            'title'       => $request->title,
            'thumbnail'    => '/uploads/'.$filename,
            'summary'     => $request->summary,
            'description' => $request->description,
            'price'       => $request->price,
        ])->save();
    
        return redirect('mypage')->with('flash_message', __('registered!'));
    }


    // ========アイデア編集（更新）処理========
    public function ideaUpdate(ValidRequest $request, $id){
        if(!ctype_digit($id)){
            return redirect('/')->with('flash_message', __('不正な操作が行われました'));
        }

        $user_id = Auth::id();
        $idea = Idea::find($id);

        // サムネ画像のパス名を変数に
        if($request->thumbnail !== null){
            $thumbnail = $request->file('thumbnail');
            $filename = $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads'), $filename);
       
        }elseif($idea->thumbnail !== null){
            $filename = basename($idea->thumbnail);
       
        }else{
            $filename = 'thumbnail-default.png';
        }

        // アイデアの所持者とアクセスしているユーザーが異なる場合、リダイレクトする
        if ($user_id !== Auth::user()->id) {
            return redirect('/')->with('flash_message', '失敗しました');
        }

        // DB情報更新
        $idea->update([
            'user_id'     => $user_id,
            'category_id' => $request->category,
            'title'       => $request->title,
            'thumbnail'    => '/uploads/'.$filename,
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

}
