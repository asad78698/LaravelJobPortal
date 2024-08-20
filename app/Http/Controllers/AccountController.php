<?php


namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobTypes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class AccountController extends Controller
{
    // Function to load registration page
    public function registration()
    {
        return view('front.account.register');
    }

    public function registerprocess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'Password' => 'required|min:6|same:CPassword',
            'CPassword' => 'required',
        ]);

        if ($validator->passes()) {
            $user  = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->Password);
            $user->save();

            session()->flash('success', 'User registered successfully');

            return response()->json([
                "status" => true,
                "errors" => []
            ]);
        }

        return response()->json([
            "status" => false,
            "errors" => $validator->errors()
        ]);
    }

    public function login()
    {
        return view('front.account.login');
    }

    public function authenitcateUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"

        ]);

        if ($validator->passes()) {

            if (Auth::attempt(["email" => $request->email, "password" => $request->password])) {

                return redirect()->route('account.profile');
            } else {
                return redirect()
                    ->route('account.login')->with("error", "Invalid email or password");
            }
        } else {

            return redirect()
                ->route('account.login')->withErrors($validator)->withInput(request()->only("email"));
        }
    }

    public function profilepage()
    {

        $id = Auth::user()->id;

        $user = User::find($id);

        return view(
            'front.account.profile',

            ["user" => $user]
        );
    }

    public function updateprofie(Request $request)
    {
        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->phone = $request->mobile;
        $user->save();

        session()->flash("success", "Profile Updated Successfully");


        return response()->json([
            "status" => "true",
            "errors" => []
        ]);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request)
    {

        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [

            'image' => 'required|image'
        ]);

        if ($validator->passes()) {

            $image = $request->file('image');
            $txt = $image->getClientOriginalExtension();
            $imageName =   $id . '-' . time() . '.' . $txt;
            $image->move(public_path('profilepic'), $imageName);


            // creating small image intervention php package works with laravel 10
            // iskaliye xampp mai gd install hona chaiye check in php folder php.ini file        

            // $manager = new ImageManager(Driver::class);
            // $image  = $manager->read('');


            File::delete(public_path('/profilepic/' . Auth::user()->image));

            User::where('id', $id)->update(['image' => $imageName]);
            session()->flash('success', 'Profile Picture Updated');

            return response()->json([
                "status" => true,
                "errors" => []
            ]);
        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function createjob()
    {
        $categories  =  Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobtypes =  JobTypes::orderBy('name', 'ASC')->where('status', 1)->get();

        return view(
            'front.account.job.create',
            [
                "categories" => $categories,
                "jobtypes" => $jobtypes
            ]
        );
    }

    public function saveJobs(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'title' => 'required|min:10|max:200',
            'category' => 'required',
            'jobtype' => 'required',
            'vacancy' => 'required|integer',
            'description' => 'required',
            'company_name' => 'required|min:5|max:50'
        ]);

        if ($validator->passes()) {

            $job = new Job();

            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->jobtypes_id = $request->jobtype;
            $job->user_id = Auth::user()->id;
            $job->vacancies = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibilies = $request->responsibility;
            $job->qualification = $request->qualification;
            $job->experience = $request->experience;
            $job->companyname = $request->company_name;
            $job->companylocation = $request->company_location;
            $job->companywebsite = $request->company_website;

            $job->save();

            session()->flash('success', 'Job Added Successfully');

            return response()->json([
                "status" => true,
                "errors" => []
            ]);
        } else {

            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function myjobs()
    {
        $jobs  = Job::where('user_id', Auth::user()->id)->with('jobType')->orderBy('created_at', 'desc')->paginate(10);

        return view('front.account.job.myjobs', [
            "jobs" => $jobs
        ]);
    }

    public function editmyjob(Request $request, $jobid)
    {

        $categories  =  Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobtypes =  JobTypes::orderBy('name', 'ASC')->where('status', 1)->get();

        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $jobid
        ])->first();

        if ($job == null) {

            return abort(404);
        }

        return view('front.account.job.editjob', [
            "categories" => $categories,
            "jobtypes" => $jobtypes,
            "job" => $job
        ]);
    }

    public function updatejob(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [

            'title' => 'required|min:10|max:200',
            'category' => 'required',
            'jobtype' => 'required',
            'vacancy' => 'required|integer',
            'description' => 'required',
            'company_name' => 'required|min:5|max:50'
        ]);

        if ($validator->passes()) {

            $job = Job::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->jobtypes_id = $request->jobtype;
            $job->user_id = Auth::user()->id;
            $job->vacancies = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibilies = $request->responsibility;
            $job->qualification = $request->qualifications;
            $job->experience = $request->experience;
            $job->companyname = $request->company_name;
            $job->companylocation = $request->company_location;
            $job->companywebsite = $request->company_website;

            $job->save();

            session()->flash('success', 'Job Updated Successfully');

            return response()->json([
                "status" => true,
                "errors" => []
            ]);
        } else {

            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

public function deletejob(Request $request)
    {

        $job =  Job::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId
        ])->first();
     

        if ($job == null) {

            return session()->flash('error', 'Error in Deleting Job');
        }

        Job::where('id', $request->jobId)->delete(); 
        session()->flash('success', 'Job Deleted Successfully');

        return response()->json([

            'status' => true
        ]);


    }
}
