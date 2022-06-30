<script lang="ts">
    import { onMount } from "svelte";

    import type { Game, Poule, Stats } from "@/types";

    import Loader from "@/components/load-ball.svelte";
    import Result from "@/components/result.svelte";
    import Toggle from "@/components/toggle.svelte";
import Standings from "@/components/standings.svelte";

    let poule: Poule;
    let games: Array<Game> = [];
    let stats: Array<Stats> = [];

    let error = false;
    let loaded = false;

    export let params = {id: -1};
    let details = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        try {
            const g_resp = await fetch(server + "g_games.php?p=" + params.id);
            games = await g_resp.json();

            const p_resp = await fetch(server + "g_poules.php?p=" + params.id);
            poule = await p_resp.json();

            const s_resp = await fetch(server + "g_stats.php?p=" + params.id);
            stats = await s_resp.json();

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    });
</script>

<template>
    <div class="flex flex-col items-center sm:items-start sm:text-left m-4">
        {#if loaded && !error}
            <h1 class="my-2 text-[12vw] leading-none font-light text-center mi:text-5xl">Poule: {poule.name}</h1>

            <!-- Poule standings -->
            <h2 class="m-4 font-light text-2xl">Stand</h2>
            <Standings standings={stats}/>

            <!-- Poule games -->
            <h2 class="m-4 font-light text-2xl">Wedstrijden</h2>
            {#each games as game (game.id)}
                <Result team1={game.team1} team2={game.team2}/>
            {:else}
                <p>Geen Wedstrijden gepland.</p>
            {/each}
        {:else}
            <!-- Loader -->
            <div class="fixed top-[40%] sm:left-1/2"><Loader {error}/></div>
        {/if}       
    </div>
</template>