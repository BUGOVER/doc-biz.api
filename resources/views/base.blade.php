<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{env('APP_NAME')}}</title>
  <base href="{{ url('/') }}">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
</head>
<body>

<app-root></app-root>
<script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/polyfills.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/panel-panel-module.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/runtime.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/styles.js') }}"></script>
</body>
</html>
