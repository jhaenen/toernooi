<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Game, Poule, Stats } from "@/types/types";

    // Components
    import Loader from "@/components/load-ball.svelte";
    import Result from "@/components/result.svelte";
    import Standings from "@/components/standings.svelte";

    import logo from "@/assets/logo.svg";

    let poules: Array<Poule> = [];
    let next_games: Array<Game> = [];
    let last_results: Array<Game> = [];
    // let games: Array<Game> = [];


    let error = false;
    let loaded = false;

    onMount(async () => {
        get_data();

        // Get data every 1 minute
        setInterval(get_data, 60000);
    });

    async function get_data() {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get stats from server
        try {
            const response = await fetch(server + "screen");
            const json = await response.json();

            poules = json.stats;
            next_games = json.next;
            last_results = json.latest;

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    }
</script>

<template>
    <div class="w-full py-10 flex items-center justify-center gap-4">
        <img src={logo} alt="DAS logo" class="w-44">
        <h1 class="m-4 font-light text-7xl font-logo">DAS Familietoernooi</h1>
    </div>

    <div class="flex flex-roww-full justify-center gap-x-[5vw]">
        {#each poules as poule (poule.id)}
            <div class="w-fit">
                <div class="font-light text-3xl mb-5 border-b-2 pb-2" style={"border-color:" + poule.color}>{poule.name}</div>
                <Standings standings={poule.standings} showToggle={false} hideOverflow={true}/>
            </div>
        {/each}
    </div>

    
        
    <div>
        <h2 class="text-3xl text-center mt-8 mb-5">Volgende wedstrijden</h2>

        <div class="flex flex-roww-full justify-center gap-x-[5vw]">
            {#each next_games as game (game.id)}
                <div class="flex flex-col">
                    <Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)} poule={game.poule.name} court_num={game.court_num} ref={game.ref} banner color={game.poule.color} />
                </div>
            {:else}
                <div class="flex h-40 items-center justify-center border-b-2 w-full mx-10"><span class="text-3xl font-light">Geen wedstrijden meer gepland</span></div>
            {/each}
        </div>
    </div>

    <div>
        <h2 class="text-3xl text-center mt-10 mb-5">Laatste Uitslagen</h2>

        <div class="flex flex-row w-full justify-center gap-x-[5vw]">
            {#each last_results as game (game.id)}
                <div class="flex flex-col">
                    <Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)} poule={game.poule.name} court_num={game.court_num} ref={game.ref} banner color={game.poule.color} />
                </div>
            {:else}
            <div class="flex h-40 items-center"><span class="text-3xl font-light">Nog geen wedstrijden gespeelt</span></div>
            {/each}
        </div>
    </div>

    
</template>