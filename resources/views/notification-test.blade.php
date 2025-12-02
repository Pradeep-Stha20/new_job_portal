<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Test Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-warning:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            border-radius: 4px;
            margin-top: 30px;
            color: #333;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0d6efd;
        }
        .info-box code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>

<div class="test-container">
    <h1><i class="fa fa-bell"></i> Notification System Test</h1>
    
    <p>Click the buttons below to test different notification types:</p>
    
    <div class="button-group">
        <button class="btn-success" onclick="testSuccess()">
            <i class="fa fa-check-circle"></i> Success Notification
        </button>
        <button class="btn-danger" onclick="testError()">
            <i class="fa fa-exclamation-circle"></i> Error Notification
        </button>
        <button class="btn-warning" onclick="testWarning()">
            <i class="fa fa-exclamation-triangle"></i> Warning Notification
        </button>
    </div>

    <div class="info-box">
        <h3>ℹ️ How to Debug Notifications</h3>
        <ol>
            <li>Open <strong>Browser Developer Tools</strong> (F12 or Right-click → Inspect)</li>
            <li>Go to the <strong>Console</strong> tab</li>
            <li>Apply for a job and watch the console logs</li>
            <li>Look for <code>✅ AJAX Success!</code> or <code>❌ AJAX Error!</code> messages</li>
            <li>Check if <code>showSuccessNotification()</code> function is being called</li>
        </ol>
        <hr>
        <strong>Expected Console Output:</strong>
        <ul>
            <li>✅ AJAX Success! Response: {status: true, message: "..."}
            <li>✅ Application submitted successfully!
            <li>Calling showSuccessNotification...
        </ul>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script>
    // Global notification functions (copy from layout)
    function showSuccessNotification(title, message) {
        const notificationHtml = `
            <div class="notification-container notification-success animated slideInDown">
                <div class="notification-header">
                    <i class="fa fa-check-circle"></i>
                    <h5>${title}</h5>
                    <button type="button" class="btn-close" onclick="closeNotification(this)"></button>
                </div>
                <div class="notification-body">
                    <p>${message}</p>
                </div>
                <div class="notification-progress"></div>
            </div>
        `;
        
        $('body').append(notificationHtml);
        
        // Auto-close after 4 seconds
        setTimeout(function() {
            closeNotification($('.notification-container:last').find('.btn-close'));
        }, 4000);
    }

    function showErrorNotification(title, message) {
        const notificationHtml = `
            <div class="notification-container notification-error animated slideInDown">
                <div class="notification-header">
                    <i class="fa fa-exclamation-circle"></i>
                    <h5>${title}</h5>
                    <button type="button" class="btn-close" onclick="closeNotification(this)"></button>
                </div>
                <div class="notification-body">
                    <p>${message}</p>
                </div>
                <div class="notification-progress"></div>
            </div>
        `;
        
        $('body').append(notificationHtml);
        
        // Auto-close after 5 seconds
        setTimeout(function() {
            closeNotification($('.notification-container:last').find('.btn-close'));
        }, 5000);
    }

    function showWarningNotification(title, message) {
        const notificationHtml = `
            <div class="notification-container notification-warning animated slideInDown">
                <div class="notification-header">
                    <i class="fa fa-exclamation-triangle"></i>
                    <h5>${title}</h5>
                    <button type="button" class="btn-close" onclick="closeNotification(this)"></button>
                </div>
                <div class="notification-body">
                    <p>${message}</p>
                </div>
                <div class="notification-progress"></div>
            </div>
        `;
        
        $('body').append(notificationHtml);
        
        // Auto-close after 4 seconds
        setTimeout(function() {
            closeNotification($('.notification-container:last').find('.btn-close'));
        }, 4000);
    }

    function closeNotification(btn) {
        const notification = $(btn).closest('.notification-container');
        notification.removeClass('slideInDown').addClass('slideOutUp');
        setTimeout(function() {
            notification.remove();
        }, 500);
    }

    // Test functions
    function testSuccess() {
        console.log('✅ Testing success notification...');
        showSuccessNotification('Success!', 'This is a success notification. It will auto-close after 4 seconds.');
    }

    function testError() {
        console.log('❌ Testing error notification...');
        showErrorNotification('Error!', 'This is an error notification. It will auto-close after 5 seconds.');
    }

    function testWarning() {
        console.log('⚠️ Testing warning notification...');
        showWarningNotification('Warning!', 'This is a warning notification. It will auto-close after 4 seconds.');
    }
</script>

</body>
</html>
