@extends('layouts.parent')

@section('title', '投稿一覧')

@section('header')

@section('main')
  <myposts-component :user_id="'{{ Auth::id() }}'"></myposts-component>
@endsection

@section('footer')
