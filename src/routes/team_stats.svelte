<script lang="ts">
    import { onMount } from "svelte";
    import type { Game, Poule } from "@/types/types";
    import Result from "@/components/result.svelte";
    import Loader from "@/components/load-ball.svelte";

    export let params = { id: -1 };

    interface TeamInfo {
        id: number;
        name: string;
        poule: Poule;
    }

    interface MergedEntry {
        game: Game;
        isRef: boolean;
    }

    let team: TeamInfo | null = null;
    let mergedList: Array<MergedEntry> = [];
    let error = false;
    let loaded = false;

    let previousId: number | null = null;

    $: if (params.id !== previousId) {
        previousId = params.id;
        loadData();
    }

    onMount(async () => {
        await loadData();
    });

    window.addEventListener('online', () => { loadData(); });

    async function loadData() {
        const server = import.meta.env.VITE_SERVER_URL;
        try {
            const response = await fetch(server + "teams/" + params.id + "/stats");
            const data = await response.json();
            team = data.team;

            // Tag each game with whether this team is refereeing it
            const played: Array<MergedEntry> = (data.played_games as Game[]).map(g => ({ game: g, isRef: false }));
            const refs:   Array<MergedEntry> = (data.ref_games   as Game[]).map(g => ({ game: g, isRef: true  }));

            // Merge and sort by time string (HH:MM — lexicographic sort works here)
            mergedList = [...played, ...refs].sort((a, b) =>
                a.game.time.localeCompare(b.game.time)
            );

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    }

    function resultLabel(game: Game, teamId: number): string {
        const played = game.team1.score > 0 || game.team2.score > 0;
        if (!played) return "";
        const isTeam1  = game.team1.id === teamId;
        const myScore  = isTeam1 ? game.team1.score : game.team2.score;
        const oppScore = isTeam1 ? game.team2.score : game.team1.score;
        if (myScore > oppScore) return "W";
        if (myScore < oppScore) return "V";
        return "G";
    }

    function resultColor(label: string): string {
        if (label === "W") return "text-green-600";
        if (label === "V") return "text-red-500";
        if (label === "G") return "text-yellow-500";
        return "";
    }
</script>

<template>
    <div class="flex flex-col items-center m-4">
        {#if loaded && team}
            <!-- Header -->
            <div class="w-full border-b-2 py-2 my-6 flex flex-col items-center sm:items-start"
                 style={"border-color:" + team.poule.color}>
                <h1 class="text-[10vw] leading-none font-light mi:text-4xl">{team.name}</h1>
            </div>

            <!-- Merged game list -->
            <div class="w-full max-w-2xl">
                {#if mergedList.length === 0}
                    <p class="text-slate-500">Geen wedstrijden gepland.</p>
                {:else}
                    <div class="flex flex-col gap-4 items-center">
                        {#each mergedList as entry (entry.game.id + (entry.isRef ? '-ref' : ''))}
                            <div class="flex items-center gap-3 justify-center w-full">
                                <Result
                                    team1={entry.game.team1}
                                    team2={entry.game.team2}
                                    time={entry.game.time.substring(0, 5)}
                                    poule={entry.game.poule.name}
                                    court_num={entry.game.court_num}
                                    ref={entry.game.ref}
                                    banner
                                    color={entry.game.poule.color}
                                    highlightedTeamId={team.id}
                                />
                            </div>
                        {/each}
                    </div>
                {/if}
            </div>
        {:else}
            <Loader {error} />
        {/if}
    </div>
</template>