<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
use App\Role;
use App\Task;
use Alert;
use DB;
use Excel;
use Form;
use View;
use Validator;
use Redirect;
use SimpleXMLElement;
use Datetime;

class TaskController extends Controller
{
    // GET => Get list of tasks for a specified user loading as partial
    public function userTasks ($user_id)
    {
        try 
        {
            $user = User::where('id', '=', $user_id)->first();
            if($user == null)
            {
                Alert::info("User does not exists!");
                return View::make('users/details_users', ['user' => $user, 'role' => $user->role, 'tasks'=>null])->render();
            }
            $tasks = DB::table('tasks')
                ->where('user_id', '=', $user_id)
                ->join('users', 'tasks.user_id', '=', 'users.id')
                ->select(['tasks.*', 'users.f_name', 'users.l_name'])->paginate(5);
                
            if($tasks->items() == null)
            {
                Alert::info("This user has no tasks!");
                return View::make('users/details_users', ['user' => $user, 'role' => $user->role, 'tasks'=>null])->render();
            }
                
            // $tasksPartial = View::make('partials/_tasks_list', ['tasks'=>$tasks])->render();
            // return Response::json(['tasksPartial' => $tasksPartial]);
            return view('users/details_users', ['user' => $user, 'role' => $user->role, 'tasks' => $tasks])->render();
        }
        catch (Exception $ex) 
        {
            Alert::info("This user has no tasks!");
        }
    }
    
