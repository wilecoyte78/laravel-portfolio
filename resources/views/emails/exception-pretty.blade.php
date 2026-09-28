<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f7; color: #333; padding: 20px; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #e53e3e; margin-top: 0; }
        .label { font-weight: bold; color: #4a5568; }
        .box { background: #edf2f7; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 13px; overflow-x: auto; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>An Exception Occurred</h2>
        <p><span class="label">Environment:</span> {{ config('app.env') }}</p>
        <p><span class="label">Time:</span> {{ now() }}</p>
        <p><span class="label">Message:</span> {{ $exception->getMessage() }}</p>
        <p><span class="label">File:</span> {{ $exception->file }} : {{ $exception->line }}</p>
        
        <div class="label" style="margin-top: 20px;">Stack Trace:</div>
        <div class="box">{{ $exception->getTraceAsString() }}</div>
    </div>
</body>
</html>
