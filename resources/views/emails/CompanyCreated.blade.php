<!DOCTYPE html>
<html>
<head>
    <title>New company is successfully created!</title>
</head>
<body>
    Hi {{ $user->name }},<br/><br/>
    {{ $company->name }} has been created on {{ date('d/m/Y'); }}<br/><br/>
    Thank you<br/>
    {{ config('app.name') }}
</body>
</html>
