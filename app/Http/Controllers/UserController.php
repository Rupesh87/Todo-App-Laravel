<?php

namespace App\Http\Controllers;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Validator,Redirect,Response;
use Session;

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
        $is_admin = $request->has('is_admin') ? $request->is_admin : 0;
        // dd($is_admin);
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'is_admin'    => $is_admin,
                'password' => bcrypt($request->password)
            ]
        );
        $notification = array(
            'message' => 'Added successfully!',
            'alert-type' => 'success'
        );
        return redirect()->route('user.index')->with($notification);
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
        if ($request->password != null ) $update_user->password = bcrypt($request->password) ;
        $notification = array(
            'message' => 'Updated successfully!',
            'alert-type' => 'success'
        );
        $update_user->save() ;
        return redirect()->route('user.index')->with($notification);
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
        if ( Auth::id() === $id || $delete_user->is_super_admin === 1) {
            $notification = array(
                'message' => 'Cannot delete super_admin',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        $delete_user->delete() ;
        $notification = array(
            'message' => 'Deleted successfully!',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function change_password()
    {
        return view('user.change_password');
    }
    
    public function update_password(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = User::find(Auth::id());
        if (!\Hash::check($request->old_password, $user->password)) {
            $notification = array(
                'message' => 'Current password does not match!',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }
        $user->password = \Hash::make($request->password);
        $user->save();

        $notification = array(
            'message' => 'Password Updated!',
            'alert-type' => 'success'
        );
        return redirect()->route('task.staff')->with($notification);
    }
}
