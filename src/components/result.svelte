<script lang="ts">
    import type { Team, TeamResult } from "@/types/types";

    export let team1: TeamResult; 
    export let team2: TeamResult;
    export let time = "00:00";
    export let poule = "";
    export let court_num = 0;
    export let ref: Team | null = null;
    export let banner = false;

    export let color = "#000000";
</script>

<template>
    <div class="relative border-2 rounded-lg border-slate-400 p-2 text-center max-w-sm w-fit">
        <div class="text-xs font-bold mt-2">
            <span class:hidden={poule===""}>{poule}</span>
            <span class="mx-1" class:hidden={poule==="" || court_num==0}>-</span>
            <span class:hidden={court_num==0}>Veld {court_num}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <div class="w-[35vw] mi:w-40">
                <div class="m-1 mi:leading-7 font-bold" style="font-size: min(1rem, 1.25rem);">{team1.name}</div>
                <div class="text-[8vw] mi:text-4xl font-light">{team1.score}</div>
            </div>
            
            <div class="mt-6 text-sm">
                <div class="mb-1">VS</div>
                <div>{time}</div>
            </div>

            <div class="w-[35vw] mi:w-40">
                <div class="m-1 mi:leading-7 font-bold" style="font-size: min(1rem, 1.25rem);">{team2.name}</div>
                <div class="text-[8vw] mi:text-4xl font-light">{team2.score}</div>
            </div>
        </div>

        {#if ref !== null}
            <div class="text-xs my-2" >
                <div class="font-bold">Tafel/Scheids</div> 
                <div>{ref.name}</div>
            </div>
        {:else}
            <div class="text-xs my-2 select-none">
                <div class="font-bold">&nbsp;</div> 
                <div>&nbsp;</div>
            </div>
        {/if}
        
        <div class="absolute w-2 h-full top-0 right-0 -z-10" class:hidden={!banner} style={"background-color: " + color}></div>
    </div>
</template>