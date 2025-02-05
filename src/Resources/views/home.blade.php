<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - From Package</title>
</head>
<body>
<h1>Welcome to the Home Page from the Laravel Package!</h1>
@csrf
<form method="POST" action="{{route('handle.input')}}">
    <label>Enter Prompt?<input></label>
    <button type="submit">Submit</button>
</form>
</body>
</html>