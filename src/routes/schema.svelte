<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Game } from "@/types/types";

    // Components
    import Result from "@/components/result.svelte";
    import Loader from "@/components/load-ball.svelte";

    let games: Array<Game> = [];

    let error = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get games from server
        try {
            const response = await fetch(server + "games");
            games = await response.json();
            setTimeout(scrollToCurrentGame, 500);

            error = false;
        } catch (err) {
            error = true;
            console.error(err);
        }

        
    });

    function scrollToCurrentGame() {
        if (games.length == 0) return;

        // const now = new Date(Date.now());
        const now = new Date(2022, 9, 10, 12, 23);

        let currentGame: Game;
        
        let cur_diff = Number.MAX_VALUE;

        for(const game of games) {
            const time_arr = game.time.split(":");
            const game_date = new Date(2022, 9, 10, parseInt(time_arr[0]), parseInt(time_arr[1]));

            const diff = Math.abs(game_date.getTime() - now.getTime());
            
            if(cur_diff > diff) {
                cur_diff = diff;
                currentGame = game;
            }
        }

        const currentGame_el = document.getElementById("game" + currentGame.id);
        const bounding = currentGame_el.getBoundingClientRect();

        if(!(bounding.top >= 0 && bounding.left >= 0 && bounding.right <= window.innerWidth && bounding.bottom <= window.innerHeight)) {
            if (currentGame_el) {
                currentGame_el.scrollIntoView({ behavior: "smooth" });
            } else {
                console.error("Could not find current game");
            }
        }
    }
</script>

<main>
    <div class="flex flex-col items-center gap-y-5 m-4">
        <!-- Title -->
        <h1 class="my-2 text-[12vw] leading-none font-thin mi:text-5xl">Dagschema</h1>

        <!-- Game list -->
        {#each games as game (game.id)}
            <div id={"game" + game.id} class="scroll-mt-5"><Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)} poule={game.poule.name} court_num={game.court_num} ref={game.ref} banner color={game.poule.color}/></div>
        {:else}
            <Loader {error}/>
        {/each}
    </div>
</main>
