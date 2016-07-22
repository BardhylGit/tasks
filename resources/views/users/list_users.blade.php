<!--UserController@index-->
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row m-b-10">
        <div class="col-md-12">
            <a href="{{url('/user/create')}}">
                <button class="btn btn-primary">Create User</button>
            </a>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <table class="table table-bordered" id="users-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th colspan="3" class="text-center">Actions</th>
                    </tr>
                </thead>
                {!! $users->render() !!}
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->f_name}}</td>
                            <td>{{$user->l_name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->display_name}}</td>
                            <td class="text-justif">
                                <a href="{{url('/user/details', ['user_id' => $user->id])}}">
                                    <button class="btn btn-block btn-primary">View</button>
                                </a>
                            </td>
                            <td class="text-justif">
                                <a href="{{url('/user/edit', ['user_id' => $user->id])}}">
                                    <button class="btn btn-block btn-warning">Edit</button>
                                </a>
                            </td>
                            <td class="text-justif">
                                <button onclick="deleteDialog({'f_name':'{{$user->f_name}}', 'l_name': '{{$user->l_name}}', 'id': '{{$user->id}}'});" class="btn btn-block btn-danger">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $users->render() !!}
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script type="text/javascript">
    function deleteDialog(user)
    {
        swal({   title: "Delete " + user.f_name + " " + user.l_name + "?",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Delete it!",   
            closeOnConfirm: false 
            
        }, 
        function(){  
            $.ajax({
                method: "POST",
                url: "/api/users/delete",
                data: { '_method': 'DELETE', 'user_id': user.id }
            })
            .done(function( msg ) {
                swal("Deleted!", "", "success"); 
                location.reload(); 
                console.log(msg);
            })
            .fail(function(msg) {
                swal("Error!", "Internal Server Error", "error"); 
                console.log(msg);
            });
        });
    }
</script>
@endsection