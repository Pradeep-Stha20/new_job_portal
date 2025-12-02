# Application Validation - Visual Guide & Examples

## 🎯 Quick Reference

### Notification Types

```
┌─────────────────────────────────────────────────┐
│ ✓ APPLICATION SUBMITTED SUCCESSFULLY!           │ GREEN
│                                                 │
│ Your application has been submitted. The        │
│ employer will review it shortly.               │
│                                                 │
│ ════════════════════════════════════════════════│ PROGRESS BAR
└─────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────┐
│ ✗ APPLICATION ERROR                             │ RED
│                                                 │
│ You already applied on this job.               │
│                                                 │
│ ════════════════════════════════════════════════│ PROGRESS BAR
└─────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────┐
│ ⚠ SUBMISSION ERROR                              │ YELLOW
│                                                 │
│ Please login to apply for jobs.                │
│                                                 │
│ ════════════════════════════════════════════════│ PROGRESS BAR
└─────────────────────────────────────────────────┘
```

---

## 📋 Application Form Validation States

### State 1: Empty Form (Initial Load)
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                           0/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ Tell us why you are a great fit for this   │ │
│ │ job (minimum 20 characters)                │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Choose file                                │ │
│ └──────────────────────────────────────────────┘ │
│ ✓ Accepted formats: PDF, DOC, DOCX (Max 2MB)  │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘
```

### State 2: User Typing (Character Counter Active)
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                          45/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position      │ │
│ │ because I have the required skills and     │ │
│ │ experience.                                │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Choose file                                │ │
│ └──────────────────────────────────────────────┘ │
│ ✓ Accepted formats: PDF, DOC, DOCX (Max 2MB)  │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘
```

### State 3: File Selected (File Info Shows)
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                         245/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position      │ │
│ │ because I have the required skills and     │ │
│ │ experience. With 3 years of experience     │ │
│ │ in this field, I am confident I can make   │ │
│ │ a significant contribution to your team.   │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Resume_2024.pdf                            │ │
│ └──────────────────────────────────────────────┘ │
│ ✓ Resume_2024.pdf (0.82MB)                      │
│ ✓ Accepted formats: PDF, DOC, DOCX (Max 2MB)  │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘
```

### State 4: Near Character Limit (Warning Shows)
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                       1850/2000   │
│ ┌──────────────────────────────────────────────┐ │
│ │ [Long cover letter text...]                │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters    ⚠ Getting close to limit│
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Resume_2024.pdf                            │ │
│ └──────────────────────────────────────────────┘ │
│ ✓ Resume_2024.pdf (0.82MB)                      │
│ ✓ Accepted formats: PDF, DOC, DOCX (Max 2MB)  │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘
```

### State 5: Submitting (Button Shows Loading State)
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                         245/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position...   │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Resume_2024.pdf                            │ │
│ └──────────────────────────────────────────────┘ │
│ ✓ Resume_2024.pdf (0.82MB)                      │
│                                                  │
│           [Cancel] [⏳ Submitting...] (disabled) │
└──────────────────────────────────────────────────┘
```

---

## ❌ Validation Error Scenarios

### Scenario 1: Cover Letter Too Short
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                          12/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I like this job.                           │ │
│ └──────────────────────────────────────────────┘ │
│ ✗ Cover letter must be at least 20 characters. │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Choose file                                │ │
│ └──────────────────────────────────────────────┘ │
│ ✗ Please select a CV file.                      │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘

💬 Error Notification:
   ✗ APPLICATION ERROR
   Please fix the errors below and try again.
```

### Scenario 2: Invalid File Type
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                         150/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position...   │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Resume.txt                                 │ │
│ └──────────────────────────────────────────────┘ │
│ ✗ Resume.txt (0.12MB)                           │
│ ✗ Only PDF, DOC, and DOCX files are allowed.   │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘

💬 Error Notification:
   ✗ APPLICATION ERROR
   Only PDF, DOC, and DOCX files are accepted
```

### Scenario 3: File Too Large
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                         200/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position...   │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ CV_With_Portfolio.pdf                      │ │
│ └──────────────────────────────────────────────┘ │
│ ✗ CV_With_Portfolio.pdf (5.2MB)                │
│ ✗ File size must not exceed 2MB.               │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘

💬 Error Notification:
   ✗ APPLICATION ERROR
   CV file size cannot exceed 2MB
```

### Scenario 4: No File Selected
```
┌─ Apply for this Job ─────────────────────────────┐
│                                                  │
│ Cover Letter *                         180/2000  │
│ ┌──────────────────────────────────────────────┐ │
│ │ I am very interested in this position...   │ │
│ └──────────────────────────────────────────────┘ │
│ Minimum 20 characters, maximum 2000 characters  │
│                                                  │
│ Upload Your CV *                                │
│ ┌──────────────────────────────────────────────┐ │
│ │ Choose file                                │ │
│ └──────────────────────────────────────────────┘ │
│ ✗ Please select a CV file.                      │
│                                                  │
│                    [Cancel] [Submit Application] │
└──────────────────────────────────────────────────┘

💬 Error Notification:
   ✗ APPLICATION ERROR
   Please upload your CV
```

