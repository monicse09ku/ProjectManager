<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #3490dc;
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .task-details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-left: 4px solid #3490dc;
            border-radius: 3px;
        }
        .detail-row {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            min-width: 120px;
        }
        .detail-value {
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Task Assigned</h1>
        </div>

        <div class="content">
            <p>Hello {{ $assignedUser->name }},</p>

            <p>You have been assigned a new task. Here are the details:</p>

            <div class="task-details">
                <div class="detail-row">
                    <span class="detail-label">Task Title:</span>
                    <span class="detail-value">{{ $task->title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Project:</span>
                    <span class="detail-value">{{ $project?->title ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Deadline:</span>
                    <span class="detail-value">{{ $task->deadline }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($task->status) }}</span>
                </div>
            </div>

            <p>Please review the task and let us know if you have any questions.</p>

            <p>Best regards,<br>The ProjectManager Team</p>
        </div>

        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
