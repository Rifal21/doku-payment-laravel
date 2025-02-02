<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
  </head>
  <body>
    @include('layouts.partials.navbar')
    <div class="container">
      @yield('content')
    </div>
  </body>
</html>