@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="action">
                </div>
            <h3>MY Task</h3></div>
                <div class="card-body">
                <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Task Title</th>
                            <th scope="col">Assign To</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Completed</th>
                            <th>Created At</th>
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        @if ( !$tasks->isEmpty() ) 
                        <tbody>
                        @foreach ( $tasks as $key => $task)
                            <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $task->task_title }}</td>
                            <td>{{ isset($task->user->name) ? $task->user->name : '' }}</td>
                            <td>
                                @if ( $task->priority == 0 )
                                    <span class="label label-green">Normal</span>
                                @else
                                    <span class="label label-danger">High</span>
                                @endif
                            </td>
                            <td>
                                @if ( !$task->completed )
                                    <a href="{{ route('task.completed', ['id' => $task->id]) }}" class="btn btn-warning btn-sm"> Mark as completed</a>
                                @else
                                    <span class="btn btn-success btn-sm">Completed</span>
                                @endif
                            </td>
                            <td>{{ Carbon\Carbon::parse($task->created_at)->format('m-d-Y') }}</td>
                            <td>
                                <a href="{{ route('task.view', ['id' => $task->id]) }}" class="btn btn-primary btn-sm"> <i class="fas fa-cloud"></i><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span><svg class="bi bi-eye-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.5 8a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z" clip-rule="evenodd"/>
                                </svg> View Detail</a>
                            </td>
                            </tr>
                            @endforeach
                            @else 
                                <p><em>There are no tasks assigned yet</em></p>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                    
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection