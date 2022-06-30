<script lang="ts">
    import { onMount } from "svelte";

    import type { Game, Poule, Stats } from "@/types";

    import Loader from "@/components/load-ball.svelte";
    import Result from "@/components/result.svelte";

    let poule: Poule;
    let games: Array<Game> = [];
    let stats: Array<Stats> = [];

    let error = false;
    let loaded = false;

    export let params = {id: -1};

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
            <h1 class="text-[12vw] leading-none font-light text-center mi:text-5xl">Poule: {poule.name}</h1>


            <h2 class="m-4 font-light text-2xl">Wedstrijden</h2>
            {#each games as game (game.id)}
                <Result team1={game.team1} team2={game.team2}/>
            {:else}
                <p>Geen Wedstrijden gepland.</p>
            {/each}

            <h2 class="m-4 font-light text-2xl">Stand</h2>
            <table>
                <thead>
                    <tr>
                        <th class="px-2 text-center">Team</th>
                        <th class="px-2 text-center">G</th>
                        <th class="px-2 text-center">P</th>
                        <th class="px-2 text-center">W</th>
                        <th class="px-2 text-center">V</th>
                        <th class="px-2 text-center">+</th>
                        <th class="px-2 text-center">-</th>
                        <th class="px-2 text-center">D</th>
                    </tr>
                </thead>
                <tbody>
                    {#each stats as team (team.id)}
                        <tr>
                            <td class="px-2 text-center">{team.name}</td>
                            <td class="px-2 text-center">{team.played}</td>
                            <td class="px-2 text-center">{team.points}</td>
                            <td class="px-2 text-center">{team.won}</td>
                            <td class="px-2 text-center">{team.lost}</td>
                            <td class="px-2 text-center">{team.score_for}</td>
                            <td class="px-2 text-center">{team.score_against}</td>
                            <td class="px-2 text-center">{team.score_diff}</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        {:else}
            <div class="fixed top-[40%] sm:left-1/2"><Loader {error}/></div>
        {/if}       
    </div>
</template>