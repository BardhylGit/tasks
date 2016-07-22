<!--UserController@details-->
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row well well-sm">
        <div class="row m-b-10">
            <div class="col-md-12">
                    <h2>Tasks {{$task->id}}  Details</h2>
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                User
            </div>
            <div class="col-md-6">
                {{$user->f_name}} {{$user->l_name}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                State
            </div>
            <div class="col-md-6">
                {{$task->state}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Description
            </div>
            <div class="col-md-6">
                {{$task->description}}
            </div>
        </div>
        <div class="row border-b-1 m-0 font-s-18 m-b-5">
            <div class="col-md-6">
                Created
            </div>
            <div class="col-md-6">
                {{$task->created_at}}
            </div>
        </div>
        <div class="row m-0 font-s-18">
            <div class="col-md-6">
                Updated
            </div>
            <div class="col-md-6">
                {{$task->updated_at}}
            </div>
        </div>
    </div>
    <div class="row">
        <a href="{{redirect()->back()->getTargetUrl()}}">    
            <button class="btn btn-primary">Back to list</button>
        </a>
    </div>
</div>
@endsection
