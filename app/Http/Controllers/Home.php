<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;

class Home extends Controller
{   

    public function index(){
       

        $categories = Category::where('status', 1)->take(8)->get();
        $FeaturedJobs = Job::where('status', 1)->where('isFeatured', 1)->with('jobType')->orderBy('created_at', 'desc')->take(3)->get();
        $LatestJobs = Job::where('status', 1)->with('jobType')->orderBy('created_at', 'desc')->take(3)->get();

        return view('/front/home', [
            "categories" => $categories,
            "FeaturedJobs" => $FeaturedJobs,
            "LatestJobs" =>  $LatestJobs
        ]); 
    }


    public function searchJobs(){
        
    }
    
    
}