---

## ✅ Successful Submission

### Scenario: Valid Application Submitted
```
1️⃣  User fills form with valid data
    - Cover letter: 250 characters ✓
    - File: Resume.pdf, 0.8MB ✓

2️⃣  User clicks "Submit Application"

3️⃣  Button shows loading state
    [⏳ Submitting...]

4️⃣  Application is validated and saved

5️⃣  Success notification appears (top-right):

    ┌─────────────────────────────────────────────┐
    │ ✓ APPLICATION SUBMITTED SUCCESSFULLY!        │
    │                                             │
    │ Your application has been submitted. The    │
    │ employer will review it shortly.           │
    │                                             │
    │ ════════════════════════════════════════════│
    └─────────────────────────────────────────────┘
    
    ⏱️ Auto-closes after 4 seconds

6️⃣  Page redirects after 2 seconds
```

---

## 🚫 Server-Side Error Scenarios

### Already Applied
```
💬 Error Notification:
   ✗ APPLICATION ERROR
   You already applied on this job.
```

### Can't Apply to Own Job
```
💬 Error Notification:
   ✗ APPLICATION ERROR
   You can not apply on your own job.
```

### Not Logged In
```
💬 Error Notification:
   ✗ SUBMISSION ERROR
   Please login to apply for jobs.
```

### Job Not Found
```
💬 Error Notification:
   ✗ SUBMISSION ERROR
   Job does not exist.
```

---

## 📱 Mobile View (< 768px)

### Form Layout (Stacked)
```
┌────────────────────────────────┐
│ 🧳 Apply for this Job          │
│                                │
│ Cover Letter *        0/2000   │
│ ┌──────────────────────────┐   │
│ │ Tell us why you are a   │   │
│ │ great fit for this job  │   │
│ └──────────────────────────┘   │
│ Minimum 20, max 2000 characters│
│                                │
│ Upload Your CV *               │
│ ┌──────────────────────────┐   │
│ │ Choose file              │   │
│ └──────────────────────────┘   │
│ ✓ Accepted: PDF, DOC, DOCX    │
│ Max 2MB                        │
│                                │
│ [Cancel]    [Submit Application]
│                                │
└────────────────────────────────┘
```

### Notification (Full Width)
```
┌────────────────────────────────┐
│ ✓ APPLICATION SUBMITTED         │
│                                │
│ Your application has been      │
│ submitted. The employer will   │
│ review it shortly.            │
│                                │
│ ════════════════════════════════│
└────────────────────────────────┘
(Spans 100% - 20px with 10px margin)
```

---

## ⌨️ Keyboard Shortcuts & Accessibility

**Tab Navigation:**
- Tab through form fields
- Tab to Submit/Cancel buttons
- Enter key submits form
- Escape key closes modal

**Screen Readers:**
- All labels properly associated
- Error messages announced
- Required field indicators
- ARIA labels present

---

## 🎨 Color Scheme

| Element | Color | Hex | Usage |
|---------|-------|-----|-------|
| Success | Green | #28a745 | ✓ Valid, Success |
| Error | Red | #dc3545 | ✗ Invalid, Error |
| Warning | Yellow | #ffc107 | ⚠ Warning |
| Info | Blue | #0d6efd | ℹ Information |
| Text Error | Dark Red | #666 | Error messages |
| Border Error | Red | #dc3545 | Invalid fields |

---

## 📊 Response Time Expectations

| Action | Time | Feedback |
|--------|------|----------|
| Character count update | <1ms | Real-time |
| File selection | <1ms | Real-time |
| Frontend validation | <5ms | Instant |
| AJAX submission | 500-1500ms | Loading spinner |
| Success notification | 4000ms | Auto-close |
| Error notification | 5000ms | Auto-close |
| Page redirect | 2000ms | After success |

---

## 🔒 Security Notes

✅ All validation done on both frontend and backend
✅ CSRF token protection on form submission
✅ File type validation (not just extension)
✅ File size limits enforced
✅ User authentication required
✅ Authorization checks (can't apply to own jobs)
✅ Rate limiting via duplicate prevention

---

## 📞 Support Messages

| Scenario | User Message |
|----------|--------------|
| Server error | "An error occurred. Please try again." |
| Network error | "Connection error. Please check your internet." |
| Timeout | "Request timeout. Please try again." |
| Not logged in | "Please login to apply for jobs." |
| Already applied | "You already applied on this job." |
| Own job | "You can not apply on your own job." |
| Job deleted | "Job does not exist." |

---

**Visual Guide Version:** 1.0
**Last Updated:** December 2, 2025
