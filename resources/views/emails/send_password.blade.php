{{--
    
    @ eMail Create new Manager, UserController@store
    @ resources/view/emails/send_password.blade.php
    
--}}

@extends('layouts.email.main')

@section('title')
Hello {{ $email }}, <br>
your password is: {{$password}} <br>

@stop
