<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Illuminate\Http\Request;
use Validator,Redirect,Response;
use UxWeb\SweetAlert\SweetAlert;
class TaskController extends Controller
{

    public function index()
    {
        $data = [
            'tasks'  => Task::with('user')->orderBy('created_at', 'desc')->paginate(10),
            'users' => User::all(),
            'auth_id' => auth()->id()
        ];
        // dd($data['tasks']);
        return view('task.task')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function staff()
    {
        $data = [
            'tasks'  => Task::with('user')->orderBy('created_at', 'desc')->paginate(10),
            'users' => User::all(),
            'auth_id' => auth()->id()
        ];
       
        return view('task.staff')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            'task_title' => ['required', 'string', 'max:255'],
            'user_id' => 'required|numeric',
            'admin_id' => 'required|numeric',
            'duedate' => 'required',
            'status' => 'required',
            'priority' => 'required|numeric',
        ]);
        $task = Task::create($data);
        return Redirect::to("tasks")->withSuccess('Great! Form successfully submit with validation.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit_task = Task::find($id); 
        $tasks  = Task::orderBy('created_at', 'desc')->paginate(10);
        $users = User::all() ;
        return view('task.task')->with('tasks', $tasks)
                                ->with('auth_id', auth()->id())
                                ->with('edit_task', $edit_task)
                                ->with('users', $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update_task = Task::find($id) ;

        $data = request()->validate([
            'task_title' => ['required', 'string', 'max:255'],
            'user_id' => 'required|numeric',
            'admin_id' => 'required|numeric',
            'duedate' => 'required',
            'status' => 'required|numeric',
            'priority' => 'required|numeric',
            'completed' => 'required|numeric',
        ]);

        $update_task->task_title = $request->task_title; 
        $update_task->user_id       = $request->user_id;
        $update_task->admin_id    = $request->admin_id;
        $update_task->duedate = $request->duedate;
        $update_task->status   = $request->status;
        $update_task->priority  = $request->priority;
        $update_task->completed  = $request->completed;

        $update_task->save() ;
        
        // Session::flash('success', 'Task was sucessfully edited') ;
        return redirect()->route('task.show')->with('success', 'Login Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    //     SweetAlert::message('Robots are working!');
    //    return redirect('tasks');
    //     exit;
        $delete_task = Task::find($id) ;
        $delete_task->delete() ;
        // Session::flash('success', 'Task was deleted') ;
        return redirect()->back();
    }

    public function completed($id)
    {
        $task_complete = Task::find($id) ;
        $task_complete->completed = 1;
        $task_complete->save() ;
        return redirect()->back()->with('success', 'Login Successfully!');;
    }

    public function view($id)  {

        $task_view = Task::find($id) ;
        $assign_to = User::find($task_view->user_id, ['name']);
        // dd($assign_to);
        // Get task created and due dates
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->created_at);
        $to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->duedate ); // add here the due date from create task

        $current_date = \Carbon\Carbon::now();
 
        $formatted_from = $from->toRfc850String();  
        $formatted_to   = $to->toRfc850String();

        $diff_in_days = $current_date->diffInDays($to);

        $is_overdue = ($current_date->gt($to) ) ? true : false ;

        return view('task.view')
            ->with('task_view', $task_view) 
            ->with('diff_in_days', $diff_in_days )
            ->with('is_overdue', $is_overdue) 
            ->with('formatted_from', $formatted_from ) 
            ->with('assign_to', $assign_to ) 
            ->with('formatted_to', $formatted_to );
    }

}
