<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @foreach ($errors->all() as $error)
        <p>{{$error}}</p>
    @endforeach
    <form action="{{route('lawyer.signup')}}" method="post">
        @csrf
        <input type="text" name="name">
        <input type="email" name="email">
        <input type="tel" name="phone">
        <input type="password" name="password">
        <input type="password" name="password_confirmation">
        <button>submit</button>
    </form>
</body>
</html>