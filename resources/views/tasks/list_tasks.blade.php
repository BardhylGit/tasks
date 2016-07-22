<!--UserController@index-->
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row m-b-10">
        <div class="col-md-8">
            <a href="{{url('/task/create')}}">
                <button class="btn btn-primary">Create New Task</button>
            </a>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{url('/task/list/csv')}}">
                <button class="fa fa-download btn btn-primary"> CSV</button>
            </a>
            <a href="{{url('/task/list/xml')}}">
                
                <button class="fa fa-download btn btn-primary"> XML</button>
            </a>
        </div>
        
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <table class="table table-bordered" id="task-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>User</th>
                        <th>State</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th colspan="2" class="text-center">Actions</th>
                    </tr>
                </thead>
                {!! $tasks->render() !!}
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{$task->id}}</td>
                            <td>{{$task->f_name}} {{$task->l_name}}</td>
                            <td style="position:relative; color: {{$task->state == 'new' ? 'blue' : ($task->state == 'in-progress' ? 'orange' : 'green')}};">
                                {{$task->state}}
                                <div onclick="edit({'task':{{json_encode($task)}}})" class="fa fa-btn fa-pencil-square-o" style="position:absolute; right:0px; color:black;"></div>
                            </td>
                            
                            <td>{{$task->created_at}}</td>
                            <td>{{$task->updated_at}}</td>
                            <td class="text-justif">
                                <a href="{{url('/task/details', ['task_id' => $task->id])}}">
                                    <button class="btn btn-block btn-primary">View</button>
                                </a>
                            </td>
                            <td class="text-justif">
                                <button onclick="deleteDialog({{$task->id}});" class="btn btn-block btn-danger">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $tasks->render() !!}
        </div>
    </div>
</div>

<div id="task_edit" class="container task-modal-wrapper">
    <div style="position:absolute; left:50%;top: 30%;">
        <div class="row task-modal">
            <div onclick="closeTaskModal();" class="col fa fa-times-circle close-btn"></div>
            <div class="col col-md-12">
                <div class="form-horizontal">
                
                <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label" style="text-align:left !important;">ID</label>

                    <div class="col-md-9">
                        <input type="text" class="form-control" name="id" readonly="readonly" />
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label" style="text-align:left !important;">User</label>

                    <div class="col-md-9">
                        <input type="text" class="form-control" name="user" readonly="readonly" />
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label" style="text-align:left !important;">Tast State</label>

                    <div class="col-md-9">
                            <div class="col-md-4 col-sm-12">
                                <div class="row">
                                    <div class="col col-sm-8"><label class="col-md-4 control-label">New</label></div>
                                    <div class="col col-sm-4"><input name="state" type="radio" value="new" style="margin-top: 12px;"></div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="row">
                                    <div class="col col-sm-8"><label class="col-md-4 control-label">In Progress</label></div>
                                    <div class="col col-sm-4"><input name="state" type="radio" value="in-progress" style="margin-top: 12px;"></div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="row">
                                    <div class="col col-sm-8"><label class="col-md-4 control-label">Finished</label></div>
                                    <div class="col col-sm-4"><input name="state" type="radio" value="finished" style="margin-top: 12px;"></div>
                                </div>
                            </div>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label" style="text-align:left !important;">Description</label>

                    <div class="col-md-9">
                        <textarea id="task_description" rows="5" class="form-control" name="description" value=""></textarea>

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <button onclick="updateTask()" class="btn btn-primary">
                            <i class="fa fa-btn fa-user"></i>Save
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('customJs')
<script type="text/javascript">
    function deleteDialog(task_id)
    {
        debugger;
        swal({   title: "Delete task with ID: " + task_id+"?",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Delete it!",   
            closeOnConfirm: false 
            
        }, 
        function(){  
            $.ajax({
                method: "POST",
                url: "/api/task/delete",
                data: { '_method': 'DELETE', 'task_id': task_id }
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
    
    function edit (obj) 
    {
        $('#task_edit').show();
        var $radios = $('input:radio[name=state]');
        $radios.filter('[value='+obj.task.state+']').prop('checked', true);
        
        var $description = $('textarea[name=description]');
        $description.prop('value', obj.task.description);
        
        var $id = $('input[name=id]');
        $id.prop('value', obj.task.id);
        
        var $user = $('input[name=user]');
        $user.prop('value', obj.task.f_name + ' ' + obj.task.l_name);
        
        
    }
    
    function updateTask () 
    {
        debugger;
        var task_id = $("input[name=id]").val();
        var radio = $("input[name=state]:checked").val();
        var description = $('textarea[name=description]').val();
        
        $.ajax({
            method: "POST",
            url: "/api/task/update",
            data: { '_method': 'PUT', 'id': task_id, 'state': radio, 'description': description }
        })
        .done(function( msg ) {
            swal("Updated!", "", "success"); 
            location.reload(); 
            console.log(msg);
        })
        .fail(function(msg) {
            swal("Error!", "Internal Server Error", "error"); 
            console.log(msg);
        });
    }

    function closeTaskModal () 
    {
        $('#task_edit').hide();
    }
</script>
@endsection