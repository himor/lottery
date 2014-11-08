<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<base href="{{ URL::to('/') }}">
<title>Lottery Analyzer</title>
<meta name="description" content="">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="main.css">
</head>
<body>

<div class="menu">
    <ul>
        <li>
            @yield('cache')
        </li>
        <li>
            <a href="{{ URL::route('base') }}">Stat</a>
        </li>
        <li>
            <a href="{{ URL::route('the_set') }}">Random Set</a>
        </li>
        <li>
            <a href="{{ URL::route('the_best') }}">Best Random Set</a>
        </li>
        <li>
            <a href="{{ URL::route('check') }}">Check Set</a>
        </li>
    </ul>
</div>

@yield('content')

</body>
</html>