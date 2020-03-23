@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="action">
               
                <a href="{{ url('/tasks/create') }}" class="btn btn-primary float-right">Create Task</a>

                </div>
            <h3>Manage Task</h3></div>
                <div class="card-body">
                <div class="row">
                <div class="col-md-8">
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
                                </svg></a>
                                <a href="{{ route('task.edit', ['id' => $task->id]) }}" class="btn btn-primary btn-sm"> <svg class="bi bi-pencil-square" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 010 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 01.707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 00-.121.196l-.805 2.414a.25.25 0 00.316.316l2.414-.805a.5.5 0 00.196-.12l6.813-6.814z"></path>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 002.5 15h11a1.5 1.5 0 001.5-1.5v-6a.5.5 0 00-1 0v6a.5.5 0 01-.5.5h-11a.5.5 0 01-.5-.5v-11a.5.5 0 01.5-.5H9a.5.5 0 000-1H2.5A1.5 1.5 0 001 2.5v11z" clip-rule="evenodd"></path>
                                </svg> </a> 
                                <a href="{{ route('task.delete', ['id' => $task->id]) }}" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span><svg class="bi bi-x-square-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M2 0a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V2a2 2 0 00-2-2H2zm9.854 4.854a.5.5 0 00-.708-.708L8 7.293 4.854 4.146a.5.5 0 10-.708.708L7.293 8l-3.147 3.146a.5.5 0 00.708.708L8 8.707l3.146 3.147a.5.5 0 00.708-.708L8.707 8l3.147-3.146z" clip-rule="evenodd"/>
                                </svg></a>
                            </td>
                            </tr>
                            @endforeach
                            @else 
                                <p><em>There are no tasks assigned yet</em></p>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                <div class="card">
                @if (Request::is('tasks'))
                    <div class="card-header">{{ __('Add New Task') }}</div>
                    <form method="POST" action="{{ url('/tasks/store') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="task_title" class="col-md-4 col-form-label text-md-right">{{ __('Task Title') }}</label>
                            <div class="col-md-6">
                                <input id="task_title" type="text" class="form-control @error('task_title') is-invalid @enderror" name="task_title" value="{{ old('name') }}"  autocomplete="name" autofocus>
                                
                                <input name="admin_id" value={{$auth_id}} hidden/>
                                @error('task_title')
                                    <span class="iValidator::make($valid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                            <textarea id="w3mission" rows="4" cols="17" id="description"  class="form-control @error('description') is-invalid @enderror" name="description">
                            </textarea>
                                @error('description')
                                    <span class="iValidator::make($valid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">{{ __('Assign To') }}</label>

                            <div class="col-md-6">
                            <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" >
                            <option  disabled> Please select</option>
                            @foreach($users as $user)
                                <option value={{$user->id}} > {{ $user->name }}</option>
                            @endforeach
                            </select>
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="priority" class="col-md-4 col-form-label text-md-right">{{ __('Priority') }}</label>

                            <div class="col-md-6">
                                <select id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror">
                                    <option value="0">Normal</option>
                                    <option value="1">High</option>
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6">
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="duedate" class="col-md-4 col-form-label text-md-right">{{ __('Due Date') }}</label>

                            <div class="col-md-6">
                                <input id="duedate" type="date" class="date form-control @error('duedate') is-invalid @enderror" name="duedate" value="{{ old('name') }}"  autocomplete="name" autofocus>

                                @error('duedate')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                @elseif (\Request::route()->getName() == 'task.edit')
                <div class="card-header">{{ __('Update Task') }}</div>
                    <form method="POST" action="{{ url('tasks/update', [ 'id' => $edit_task->id ] ) }}">
                        @csrf
                        <div class="form-group row">
                            <label for="task_title" class="col-md-4 col-form-label text-md-right">{{ __('Task Title') }}</label>
                            <div class="col-md-6">
                                <input id="task_title" type="text" class="form-control @error('task_title') is-invalid @enderror" name="task_title" value="{{ $edit_task->task_title }}"  autocomplete="name" autofocus>
                                <input name="id" value="{{$edit_task->id}}" hidden/>
                                <input name="admin_id" value={{$auth_id}} hidden/>
                                @error('task_title')
                                    <span class="iValidator::make($valid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                            <textarea id="w3mission" rows="4" cols="17" id="description"  class="form-control @error('description') is-invalid @enderror" name="description">
                                {{$edit_task->description}}
                            </textarea>
                                @error('description')
                                    <span class="iValidator::make($valid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">{{ __('Assign To') }}</label>

                            <div class="col-md-6">
                            <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" >
                            <option disabled> Please select</option>
                            @foreach($users as $user)
                                <option  value={{$user->id}}
                                @if ($user->id == $edit_task->user_id)
                                    selected="selected"
                                @endif
                                 > {{ $user->name }}</option>
                            @endforeach
                            </select>
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="priority" class="col-md-4 col-form-label text-md-right">{{ __('Priority') }}</label>

                            <div class="col-md-6">
                                <select id="priority" name="priority"  class="form-control @error('priority') is-invalid @enderror">
                                    <option value="0"  
                                    @if (!$edit_task->priority)
                                        selected="selected"
                                    @endif>Normal</option>
                                    <option value="1"
                                    @if ($edit_task->priority)
                                        selected="selected"
                                    @endif>High</option>
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6">
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1"
                                    @if ($edit_task->status)
                                        selected="selected"
                                    @endif>Active</option>
                                    <option value="0"
                                    @if (!$edit_task->status)
                                        selected="selected"
                                    @endif>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="completed" class="col-md-4 col-form-label text-md-right">{{ __('Completed') }}</label>

                            <div class="col-md-6">
                                <select id="completed" name="completed" class="form-control @error('completed') is-invalid @enderror">
                                    <option value="1"
                                    @if ($edit_task->completed)
                                        selected="selected"
                                    @endif>Yes</option>
                                    <option value="0"
                                    @if (!$edit_task->completed)
                                        selected="selected"
                                    @endif>No</option>
                                </select>
                                @error('completed')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="duedate" class="col-md-4 col-form-label text-md-right">{{ __('Due Date') }}</label>

                            <div class="col-md-6">
                                <input id="duedate" type="date" class="date form-control @error('duedate') is-invalid @enderror" value={{ Carbon\Carbon::parse($edit_task->duedate)->format('m-d-Y') }} name="duedate"   autocomplete="name" autofocus>
                                @error('duedate')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
                </div>
                </div>
                    
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection