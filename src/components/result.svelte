<script lang="ts">
    import type { TeamResult } from "@/types/types";

    export let team1: TeamResult; 
    export let team2: TeamResult;
    export let time = "00:00";
    export let poule = "";
</script>

<template>
    <div class="border-2 rounded-lg p-2 text-center max-w-sm w-fit">
        <div class="flex justify-between items-center">
            <div class="w-[35vw] mi:w-40">
                <div class="text-[5vw] m-1 mi:text-xl font-bold">{team1.name}</div>
                <div class="text-[8vw] mi:text-4xl font-light">{team1.score}</div>
            </div>
            
            <div class="mt-6 text-sm">
                <div class="mb-1">VS</div>
                <div>{time}</div>
            </div>

            <div class="w-[35vw] mi:w-40">
                <div class="text-[5vw] m-1 mi:text-xl font-bold">{team2.name}</div>
                <div class="text-[8vw] mi:text-4xl font-light">{team2.score}</div>
            </div>
        </div>
        <div class="text-xs font-bold mt-2" class:hidden={poule===""}>{poule}</div>
    </div>
</template>