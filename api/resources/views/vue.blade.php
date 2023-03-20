<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Vite React</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="counter">
            カウントアップ!!: @{{ counter }}
            <br>
            @{{message}}
        </div>

        <div>
            <a href="/">戻る</a>
        </div>
    </body>
</html>


<html>
<body>

    {{-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> --}}
    {{-- <script src="{{ mix('js/app.js') }}"></script> --}}


    <script>
        // import './bootstrap';
        import { createApp } from "vue";

        createApp({
            data() {
                return {
                    message: 'Hello Vue!',

                    // games: game
                }
            }
        }).mount('#counter')
    </script>


</body>
</html>
