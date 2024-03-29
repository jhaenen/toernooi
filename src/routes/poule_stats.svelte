<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Game, Poule, Stats } from "@/types/types";

    // Components
    import Loader from "@/components/load-ball.svelte";
    import Result from "@/components/result.svelte";
    import Standings from "@/components/standings.svelte";

    // Assets
    import DownIcon from '@/assets/icons/arrow-down.svg?component';

    let poule: Poule;
    let games: Array<Game> = [];
    let stats: Array<Stats> = [];

    let error = false;
    let loaded = false;

    export let params = {id: -1};

    onMount(async () => {
        await loadData();
    });

    window.addEventListener('online', () => { loadData(); });

    async function loadData() {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get stats from server
        try {
            const response = await fetch(server + "poules/" + params.id + "/stats");
            const combi = await response.json();

            games = combi.games;
            poule = combi.poule;
            stats = combi.stats;

            // Get game date
            const date_resp = await fetch(server + "params/date");
            const date = await date_resp.json();

            setTimeout(() => checkScroll(date.datum), 10);

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    }

    let scrollTarget: HTMLElement;
    let showScroll = false;

    function checkScroll(date: string) {
        if (games.length == 0) return;

       const now = new Date(Date.now());
    //    const now = new Date(2022, 9, 10, 13, 23);

        let currentGame: Game;

        let cur_diff = Number.MAX_VALUE;

        const date_arr = date.split("-");
        date_arr[1] = (parseInt(date_arr[1]) - 1).toString();

        for(const game of games) {
            const time_arr = game.time.split(":");
            const game_date = new Date(parseInt(date_arr[0]), parseInt(date_arr[1]), parseInt(date_arr[2]), parseInt(time_arr[0]), parseInt(time_arr[1]));

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
                scrollTarget = currentGame_el;
                showScroll = true;

                setTimeout(() => {
                    showScroll = false;
                }, 4000);

            } else {
                console.error("Could not find current game");
            }
        } 
    }

    function scrollToCurrentGame() {
        scrollTarget.scrollIntoView({ behavior: "smooth" });
        showScroll = false;
    }
</script>

<template>
    <div class="flex flex-col items-center m-4"><!-- lg:items-start lg:text-left -->
        {#if loaded && !error}
            <!-- Title -->
            <h1 class="text-[12vw] leading-none font-light text-center border-b-2 py-[5px] mi:text-5xl lg:py-5 lg:w-full lg:bg-white lg:sticky lg:top-0 sm:my-2 sm:border-b-0 sm:underline sm:underline-offset-[10px] sm:decoration-2" style={"text-decoration-color:" + poule.color + ";border-color:" + poule.color}>
                {poule.name}
            </h1>

            <div class="flex flex-col lg:flex-row-reverse gap-x-10 gap-y-4 m-4">
                <div>
                    <div class="flex flex-col items-center lg:items-start gap-y-3 w-screen lg:w-auto px-4 lg:sticky lg:top-20">
                        <!-- Poule standings -->
                        <h2 class="font-light text-3xl">Stand</h2>
                        <Standings standings={stats}/>
                    </div>
                </div>

                <div class="flex flex-col items-center lg:items-start gap-y-5 -z-10">
                    <!-- Poule games -->
                    <h2 class="font-light text-3xl lg:w-full lg:bg-white lg:sticky lg:top-20 z-10">Wedstrijden</h2>
                    {#each games as game (game.id)}
                        <div id={"game" + game.id} class="scroll-mt-5 sm:scroll-mt-32"><Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)} court_num={game.court_num} ref={game.ref} banner color={game.poule.color}/></div>
                    {:else}
                        <p>Geen Wedstrijden gepland.</p>
                    {/each}
                </div>
            </div>
        {:else}
            <!-- Loader -->
            <Loader {error}/>
        {/if}  
        
        <div class="bg-primary text-white p-2 rounded-2xl hover:cursor-pointer flex gap-1 items-center fixed bottom-24 sm:bottom-5 transition-opacity ease-linear duration-400" class:opacity-0={!showScroll} on:click={scrollToCurrentGame}>
            <div class="mb-1">Actuele wedstrijd</div>
            <div><DownIcon class="h-[22.5px] w-[22.5px] mb-0.5" fill="white"/></div>
        </div>
    </div>
</template>