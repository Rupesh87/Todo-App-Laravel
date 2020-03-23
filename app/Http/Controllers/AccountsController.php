<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
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
        $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            $data = ['message' => 'Please check this link to reset Password'. $link];

            Mail::send('emails.reminder', ['data' => $data], function ($m) use ($data) {
                $m->from('noreply@app.com', 'TO Do');
    
                $m->to(emaill, 'Dear User')->subject('Your Password reset Link');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required|confirmed'
        ]);

        //check if input is valid before moving on
        if ($validator->fails()) {
            $notification = array(
                'message' => 'Please complete the form.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }

        $password = $request->password;
        // Validate the token
        $tokenData = DB::table('password_resets')
        ->where('token', $request->token)->first();
        // Redirect the user back to the password reset request form if the token is invalid
        if (!$tokenData) return view('auth.passwords.email');

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

        //Send Email Reset Success Email
        if ($this->sendSuccessEmail($tokenData->email)) {
            return view('index');
        } else {
            $notification = array(
                'message' => 'A Network Error occurred. Please try again.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }

    }

    private function sendSuccessEmail($email)
    {
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->first();
        $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            Mail::send('emails.test', function ($m)  {
                $m->from('noreply@email.com', 'Your Application');
    
                $m->to($email, "User")->subject('Your Password has been updated!');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
