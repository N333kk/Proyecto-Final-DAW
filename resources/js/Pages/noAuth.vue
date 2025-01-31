<script setup>

import { Link } from '@inertiajs/vue3';

</script>

<template>

    <div class="min-h-screen min-w-screen flex flex-col items-center justify-center bg-zinc-800">
        <div class="text-neutral-200 text-center">
        <h1 class="font-bold text-4xl">Unauthorized</h1>
        <p class="font-bold">No puedes acceder al dashboard, no eres admin!.</p>
        <div class="flex items-center justify-center mt-4">
            <Link class="text-center rounded bg-red-800 px-4 py-1 border border-zinc-700" href="/">Go back to the home page</Link>
        </div>
        </div>
    </div>
</template>
