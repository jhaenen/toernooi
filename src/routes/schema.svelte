<script lang="ts">
    import { onMount } from "svelte";
    import type { Game } from "@/types";

    import Result from "@/components/result.svelte";

    import Loader from "@/components/load-ball.svelte";

    let games: Array<Game> = [];

    let error = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        try {
            const response = await fetch(server + "g_games.php");
            games = await response.json();
            error = false;
        } catch (err) {
            error = true;
            console.error(err);
        }
    });

</script>

<main>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <h1 class="m-4 text-[12vw] leading-none font-thin mi:text-5xl">Resultaten</h1>

        {#each games as game (game.id)}
            <Result team1={game.team1} team2={game.team2}/>
        {:else}
            <div class="fixed top-[40%] sm:left-1/2"><Loader {error}/></div>
        {/each}
    </div>
</main>
