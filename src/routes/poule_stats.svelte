<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Game, Poule, Stats } from "@/types/types";

    // Components
    import Loader from "@/components/load-ball.svelte";
    import Result from "@/components/result.svelte";
    import Standings from "@/components/standings.svelte";

    let poule: Poule;
    let games: Array<Game> = [];
    let stats: Array<Stats> = [];

    let error = false;
    let loaded = false;

    export let params = {id: -1};

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get stats from server
        try {
            const g_resp = await fetch(server + "games/" + params.id);
            games = await g_resp.json();

            const p_resp = await fetch(server + "poules/" + params.id);
            poule = await p_resp.json();

            const s_resp = await fetch(server + "stats/" + params.id);
            stats = await s_resp.json();

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    });
</script>

<template>
    <div class="flex flex-col items-center m-4"><!-- lg:items-start lg:text-left -->
        {#if loaded && !error}
            <!-- Title -->
            <h1 class="my-2 text-[12vw] leading-none font-light text-center mi:text-5xl lg:py-5 lg:w-full lg:bg-white lg:sticky lg:top-0">Poule: {poule.name}</h1>

            <div class="flex flex-col lg:flex-row-reverse gap-x-10 gap-y-4 m-4">
                <div>
                    <div class="flex flex-col items-center lg:items-start gap-y-3 w-screen lg:w-auto px-4 lg:sticky lg:top-20">
                        <!-- Poule standings -->
                        <h2 class="font-light text-3xl">Stand</h2>
                        <Standings standings={stats}/>
                    </div>
                </div>

                <div class="flex flex-col items-center lg:items-start gap-y-5">
                    <!-- Poule games -->
                    <h2 class="font-light text-3xl lg:w-full lg:bg-white lg:sticky lg:top-20">Wedstrijden</h2>
                    {#each games as game (game.id)}
                        <Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)}/>
                    {:else}
                        <p>Geen Wedstrijden gepland.</p>
                    {/each}
                </div>
            </div>
        {:else}
            <!-- Loader -->
            <Loader {error}/>
        {/if}       
    </div>
</template>