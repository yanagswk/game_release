<html>
<body>

    {{-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> --}}

    <div id="counter">
        カウントアップ!!: @{{ counter }}
    </div>

    {{-- <div id="app">@{{ message }}</div> --}}

    {{-- <p>
        @foreach ($games as $item)
            <div>{{$item["title"]}}</div>
        @endforeach
    </p> --}}

    {{-- <p>
        <div v-for="game in games" :key="game">
            @{{game["title"]}}
        </div>
    </p> --}}

    <script src="{{ mix('js/app.js') }}"></script>

    {{-- <script>
        const { createApp } = Vue


        console.log("やあ")

        const game = @json($games)

        console.log(game)

        createApp({
            data() {
                return {
                    message: 'Hello Vue!',

                    games: game
                }
            }
        }).mount('#app')
    </script> --}}


</body>
</html>
