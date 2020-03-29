<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Illuminate\Http\Request;
use Validator,Redirect,Response;
use UxWeb\SweetAlert\SweetAlert;
use Session;
use App\Notifications\TaskNotification;
class TaskController extends Controller
{

    public function index()
    {
        // $request->session()->flash('success', 'Post created successfully.');
        $data = [
            'tasks'  => Task::with('user')->orderBy('created_at', 'desc')->paginate(10),
            'users' => User::where('is_admin', '=', 0)->get(),
            'auth_id' => auth()->id()
        ];
        // dd($data['users']);
        if($data['users']->count() == 0)
        {
            Session::flash('message', 'Please create staff first to assign task!') ;
            Session::flash('alert-type', 'warning'); 
        }
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
            'description' => ['required', 'string', 'max:255']
        ]);
        $task = Task::create($data);
        $notification = array(
            'message' => 'Added Task successfully!',
            'alert-type' => 'success'
        );
        return Redirect::to("tasks")->with($notification);
    }

    /**e' => 'required',
            'status' => 'required',
            'priority' => 'required|numeric',
            'description' => ['required', 'string', 'max:255']
        ]);
        $task = Task::create($data);
        $notification = array(
            'message' => 'Added Task successfully!',
            'alert-type' => 'success'
        );
        return Redirect::to("tasks")->with($notification);
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
            'description' => ['required', 'string', 'max:255'],
        ]);

        $update_task->task_title = $request->task_title; 
        $update_task->user_id       = $request->user_id;
        $update_task->admin_id    = $request->admin_id;
        $update_task->duedate = $request->duedate;
        $update_task->status   = $request->status;
        $update_task->priority  = $request->priority;
        $update_task->completed  = $request->completed;
        $update_task->description  = $request->description; 

        $update_task->save();
        return redirect()->route('task.show')->with(['message' => 'Updated successfully!',
        'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete_task = Task::find($id) ;
        $delete_task->delete() ;
        $notification = array(
            'message' => 'Deleted successfully!',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function completed($id)
    {
        $task_complete = Task::find($id) ;
        $task_complete->completed = 1;
        $task_complete->save();
        if(auth()->user()->is_admin){
            $user = User::find($task_complete->user_id);
            $message = $task_complete->task_title. ' task title has been set to completed by '. auth()->user()->name. '(Admin)';
            $user->notify(new TaskNotification($message,$id));
        } else {
            $user = User::find($task_complete->admin_id);
            $message = $task_complete->task_title. ' task has been completed by '. auth()->user()->name;
            $user->notify(new TaskNotification($message, $id));
        }
        return redirect()->back()->with(['message' => 'Marked as complected!',
        'alert-type' => 'success']);;
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
