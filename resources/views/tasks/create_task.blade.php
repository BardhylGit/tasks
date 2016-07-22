<!--TaskController@create-->

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Add New Task</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/api/task/store') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Tast State</label>
    
                            <div class="col-md-6">
                                @foreach($states as $state)
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col col-sm-6"><label class="col-md-4 control-label">{{$state->name}}</label></div>
                                            <div class="col col-sm-6"><input name="state" type="radio" value="{{$state->key}}" style="margin-top: 12px;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Description</label>

                            <div class="col-md-6">
                                <textarea rows="5" class="form-control" name="description" value="{{ old('description') }}"></textarea>

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Create Task
                                </button>
                                <button type="reset" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script type="text/javascript">
    $(function() {
        var $radios = $('input:radio[name=state]');
        if($radios.is(':checked') === false) 
        {
            $radios.filter('[value=new]').prop('checked', true);
        }
    });
</script>
@endsection
