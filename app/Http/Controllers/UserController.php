<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
use App\Role;
use Alert;
use DB;
use Form;
use View;
use Validator;
use Redirect;
use Mail;
use Hash;


class UserController extends Controller
{
    // Inner function to send password to created users
    public function sendMail($template, $data, $to, $subject) 
    {
        Mail::send($template, $data, function($message) use($to, $subject) {
            $message-> to($to);
            $message-> subject($subject);
        });
     }
    
    // GET => redirect to users list view
    public function index ()
    {
        try 
        {
            $admin = Auth::user();
            $query = DB::table('users');
            $query->where('users.id', '!=', $admin->id);
            
             $users = $query->join('roles', 'users.role_id', '=', 'roles.id')
                ->select(['users.id', 'users.f_name', 'users.l_name', 'users.email', 'roles.display_name'])->paginate(5);
            
            return view('users/list_users', ['users' => $users]);
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // GET => redirect to create new user view
    public function create ()
    {
        try 
        {
            $roles = Role::all(['id', 'display_name']);
            return View::make('users/create_users', ['roles' => $roles]);
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // POST => Store a new user
    public function store (Request $request) 
    {
        try 
        { // create user
            
            $DEFAULT_PASS = '123456';
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|unique:users,email',
                    'role_id' => 'required|exists:roles,id'
                ]
            );
            
            if($validator->fails()) 
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
                
                // $roles = Role::all(['id', 'display_name']);
                // return View::make('users/create_users',['roles' => $roles])->withInput($request->all())->withErrors($validator);
            }
            
            $user = new User();
            $user->f_name = $request->input('first_name');
            $user->l_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->role_id = $request->input('role_id');
            $user->password = Hash::make($DEFAULT_PASS);
            
            $user->save();
            
            $this->sendMail('emails.send_password', ['password'=>$DEFAULT_PASS, 'email' => $user->email], $user->email, 'Task Management Password');
            
            Alert::basic('User created successfully!', 'Success');
            return Redirect::to('/user/create');
        } 
        catch (Exception $ex) 
        {
            Alert::error($ex->getMessage(), 'Server error');
            return redirect()->back()->withErrors($validator)->withInput($request->except('email'));
        }
    }
    
    // GET => redirect to the editing view
    public function edit ($user_id)
    {
        try 
        {
           $validator = Validator::make(
                ['user_id' => $user_id],
                [
                    'user_id' => 'required|exists:users,id',
                ]
            ); 
            
            if($validator->fails()) 
            {
                Alert::error('Generic Error', 'User not found!');
                return redirect()->back()->withErrors($validator);
            }
            $userToEdit = User::where('id', '=', $user_id)->first();
            $roles = Role::all(['id', 'display_name']);
            return View::make('users/edit_users', ['user' => $userToEdit, 'role' => $userToEdit->role, 'roles' => $roles]);
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // PUT => update user data
    public function update (Request $request) 
    {
        try 
        { // update user
            //check if all filds are OK
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => 'required|exists:users,id',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'role_id' => 'required|exists:roles,id'
                ]
            );
            
            if($validator->fails()) 
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            // get the user
            $userToEdit = User::where('id', '=', $request->input('id'))->first();
            // check if entered email is the same or another new one
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'unique:users,email,'.$userToEdit->id
                ]
            );
            
            if($validator->fails()) 
            { //if the email is another existing email
                return redirect()->back()->withErrors($validator);
            }
            
            $userToEdit->f_name = $request->input('first_name');
            $userToEdit->l_name = $request->input('last_name');
            $userToEdit->email = $request->input('email');
            $userToEdit->role_id = $request->input('role_id');
            
            $userToEdit->save();
            Alert::basic('User update successfully!', 'Success');
            return redirect()->back();
        } 
        catch (Exception $ex) 
        {
            Alert::error($ex->getMessage(), 'Server error');
            return redirect()->back()->withErrors($validator)->withInput($request->except('email'));
        }
    }
    
    // AJAX DELETE => Delete user and all its tasks from DB
    public function destroy (Request $request)
    {
        try 
        {
            $validator = Validator::make(
                $request->all(),
                ['user_id' => 'required|exists:users,id']
            );
            if($validator->fails())
            {
                Alert::error('Error!', $validator->$messages[0]);
            }
            $userToDelete = User::where('id', '=', $request->input('user_id'))->first();
            // Remove all the tasks created from this user
            $userTasks = $userToDelete->tasks()->delete();
            // Delete the user itself
            $userToDelete->delete();
            return redirect()->back();
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // GET => redirect to the user details view
    public function details ($user_id) 
    {
        $admin = Auth::user();
        $user = User::where('id', '=', $user_id)
            ->where('id', '!=', $admin->id)
            ->first();
        if($user == null)
        {
            Alert::info('User does not exist!');
            return redirect()->back();
        }
        return View::make('users/details_users', ['user' => $user, 'role' => $user->role, 'tasks'=> null]);
    }
}
