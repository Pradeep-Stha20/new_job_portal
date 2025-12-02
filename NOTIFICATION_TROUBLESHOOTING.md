# Notification System - Troubleshooting Guide

## Issue: Notifications not showing when applying for jobs

### Quick Test
1. Go to: `http://yourapp.local/test-notifications`
2. Click the notification buttons
3. **If notifications appear**: Problem is with job application flow
4. **If notifications don't appear**: Problem is with notification system itself

---

## Step-by-Step Debug

### Step 1: Check Browser Console
1. Open job detail page
2. Press `F12` to open Developer Tools
3. Click on "Console" tab
4. Apply for a job
5. Look for these messages:

**If you see:**
```
✅ FormData created, submitting...
✅ AJAX Success! Response: {status: true, message: "..."}
✅ Application submitted successfully!
Calling showSuccessNotification...
```
→ **Notification system is working! Problem might be elsewhere.**

**If you see:**
```
❌ AJAX Error! [error details]
```
→ **Backend issue - check server error**

---

### Step 2: Check Network Tab
1. Press `F12` → Go to "Network" tab
2. Apply for a job
3. Look for request to `/apply-job`
4. Check the response:
   - **Status 200 OK** → Backend working
   - **Status 422** → Validation error
   - **Status 500** → Server error
   - **Status 401** → Not authenticated

Click the response and check the JSON:
```json
{
  "status": true,
  "message": "You have successfully applied."
}
```

---

### Step 3: Verify CSS is Loaded
1. Press `F12` → Go to "Elements" tab
2. Find `<head>` section
3. Look for:
   ```html
   <link rel="stylesheet" type="text/css" href="/css/app.css" />
   ```

**If missing**: Add it to `resources/views/front/layouts/app.blade.php`

---

### Step 4: Verify JavaScript Functions
1. Press `F12` → Go to "Console" tab
2. Type: `typeof showSuccessNotification`
3. **If it says `function`** → Function is defined
4. **If it says `undefined`** → Function not loaded

---

## Common Issues & Fixes

### Issue 1: "showSuccessNotification is not defined"
**Problem**: Notification functions not in global scope

**Fix**: Verify in `resources/views/front/layouts/app.blade.php`:
```html
<!-- Should be in <script> before @yield('customJs') -->
<script>
    function showSuccessNotification(title, message) {
        // ... code ...
    }
</script>
```

---

### Issue 2: Notification doesn't appear but console shows success
**Problem**: CSS not loaded or z-index issue

**Check**:
1. `app.css` is linked in layout
2. No other CSS overriding z-index
3. Browser window is in focus

**Verify CSS**:
```css
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;  /* Make sure this is high */
}
```

---

### Issue 3: Notification appears but disappears immediately
**Problem**: Animation or progress bar issue

**Check**: Open DevTools → Click notification → Check computed styles
- Should show: `animation: slideInDown 0.5s`
- Should have: `animation-duration: 0.5s`

---

### Issue 4: Multiple notifications overlap
**Problem**: Not resetting notification container

**Expected behavior**: Only 1 notification at a time

**Fix**: Check `closeNotification()` function is removing notifications

---

## Testing Flow

### Manual Test (No job application needed)
```javascript
// In browser console, type:
showSuccessNotification('Test', 'Does this appear?')
```

**If notification appears**: ✅ System works
**If nothing happens**: ❌ Check CSS/JavaScript

---

### Full Application Test
1. **Apply for a job** with valid inputs
2. **Expected in 2 seconds**: 
   - Modal closes
   - Success notification appears (top-right)
   - Page reloads
3. **Check notification styling**:
   - Green border on left
   - Checkmark icon
   - Auto-closes after 4 seconds

---

## Browser Console Logs to Check

### Successful Application
```
✅ AJAX Success! Response: {status: true, message: "You have successfully applied."}
✅ Application submitted successfully!
Calling showSuccessNotification...
Redirecting after 2 seconds...
```

### Validation Error
```
❌ AJAX Error! 422 Unprocessable Entity
Response: {status: false, errors: {cover_letter: ["..."]}}
Showing error notification: ...
```

### Authentication Error
```
❌ AJAX Error! 401 Unauthorized
Response: {status: false, message: "Please login to apply"}
Showing error notification: Please login to apply for jobs.
```

---

## Advanced Debugging

### Check jQuery is loaded
```javascript
// Type in console:
typeof jQuery  // Should return "function"
```

### Check Bootstrap is available
```javascript
// Type in console:
typeof bootstrap  // Should return "object"
```

### Manually trigger notification
```javascript
// Type in console:
showSuccessNotification('Manual Test', 'If you see this, notifications work!');
```

---

## Production Checklist

✅ Remove test route `/test-notifications`
✅ Remove debug console.log statements
✅ Verify CSS minified in production
✅ Check z-index doesn't conflict with other modals
✅ Test on mobile devices (notifications responsive)
✅ Test in different browsers (Chrome, Firefox, Safari, Edge)

---

## Support

If notifications still don't work after these steps:

1. **Check application logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Test backend**:
   ```bash
   curl -X POST http://yourapp/apply-job -d '{"id":1, ...}'
   ```

3. **Check email functionality** (if notifications send emails)

---

**Still stuck?** Check the browser console logs and provide the full error message.
