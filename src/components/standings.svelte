<script lang="ts">
    import type { Stats } from "@/types";

    import Toggle from "@/components/toggle.svelte";

    export let standings: Array<Stats> = [];

    let details = false;
</script>

<template>
    <div class="overflow-x-scroll overflow-y-visible p-0 w-max">
        <table>
            <thead>
                <tr>
                    <th class="px-2 text-center">Team</th>
                    <th class="px-2 text-center">G</th>
                    <th class="px-2 text-center">P</th>
                    <th class="px-2 text-center" class:hidden={!details}>W</th>
                    <th class="px-2 text-center" class:hidden={!details}>V</th>
                    <th class="px-2 text-center" class:hidden={!details}>+</th>
                    <th class="px-2 text-center" class:hidden={!details}>-</th>
                    <th class="px-2 text-center">D</th>
                </tr>
            </thead>
            <tbody>
                {#each standings as team (team.id)}
                    <tr>
                        <td class="px-2 text-center">{team.name}</td>
                        <td class="px-2 text-center">{team.played}</td>
                        <td class="px-2 text-center">{team.points}</td>
                        <td class="px-2 text-center" class:hidden={!details}>{team.won}</td>
                        <td class="px-2 text-center" class:hidden={!details}>{team.lost}</td>
                        <td class="px-2 text-center" class:hidden={!details}>{team.score_for}</td>
                        <td class="px-2 text-center" class:hidden={!details}>{team.score_against}</td>
                        <td class="px-2 text-center">{team.score_diff}</td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
    <div class="p-4"><Toggle label="Details" bind:checked={details}/></div>
</template>