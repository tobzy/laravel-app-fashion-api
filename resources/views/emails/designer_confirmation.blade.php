<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Verify Your Email Address</h2>

<div>
    <p>Thanks for creating an account with NATTIV.</p>

    <p>Please follow the link below to verify your email address</p>
    <p>{{ URL::to('v1/register/verify/' . $confirmation_code) }}.</p>

</div>

</body>
</html>