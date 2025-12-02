@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Job: {{ $job->title }}</h3>
                                <p class="text-muted">Location: {{ $job->location }}</p>
                            </div>
                            <div>
                                <a href="{{ route('account.myJobs') }}" class="btn btn-secondary">Back to My Jobs</a>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3" role="alert">
                            <strong>Smart Hiring Features:</strong> Applicants are ranked by job match score (fit %). Fraud detection flags suspicious applications. Green bar = Good fit, Red = Lower compatibility.
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Fit Score</th>
                                        <th>Fraud Risk</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($applications->isNotEmpty())
                                        @foreach ($applications as $application)
                                        <tr>
                                            <td>
                                                <strong>{{ $application->user->name }}</strong>
                                                <br><small class="text-muted">{{ $application->user->mobile ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $application->user->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div style="width: 70px; margin-right: 10px;">
                                                        <div class="progress" style="height: 24px;">
                                                            <div class="progress-bar {{ $application->fit_score >= 70 ? 'bg-success' : ($application->fit_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $application->fit_score }}%;" 
                                                                 aria-valuenow="{{ $application->fit_score }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold">{{ round($application->fit_score, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($application->fraud_score >= 80)
                                                    <span class="badge bg-danger" title="High fraud risk - Review carefully">
                                                        <i class="fa fa-exclamation-triangle"></i> High
                                                    </span>
                                                @elseif ($application->fraud_score >= 60)
                                                    <span class="badge bg-warning text-dark" title="Moderate fraud risk">
                                                        <i class="fa fa-exclamation-circle"></i> Moderate
                                                    </span>
                                                @elseif ($application->fraud_score >= 40)
                                                    <span class="badge bg-info" title="Low fraud risk">
                                                        <i class="fa fa-info-circle"></i> Low
                                                    </span>
                                                @else
                                                    <span class="badge bg-success" title="Safe - No fraud indicators">
                                                        <i class="fa fa-check-circle"></i> Safe
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($application->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif ($application->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif ($application->status == 'flagged')
                                                    <span class="badge bg-danger">Flagged</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-dots">
                                                    <button href="#" class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#" onclick="viewApplication({{ $application->id }})"> 
                                                            <i class="fa fa-eye"></i> View Details
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="viewFraudReport({{ $application->id }})"> 
                                                            <i class="fa fa-shield"></i> Fraud Report
                                                        </a></li>
                                                        @if ($application->cv)
                                                        <li><a class="dropdown-item" href="{{ asset('storage/' . $application->cv) }}" download> 
                                                            <i class="fa fa-download"></i> Download CV
                                                        </a></li>
                                                        @endif
                                                        @if ($application->status == 'pending')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-success" href="#" onclick="approveApplication({{ $application->id }})"> 
                                                            <i class="fa fa-check"></i> Approve
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="rejectApplication({{ $application->id }})"> 
                                                            <i class="fa fa-times"></i> Reject
                                                        </a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No applicants yet
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div>
                            {{ $applications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Details Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Applicant Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong><br><span id="applicantName"></span></p>
                                <p><strong>Email:</strong><br><span id="applicantEmail"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Mobile:</strong><br><span id="applicantMobile"></span></p>
                                <p><strong>Designation:</strong><br><span id="applicantDesignation"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Cover Letter</h6>
                    </div>
                    <div class="card-body">
                        <p id="coverLetterText" style="white-space: pre-wrap; line-height: 1.6;"></p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Resume/CV</h6>
                    </div>
                    <div class="card-body">
                        <p id="cvInfo"></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Application Status</h6>
                    </div>
                    <div class="card-body">
                        <p id="applicationStatus"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Fraud Report Modal -->
<div class="modal fade" id="fraudReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fraud Detection Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <strong>Risk Level:</strong> <span id="riskLevel"></span>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Fraud Score Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Overall Fraud Score:</strong><br><span id="fraudScore" class="h5"></span></p>
                            </div>
                            <div class="col-md-6">
                                <div class="progress" style="height: 30px;">
                                    <div id="fraudScoreBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detailed Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Duplicate Applications:</strong> <span id="duplicateApps"></span>%</li>
                            <li class="mb-2"><strong>Cover Letter Spam Score:</strong> <span id="coverLetterSpam"></span>%</li>
                            <li class="mb-2"><strong>Profile Completeness:</strong> <span id="profileCompleteness"></span>%</li>
                            <li class="mb-2"><strong>CV Validation:</strong> <span id="cvValidation"></span>%</li>
                            <li class="mb-2"><strong>Application Pattern Risk:</strong> <span id="appPattern"></span>%</li>
                            <li class="mb-2"><strong>Account Age Risk:</strong> <span id="accountAgeRisk"></span>%</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Recommendations</h6>
                    </div>
                    <div class="card-body">
                        <ul id="recommendations" class="list-group list-group-flush"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('customJs')
<script type="text/javascript">
let applicationModal = null;
let fraudReportModal = null;

function viewApplication(appId) {
    $.ajax({
        url: '/api/applications/' + appId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response) {
                const app = response;
                document.getElementById('applicantName').textContent = app.user.name;
                document.getElementById('applicantEmail').textContent = app.user.email;
                document.getElementById('applicantMobile').textContent = app.user.mobile || 'N/A';
                document.getElementById('applicantDesignation').textContent = app.user.designation || 'N/A';
                document.getElementById('coverLetterText').textContent = app.cover_letter || 'No cover letter';
                
                let statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                if (app.status === 'approved') statusBadge = '<span class="badge bg-success">Approved</span>';
                else if (app.status === 'rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
                document.getElementById('applicationStatus').innerHTML = statusBadge;
                
                let cvInfo = '<p class="text-muted">No CV uploaded</p>';
                if (app.cv) {
                    cvInfo = '<a href="/storage/' + app.cv + '" class="btn btn-sm btn-primary" download><i class="fa fa-download"></i> Download</a>';
                }
                document.getElementById('cvInfo').innerHTML = cvInfo;
                
                if (!applicationModal) {
                    applicationModal = new bootstrap.Modal(document.getElementById('applicationModal'));
                }
                applicationModal.show();
            }
        },
        error: function() {
            alert('Failed to load application details');
        }
    });
}

function viewFraudReport(appId) {
    $.ajax({
        url: '/account/applicant/' + appId + '/fraud-report',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status && response.data) {
                const report = response.data;
                
                document.getElementById('fraudScore').textContent = report.fraud_score.toFixed(1) + '/100';
                const bar = document.getElementById('fraudScoreBar');
                bar.style.width = report.fraud_score + '%';
                bar.className = 'progress-bar';
                if (report.fraud_score >= 80) bar.classList.add('bg-danger');
                else if (report.fraud_score >= 60) bar.classList.add('bg-warning');
                else if (report.fraud_score >= 40) bar.classList.add('bg-info');
                else bar.classList.add('bg-success');
                
                let riskBadge = '<span class="badge bg-success">Safe</span>';
                if (report.risk_level === 'moderate') riskBadge = '<span class="badge bg-warning text-dark">Moderate</span>';
                else if (report.risk_level === 'high') riskBadge = '<span class="badge bg-danger">High Risk</span>';
                document.getElementById('riskLevel').innerHTML = riskBadge;
                
                document.getElementById('duplicateApps').textContent = report.duplicate_applications.toFixed(1);
                document.getElementById('coverLetterSpam').textContent = report.cover_letter_spam.toFixed(1);
                document.getElementById('profileCompleteness').textContent = report.profile_completeness.toFixed(1);
                document.getElementById('cvValidation').textContent = report.cv_validation.toFixed(1);
                document.getElementById('appPattern').textContent = report.application_pattern.toFixed(1);
                document.getElementById('accountAgeRisk').textContent = report.account_age_risk.toFixed(1);
                
                const recList = document.getElementById('recommendations');
                recList.innerHTML = '';
                report.recommendations.forEach(function(rec) {
                    let item = document.createElement('li');
                    item.className = 'list-group-item';
                    item.textContent = rec;
                    recList.appendChild(item);
                });
                
                if (!fraudReportModal) {
                    fraudReportModal = new bootstrap.Modal(document.getElementById('fraudReportModal'));
                }
                fraudReportModal.show();
            }
        },
        error: function() {
            alert('Failed to load fraud report');
        }
    });
}

function approveApplication(appId) {
    if (confirm('Approve this application?')) {
        $.ajax({
            url: '/account/applicant/' + appId + '/approve',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Error occurred');
                }
            },
            error: function() {
                alert('Error approving application');
            }
        });
    }
}

function rejectApplication(appId) {
    if (confirm('Reject this application?')) {
        $.ajax({
            url: '/account/applicant/' + appId + '/reject',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Error occurred');
                }
            },
            error: function() {
                alert('Error rejecting application');
            }
        });
    }
}
</script>
@endsection
