<script lang="ts">
    // Framework
    import Router from 'svelte-spa-router';
    import { wrap } from 'svelte-spa-router/wrap';

    // Components
    import NavButton from '@/components/nav-button.svelte';
    import Loader from '@/components/load-ball.svelte';

    // Assets
    import logo from "@/assets/logo.svg";
    import home_icon from "@/assets/icons/house.svg";
    import poules_icon from "@/assets/icons/basketball.svg";
    import schedule_icon from "@/assets/icons/newspaper.svg";
    import org_icon from "@/assets/icons/org.svg";


    // Routes
    const routes = {
        '/': wrap({asyncComponent: () => import('@/routes/schema.svelte'), loadingComponent: Loader}),
        '/poules': wrap({asyncComponent: () => import('@/routes/poules.svelte'), loadingComponent: Loader}),
        '/poules/:id': wrap({asyncComponent: () => import('@/routes/poule_stats.svelte'), loadingComponent: Loader}),
        '/info': wrap({asyncComponent: () => import('@/routes/info.svelte'), loadingComponent: Loader}),
        '*': wrap({asyncComponent: () => import('@/routes/404.svelte'), loadingComponent: Loader}),
    };

    const icon_color = '#ffffff';
</script>

<main>
    <!-- Navigation bar -->
    <div class="fixed bottom-0 w-full m-0 p-0 sm:top-0 sm:bottom-auto z-10">
        <nav class="flex h-[82px] sm:h-[100px] justify-evenly flex-wrap sm:justify-start sm:items-center bg-primary text-white py-4 sm:p3">
            <a href="#/" class="hidden h-16 w-16 mx-4 sm:block"><img src={logo} alt="Svelte Logo"/></a>
            <a href="#/" class="w-24 sm:py-3 sm:px-4 sm:w-auto"><NavButton label="Dagschema" icon={home_icon} color={icon_color}/></a>
            <a href="#/poules" class="w-24 sm:py-3 sm:px-4 sm:w-auto"><NavButton label="Poules" icon={poules_icon} color={icon_color}/></a>
            <a href="#/info" class="w-24 sm:py-3 sm:px-4 sm:w-auto"><NavButton label="Info" icon={schedule_icon} color={icon_color}/></a>
            <!-- <a href="#/org" class="w-24 sm:py-3 sm:px-4 hidden mi:block sm:w-auto"><NavButton label="Organisatie" icon={org_icon} color={icon_color}/></a> -->
        </nav>
    </div>
    
    <!-- Main content -->
    <div class="p-1 absolute w-full top-0 bottom-[78px] sm:top-[78px] overflow-y-scroll sm:bottom-0">
        <Router {routes}/>
        <div class="sm:hidden h-5"></div>
    </div>
</main>