# Project Restructuring Implementation Guide

## Overview
This guide provides step-by-step instructions for implementing the new project structure without disrupting existing functionality.

---

## Phase 1: Core Infrastructure (Week 1)

### Step 1.1: Create Request Classes

Create request classes for form validation in `app/Http/Requests/`

#### Example: JobRequest.php
```php
<?php
namespace App\Http\Requests;

class JobRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'experience' => 'required|string',
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|min:0|gte:salary_min',
            'location' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Job Title',
            'category_id' => 'Category',
            'salary_min' => 'Minimum Salary',
            'salary_max' => 'Maximum Salary',
        ];
    }
}
```

#### Example: ApplicationRequest.php
```php
<?php
namespace App\Http\Requests;

class ApplicationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'cover_letter' => 'required|string|min:20|max:2000',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'cover_letter.min' => 'Cover letter must be at least 20 characters',
            'cover_letter.max' => 'Cover letter cannot exceed 2000 characters',
            'cv.max' => 'CV file size cannot exceed 2MB',
        ];
    }
}
```

---

### Step 1.2: Create Repository Classes

Create repositories in `app/Repositories/`

#### Example: JobRepository.php
```php
<?php
namespace App\Repositories;

use App\Models\Job;

class JobRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Job();
    }

    public function getActiveJobs()
    {
        return $this->model->where('status', 'active')->get();
    }

    public function getJobsByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->where('status', 'active')
            ->get();
    }

    public function getJobsByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function searchJobs($keyword)
    {
        return $this->model
            ->where('status', 'active')
            ->where(function($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('keywords', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->get();
    }
}
```

#### Example: ApplicationRepository.php
```php
<?php
namespace App\Repositories;

use App\Models\JobApplication;

class ApplicationRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new JobApplication();
    }

    public function getApplicationsByJob($jobId)
    {
        return $this->model->where('job_id', $jobId)
            ->with('user')
            ->orderBy('fit_score', 'desc')
            ->get();
    }

    public function getApplicationsByUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->with('job')
            ->latest()
            ->get();
    }

    public function getFlaggedApplications()
    {
        return $this->model->where('status', 'flagged')
            ->where('fraud_score', '>=', 85)
            ->get();
    }

    public function getHighQualityApplications($jobId)
    {
        return $this->model->where('job_id', $jobId)
            ->where('fraud_score', '<', 60)
            ->where('fit_score', '>=', 60)
            ->orderBy('fit_score', 'desc')
            ->get();
    }
}
```

---

### Step 1.3: Update Service Provider

Create `RepositoryServiceProvider.php` in `app/Providers/`

```php
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\JobRepository;
use App\Repositories\ApplicationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('JobRepository', function() {
            return new JobRepository();
        });

        $this->app->bind('ApplicationRepository', function() {
            return new ApplicationRepository();
        });
    }
}
```

Register in `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

---

## Phase 2: Update Controllers (Week 2)

### Step 2.1: Update JobsController

```php
<?php
namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Http\Requests\ApplicationRequest;
use App\Repositories\JobRepository;
use App\Repositories\ApplicationRepository;
use App\Services\JobMatchingService;
use App\Services\FraudDetectionService;

class JobsController extends Controller
{
    public function __construct(
        protected JobRepository $jobRepository,
        protected ApplicationRepository $applicationRepository,
        protected JobMatchingService $matchingService,
        protected FraudDetectionService $fraudService,
    ) {}

    public function index()
    {
        $jobs = $this->jobRepository->getActiveJobs()->paginate(15);
        return view('front.jobs', compact('jobs'));
    }

    public function detail($id)
    {
        $job = $this->jobRepository->findOrFail($id);
        return view('front.jobDetail', compact('job'));
    }

    public function applyJob(ApplicationRequest $request)
    {
        // Validation is automatically handled by ApplicationRequest
        
        $application = $this->applicationRepository->create([
            'job_id' => $request->id,
            'user_id' => auth()->id(),
            'cover_letter' => $request->cover_letter,
            'cv' => $request->file('cv')->store('job_applications_cv', 'public'),
        ]);

        // Calculate scores
        $this->matchingService->saveFitScore($application);
        $this->fraudService->saveFraudScore($application);

        return response()->json([
            'status' => true,
            'message' => 'Application submitted successfully'
        ]);
    }
}
```

---

### Step 2.2: Update AccountController

```php
<?php
namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Repositories\JobRepository;

