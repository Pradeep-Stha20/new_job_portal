# 🚀 Job Matching & Fraud Detection - Quick Start Guide

## What's New?

Your job portal now has **two intelligent algorithms** automatically processing every job application:

### 1️⃣ Job Matching Algorithm
- **Scores** how well a job seeker matches a job position (0-100%)
- Uses **Cosine Similarity** to compare skills
- Considers experience level and profile completeness
- Automatically sorts applicants by best fit

### 2️⃣ Fraud Detection Algorithm  
- **Detects** suspicious applications (0-100 risk score)
- Analyzes 6 risk factors (duplicates, spam, profile gaps, CV issues, bot activity, account age)
- Auto-flags high-risk applications for review
- Provides detailed recommendations

---

## 🎯 For Job Seekers

### What Happens When You Apply?

1. ✅ Cover letter and CV are validated
2. ✅ **Fit score** calculated automatically (you'll see applicants ranked by this)
3. ✅ **Fraud check** runs in background (protects the platform)
4. ✅ Application stored and employer notified

### Tips to Get Higher Fit Score

- 📝 Complete your profile (name, email, phone, designation)
- 💼 Add your skills: Go to Account → Profile, add comma-separated skills
- 📅 Set your experience level: Add years of experience to profile
- ✍️ Write detailed cover letter (minimum 30 characters, maximum 2000)
- 📄 Upload a proper CV (PDF, DOC, DOCX - at least 5KB)

---

## 👔 For Employers

### View Applicants with Smart Scoring

Go to: **Account → My Jobs → View Applicants**

**You'll see:**

| Feature | What It Means |
|---------|--------------|
| **Fit Score (%)** | How well applicant matches your job. Green bar = good match |
| **Fraud Risk** | Safety indicator (Safe, Low, Moderate, High) |
| **Status** | Pending, Approved, Rejected, or Flagged |

**Example:**
```
Applicant: John Doe
Fit Score: 78% [████████░] (Good match - has required skills)
Fraud Risk: Safe ✓ (No suspicious indicators)
Status: Pending
```

### How to Use Fraud Reports

Click "Fraud Report" on any applicant to see:

- **Overall fraud score** with risk level
- **6 detailed factors** (what raised red flags if any)
- **Recommendations** (approve, review carefully, or reject)

**Interpret Risk Levels:**
- 🟢 **Safe (0-30)**: Trust this applicant
- 🔵 **Low (31-40)**: Minor concerns, probably fine
- 🟡 **Moderate (41-60)**: Review carefully before hiring
- 🔴 **High (61-100)**: Likely fraudulent, verify independently or reject

### Action Items

1. **Read the Fit Score** - Sort applicants by best match first
2. **Check Fraud Report** - Review if any risk indicators appear
3. **Approve/Reject** - Make hiring decisions faster with data

---

## 📊 Database Fields Added

### User Profile
```
skills (text)
  Example: "Laravel, PHP, MySQL, JavaScript, React"
  
years_experience (integer)
  Example: 5 (means 5 years of experience)
```

### Job Applications
```
fit_score (float 0-100)
  How well they match the job
  
fraud_score (float 0-100)
  Risk level (lower is better)
  
status (enum)
  pending, approved, rejected, flagged
```

---

## 🔧 How to Test

### Test Job Matching
```
1. Create/Edit your profile: Add skills and experience
2. View a job listing
3. You'll see a "Match Score" (estimated fit percentage)
```

### Test Fraud Detection
```
1. Try applying with unusual cover letter
   (e.g., contains "click here", "free money")
2. Apply 5 times in quick succession
3. Upload a very small CV file
4. Check fraud report - should show higher risk score
```

---

## 📍 New API Endpoints

Developers can integrate these endpoints:

```php
// Get matched jobs for logged-in user
GET /jobs/matched?limit=10

// Get fit score for specific job
GET /jobs/{jobId}/match-score

// Get detailed fraud report
GET /account/applicant/{applicationId}/fraud-report

// Get applicants ranked by fit score
GET /account/job/{jobId}/ranked-applicants
```

---

## ⚙️ Configuration

### Update User Profile with Skills

1. Login to your account
2. Go to Account → Profile
3. Add your skills (comma-separated)
4. Add years of professional experience
5. Save

### Admin/Employer Review

For each job applicant:
1. View applicant details (name, skills, cover letter, CV)
2. Check fit score percentage
3. Review fraud report if needed
4. Approve or reject based on scores + your judgment

---

## 🔐 Security Features

✅ **Spam Detection**
- Blocks keywords: "cryptocurrency", "click here", "free money", etc.
- Detects repeated character patterns
- Checks for excessive capitalization

✅ **Bot Detection**
- Flags if user applies >3 times per hour
- Monitors for rapid application patterns
- Considers account age

✅ **File Validation**
- Verifies CV file exists and has proper format
- Checks file size (minimum 5KB, max 10MB)
- Validates MIME type (PDF, DOC, DOCX only)

✅ **Account Safety**
- Brand new accounts flagged with higher risk
- Incomplete profiles get lower scores
- Authorization checks on all actions

---

## 📈 Algorithm Scoring Details

### Job Matching Score (0-100)

| Component | Points | How It's Calculated |
|-----------|--------|-------------------|
| Skills Match | 0-50 | Cosine similarity between your skills and job skills |
| Experience | 0-30 | Your experience vs. job requirement |
| Profile Completeness | 0-20 | How complete your profile is |
| **Total** | **0-100** | **Sum of all components** |

### Fraud Detection Score (0-100)

| Factor | Max Risk | What Triggers It |
|--------|----------|-----------------|
| Duplicates | 20 | Applying to same job twice, spam applications |
| Spam Content | 25 | Suspicious keywords, excessive caps, patterns |
| Profile Gaps | 15 | Missing name, email, skills, etc. |
| CV Issues | 20 | Missing, too small, wrong format |
| Application Rate | 15 | Applying too many times too fast |
| Account Age | 5 | Very new account |
| **Total** | **100** | **Risk Level Calculation** |

---

## 🎓 Example Scenarios

### Scenario 1: Perfect Applicant
```
User: Alice
Skills: "Laravel, PHP, JavaScript" ✓
Experience: 5 years ✓
Profile: Complete ✓
CV: Valid PDF 50KB ✓

Result:
- Fit Score: 85% (Excellent match if job needs these skills)
- Fraud Score: 10 (Safe - established account, complete profile, valid CV)
- Recommendation: APPROVE
```

### Scenario 2: Suspicious Applicant
```
User: Bob (Account created 1 hour ago)
Skills: Empty ✗
Experience: Not set ✗
Cover Letter: "Click here to make $5000/month!!! Bitcoin!!!"
CV: 2KB text file (unusually small)
Applied to 12 jobs in 30 minutes

Result:
- Fit Score: 5% (Terrible match, no skills listed)
- Fraud Score: 92 (High - all red flags present)
- Status: FLAGGED (Auto-flagged due to high fraud score)
- Recommendation: REJECT (Likely fake application)
```

### Scenario 3: Good Candidate with Minor Red Flags
```
User: Carol
Skills: "Python, SQL" (matches job needs)
Experience: 3 years (meets requirement)
Profile: 85% complete
Applied to 3 jobs today (normal activity)
CV: Valid 150KB PDF
Account: Created 2 months ago

Result:
- Fit Score: 72% (Good match)
- Fraud Score: 22 (Low - minor concerns only)
- Status: PENDING
- Recommendation: REVIEW & INTERVIEW (Good candidate)
```

---

## 🆘 Troubleshooting

**Q: Why is my fit score low?**
A: Check if you've added skills and experience to your profile. Incomplete profiles get lower scores.

**Q: Why is fraud score high?**
A: Review the fraud report. Common issues: new account, incomplete profile, spam-like keywords.

**Q: Why isn't fit score calculated?**
A: Ensure the job and user have proper data. Give database migration time to complete.

**Q: Can I see match scores as a job seeker?**
A: Coming soon! Currently visible to employers reviewing applicants.

---

## 📞 Support

For technical issues:
1. Check `ALGORITHM_DOCUMENTATION.md` for detailed docs
2. Review fraud report details for specific concerns
3. Contact admin if scores seem incorrect

---

## 🎉 Summary

You now have:
- ✅ **Smart Job Matching** - Find the best candidates automatically
- ✅ **Fraud Protection** - Safe platform with spam & bot detection  
- ✅ **Better Decisions** - Data-driven hiring with detailed reports
- ✅ **Better Visibility** - Scores show at a glance who's a good fit

**Start using it now!** Apply for jobs with complete profiles to see your fit scores. Employers, check the fraud reports to make better hiring decisions.

Happy hiring! 🚀
