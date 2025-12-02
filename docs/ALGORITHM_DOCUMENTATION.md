# Job Matching & Fraud Detection Implementation Guide

## Overview

Your Laravel Job Portal now includes two powerful algorithms:
1. **Job Matching Algorithm** - Intelligently matches job seekers with relevant jobs
2. **Fraud Detection Algorithm** - Identifies suspicious applications and protects your platform

Both algorithms are automatically triggered when users apply for jobs and when employers view applicants.

---

## 1. Job Matching Algorithm

### How It Works

The Job Matching Service uses **Cosine Similarity** to calculate a fit score (0-100) for each application.

**Score Breakdown:**
- **Skills Match (0-50 points)**: Uses cosine similarity to compare user skills with job requirements
- **Experience Match (0-30 points)**: Compares years of experience with job requirement
- **Profile Completion (0-20 points)**: Checks if user has filled critical profile fields

**Formula:**
```
Fit Score = Skills Match + Experience Match + Profile Completion
Cosine Similarity = (Matching Skills) / √(User Skills × Job Skills)
```

### Key Features

- ✅ Automatically calculates fit score when users apply
- ✅ Sorts applicants by fit score (highest first)
- ✅ Accounts for skill relevance and relevance
- ✅ Rewards complete profiles
- ✅ Scales experience match proportionally

### Usage in Database

**Files Added:**
- `app/Services/JobMatchingService.php` - Core matching logic
- `database/migrations/2025_12_02_120000_add_algorithm_fields.php` - Schema changes

**New Database Fields:**
```
users.skills (text) - Comma-separated skills
users.years_experience (int) - Total years of professional experience
job_applications.fit_score (float) - Match score 0-100
```

### API Endpoints

**Get matched jobs for current user:**
```
GET /jobs/matched?limit=10
```

**Get fit score for specific job:**
```
GET /jobs/{jobId}/match-score
```

**Get ranked applicants for a job:**
```
GET /account/job/{jobId}/ranked-applicants
```

### Example Usage in Code

```php
// In JobsController
$matchingService->saveFitScore($application);

// Get matched jobs
$matchedJobs = $matchingService->getMatchedJobs($user, 10);

// Rank applicants
$ranked = $matchingService->rankApplicantsByFitScore($job);
```

---

## 2. Fraud Detection Algorithm

### How It Works

Multi-factor fraud detection analyzes 6 risk indicators:

**Scoring System (0-100):**
- **0-30**: Safe (no fraud indicators)
- **31-60**: Moderate risk (some suspicious activity)
- **61-100**: High risk (likely fraudulent)

**Risk Factors:**

1. **Duplicate Applications (0-20 points)**
   - Same user applying to same job twice
   - More than 10 applications in 24 hours
   - Bot/spam detection

2. **Cover Letter Spam (0-25 points)**
   - Detects spam keywords (click here, buy now, cryptocurrency, etc.)
   - Checks for excessive capitalization
   - Monitors for repeated character patterns
   - Validates cover letter length (30-500 words)

3. **Profile Completeness (0-15 points)**
   - Missing name, email, mobile
   - Empty designation or skills
   - No profile picture

4. **CV File Validation (0-20 points)**
   - Missing CV (highest risk)
   - File too small (<5KB) - likely not real CV
   - File too large (>10MB) - suspicious
   - Invalid MIME type

5. **Application Rate Pattern (0-15 points)**
   - More than 3 applications/hour = suspicious
   - More than 15 applications/6 hours = spam
   - Detects bot activity

6. **Account Age (0-5 points)**
   - Brand new account (<1 hour) = highest risk
   - Very new account (<24 hours) = moderate
   - Established accounts (>7 days) = no risk

### Key Features

- ✅ Automatically runs when user applies for job
- ✅ Auto-flags applications with score ≥ 85 as "flagged" status
- ✅ Detailed fraud analysis report
- ✅ Actionable recommendations for employers
- ✅ False positive mitigation (accounts for legitimate high activity)

### Usage in Database

**New Database Fields:**
```
job_applications.fraud_score (float) - Fraud risk 0-100
job_applications.status (enum) - pending, approved, rejected, flagged
```

### API Endpoints

**Get fraud report for application:**
```
GET /account/applicant/{applicationId}/fraud-report
```

Returns:
```json
{
  "fraud_score": 45.5,
  "risk_level": "moderate",
  "duplicate_applications": 0,
  "cover_letter_spam": 25.5,
  "profile_completeness": 80,
  "cv_validation": 100,
  "application_pattern": 0,
  "account_age_risk": 20,
  "recommendations": [...]
}
```

### Example Usage in Code

```php
// In JobsController applyJob()
$fraudService->saveFraudScore($application);
// Auto-flagged if fraud_score >= 85

// Get detailed report
$report = $fraudService->getFraudAnalysisReport($application);

// Batch process
$results = $fraudService->batchProcessFraudDetection($applicationIds);
```

---

## 3. User Interface Changes

### Employer Applicant View

**New Features in `/account/job/{jobId}/applicants`:**

1. **Fit Score Column** with Progress Bar
   - Green bar (70+): Good fit
   - Yellow bar (40-69): Moderate fit
   - Red bar (<40): Poor fit

2. **Fraud Risk Column** with Icons
   - 🟢 Safe (0-30)
   - 🔵 Low (31-40)
   - 🟡 Moderate (41-60)
   - 🔴 High (61-100)

3. **Fraud Report Modal**
   - Detailed breakdown of all 6 factors
   - Visual fraud score bar
   - Actionable recommendations
   - Risk level badge

