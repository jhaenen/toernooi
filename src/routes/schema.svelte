<script lang="ts">
    import { onMount } from "svelte";
    import type { TeamResult } from "@/types";

    import Result from "@/view/result.svelte";

    import Loader from "@/view/load-ball.svelte";

    interface Game {
        id: number;
        poule_id: number;
        poule_name: string;
        team1: TeamResult;
        team2: TeamResult;
        time: string;
    }

    let games: Array<Game> = [];
    
    onMount(async () => {
        const response = await fetch("http://localhost:4000/g_games.php");
        const data = await response.json();
        games = data;
    });

</script>

<main>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <h1 class="font-thin text-5xl">Resultaten</h1>

        {#each games as game (game.id)}
            <Result team1={game.team1} team2={game.team2}/>
        {:else}
            <div class="fixed top-[40%] sm:static"><Loader/></div>
        {/each}
    </div>
</main>
