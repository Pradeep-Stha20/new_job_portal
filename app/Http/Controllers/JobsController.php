<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use App\Services\JobMatchingService;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobsController extends Controller
{
    protected $matchingService;
    protected $fraudService;

    public function __construct(JobMatchingService $matchingService, FraudDetectionService $fraudService)
    {
        $this->matchingService = $matchingService;
        $this->fraudService = $fraudService;
    }

    // This method will show jobs page
    public function index(Request $request) {
        $categories = Category::where('status',1)->get();
        $jobTypes = JobType::where('status',1)->get();

        $jobs = Job::where('status',1);

        // Search using keyword
        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function($query) use ($request) {
                $query->orWhere('title','like','%'.$request->keyword.'%');
                $query->orWhere('keywords','like','%'.$request->keyword.'%');
            });
        }

        // Search using location
        if(!empty($request->location)) {
            $jobs = $jobs->where('location',$request->location);
        }

        // Search using category
        if(!empty($request->category)) {
            $jobs = $jobs->where('category_id',$request->category);
        }

        $jobTypeArray = [];
        // Search using Job Type
        if(!empty($request->jobType)) {
            $jobTypeArray = explode(',',$request->jobType);

            $jobs = $jobs->whereIn('job_type_id',$jobTypeArray);
        }

        // Search using experience
        if(!empty($request->experience)) {
            $jobs = $jobs->where('experience',$request->experience);
        }


        $jobs = $jobs->with(['jobType','category']);

        if($request->sort == '0') {
            $jobs = $jobs->orderBy('created_at','ASC');
        } else {
            $jobs = $jobs->orderBy('created_at','DESC');
        }
        

        $jobs = $jobs->paginate(9);


        return view('front.jobs',[
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    // This method will show job detail page
    public function detail($id) {

        $job = Job::where([
                            'id' => $id, 
                            'status' => 1
                        ])->with(['jobType','category'])->first();
        
        if ($job == null) {
            abort(404);
        }

        $count = 0;
        if (Auth::user()) {
            $count = SavedJob::where([
                'user_id' => Auth::user()->id,
                'job_id' => $id
            ])->count();
        }
        

        // fetch applicants

        $applications = JobApplication::where('job_id',$id)->with('user')->get();


        return view('front.jobDetail',[ 'job' => $job,
                                        'count' => $count,
                                        'applications' => $applications
                                    ]);
    }

    public function applyJob(Request $request) {
        $id = $request->id;

        // Validate cover letter and CV
        $validator = Validator::make($request->all(), [
            'cover_letter' => 'required|string|min:20|max:2000',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            $errors = [];
            $errorMessages = $validator->errors()->toArray();
            
            // Format error messages for better UX
            if (isset($errorMessages['cover_letter'])) {
                foreach ($errorMessages['cover_letter'] as $msg) {
                    if (strpos($msg, 'required') !== false) {
                        $errors['cover_letter'] = ['Cover letter is required'];
                    } elseif (strpos($msg, 'min') !== false) {
                        $errors['cover_letter'] = ['Cover letter must be at least 20 characters long'];
                    } elseif (strpos($msg, 'max') !== false) {
                        $errors['cover_letter'] = ['Cover letter cannot exceed 2000 characters'];
                    }
                }
            }
            
            if (isset($errorMessages['cv'])) {
                foreach ($errorMessages['cv'] as $msg) {
                    if (strpos($msg, 'required') !== false) {
                        $errors['cv'] = ['Please upload your CV'];
                    } elseif (strpos($msg, 'mimes') !== false) {
                        $errors['cv'] = ['Only PDF, DOC, and DOCX files are accepted'];
                    } elseif (strpos($msg, 'max') !== false) {
                        $errors['cv'] = ['CV file size cannot exceed 2MB'];
                    } elseif (strpos($msg, 'file') !== false) {
                        $errors['cv'] = ['Please select a valid file'];
                    }
                }
            }
            
            return response()->json([
                'status' => false,
                'errors' => $errors ?: $errorMessages,
                'message' => 'Please fix the errors below and try again.'
            ]);
        }

        $job = Job::where('id',$id)->first();

        // If job not found in db
        if ($job == null) {
            $message = 'Job does not exist.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        // you can not apply on your own job
        $employer_id = $job->user_id;

        if ($employer_id == Auth::user()->id) {
            $message = 'You can not apply on your own job.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        // You can not apply on a job twice
        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();
        
        if ($jobApplicationCount > 0) {
            $message = 'You already applied on this job.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        // Store CV file
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('job_applications_cv', 'public');
        }

        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->cover_letter = $request->cover_letter;
        $application->cv = $cvPath;
        $application->save();

        // Calculate and save fit score
        $this->matchingService->saveFitScore($application);

        // Run fraud detection and save fraud score
        $this->fraudService->saveFraudScore($application);

        // Send Notification Email to Employer
        $employer = User::where('id',$employer_id)->first();
        
        $mailData = [
            'employer' => $employer,
            'user' => Auth::user(),
            'job' => $job,
        ];

        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

        $message = 'You have successfully applied.';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    /**
     * Get matched jobs for current user
     * Returns jobs sorted by fit score (highest match first)
     */
    public function getMatchedJobs(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to see matched jobs'
            ]);
        }

        $limit = $request->input('limit', 10);
        $user = Auth::user();

        $matchedJobs = $this->matchingService->getMatchedJobs($user, $limit);

        return response()->json([
            'status' => true,
            'data' => $matchedJobs
        ]);
    }

    /**
     * API endpoint to get job match score for a specific user and job
     */
    public function getJobMatchScore($jobId)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login'
            ], 401);
        }

        $job = Job::find($jobId);
        if (!$job) {
            return response()->json([
                'status' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $user = Auth::user();
        $fitScore = $this->matchingService->calculateJobMatchScore($user, $job);

        return response()->json([
            'status' => true,
            'data' => [
                'job_id' => $jobId,
                'fit_score' => $fitScore,
                'match_percentage' => round($fitScore, 2) . '%'
            ]
        ]);
    }

    public function saveJob(Request $request) {

        $id = $request->id;

        $job = Job::find($id);

        if ($job == null) {
            session()->flash('error','Job not found');

            return response()->json([
                'status' => false,
            ]);
        }

        // Check if user already saved the job
        $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($count > 0) {
            session()->flash('error','You already saved this job.');

            return response()->json([
                'status' => false,
            ]);
        }

        $savedJob = new SavedJob;
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();

        session()->flash('success','You have successfully saved the job.');

        return response()->json([
            'status' => true,
        ]);

    }
}