4. **Applicant Sorting**
   - Automatically sorted by fit_score DESC
   - Then by application date DESC

### Job Seeker Experience

- ✅ Automatic fit score calculation
- ✅ Smart job recommendations (coming soon)
- ✅ Real-time feedback during application

---

## 4. Configuration & Customization

### Adjusting Scoring Weights

Edit `app/Services/JobMatchingService.php`:
```php
// Line 50 - Change skill weight
$skillsScore = ($cosineSimilarity * 100) * 0.5; // Currently 50 points max

// Line 68 - Change experience weight  
return 30; // Currently 30 points max

// Line 83 - Change profile weight
$profileScore = ($percentage / 100) * 20; // Currently 20 points max
```

Edit `app/Services/FraudDetectionService.php`:
```php
// Adjust point values (lines 26-30)
$fraudScore += $this->checkDuplicateApplications($application) * 20; // Change 20
$fraudScore += $this->analyzeCoverLetterSpam($application->cover_letter) * 25; // Change 25
// ... etc
```

### Spam Keywords

Add/modify spam detection keywords in `FraudDetectionService.php` line 78:
```php
$spamKeywords = [
    'your keywords here',
    'cryptocurrency',
    // Add more...
];
```

### Account Age Risk Thresholds

Adjust in `FraudDetectionService.php` lines 236-245:
```php
if ($accountAge < 1) {      // Less than 1 hour
    return 1.0;
}
// Adjust these thresholds as needed
```

---

## 5. Data Migration Instructions

### Step 1: Apply Migration
```bash
php artisan migrate
```

### Step 2: Update Existing User Profiles
Add skills and experience to existing users:
```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->skills = 'Laravel, PHP, JavaScript, Database Design';
$user->years_experience = 5;
$user->save();
```

### Step 3: Recalculate Existing Applications
```php
$applications = JobApplication::all();
foreach ($applications as $app) {
    app(\App\Services\JobMatchingService::class)->saveFitScore($app);
    app(\App\Services\FraudDetectionService::class)->saveFraudScore($app);
}
```

---

## 6. Testing the Algorithms

### Test Job Matching
1. Create a test user with skills: "Laravel, PHP, SQL"
2. Create a test job requiring: "Laravel, SQL"
3. Apply for the job
4. Expected fit score: ~60-70%

### Test Fraud Detection
1. Apply multiple times in quick succession (>3 applications/hour)
2. Use spam keywords in cover letter
3. Upload very small CV file
4. Expected fraud score: 60+ (moderate/high)

### Monitor Results
```bash
# View fraud scores
SELECT id, user_id, fit_score, fraud_score, status FROM job_applications LIMIT 10;

# Check flagged applications
SELECT * FROM job_applications WHERE status = 'flagged';
```

---

## 7. Security Considerations

✅ **Implemented:**
- CV file validation (size, type, existence check)
- SQL injection prevention (Laravel Eloquent)
- CSRF token protection on all actions
- Authorization checks (job ownership)
- User authentication required

⚠️ **Recommendations:**
- Monitor flagged applications manually
- Don't auto-reject based on fraud score alone
- Consider ML improvements over time
- Log all fraud detections for audit trail

---

## 8. Performance Optimization

**Database Indexes:**
```sql
ALTER TABLE job_applications ADD INDEX idx_fit_score (fit_score);
ALTER TABLE job_applications ADD INDEX idx_fraud_score (fraud_score);
ALTER TABLE job_applications ADD INDEX idx_status (status);
```

**Caching Recommendations:**
```php
// Cache matched jobs (1 hour)
Cache::remember("user.{$userId}.matched_jobs", 3600, function() {
    return $matchingService->getMatchedJobs($user, 10);
});
```

---

## 9. API Reference

### JobMatchingService Methods

```php
// Calculate single match score
calculateJobMatchScore(User $user, Job $job): float

// Get all matched jobs for user
getMatchedJobs(User $user, int $limit = 10): array

// Rank applicants by fit score
rankApplicantsByFitScore(Job $job): array

// Save fit score to database
saveFitScore(JobApplication $application): void
```

### FraudDetectionService Methods

```php
// Calculate fraud score
calculateFraudScore(JobApplication $application): float

// Get detailed report
getFraudAnalysisReport(JobApplication $application): array

// Save fraud score and auto-flag if needed
saveFraudScore(JobApplication $application): void

// Process multiple applications
batchProcessFraudDetection(array $applicationIds): array
```

---

## 10. Troubleshooting

### Issue: Fit score not calculating
**Solution:** Ensure `skills` and `years_experience` fields are populated in users table

### Issue: Fraud detection too strict
**Solution:** Adjust thresholds in `FraudDetectionService.php` or reduce point values

### Issue: Performance slow with many applicants
**Solution:** Add database indexes and implement caching

### Issue: False positives in spam detection
**Solution:** Review and update spam keywords list, consider ML approach

---

## Summary

Your job portal now has enterprise-grade:
- 🎯 Intelligent job matching (Cosine Similarity algorithm)
- 🛡️ Comprehensive fraud detection (6-factor analysis)
- 📊 Visual applicant scoring for employers
- 🚀 Better hiring decisions with data-driven insights

**Next Steps:**
1. Test algorithms with real data
2. Train team on fraud report interpretation
3. Monitor false positives and adjust
4. Consider adding ML improvements
5. Set up audit logging for compliance

For questions or improvements, refer to the service files in `app/Services/`
