<script lang="ts">
    import type { TeamResult } from "@/types";

    export let team1: TeamResult; 
    export let team2: TeamResult;
</script>

<template>
    <p>{team1.name} vs {team2.name}: {team1.score} - {team2.score}</p>
</template>