// import './bootstrap';

require("./bootstrap");

console.log("やあ");

// const game_json = @games;

// console.log(game_json);

const Counter = {
    data() {
        return {
            counter: 0,
        };
    },
    mounted() {
        setInterval(() => {
            this.counter++;
        }, 1000);
    },
};

Vue.createApp(Counter).mount("#counter");
