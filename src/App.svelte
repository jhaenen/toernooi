<script lang="ts">
    import Router from 'svelte-spa-router';

    import Home from '@/routes/home.svelte';
    import Results from '@/routes/results.svelte';

    let current = Home;

    const routes = {
        '/': Home,
        '/results': Results,
    };
</script>

<main>
    <Router {routes}/>
</main>
