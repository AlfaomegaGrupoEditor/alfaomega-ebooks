<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {computed} from 'vue';
    import { useRoute } from 'vue-router';

    const {t} = useI18n();
    const route = useRoute();
    const pageTitle = computed(() => `Alfaomega eBooks [${t(route?.name || 'dashboard')}]`);
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <main class="px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fs-6 fw-bold">{{ pageTitle }}</h1>
                    <BButton
                        v-if="route.name !== 'dashboard'"
                        variant="primary"
                        size="sm"
                        style="min-width: 100px; background-image: linear-gradient(#257ec5, #267fc6 6%, #2171B1); border: 1px solid #1b5e93;"
                        :to="{name: 'dashboard'}"
                    >
                        {{ $t('back') }}
                    </BButton>
                </div>
            </main>
            <section>
                <router-view></router-view>
            </section>
        </div>
    </div>
</template>

<style scoped>

</style>
