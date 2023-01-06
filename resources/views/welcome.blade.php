<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Kardex | API</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
      
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>

        <link rel="icon" href{{ asset('/img/favicon.ico') }}">
        
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
    </head>

    <body class="">
        <!--<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

        </div>-->

        <div class="h-screen relative overflow-auto max-w-8xl bg-gradient-to-r from-green-200 via-green-300 to-green-400">
    
            <div class="w-full container mx-auto">
                <div class="w-full flex items-center justify-between">

                    <div class="pt-2 pl-4 md:pl-0 flex items-center text-green-600 font-bold text-2xl lg:text-4xl">

                        <span class="border p-2 shadow-md border-gray-50 rounded mr-2 ">
                          Kar
                        </span>

                        <span class="border p-2  shadow-md border-gray-600 rounded mt-10 bg-clip-text text-transparent bg-gradient-to-r from-green-400 via-green-500 to-green-800">
                          dex
                        </span>

                    </div>


                </div>
            </div>

            <div class="h-4/6 pt-5 lg:w-2/4 mx-auto flex ">

                <div class="mx-2 flex flex-col w-full items-center">
                
                    <div class="z-0 fixed mx-auto w-4/5 sm:w-4/6 lg:w-3/6 xl:w-3/6 2xl:w-2/6 max-w-2xl">
                        <img class="mx-auto w-full" src="{{ config('app.urlb')}}/storage/images/turquesa.svg" />
                    </div>

                    <div class="flex-1 z-10 ">

                        <h1 class="z-10 text-7xl md:text-8xl text-white opacity-75 font-bold leading-tight text-center">
                            Kar
                            <span class="-ml-5 bg-clip-text text-transparent bg-gradient-to-r from-green-500 via-green-600 to-green-800">
                               dex
                            </span>
                        </h1>

                        <p class="z-10 text-gray-600 leading-normal font-semibold text-base md:text-2xl mb-8 text-center">
                           Control de Inventario
                        </p>

                        <p class="z-10 text-gray-600 leading-normal font-semibold text-base mb-8 text-center">
                           API
                        </p>

                    </div>

                </div>

            </div>

            <div class="mt-5 absolute bottom-0 bg-gradient-to-r from-green-500 via-green-400 to-green-500 flex-col place-items-end w-full text-sm  text-center">

                <b class="text-gray-600 no-underline hover:no-underline">&copy; KARDEX 2022 </b>
                    <span class="text-green-600">_vr. {{ config('app.version')}} _</span>
                <b class="text-gray-600">  NZCAICEDO  </b>

                <a href="{{ config('app.urlf')}}" style='color:blue;font-size:15px' class="underline">kardex.com.co</a>

            </div>

        </div>

    </body>
</html>
