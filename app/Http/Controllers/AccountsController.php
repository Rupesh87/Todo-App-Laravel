<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Str;
use Carbon\Carbon;
use App\Mail\TestEmail;
use Mail;

class AccountsController extends Controller
{
    public function validatePasswordRequest(Request $request)
    {
        $user = User::where('email', '=', $request->email)->get();
        //Check if the user exists
        if ($user->count() < 1) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }
        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')
        ->where('email', $request->email)->first();
        if ($this->sendResetEmail($request->email, $tokenData->token)) {
            $notification = array(
                'message' => 'A reset link has been sent to your email address.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'A Network Error occurred. Please try again.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }
    }

    private function sendResetEmail($email, $token)
    {
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->first();
        $link = request()->getHost() . '/password/reset/' . $token . '?email=' . urlencode($user->email);
        
        try {
            Mail::to($email)->send(new TestEmail('Please check this link to reset Password '. $link));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required|confirmed'
        ]);
        // dd( $validator->messages());
        //check if input is valid before moving on
        if ($validator->fails()) {
            return redirect()->back()->withErrors( $validator->messages());
        }

        $password = $request->password;
        // Validate the token
        $tokenData = DB::table('password_resets')
        ->where('token', $request->token)->first();
        // Redirect the user back to the password reset request form if the token is invalid
        if (!$tokenData) 
        {
            $notification = array(
                'message' => "Token doesn't match!",
                'alert-type' => 'warning'
            );
            return redirect('password/reset')->with($notification);
        }
        $user = User::where('email', $tokenData->email)->first();
        // Redirect the user back if the email is invalid
        if (!$user) return redirect()->back()->withErrors(['email' => 'Email not found']);
        //Hash and update the new password
        $user->password = \Hash::make($password);
        $user->update(); //or $user->save();

        //login the user immediately they change password successfully
        Auth::login($user);

        //Delete the token
        DB::table('password_resets')->where('email', $user->email)
        ->delete();
        return redirect('home')->with([
            'message' => 'Welcome Back. Password has been reset!',
            'alert-type' => 'warning'
        ]);
    }

}