    // GET => redirect to task list view
    public function index ()
    {
        try 
        {
            $user = Auth::user();
            $query = DB::table('tasks');
            if($user->role->name == 'regular')
            {
                $query->where('tasks.user_id', '=', $user->id);
            }
            $tasks = $query->join('users', 'tasks.user_id', '=', 'users.id')
                ->select(['tasks.*', 'users.f_name', 'users.l_name'])->paginate(5);
            // return response(['tasks'=>$tasks, 200]);            
            return view('tasks/list_tasks', ['tasks' => $tasks]);
        }
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // GET => redirect to create new task view
    public function create ()
    {
        try 
        {
            $taskStates = [
                (object) ['key'=>'new','name'=>'New'], 
                (object) ['key'=>'in-progress','name'=>'In Progress'],
                (object) ['key'=>'finished','name'=>'Finished']
            ];
            return view('tasks/create_task', ['states'=>$taskStates]);
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    public function store (Request $request) 
    {
        try 
        {
            $user = Auth::user();
            $validator = Validator::make(
                $request->all(),
                [
                    'state' => 'required|in:new,in-progress,finished',
                    'description' => 'string'
                ]
            );
            if($validator->fails()) 
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            
            $task = new Task();
            $task->state = $request->input('state');
            if($request->input('description') !== null)
            {
                $task->description = $request->input('description');
            }
            
            $task->user_id = $user->id;
            
            $task->save();
            
            Alert::basic('Task created successfully!', 'Success');
            return redirect()->back();
        } 
        catch (Exception $ex) 
        {
            Alert::error($ex->getMessage(), 'Server error');
            return redirect()->back()->withErrors($validator)->withInput($request->except('email'));
        }
    }
    
    // DELETE => Delete task
    public function destroy (Request $request)
    {
        try 
        {
            $user = Auth::user();
            $validator = Validator::make(
                $request->all(),
                ['task_id' => 'required|exists:tasks,id']
            );
            if($validator->fails())
            {
                Alert::error('Error!', $validator->$messages[0]);
            }
            $taskToDelete = Task::where('id', '=', $request->input('task_id'))
                ->where('user_id', '=', $user->id)
                ->first();
            // Delete the task only if the user has created it or the user is an Admin
            
            if($taskToDelete == null)
            {
                if($user->role->name == 'admin')
                {
                    $taskToDelete = Task::where('id', '=', $request->input('task_id'))
                        ->first();
                }
                if($taskToDelete == null)
                {
                    Alert::error("Generic error", "This task does not belog to you!");
                    return redirect()->back();
                }
            }
            
            $deletedTask = $taskToDelete->delete();
            return redirect()->back();
        } 
        catch (Exception $ex) 
        {
            Alert::error('Something went wrong', 'Server error');
        }
    }
    
    // PUT => Update task
    public function update (Request $request) 
    {
        try 
        {
            $user = Auth::user();
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => 'required|exists:tasks,id',
                    'state' => 'required|in:new,in-progress,finished',
                    'description' => 'string'
                ]
            );
            if($validator->fails()) 
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            
            $taskToUpdate = Task::where('id', '=', $request->input('id'))
                    ->where('user_id', '=', $user->id)->first();
                    
            if($taskToUpdate == null)
            {
                if($user->role->name == 'admin')
                {
                    $taskToUpdate = Task::where('id', '=', $request->input('id'))->first();
                }
                
                if($taskToUpdate == null)
                {
                    Alert::error("Generic error", "This is not your task!");
                    return redirect()->back();
                }
                   
            }
            
            $taskToUpdate->state = $request->input('state');
            if($request->input('description') !== null)
            {
                $taskToUpdate->description = $request->input('description');
            }
            
            $taskToUpdate->user_id = $user->id;
            
            $taskToUpdate->save();
            
            // Alert::basic('Task created successfully!', 'Success');
            return 'Task updated successfully!';
        } 
        catch (Exception $ex) 
        {
            // Alert::error($ex->getMessage(), 'Server error');
            return $ex->getMessage();
        }
    }
    
    // GET => get Task details
    public function details ($task_id) 
    {
        $requestor = Auth::user();
        
        if($requestor->role->name == 'admin')
        {
            $task = Task::where('id', '=', $task_id)->first();
        }
        else 
        {
            $task = Task::where('id', '=', $task_id)
                ->where('user_id', '=', $requestor->id)->first();
        }
        
        if($task == null)
        {
            Alert::info('Task does not exist!');
            return redirect()->back();
        }
        
        return View::make('tasks/details_task', ['user' => $requestor, 'task' => $task]);    
    }
    
    // GET => download list of tasks
    public function downloadTaskList ($file_ext) 
    {
        try 
        {
            $user = Auth::user();
            
            $tasksArray = Task::select(
                    [    'id as ID', 
                        'state as STATE', 
                        'description as DESCRIPTION', 
                        'created_at as CREATED', 
                        'updated_at as UPDATED'
                    ])->get()->toArray();
            
            if($user->role->name == 'regular')
            {
                $tasksArray = Task::where('user_id', '=', $user->id)
                    ->select(
                    [
                        'id as ID', 
                        'state as STATE', 
                        'description as DESCRIPTION', 
                        'created_at as CREATED', 
                        'updated_at as UPDATED'
                    ])->get()->toArray();
            }
            $now = new Datetime("NOW");
            $fileName = $user->f_name.$user->l_name."_Tasks_" . $now->format('Y-m-d\TH:i:s');
            // return response(['tasks'=>$tasksArray, 200]);
            
            if($tasksArray != null)
            {
                switch ($file_ext) {
                    case 'csv':
                        $this->generateCSVFile($tasksArray, $fileName);
                        break;
                        
                    case 'xml':
                        $this->generateXMLFile($tasksArray, $fileName);
                        break;
                    
                    default:
                        $this->generateCSVFile($tasksArray, $fileName);
                        break;
                }
            }
            else
            {
                Alert::info("You don't have tasks");
            }
            return redirect()->back();
        } 
        catch (Exception $ex) 
        {
            Alert::error("Something went wrong!", "Generic Error!");
            return redirect()->back();
        }
    }
    
    private function generateCSVFile($tasksArray, $fileName) 
    {
        try 
        {
            Excel::create($fileName, function($excel) use($tasksArray) {
            
                $excel->sheet('Sheet 1', function($sheet) use($tasksArray) {
                    $sheet->fromArray($tasksArray);
                    
                    // Set multiple column formats
                    $sheet->setColumnFormat(array(
                        'D' => 'yyyy-mm-dd',
                        'E' => 'yyyy-mm-dd',
                    ));
                });
            })->export('csv');
        } 
        catch (Exception $ex) 
        {
            Alert::error("Something went wrong!", "Generic Error!");
            return redirect()->back();
        }
    }
    
    private function generateXMLFile ($tasksArray, $fileName)
    {
        try
        {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><tasks></tasks>");
            $xml = $this->array_to_xml($tasksArray, $xml);
            
            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename='. $fileName . '.xml');
            $stringFile = $xml->asXML();
            echo $stringFile;
            exit();
        }
        catch (Exception $ex)
        {
            Alert::error("Something went wrong!", "Generic Error!");
            return redirect()->back();
        }
    }
    
    public function array_to_xml(array $data, SimpleXMLElement $xml)
    {
        foreach($data as $key => $val) {
            if(is_array($val)) {

                if(is_numeric($key)) {
                    $node  = $xml->addChild('task');
                    $nodes = $node->getName('task');
                } else {

                    $node  = $xml->addChild($key);
                    $nodes = $node->getName($key);
                }

                $node->addChild($nodes, self::array_to_xml($val, $node));
            } else {
                $xml->addChild($key, $val);
            }
        }
        return $xml;
    }
}
