<?php

namespace App\Http\Controllers;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Validator,Redirect,Response;

class UserController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('user.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $
        // return view('task.task')->with($data);
        return view('user.create') ;
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
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' =>  'required|confirmed|min:8'
        ]);
        // dd($request->all());
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'is_admin'    => $request->is_admin,
                'password' => bcrypt($request->password)
            ]
        );
        return redirect()->route('user.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::paginate(15);
        $old_user = User::find($id) ;
        return view('user.index')
                ->with('old_user', $old_user )
                ->with('users', $users ) ;

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update_user = User::find($id);
        $data = request()->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
            'password' =>  'confirmed'
        ]);

        $update_user->name  = $request->name; 
        $update_user->email = $request->email;
        $update_user->is_admin = $request->is_admin ? $request->is_admin : 0;

        // update pass if available
        if ($request->has('password') ) $update_user->password = bcrypt($request->password) ;
        $update_user->save() ;
        return redirect()->route('user.index') ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete_user = User::find($id);
        if ( Auth::id() == $id || \Auth::user()->is_super_admin === 1) {
	        return redirect()->back();
        }
        $delete_user->delete() ;
        return redirect()->back();
    }

}
