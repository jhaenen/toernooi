<script lang="ts">
    import page from 'page';

    import Home from '@/routes/home.svelte';
    import Results from '@/routes/results.svelte';

    let current = Home;

    page('/', () => {current = Home;});
    page('/results', () => {current = Results;});

    page.start();
</script>

<main>
    <svelte:component this={current} />
</main>
