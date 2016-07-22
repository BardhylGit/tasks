<!--UserController@details-->
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row well well-sm">
        <div class="row m-b-10">
            <div class="col-md-12">
                    <h2>{{$user->f_name}} {{$user->l_name}} Details</h2>
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                First Name
            </div>
            <div class="col-md-6">
                {{$user->f_name}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Last Name
            </div>
            <div class="col-md-6">
                {{$user->l_name}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Email
            </div>
            <div class="col-md-6">
                {{$user->email}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Role
            </div>
            <div class="col-md-6">
                {{$role->display_name}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Created
            </div>
            <div class="col-md-6">
                {{$user->created_at}}
            </div>
        </div>
        <div class="row m-0 font-s-18">
            <div class="col-md-6">
                Updated
            </div>
            <div class="col-md-6">
                {{$user->updated_at}}
            </div>
        </div>
    </div>
    <div class="row">
        <a href="{{url('/users/list')}}">    
            <button class="btn btn-primary">Back to list</button>
        </a>
        <a href="{{url('/user/tasks', ['user_id'=>$user->id])}}">
            <button class="btn btn-primary">Load Tasks</button>
        </a>
    </div>
    <div class="row m-t-10"></div>
    @if(isset($tasks))
        <div class="row m-t-10">
            @include('partials/_tasks_list', ['tasks'=>$tasks])
        </div>
    @endif
</div>
@endsection
