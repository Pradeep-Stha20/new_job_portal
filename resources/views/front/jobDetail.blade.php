@extends('front.layouts.app')

@section('main')
<section class="section-4 bg-2">    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('jobs') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                    </ol>
                </nav>
            </div>
        </div> 
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">
                @include('front.message')
                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                
                                <div class="jobs_conetent">
                                    <a href="#">
                                        <h4>{{ $job->title }}</h4>
                                    </a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-map-marker"></i> {{ $job->location }}</p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-clock-o"></i> {{ $job->jobType->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="jobs_right">
                                <div class="apply_now {{ ($count == 1) ? 'saved-job' : '' }}">
                                    <a class="heart_mark " href="javascript:void(0);" onclick="saveJob({{ $job->id }})"> <i class="fa fa-heart-o" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                            <h4>Job description</h4>
                            {!! nl2br($job->description) !!}
                            
                            
                        </div>
                        @if (!empty($job->responsibility))
                        <div class="single_wrap">
                            <h4>Responsibility</h4>
                            {!! nl2br($job->responsibility) !!}
                        </div>
                        @endif
                        @if (!empty($job->qualifications))
                        <div class="single_wrap">
                            <h4>Qualifications</h4>
                            {!! nl2br($job->qualifications) !!}
                        </div>
                        @endif
                        @if (!empty($job->benefits))
                        <div class="single_wrap">
                            <h4>Benefits</h4>
                            {!! nl2br($job->benefits) !!}
                        </div>
                        @endif
                        <div class="border-bottom"></div>
                        <div class="pt-3 text-end">
                            
                            @if (Auth::check())
                                <a href="#" onclick="saveJob({{ $job->id }});" class="btn btn-secondary">Save</a>  
                            @else
                                <a href="javascript:void(0);" class="btn btn-secondary disabled">Login to Save</a>
                            @endif

                            @if (Auth::check())
                                <a href="#" onclick="openApplicationModal({{ $job->id }})" class="btn btn-primary">Apply</a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-primary disabled">Login to Apply</a>
                            @endif
                            

                        </div>
                    </div>
                </div>

                @if (Auth::user())
                   @if (Auth::user()->id == $job->user_id)
                       
                   
                
                <div class="card shadow border-0 mt-4">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                <div class="jobs_conetent">                                    
                                    <h4>Applicants</h4>                                    
                                </div>
                            </div>
                            <div class="jobs_right"></div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <table class="table table-striped">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Applied Date</th>
                            </tr>
                            @if ($applications->isNotEmpty())
                                @foreach ($applications as $application)
                                <tr>
                                    <td>{{ $application->user->name  }}</td>
                                    <td>{{ $application->user->email  }}</td>
                                    <td>{{ $application->user->mobile  }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($application->applied_date)->format('d M, Y') }}
                                    </td>
                                </tr> 
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3">Applicants not found</td>
                                </tr>
                            @endif
                            
                        </table>
                        
                    </div>
                </div>
                @endif 
                @endif
            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Job Summery</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Published on: <span>{{ \Carbon\Carbon::parse($job->created_at)->format('d M, Y') }}</span></li>
                                <li>Vacancy: <span>{{ $job->vacancy }}</span></li>
                                

                                @if ($job->salary_negotiable)
                                <li>Salary: <span>Negotiable</span></li>
                                @elseif (!is_null($job->salary_min) || !is_null($job->salary_max))
                                <li>Salary: <span>NPR {{ number_format($job->salary_min ?? 0) }} - {{ number_format($job->salary_max ?? 0) }}</span></li>
                                @endif

                                <li>Location: <span>{{ $job->location }}</span></li>
                                <li>Job Nature: <span> {{ $job->jobType->name }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card shadow border-0 my-4">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Name: <span>{{ $job->company_name }}</span></li>

                                @if (!empty($job->company_location))
                                <li>Locaion: <span>{{ $job->company_location }}</span></li>
                                @endif

                                @if (!empty($job->company_website))
                                <li>Webite: <span><a href="{{ $job->company_website }}">{{ $job->company_website }}</a></span></li>
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationModalLabel">
                    <i class="fa fa-briefcase me-2"></i>Apply for this Job
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="applicationForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="jobId" name="id" value="">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="coverLetter" class="form-label">Cover Letter <span class="text-danger">*</span></label>
                            <small class="text-muted">
                                <span id="charCount">0</span>/2000 characters
                            </small>
                        </div>
                        <textarea class="form-control" id="coverLetter" name="cover_letter" rows="5" placeholder="Tell us why you are a great fit for this job (minimum 20 characters)" required oninput="updateCharCount()"></textarea>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">Minimum 20 characters, maximum 2000 characters</small>
                            <small id="charWarning" class="text-warning" style="display:none;">
                                <i class="fa fa-exclamation-triangle"></i> Getting close to limit
                            </small>
                        </div>
                        <div class="invalid-feedback" id="coverLetterError"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="cvFile" class="form-label">Upload Your CV <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <input type="file" class="form-control" id="cvFile" name="cv" accept=".pdf,.doc,.docx" required onchange="updateFileInfo()">
                            <small id="fileInfo" class="ms-2 text-muted"></small>
                        </div>
                        <small class="text-muted d-block">
                            <i class="fa fa-info-circle"></i> Accepted formats: PDF, DOC, DOCX (Max 2MB)
                        </small>
                        <div class="invalid-feedback" id="cvError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitBtn" onclick="submitApplication(event)">
                        <i class="fa fa-paper-plane me-2"></i>Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('customJs')
<script type="text/javascript">
let applicationModal = null;

function openApplicationModal(jobId) {
    document.getElementById('jobId').value = jobId;
    document.getElementById('applicationForm').reset();
    
    // Clear previous errors and reset display
    document.getElementById('coverLetterError').textContent = '';
    document.getElementById('cvError').textContent = '';
    document.getElementById('charCount').textContent = '0';
    document.getElementById('fileInfo').textContent = '';
    document.getElementById('charWarning').style.display = 'none';
    
    // Reset button
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fa fa-paper-plane me-2"></i>Submit Application';
    
    if (!applicationModal) {
        applicationModal = new bootstrap.Modal(document.getElementById('applicationModal'));
    }
    applicationModal.show();
}

function updateCharCount() {
    const coverLetter = document.getElementById('coverLetter').value;
    const charCount = coverLetter.length;
    document.getElementById('charCount').textContent = charCount;
    
    // Show warning if getting close to limit
    if (charCount >= 1800) {
        document.getElementById('charWarning').style.display = 'inline';
    } else {
        document.getElementById('charWarning').style.display = 'none';
    }
}

function updateFileInfo() {
    const fileInput = document.getElementById('cvFile');
    const fileInfo = document.getElementById('fileInfo');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        fileInfo.textContent = `✓ ${file.name} (${sizeInMB}MB)`;
        fileInfo.className = 'ms-2 text-muted text-success';
    } else {
        fileInfo.textContent = '';
    }
}

function submitApplication(event) {
    const jobId = document.getElementById('jobId').value;
    const coverLetter = document.getElementById('coverLetter').value.trim();
    const cvFile = document.getElementById('cvFile').files[0];
    console.log('Submitting application for job ID:', jobId);
    
    // Basic validation
    let hasErrors = false;
    document.getElementById('coverLetterError').textContent = '';
    document.getElementById('cvError').textContent = '';
    
    if (coverLetter.length < 20) {
        document.getElementById('coverLetterError').textContent = 'Cover letter must be at least 20 characters.';
        hasErrors = true;
    }
    
    if (coverLetter.length > 2000) {
        document.getElementById('coverLetterError').textContent = 'Cover letter cannot exceed 2000 characters.';
        hasErrors = true;
    }
    
    if (!cvFile) {
        document.getElementById('cvError').textContent = 'Please select a CV file.';
        hasErrors = true;
    } else {
        const validMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const validExtensions = ['pdf', 'doc', 'docx'];
        const fileExtension = cvFile.name.split('.').pop().toLowerCase();
        
        if (!validMimes.includes(cvFile.type) && !validExtensions.includes(fileExtension)) {
            document.getElementById('cvError').textContent = 'Only PDF, DOC, and DOCX files are allowed.';
            hasErrors = true;
        }
        
        if (cvFile.size > 2 * 1024 * 1024) { // 2MB in bytes
            document.getElementById('cvError').textContent = 'File size must not exceed 2MB.';
            hasErrors = true;
        }
    }
    
    if (hasErrors) {
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
    
    // Submit form via AJAX using FormData from the form itself
    const formData = new FormData(document.getElementById('applicationForm'));
    console.log('FormData created, submitting...');
    
    $.ajax({
        url: '{{ route("applyJob") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            if (response.status) {
                applicationModal.hide();
                showSuccessNotification('Application Submitted Successfully!', 'Your application has been submitted. The employer will review it shortly.');
                
                setTimeout(function() {
                    window.location.href = "{{ url()->current() }}";
                }, 2000);
            } else {
                // Show validation errors
                if (response.errors) {
                    if (response.errors.cover_letter) {
                        document.getElementById('coverLetterError').textContent = response.errors.cover_letter[0];
                    }
                    if (response.errors.cv) {
                        document.getElementById('cvError').textContent = response.errors.cv[0];
                    }
                }
                if (response.message) {
                    showErrorNotification('Application Error', response.message);
                }
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa fa-paper-plane me-2"></i>Submit Application';
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            
            let errorMessage = 'Please try again.';
            if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 401) {
                errorMessage = 'Please login to apply for jobs.';
            } else if (xhr.status === 422) {
                errorMessage = 'Validation failed. Please check your inputs.';
            }
            
            showErrorNotification('Submission Error', errorMessage);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane me-2"></i>Submit Application';
        }
    });
}

function saveJob(id){
    $.ajax({
        url : '{{ route("saveJob") }}',
        type: 'post',
        data: {id:id},
        dataType: 'json',
        success: function(response) {
            window.location.href = "{{ url()->current() }}";
        } 
    });
}

</script>
@endsection