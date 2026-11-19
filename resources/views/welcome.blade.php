<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Unauthorized Access</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: system-ui, sans-serif;
      background: #f1f1f1;
      color: #1f2937;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .card {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      max-width: 400px;
      text-align: center;
    }
    h1 {
      color: #dc2626;
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }
    .info {
      font-size: 0.9rem;
      color: #4b5563;
      text-align: left;
      margin-top: 1rem;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>🚫 Unauthorized Access</h1>
    <p>You are not authorized to view this page.</p>

    <div class="info">
      <strong>Device:</strong> {{ $device['device'] ?? 'Unknown' }}<br>
      <strong>Browser:</strong> {{ $device['browser'] ?? 'Unknown' }}<br>
      <strong>OS:</strong> {{ $device['os'] ?? 'Unknown' }}<br><br>
      @if ($location)
        <strong>IP:</strong> {{ $location['ip'] }}<br>
        <strong>City:</strong> {{ $location['city'] }}<br>
        <strong>Region:</strong> {{ $location['region'] }}<br>
        <strong>Country:</strong> {{ $location['country'] }}<br>
      @else
        <em>Location not detected</em>
      @endif
    </div>
  </div>
</body>
</html>
