@extends('layouts.parent')

@section('title', 'メッセージボード')

@section('main')
  <chat-component :idea_id = "'{{ $idea_id }}'" :seller_id = "'{{ $seller_id }}'" :user_id = "'{{ $user_id }}'" :chat_id = "'{{ $chat_id }}'">
  </chat-component>
@endsection

@section('footer')
