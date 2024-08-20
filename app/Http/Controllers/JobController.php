<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobTypes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function index(Request $request) {
        $categories = Category::where('status', 1)->get();
        $jobtypes = JobTypes::where('status', 1)->get();
        
        $jobs = Job::where('status', 1);
    
        // Apply keyword search if provided
        if (!empty($request->keyword)) {
            $jobs->where(function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%')
                      ->orWhere('keywords', 'like', '%' . $request->keyword . '%');
            });
        }
    
        // Apply location filter if provided
        if (!empty($request->location)) {
            $jobs->where('location', $request->location);
        }

        if(!empty($request->category)){
            $jobs->where('category_id', $request->category);

        }
         
        $jobTypeArray = [];
        // Search using Job Type
        if(!empty($request->jobtype)) {
            $jobTypeArray = explode(',',$request->jobtype);

          $jobs->whereIn('jobtypes_id',$jobTypeArray);
        }


        if(!empty($request->experience)){
            $jobs->where('experience', $request->experience);
        }
    
        // Add relationship and sorting
        $jobs->with('jobType', 'category');
        
         if($request->sort == 'asc'){

            $jobs->orderBy('created_at', 'asc');
         }

         else{
            $jobs->orderBy('created_at', 'desc');
         }
    
        // Paginate results
        $jobs = $jobs->paginate(9);
    
        return view('front.jobpage', [
            'categories' => $categories,
            'jobtypes' => $jobtypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
          
        ]);
    }

    public function jobdetails($jobid){

        $job = Job::where([
            'id' => $jobid, 
            'status' =>1
            ])->with(['JobType', 'category'])->first();

       if($job == null){
        abort(404);
       }

       return view('front.jobdetails', [
        'job' => $job
       ]);

}

public function applyjob(Request $request){
   
    $job = Job::where('id', $request->job_id)->first();

    if($job == null){
        session()->flash('error', 'Job Not Found');
        return response()->json([
            'status' => false,
            'message' => 'Job Not Found'
        ]);
        }

        $applicationChcek = JobApplication::where([
            'user_id' => auth()->user()->id,
            'jobs_id' => $job->id
        ])->count();

        if($applicationChcek > 0){
            session()->flash('error', 'You have already applied for this job');
            return response()->json([
                'status' => false,
                'message' => 'You have already applied for this job'
            ]);
        }

        $application = new JobApplication();
        $application->jobs_id = $job->id;
        $application->user_id = auth()->user()->id;
        $application->employee_id = $job->user_id;
        $application->date_applied = now();
        $application->save();

        $employer = User::where('id', $job->user_id)->first();
          
        $mailData = [
            'employer' => $employer,
            'job' => $job,
            'user' => auth()->user()
        ];
       
        session()->flash('success', 'Job Applied Successfully');

        
        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

       
        return response()->json([
            'status' => true,
            'message' => 'Job Applied Successfully'
        ]);
    
}
    
}
