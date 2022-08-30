<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Game } from "@/types/types";

    // Components
    import Result from "@/components/result.svelte";
    import Loader from "@/components/load-ball.svelte";

    // Assets
    import logo from "@/assets/logo.svg";

    interface TimeSlot {
        time: string;
        games: Array<Game>;
    }

    let gamesSorted: Array<TimeSlot> = [];

    let error = false;
    let loaded = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get games from server
        try {
            const response = await fetch(server + "games/sorted");
            gamesSorted = await response.json();

            setTimeout(scrollToCurrentGame, 500);

            error = false;
            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }

        
    });

    function scrollToCurrentGame() {
        if (gamesSorted.length == 0) return;

        const now = new Date(Date.now());
        // const now = new Date(2022, 9, 10, 12, 23);
        // log now

        let currentGame: Game;
        
        let cur_diff = Number.MAX_VALUE;

        for(const slot of gamesSorted) {
            const time_arr = slot.time.split(":");
            const game_date = new Date(2022, 8, 10, parseInt(time_arr[0]), parseInt(time_arr[1]));

            const diff = Math.abs(game_date.getTime() - now.getTime());
            
            if(cur_diff > diff) {
                cur_diff = diff;
                currentGame = slot.games[0];
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

<template>
    <div class="flex flex-col items-center m-4">
        <!-- Logo -->
        <img class="w-[40%] h-auto max-w-xs sm:hidden" src={logo} alt="DAS logo" width="320" height="320"/>
        
        <!-- Title -->
        <h1 class="my-2 text-[12vw] leading-none font-thin mi:text-5xl sm:mt-8">Dagschema</h1>

        <!-- Game list -->
        <div class="flex flex-col items-center w-fit">
            {#each gamesSorted as slot (slot.time)}
                <div class="border-b-2 border-slate-300 p-5 flex flex-col items-center justify-center gap-5 lg:flex-row lg:p-10 w-full flex-wrap">  
                    {#each slot.games as game (game.id)}
                        <div id={"game" + game.id} class="scroll-mt-5 sm:scroll-mt-10">
                            <Result team1={game.team1} team2={game.team2} time={game.time.substring(0, 5)} poule={game.poule.name} court_num={game.court_num} ref={game.ref} banner color={game.poule.color}/>
                        </div>
                    {/each}
                </div> 
            {:else}
                {#if !loaded} 
                    <Loader {error}/>
                {:else}
                    <div class="text-center">
                        <p>Er zijn geen wedstrijden gepland.</p>
                    </div>
                {/if}
            {/each}
        </div>
    </div>
</template>
