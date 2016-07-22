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
                            </td>
                            
                            <td>{{$task->created_at}}</td>
                            <td>{{$task->updated_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $tasks->render() !!}
        </div>
    </div>