class AccountController extends Controller
{
    public function __construct(
        protected JobRepository $jobRepository,
    ) {}

    public function saveJob(JobRequest $request)
    {
        // Use validated data from request
        $validated = $request->validated();
        
        $job = $this->jobRepository->create($validated + [
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('account.myJobs')
            ->with('success', 'Job created successfully');
    }

    public function updateJob($jobId, JobRequest $request)
    {
        $this->jobRepository->update($jobId, $request->validated());

        return redirect()->route('account.myJobs')
            ->with('success', 'Job updated successfully');
    }
}
```

---

## Phase 3: Create Events & Listeners (Week 2-3)

### Step 3.1: Create Events

Create in `app/Events/`

```php
<?php
namespace App\Events;

use App\Models\JobApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public JobApplication $application
    ) {}
}
```

### Step 3.2: Create Listeners

Create in `app/Listeners/`

```php
<?php
namespace App\Listeners;

use App\Events\ApplicationSubmitted;
use App\Mail\JobNotificationEmail;
use Illuminate\Support\Facades\Mail;

class SendApplicationNotification
{
    public function handle(ApplicationSubmitted $event)
    {
        $employer = $event->application->job->user;
        
        Mail::to($employer->email)->queue(
            new JobNotificationEmail($event->application)
        );
    }
}
```

### Step 3.3: Register Events

Update `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    'App\Events\ApplicationSubmitted' => [
        'App\Listeners\SendApplicationNotification',
    ],
];
```

---

## Phase 4: Frontend Organization (Week 3)

### Step 4.1: Organize CSS

Create modular CSS files in `resources/css/components/`

#### notifications.css
```css
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    width: 400px;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 9999;
    animation: slideInDown 0.3s ease-in;
}

.notification-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.notification-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
```

### Step 4.2: Organize JavaScript

Create modular JS files in `resources/js/components/`

#### notification.js
```javascript
export function showSuccessNotification(title, message) {
    const html = `
        <div class="notification-container notification-success">
            <strong>${title}</strong>
            <p>${message}</p>
        </div>
    `;
    $('body').append(html);
    
    setTimeout(() => {
        $('.notification-container').fadeOut(() => {
            $('.notification-container').remove();
        });
    }, 4000);
}
```

---

## Phase 5: Testing & Documentation (Week 4)

### Step 5.1: Create Tests

Create in `tests/Feature/` and `tests/Unit/`

```php
<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;

class JobApplicationTest extends TestCase
{
    public function test_user_can_apply_for_job()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($user)->post('/apply-job', [
            'id' => $job->id,
            'cover_letter' => 'This is my cover letter with enough content',
            'cv' => UploadedFile::fake()->create('resume.pdf'),
        ]);

        $response->assertJson(['status' => true]);
    }
}
```

### Step 5.2: Create Documentation

Create in `docs/` folder with Markdown files:
- API.md
- DATABASE.md
- ARCHITECTURE.md
- INSTALLATION.md

---

## Migration Checklist

- [ ] Create Request classes
- [ ] Create Repository classes
- [ ] Update Service Providers
- [ ] Update Controllers to use Repositories
- [ ] Create Event classes
- [ ] Create Listener classes
- [ ] Update Event Service Provider
- [ ] Organize CSS files
- [ ] Organize JavaScript files
- [ ] Create test files
- [ ] Generate documentation
- [ ] Test all functionality
- [ ] Update routes (optional)
- [ ] Deploy to production

---

## Rollback Plan (If Needed)

If any issues arise:
1. Old code remains functional
2. New code runs alongside old code
3. Simply comment out new implementations
4. Routes remain unchanged
5. Database schema unchanged

---

## Performance Considerations

✅ Repositories reduce query duplication
✅ Request classes reduce code repetition
✅ Event-driven architecture improves scalability
✅ Modular CSS/JS reduces load time
✅ No performance degradation expected

---

## Success Criteria

✅ All existing routes work
✅ All existing functionality preserved
✅ No database migrations needed
✅ All tests pass
✅ Code is more organized
✅ New developers can navigate easily
✅ Easier to add new features

---

## Support & Troubleshooting

**Issue**: Service Provider not loading
**Solution**: Ensure it's registered in `config/app.php`

**Issue**: Repository not found
**Solution**: Check namespace and binding in ServiceProvider

**Issue**: Request validation failing
**Solution**: Verify rules() and messages() methods

---

**Next Steps**: Follow the implementation guide above to restructure your project systematically.
